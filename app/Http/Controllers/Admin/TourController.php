<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Category;
use App\Models\Tour;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TourController extends Controller
{
    public function index()
    {
        $tours = Tour::with('category')->latest()->get();
        return view('admin.tours.index', compact('tours'));
    }

    public function create()
    {
        $categories = Category::where('is_active', true)->get();
        return view('admin.tours.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'category_id'           => 'required|exists:categories,id',
            'title'                 => 'required|string|max:255',
            'price'                 => 'required|numeric|min:0',
            'hpp_price'             => 'nullable|numeric|min:0',
            'commission_percentage' => 'nullable|numeric|min:0|max:100',
            'location'              => 'nullable|string|max:255',
            'duration'              => 'nullable|string|max:100',
            'departure_date'        => 'nullable|date',
            'short_description'     => 'nullable|string',
            'description'           => 'nullable|string',
            'itinerary'             => 'nullable|string',
            'facilities'            => 'nullable|string',
            'terms'                 => 'nullable|string',
            'is_active'             => 'boolean',
            'thumbnail'             => 'nullable|image|max:3072',
            'gallery.*'             => 'nullable|image|max:3072',
        ]);
        $data['hpp_price']             = $data['hpp_price'] ?? 0;
        $data['commission_percentage'] = $data['commission_percentage'] ?? 0;

        $data['slug']      = Str::slug($data['title']) . '-' . Str::random(4);
        $data['is_active'] = $request->boolean('is_active', true);

        if ($request->hasFile('thumbnail')) {
            $data['thumbnail'] = $request->file('thumbnail')->store('tours', 'public');
        }

        if ($request->hasFile('gallery')) {
            $gallery = [];
            foreach ($request->file('gallery') as $file) {
                $gallery[] = $file->store('tours/gallery', 'public');
            }
            $data['gallery'] = $gallery;
        }

        Tour::create($data);
        ActivityLog::record("Menambahkan paket wisata: {$data['title']}");

        return redirect()->route('admin.tours.index')->with('success', 'Paket wisata berhasil ditambahkan.');
    }

    public function edit(Tour $tour)
    {
        $categories = Category::where('is_active', true)->get();
        return view('admin.tours.edit', compact('tour', 'categories'));
    }

    public function update(Request $request, Tour $tour)
    {
        $data = $request->validate([
            'category_id'           => 'required|exists:categories,id',
            'title'                 => 'required|string|max:255',
            'price'                 => 'required|numeric|min:0',
            'hpp_price'             => 'nullable|numeric|min:0',
            'commission_percentage' => 'nullable|numeric|min:0|max:100',
            'location'              => 'nullable|string|max:255',
            'duration'              => 'nullable|string|max:100',
            'departure_date'        => 'nullable|date',
            'short_description'     => 'nullable|string',
            'description'           => 'nullable|string',
            'itinerary'             => 'nullable|string',
            'facilities'            => 'nullable|string',
            'terms'                 => 'nullable|string',
            'is_active'             => 'boolean',
            'thumbnail'             => 'nullable|image|max:3072',
            'gallery.*'             => 'nullable|image|max:3072',
        ]);
        $data['hpp_price']             = $data['hpp_price'] ?? 0;
        $data['commission_percentage'] = $data['commission_percentage'] ?? 0;

        $data['is_active'] = $request->boolean('is_active', true);

        if ($request->hasFile('thumbnail')) {
            $data['thumbnail'] = $request->file('thumbnail')->store('tours', 'public');
        }

        if ($request->hasFile('gallery')) {
            $gallery = $tour->gallery ?? [];
            foreach ($request->file('gallery') as $file) {
                $gallery[] = $file->store('tours/gallery', 'public');
            }
            $data['gallery'] = $gallery;
        }

        $tour->update($data);
        ActivityLog::record("Mengedit paket wisata: {$tour->title}");

        return redirect()->route('admin.tours.index')->with('success', 'Paket wisata berhasil diperbarui.');
    }

    public function destroy(Tour $tour)
    {
        ActivityLog::record("Menghapus paket wisata: {$tour->title}");
        $tour->delete();
        return redirect()->route('admin.tours.index')->with('success', 'Paket wisata berhasil dihapus.');
    }
}
