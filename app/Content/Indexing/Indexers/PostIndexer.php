<?php

namespace App\Content\Indexing\Indexers;

use App\Content\DTO\PostDTO;
use App\Content\Indexing\ContentTypeIndexerInterface;
use App\Content\Indexing\IndexerOutputInterface;
use App\Content\Translation\TranslationsIndexerService;
use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Support\Str;
use SplFileInfo;

class PostIndexer extends AbstractContentIndexer implements ContentTypeIndexerInterface
{
    use CreatesRedirects;

    /** @var string Optional text marker which indicates end of an excerpt within the posts */
    private const MORE_DELIMITER = '{{{more}}}';

    public function index(SplFileInfo $file): void
    {
        $this->output->indentedLine($file->getFilename());

        $post = $this->parseContentFile($file->getPathname(), [
            'aliases' => [],
            'categories' => [],
            'language' => config('app.locale'),
            'slug' => $file->getBasename('.md'),
            'tags' => [],
            'translations' => [],
        ], PostDTO::class);

        $this->validateMetadata($post, [
            'aliases' => 'array',
            'categories' => 'required|array',
            'date' => 'required|date',
            'project' => 'exists:indexer.projects,slug',
            'slug' => 'alpha_dash|unique:indexer.posts',
            'tags' => 'array',
            'title' => 'required',
            'translations' => 'array',
        ]);

        $excerpt = null;
        $contentParts = explode(self::MORE_DELIMITER, $post->getContent(), 2);

        if (count($contentParts) === 2) {
            $excerpt = trim($contentParts[0]);

            $this->output->indentedLine('> indexed excerpt for the post', 2, IndexerOutputInterface::VERBOSITY_VERBOSE);
        }

        $content = str_replace(self::MORE_DELIMITER, '', $post->getContent());

        $postEntity = Post::create([
            'title' => $post->getTitle(),
            'slug' => $post->getSlug(),
            'project' => $post->getProject(),
            'excerpt' => $excerpt,
            'content' => $content,
            'content_searchable' => $this->createSearchableContent($content),
            'language' => $post->getLanguage(),
            'created_at' => $post->getCreatedAt(),
        ]);

        $translationsIndexer = new TranslationsIndexerService($this->output);
        $translationsIndexer->processTranslations($postEntity, $post->getTranslations());

        foreach ($post->getAliases() as $alias) {
            $this->createAlias($postEntity, $alias);
        }

        foreach ($post->getCategories() as $category) {
            $category = $this->createCategory($category);

            $postEntity->categories()->attach($category->id);
        }

        foreach ($post->getTags() as $tag) {
            $tag = $this->createTag($tag);

            $postEntity->tags()->attach($tag->id);
        }
    }

    private function createAlias(Post $post, string $alias): void
    {
        $this->output->indentedLine('> aliased post from ' . $alias, 2, IndexerOutputInterface::VERBOSITY_VERBOSE);

        $this->createRedirect($alias, route('post', $post, false), 302);
    }

    private function createCategory(string $name): Category
    {
        $category = Category::where('name', $name)->first();

        if ($category === null) {
            $this->output->indentedLine('> created category "' . $name . '"', 2, IndexerOutputInterface::VERBOSITY_VERBOSE);

            $category = Category::create([
                'name' => $name,
                'slug' => Str::slug(str_replace('&', '-', $name)),
            ]);
        }

        $this->output->indentedLine('> assigned to category "' . $name . '"', 2, IndexerOutputInterface::VERBOSITY_VERBOSE);

        return $category;
    }

    private function createTag(string $name): Tag
    {
        $tag = Tag::where('name', $name)->first();

        if ($tag === null) {
            $this->output->indentedLine('> created tag "' . $name . '"', 2, IndexerOutputInterface::VERBOSITY_VERBOSE);

            $tag = Tag::create([
                'name' => $name,
                'slug' => Str::slug($name),
            ]);
        }

        $this->output->indentedLine('> assigned to tag "' . $name . '"', 2, IndexerOutputInterface::VERBOSITY_VERBOSE);

        return $tag;
    }

    private function createSearchableContent(string $content): string
    {
        $content = strip_tags($content);

        $trimmedLines = array_map(function (string $line) {
            return trim($line, ' ');
        }, explode("\n", $content));

        $content = join("\n", $trimmedLines);

        // Replace single newlines with space character
        return preg_replace('/(?<!\n)\n(?!\n)/', ' ', $content);
    }
}
