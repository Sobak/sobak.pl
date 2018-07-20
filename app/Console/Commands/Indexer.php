<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\Page;
use App\Models\Post;
use App\Models\Project;
use App\Models\Redirect;
use App\Models\Tag;
use App\Utils\CommonMark\CodeBlockRenderer;
use App\Utils\CommonMark\ImageRenderer;
use App\Utils\CommonMark\LinkRenderer;
use Cache;
use Carbon\Carbon;
use DB;
use DirectoryIterator;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;
use League\CommonMark\DocParser;
use League\CommonMark\Environment;
use League\CommonMark\HtmlRenderer;
use SplFileInfo;
use Symfony\Component\Yaml\Yaml;

class Indexer extends Command
{
    const ASSETS_PATH_PLACEHOLDER = '{{{assets}}}';
    const BASE_URL_PLACEHOLDER = '{{{base}}}';
    const MORE_DELIMITER = '{{{more}}}';
    const VERBOSITY_NONE = null;
    const VERBOSITY_VERBOSE = 'v';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'content:index
        {--D|dry-run : Dry run does not alter the live database}
        {--N|no-assets : Skip assets processing}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Indexes content for the homepage';

    /**
     * Instance of Markdown parser with proper environment config.
     *
     * @var DocParser
     */
    protected $markdownParser;

    /**
     * Instance of Markdown HTML renderer with proper environment config.
     *
     * @var HtmlRenderer
     */
    protected $markdownHtmlRenderer;

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $timeStart = microtime(true);

        $this->info('Indexing started');

        $this->prepareIndexerDatabase();
        $this->setupMarkdownRenderer();
        $this->iterateOverContentTypes();
        $this->indexRedirects();
        $this->cacheBlogStats();
        $this->processAssets();
        $this->switchWebsiteDatabase();

        $time = number_format(microtime(true) - $timeStart, 4);

