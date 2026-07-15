<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class NewsController extends Controller
{
    public function index()
    {
        $news = News::latest()->get();
        return view('admin.news.index', compact('news'));
    }

    public function create()
    {
        return view('admin.news.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'        => 'required|string|max:255',
            'summary'      => 'nullable|string',
            'content'      => 'required|string',
            'author'       => 'nullable|string|max:100',
            'published_at' => 'nullable|date',
            'status'       => 'required|in:draft,published',
            'thumbnail'    => 'nullable|image|max:3072',
        ]);

        $data['slug']   = Str::slug($data['title']) . '-' . Str::random(4);
        $data['author'] = $data['author'] ?? auth()->user()->name;

        if ($request->hasFile('thumbnail')) {
            $data['thumbnail'] = $request->file('thumbnail')->store('news', 'public');
        }

        News::create($data);
        ActivityLog::record("Menambahkan berita: {$data['title']}");

        return redirect()->route('admin.news.index')->with('success', 'Berita berhasil ditambahkan.');
    }

    public function edit(News $news)
    {
        return view('admin.news.edit', compact('news'));
    }

    public function update(Request $request, News $news)
    {
        $data = $request->validate([
            'title'        => 'required|string|max:255',
            'summary'      => 'nullable|string',
            'content'      => 'required|string',
            'author'       => 'nullable|string|max:100',
            'published_at' => 'nullable|date',
            'status'       => 'required|in:draft,published',
            'thumbnail'    => 'nullable|image|max:3072',
        ]);

        $data['slug'] = Str::slug($data['title']);

        if ($request->hasFile('thumbnail')) {
            $data['thumbnail'] = $request->file('thumbnail')->store('news', 'public');
        }

        $news->update($data);
        ActivityLog::record("Mengedit berita: {$news->title}");

        return redirect()->route('admin.news.index')->with('success', 'Berita berhasil diperbarui.');
    }

    public function destroy(News $news)
    {
        ActivityLog::record("Menghapus berita: {$news->title}");
        $news->delete();
        return redirect()->route('admin.news.index')->with('success', 'Berita berhasil dihapus.');
    }
}
