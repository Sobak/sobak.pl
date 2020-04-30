<?php

declare(strict_types=1);

namespace App\Content;

use App\Interfaces\OutputInterface;
use App\Models\Category;
use App\Models\Page;
use App\Models\Post;
use App\Models\Project;
use App\Models\Redirect;
use App\Models\Tag;
use App\Utils\CommonMark\CodeBlockRenderer;
use App\Utils\CommonMark\ImageRenderer;
use App\Utils\CommonMark\LinkRenderer;
use Carbon\Carbon;
use DirectoryIterator;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use League\CommonMark\DocParser;
use League\CommonMark\Environment;
use League\CommonMark\HtmlRenderer;
use SplFileInfo;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Yaml\Yaml;

class Indexer
{
    /** @var string Placeholder which will be replaced with assets root path when parsing content */
    private const ASSETS_PATH_PLACEHOLDER = '{{{assets}}}';

    /** @var string Placeholder which will be replaced with root site URL when parsing content */
    private const BASE_URL_PLACEHOLDER = '{{{base}}}';

    /** @var string Optional text marker which indicates end of an excerpt within the posts */
    private const MORE_DELIMITER = '{{{more}}}';

    private OutputInterface $output;
    private DocParser $markdownParser;
    private HtmlRenderer $markdownHtmlRenderer;

    public function __construct(OutputInterface $output)
    {
        $this->output = $output;
    }

    public function index(bool $isDryRun, bool $enableAssetsProcessing)
    {
        $this->prepareIndexerDatabase($isDryRun);
        $this->setupMarkdownRenderer();
        $this->iterateOverContentTypes();
        $this->indexRedirects($isDryRun);
        $this->cacheBlogStats($isDryRun);
        $this->processAssets($isDryRun, $enableAssetsProcessing);
        $this->switchWebsiteDatabase($isDryRun);
    }

    private function prepareIndexerDatabase(bool $isDryRun)
    {
        if ($isDryRun === false) {
            $this->output->line('Initializing temporary indexer database');
        } else {
            $this->output->line('Running in dry run mode - no changes will be applied');
        }

        $indexerDatabase = config('database.connections.indexer.database');

        // There might be indexer database laying
        // around after failed validation or dry run
        if (file_exists($indexerDatabase)) {
            unlink($indexerDatabase);
        }

        touch($indexerDatabase);

        config('database.default', 'indexer');

        Artisan::call(
            'migrate',
            [
                '--database' => 'indexer',
                '--force' => true,
            ],
            new NullOutput()
        );
    }

