<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BlogController extends Controller
{
    public function index()
    {
        return $this->buildPostsResponseForLanguage(null);
    }

    public function indexPolish()
    {
        return $this->buildPostsResponseForLanguage('pl');
    }

    public function indexEnglish()
    {
        return $this->buildPostsResponseForLanguage('en');
    }

    public function show(Post $post)
    {
        return view('blog.single', [
            'language' => $post->language,
            'post' => $post,
            'single' => true,
            'title' => page_title($post->title),
        ]);
    }

    public function category(Category $category)
    {
        return view('blog.category', [
            'body_classes' => ['archive', 'category'],
            'category' => $category,
            'posts' => $category->posts()->with(['project'])->latest()->paginate(10)->onEachSide(2),
            'title' => page_title(app()->getLocale() === 'pl' ? $category->name_pl : $category->name_en),
        ]);
    }

    public function search(Request $request)
    {
        $phrase = $request->get('q');
        $phraseQuoted = str_replace("'", "\\'", $phrase);

        $posts = Post::with(['project'])
            ->where('title', 'like', "%{$phrase}%")
            ->orWhere('content_searchable', 'like', "%{$phrase}%")
            ->orderBy(DB::raw("title LIKE '%{$phraseQuoted}%'"), 'desc')
            ->latest()
            ->paginate(10)
            ->onEachSide(2);

        return view('blog.search', [
            'body_classes' => ['archive', 'search'],
            'phrase' => $phrase,
            'posts' => $posts,
            'title' => page_title(__('blog.search.title')),
        ]);
    }

    public function tag(Tag $tag)
    {
        return view('blog.tag', [
            'body_classes' => ['archive', 'tag'],
            'posts' => $tag->posts()->with(['project'])->latest()->paginate(10)->onEachSide(2),
            'tag' => $tag,
            'title' => page_title($tag->name),
        ]);
    }

    private function buildPostsResponseForLanguage(?string $language)
    {
        $posts = Post::query()
            ->with(['project'])
            ->latest()
            ->when($language, function (Builder $query, string $language) {
                $query->where('language', '=', $language);
            })
            ->paginate(10)
            ->onEachSide(2);

        if ($posts->isEmpty()) {
            abort(404);
        }

        return view('blog.index', [
            'posts' => $posts,
            'title' => blog_title($posts->currentPage()),
        ]);
    }
}
