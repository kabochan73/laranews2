<?php

namespace App\Http\Controllers;

use App\Models\Article;

class ArticleController extends Controller
{
    public function index()
    {
        $topic = request('topic', 'laravel');

        $articles = Article::where('topic', $topic)->latest('published_at')->get();
        $popular = Article::where('topic', $topic)->orderByDesc('liked_count')->take(10)->get();

        return view('articles.index', compact('articles', 'popular', 'topic'));
    }
}
