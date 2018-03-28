<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;

class BlogController extends Controller
{
    public function index()
    {
        $posts = Post::latest()->paginate(10);

        if ($posts->isEmpty()) {
            abort(404);
        }

        return view('blog.index', [
            'posts' => $posts,
            'title' => blog_title($posts->currentPage()),
        ]);
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
            'posts' => $category->posts()->latest()->paginate(10),
            'title' => page_title($category->name),
        ]);
    }

    public function tag(Tag $tag)
    {
        return view('blog.tag', [
            'body_classes' => ['archive', 'tag'],
            'posts' => $tag->posts()->latest()->paginate(10),
            'tag' => $tag,
            'title' => page_title($tag->name),
        ]);
    }
}
