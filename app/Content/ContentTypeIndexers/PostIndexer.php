<?php

namespace App\Content\ContentTypeIndexers;

use App\Content\CreatesRedirects;
use App\Interfaces\OutputInterface;
use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use Carbon\Carbon;
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
        ]);

        $post = $this->parseSingularMetadataAliases($post, [
            'aliases' => 'alias',
            'categories' => 'category',
        ]);

        $this->validateMetadata($post->metadata, [
            'aliases' => 'array',
            'categories' => 'required|array',
            'date' => 'required|date',
            'project' => 'exists:indexer.projects,slug',
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
            'slug' => $post->metadata['slug'],
            'project' => $post->metadata['project'] ?? null,
            'excerpt' => $excerpt,
            'content' => $content,
            'language' => $post->metadata['language'],
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
}
