<?php

namespace App\Http\Controllers;

use App\Models\News;

class NewsController extends Controller
{
    public function index()
    {
        $news = News::published()->latest('published_at')->paginate(9);
        return view('news.index', compact('news'));
    }

    public function show(News $news)
    {
        abort_if($news->status !== 'published', 404);
        $related = News::published()
                       ->where('id', '!=', $news->id)
                       ->latest('published_at')
                       ->take(3)
                       ->get();
        return view('news.show', compact('news', 'related'));
    }
}