        $this->info("Indexing finished in {$time}");
    }

    protected function prepareIndexerDatabase()
    {
        if ($this->option('dry-run') === false) {
            $this->line('Initializing temporary indexer database');
        } else {
            $this->line('Running in dry run mode - no changes will be applied');
        }

        $indexerDatabase = config('database.connections.indexer.database');

        // There might be indexer database laying
        // around after failed validation or dry run
        if (file_exists($indexerDatabase)) {
            unlink($indexerDatabase);
        }

        touch($indexerDatabase);

        config('database.default', 'indexer');

        $this->callSilent('migrate', [
            '--database' => 'indexer',
            '--force' => true,
        ]);
    }

    protected function switchWebsiteDatabase()
    {
        if ($this->option('dry-run')) {
            $this->line('Dry run finished - indexer database will be overriden next time');
            return true;
        }

        $this->line('Switching website database to new build');

        $indexerDatabase = config('database.connections.indexer.database');
        $websiteDatabase = config('database.connections.website.database');

        DB::disconnect('indexer');

        // Website database doesn't exist on first run
        if (file_exists($websiteDatabase)) {
            unlink($websiteDatabase);
        }

        rename($indexerDatabase, $websiteDatabase);

        return true;
    }

    protected function setupMarkdownRenderer()
    {
        $environment = Environment::createCommonMarkEnvironment();
        $environment->addBlockRenderer('League\CommonMark\Block\Element\FencedCode', new CodeBlockRenderer());
        $environment->addInlineRenderer('League\CommonMark\Inline\Element\Image', new ImageRenderer());
        $environment->addInlineRenderer('League\CommonMark\Inline\Element\Link', new LinkRenderer());

        $this->markdownParser = new DocParser($environment);
        $this->markdownHtmlRenderer = new HtmlRenderer($environment);
    }

    protected function iterateOverContentTypes()
    {
        $iterator = new DirectoryIterator(config('content.path'));

        foreach ($iterator as $fileinfo) {
            if ($fileinfo->isDir() && $fileinfo->isDot() === false) {
                $this->indexContentType($fileinfo->getBasename());
            }
        }
    }

    protected function indexContentType($contentType)
    {
        $indexerName = 'index' . studly_case(str_singular($contentType));

        if (method_exists($this, $indexerName) === false) {
            return false;
        }

        $this->line("\nIndexing {$contentType}");

        $iterator = new DirectoryIterator(config('content.path') . '/' . $contentType);

        foreach ($iterator as $fileinfo) {
            if ($fileinfo->isFile() && $fileinfo->getExtension() === 'md') {
                $this->{$indexerName}($fileinfo);
            }
        }
    }

    protected function indexPost(SplFileInfo $file)
    {
        $this->indentedLine($file->getFilename());

        $post = $this->parseContentFile($file->getPathname(), [
            'aliases' => [],
            'categories' => [],
            'language' => config('app.locale'),
            'slug' => $file->getBasename('.md'),
            'tags' => [],
        ]);

        $post = $this->parseSingularMetadataAliases($post, [
            'aliases' => 'alias',
            'categories' => 'category',
        ]);

        $this->validateMetadata($post->metadata, [
            'aliases' => 'array',
            'categories' => 'required|array',
            'date' => 'required|date',
            'slug' => 'alpha_dash|unique:indexer.posts',
            'tags' => 'array',
            'title' => 'required',
        ]);

        $excerpt = null;
        $contentParts = explode(self::MORE_DELIMITER, $post->body, 2);

        if (count($contentParts) === 2) {
            $excerpt = trim($contentParts[0]);

            $this->indentedLine('> indexed excerpt for the post', 2, self::VERBOSITY_VERBOSE);
        }

        $content = str_replace(self::MORE_DELIMITER, '', $post->body);

        $postEntity = Post::create([
            'title' => $post->metadata['title'],
            'excerpt' => $excerpt,
            'content' => $content,
            'language' => $post->metadata['language'],
            'slug' => $post->metadata['slug'],
            'created_at' => Carbon::createFromTimestamp(strtotime($post->metadata['date'])),
        ]);

        foreach ($post->metadata['aliases'] as $alias) {
            $this->createAlias($postEntity, $alias);
        }

        foreach ($post->metadata['categories'] as $category) {
            $category = $this->createCategory($category);

            $postEntity->categories()->attach($category->id);
        }

        foreach ($post->metadata['tags'] as $tag) {
            $tag = $this->createTag($tag);

            $postEntity->tags()->attach($tag->id);
        }
    }

    protected function indexPage(SplFileInfo $file)
    {
        $this->indentedLine($file->getFilename());

        $page = $this->parseContentFile($file->getPathname(), [
            'slug' => $file->getBasename('.md'),
        ]);

        $this->validateMetadata($page->metadata, [
            'slug' => 'alpha_dash|unique:indexer.pages',
            'title' => 'required',
        ]);

        Page::create([
            'title' => $page->metadata['title'],
            'content' => $page->body,
            'slug' => $page->metadata['slug'],
        ]);
    }

    protected function indexProject(SplFileInfo $file)
    {
        $this->indentedLine($file->getFilename());

        $project = $this->parseContentFile($file->getPathname(), [
            'slug' => $file->getBasename('.md'),
            'url' => null,
        ]);

        $this->validateMetadata($project->metadata, [
            'date' => 'required|date',
            'slug' => 'alpha_dash|unique:indexer.pages',
            'title' => 'required',
            'thumbnail' => 'required',
            'type' => 'required',
        ]);

        Project::create([
            'title' => $project->metadata['title'],
            'content' => $project->body,
            'url' => $project->metadata['url'],
            'slug' => $project->metadata['slug'],
            'type' => $project->metadata['type'],
            'thumbnail' => $project->metadata['thumbnail'],
            'created_at' => Carbon::createFromTimestamp(strtotime($project->metadata['date'])),
        ]);
    }

    protected function indexRedirects()
    {
        $redirectsPath = config('content.path') . '/redirects.php';

        if (file_exists($redirectsPath) === false) {
            return false;
        }

        $this->line("\nIndexing redirects");

        $redirects = require $redirectsPath;

        foreach ($redirects as $from => $to) {
            $this->indentedLine("> $from => $to", 2, self::VERBOSITY_VERBOSE);

            $this->createRedirect($from, $to);
        }

        return true;
    }

    protected function cacheBlogStats()
    {
        $wordCountQuery = DB::raw("SUM(LENGTH(content) - LENGTH(REPLACE(content, ' ', '')) + 1) AS words");

        Cache::rememberForever('blog_stats', function () use ($wordCountQuery) {
            return [
                'total_posts' => Post::count(),
                'total_words' => Post::select($wordCountQuery)->first()['words'],
            ];
        });
    }

    protected function processAssets()
    {
        if ($this->option('no-assets')) {
            $this->line("\nSkipped assets processing\n");
            return true;
        }

        $this->line("\nProcessing assets");

        $iterator = new DirectoryIterator(config('content.path') . '/assets/');

        $targetPath = public_path('assets/images/');

        if (is_dir($targetPath) === false) {
            mkdir($targetPath, 0777, true);
        }

        foreach ($iterator as $fileinfo) {
            if ($fileinfo->isFile()) {
                $this->indentedLine($fileinfo->getFilename());

                $this->copyAsset($fileinfo, $targetPath);
//              $this->createAssetThumbnail($fileinfo, $targetPath);
            }
        }

        $this->line('');
    }

    protected function copyAsset(SplFileInfo $file, string $targetPath)
    {
        $copy = copy($file->getPathname(), $targetPath . $file->getFilename());

        if ($copy === false) {
            $this->indentedLine('> FAIL when copying the file', 2);
        }
    }

    protected function createAssetThumbnail(SplFileInfo $file, string $targetPath)
    {
        // It's a bit ironic but thumbnails generated by Intervention's Image
        // library turn out to have much bigger size than the originals so let's
        // only make a copy for now but leave this method as a convenient place
        // to hook better implementation in the future.

        $fileName = pathinfo($file->getPathname(), PATHINFO_FILENAME);
        $targetPathName = $targetPath . $fileName . '_thumb.' . $file->getExtension();

        $copy = copy($file->getPathname(), $targetPathName);

        if ($copy === false) {
            $this->indentedLine('> FAIL when creating the thumbnail', 2);
        }
    }

    protected function createAlias(Post $post, $alias)
    {
        $this->indentedLine('> aliased post from ' . $alias, 2, self::VERBOSITY_VERBOSE);

        $this->createRedirect($alias, route('post', $post, false), 302);
    }

    protected function createCategory($name)
    {
        $category = Category::where('name', $name)->first();

        if ($category === null) {
            $this->indentedLine('> created category "' . $name . '"', 2, self::VERBOSITY_VERBOSE);

            $category = Category::create([
                'name' => $name,
                'slug' => str_slug(str_replace('&', '-', $name)),
            ]);
        }

        $this->indentedLine('> assigned to category "' . $name . '"', 2, self::VERBOSITY_VERBOSE);

        return $category;
    }

    protected function createRedirect($from, $to, $httpCode = 301)
    {
        $redirect = Redirect::where('source_url', $from)->first();

        if ($redirect === null) {
            $redirect = Redirect::create([
                'source_url' => $from,
                'target_url' => $to,
                'http_code' => $httpCode,
            ]);
        }

        return $redirect;
    }

    protected function createTag($name)
    {
        $tag = Tag::where('name', $name)->first();

        if ($tag === null) {
            $this->indentedLine('> created tag "' . $name . '"', 2, self::VERBOSITY_VERBOSE);

            $tag = Tag::create([
                'name' => $name,
                'slug' => str_slug($name),
            ]);
        }

        $this->indentedLine('> assigned to tag "' . $name . '"', 2, self::VERBOSITY_VERBOSE);

        return $tag;
    }

    protected function parseContentFile($path, array $defaultMetadata = [])
    {
        $content = file_get_contents($path);

        $pattern = '/[\s\r\n]---[\s\r\n]/s';

        $parts = preg_split($pattern, PHP_EOL . ltrim($content), 3);

        if (count($parts) < 3) {
            $this->indentedLine('FAIL: No YAML front matter found', 2);
            exit(2);
        }

        $body = $parts[2];
        $metadata = Yaml::parse(trim($parts[1]));

        // Try to read the title from Markdown
        if (isset($metadata['title']) === false) {
            $bodyLines = explode("\n", $body);

            if (isset($bodyLines[0]) && substr($bodyLines[0], 0, 2) === '# ') {
                $metadata['title'] = substr($bodyLines[0], 2);

                unset($bodyLines[0]);

                $body = implode("\n", $bodyLines);
            }
        }

        $body = strtr($body, [
            self::ASSETS_PATH_PLACEHOLDER => asset('assets/images'),
            self::BASE_URL_PLACEHOLDER => route('index'),
        ]);

        return (object) [
            'body' => $this->parseMarkdown($body),
            'metadata' => array_merge($defaultMetadata, $metadata),
        ];
    }

    protected function parseSingularMetadataAliases($post, $mappings)
    {
        foreach ($mappings as $key => $alias) {
            if (!isset($post->metadata[$alias])) {
                continue;
            }

            $post->metadata[$key] = array_merge($post->metadata[$key], [$post->metadata[$alias]]);
        }

        return $post;
    }

    protected function parseMarkdown($string)
    {
        $document = $this->markdownParser->parse($string);

        return $this->markdownHtmlRenderer->renderBlock($document);
    }

    protected function validateMetadata($metadata, $rules)
    {
        $validator = Validator::make($metadata, $rules);

        foreach ($validator->errors()->all() as $error) {
            $this->indentedLine("FAIL: {$error}", 2);
        }

        if ($validator->fails()) {
            exit(3);
        }
    }

    protected function indentedLine($text, $levels = 1, $verbosity = self::VERBOSITY_NONE)
    {
        $indentationStep = 2;

        $indentation = str_repeat(' ', $levels * $indentationStep);

        $this->line($indentation . $text, null, $verbosity);
    }
}