    private function switchWebsiteDatabase(bool $isDryRun)
    {
        if ($isDryRun) {
            $this->output->line('Dry run finished - website database has not been changed');
            return true;
        }

        $this->output->line('Switching website database to new build');

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

    private function setupMarkdownRenderer()
    {
        $environment = Environment::createCommonMarkEnvironment();
        $environment->addBlockRenderer('League\CommonMark\Block\Element\FencedCode', new CodeBlockRenderer());
        $environment->addInlineRenderer('League\CommonMark\Inline\Element\Image', new ImageRenderer());
        $environment->addInlineRenderer('League\CommonMark\Inline\Element\Link', new LinkRenderer());

        $this->markdownParser = new DocParser($environment);
        $this->markdownHtmlRenderer = new HtmlRenderer($environment);
    }

    private function iterateOverContentTypes()
    {
        $iterator = new DirectoryIterator(config('content.path'));

        foreach ($iterator as $fileinfo) {
            if ($fileinfo->isDir() && $fileinfo->isDot() === false) {
                $this->indexContentType($fileinfo->getBasename());
            }
        }
    }

    private function indexContentType($contentType)
    {
        $indexerName = 'index' . Str::studly(Str::singular($contentType));

        if (method_exists($this, $indexerName) === false) {
            return false;
        }

        $this->output->line("\nIndexing {$contentType}");

        $iterator = new DirectoryIterator(config('content.path') . '/' . $contentType);

        foreach ($iterator as $fileinfo) {
            if ($fileinfo->isFile() && $fileinfo->getExtension() === 'md') {
                $this->{$indexerName}($fileinfo);
            }
        }

        return true;
    }

    /** @noinspection PhpUnusedPrivateMethodInspection Method name resolved dynamically but checked for existence */
    private function indexPost(SplFileInfo $file)
    {
        $this->output->indentedLine($file->getFilename());

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

            $this->output->indentedLine('> indexed excerpt for the post', 2, OutputInterface::VERBOSITY_VERBOSE);
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

    /** @noinspection PhpUnusedPrivateMethodInspection Method name resolved dynamically but checked for existence */
    private function indexPage(SplFileInfo $file)
    {
        $this->output->indentedLine($file->getFilename());

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

    /** @noinspection PhpUnusedPrivateMethodInspection Method name resolved dynamically but checked for existence */
    private function indexProject(SplFileInfo $file)
    {
        $this->output->indentedLine($file->getFilename());

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

    private function indexRedirects(bool $isDryRun)
    {
        if ($isDryRun) {
            $this->output->line("\nSkipped indexing redirects");
            return true;
        }

        $redirectsPath = config('content.path') . '/redirects.php';

        if (file_exists($redirectsPath) === false) {
            return false;
        }

        $this->output->line("\nIndexing redirects");

        /** @noinspection PhpIncludeInspection */
        $redirects = require $redirectsPath;

        foreach ($redirects as $from => $to) {
            $this->output->indentedLine("> $from => $to", 2, OutputInterface::VERBOSITY_VERBOSE);

            $this->createRedirect($from, $to);
        }

        return true;
    }

    private function cacheBlogStats(bool $isDryRun)
    {
        if ($isDryRun) {
            return;
        }

        $wordCountQuery = DB::raw("SUM(LENGTH(content) - LENGTH(REPLACE(content, ' ', '')) + 1) AS words");

        Cache::rememberForever('blog_stats', function () use ($wordCountQuery) {
            return [
                'total_posts' => Post::count(),
                'total_words' => Post::select($wordCountQuery)->first()['words'],
            ];
        });
    }

    private function processAssets(bool $isDryRun, bool $enableAssetsProcessing)
    {
        if ($enableAssetsProcessing === false || $isDryRun) {
            $this->output->line("\nSkipped assets processing\n");
            return;
        }

        $this->output->line("\nProcessing assets");

        $iterator = new DirectoryIterator(config('content.path') . '/assets/');

        $targetPath = public_path('assets/images/');

        if (is_dir($targetPath) === false) {
            mkdir($targetPath, 0777, true);
        }

        foreach ($iterator as $fileinfo) {
            if ($fileinfo->isFile()) {
                $this->output->indentedLine($fileinfo->getFilename());

                $this->copyAsset($fileinfo, $targetPath);
//              $this->createAssetThumbnail($fileinfo, $targetPath);
            }
        }

        $this->output->line('');
    }

    private function copyAsset(SplFileInfo $file, string $targetPath)
    {
        $copy = copy($file->getPathname(), $targetPath . $file->getFilename());

        if ($copy === false) {
            $this->output->indentedLine('> FAIL when copying the file', 2);
        }
    }

    private function createAssetThumbnail(SplFileInfo $file, string $targetPath)
    {
        // It's a bit ironic but thumbnails generated by Intervention's Image
        // library turn out to have much bigger size than the originals so let's
        // only make a copy for now but leave this method as a convenient place
        // to hook better implementation in the future.

        $fileName = pathinfo($file->getPathname(), PATHINFO_FILENAME);
        $targetPathName = $targetPath . $fileName . '_thumb.' . $file->getExtension();

        $copy = copy($file->getPathname(), $targetPathName);

        if ($copy === false) {
            $this->output->indentedLine('> FAIL when creating the thumbnail', 2);
        }
    }

    private function createAlias(Post $post, $alias)
    {
        $this->output->indentedLine('> aliased post from ' . $alias, 2, OutputInterface::VERBOSITY_VERBOSE);

        $this->createRedirect($alias, route('post', $post, false), 302);
    }

    private function createCategory($name)
    {
        $category = Category::where('name', $name)->first();

        if ($category === null) {
            $this->output->indentedLine('> created category "' . $name . '"', 2, OutputInterface::VERBOSITY_VERBOSE);

            $category = Category::create([
                'name' => $name,
                'slug' => Str::slug(str_replace('&', '-', $name)),
            ]);
        }

        $this->output->indentedLine('> assigned to category "' . $name . '"', 2, OutputInterface::VERBOSITY_VERBOSE);

        return $category;
    }

    private function createRedirect($from, $to, $httpCode = 301)
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

    private function createTag($name)
    {
        $tag = Tag::where('name', $name)->first();

        if ($tag === null) {
            $this->output->indentedLine('> created tag "' . $name . '"', 2, OutputInterface::VERBOSITY_VERBOSE);

            $tag = Tag::create([
                'name' => $name,
                'slug' => Str::slug($name),
            ]);
        }

        $this->output->indentedLine('> assigned to tag "' . $name . '"', 2, OutputInterface::VERBOSITY_VERBOSE);

        return $tag;
    }

    private function parseContentFile($path, array $defaultMetadata = [])
    {
        $content = file_get_contents($path);

        $pattern = '/[\s\r\n]---[\s\r\n]/s';

        $parts = preg_split($pattern, PHP_EOL . ltrim($content), 3);

        if (count($parts) < 3) {
            $this->output->indentedLine('FAIL: No YAML front matter found', 2);

            throw new IndexerException('', 2);
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

    private function parseSingularMetadataAliases($post, $mappings)
    {
        foreach ($mappings as $key => $alias) {
            if (!isset($post->metadata[$alias])) {
                continue;
            }

            $post->metadata[$key] = array_merge($post->metadata[$key], [$post->metadata[$alias]]);
        }

        return $post;
    }

    private function parseMarkdown($string)
    {
        $document = $this->markdownParser->parse($string);

        return $this->markdownHtmlRenderer->renderBlock($document);
    }

    private function validateMetadata($metadata, $rules)
    {
        $validator = Validator::make($metadata, $rules);

        foreach ($validator->errors()->all() as $error) {
            $this->output->indentedLine("FAIL: {$error}", 2);
        }

        if ($validator->fails()) {
            throw new IndexerException('', 3);
        }
    }
}
