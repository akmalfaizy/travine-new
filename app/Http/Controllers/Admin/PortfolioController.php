<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Portfolio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PortfolioController extends Controller
{
    public function index()
    {
        $portfolios = Portfolio::latest()->get();
        return view('admin.portfolios.index', compact('portfolios'));
    }

    public function create()
    {
        return view('admin.portfolios.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required',
            'category' => 'required',
            'description' => 'required',
            'content' => 'nullable',
            'image_file' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image_file')) {
            $path = $request->file('image_file')->store('portfolios', 'public');
            $data['image'] = basename($path);
            // Move it to public/images for consistency with existing static images
            $request->file('image_file')->move(public_path('images'), $data['image']);
        }

        unset($data['image_file']);
        Portfolio::create($data);

        return redirect()->route('admin.portfolios.index')->with('success', 'Project created successfully.');
    }

    public function edit(Portfolio $portfolio)
    {
        return view('admin.portfolios.edit', compact('portfolio'));
    }

    public function update(Request $request, Portfolio $portfolio)
    {
        $data = $request->validate([
            'title' => 'required',
            'category' => 'required',
            'description' => 'required',
            'content' => 'nullable',
            'image_file' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image_file')) {
            $path = $request->file('image_file')->store('portfolios', 'public');
            $data['image'] = basename($path);
            $request->file('image_file')->move(public_path('images'), $data['image']);
        }

        unset($data['image_file']);
        $portfolio->update($data);

        return redirect()->route('admin.portfolios.index')->with('success', 'Project updated successfully.');
    }

    public function destroy(Portfolio $portfolio)
    {
        $portfolio->delete();
        return redirect()->route('admin.portfolios.index')->with('success', 'Project deleted successfully.');
    }
}
