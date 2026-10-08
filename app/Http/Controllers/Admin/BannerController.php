<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\Tour;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BannerController extends Controller
{
    public function index()
    {
        $banners = Banner::with('tour')->ordered()->paginate(15);
        return view('admin.banners.index', compact('banners'));
    }

    public function create()
    {
        $tours = Tour::where('is_active', true)->orderBy('title')->get();
        return view('admin.banners.create', compact('tours'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'        => 'required|string|max:255',
            'tag'          => 'nullable|string|max:100',
            'subtitle'     => 'nullable|string|max:500',
            'image'        => 'required|image|mimes:jpeg,png,jpg,webp|max:5120',
            'mobile_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'tour_id'      => 'nullable|exists:tours,id',
            'link_url'     => 'nullable|url|max:500',
            'button_text'  => 'nullable|string|max:50',
            'order'        => 'nullable|integer|min:0',
            'is_active'    => 'boolean',
            'is_popup'     => 'boolean',
            'start_date'   => 'nullable|date',
            'end_date'     => 'nullable|date|after_or_equal:start_date',
        ]);

        $data['is_active']   = $request->has('is_active');
        $data['is_popup']    = $request->has('is_popup');
        $data['order']       = $request->input('order', 0);
        $data['button_text'] = $request->input('button_text') ?: 'Lihat Paket';

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('banners', 'public');
        }

        if ($request->hasFile('mobile_image')) {
            $data['mobile_image'] = $request->file('mobile_image')->store('banners', 'public');
        }

        Banner::create($data);

        return redirect()->route('admin.banners.index')
            ->with('success', 'Banner slide promo berhasil ditambahkan!');
    }

    public function edit(Banner $banner)
    {
        $tours = Tour::where('is_active', true)->orderBy('title')->get();
        return view('admin.banners.edit', compact('banner', 'tours'));
    }

    public function update(Request $request, Banner $banner)
    {
        $data = $request->validate([
            'title'        => 'required|string|max:255',
            'tag'          => 'nullable|string|max:100',
            'subtitle'     => 'nullable|string|max:500',
            'image'        => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'mobile_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'tour_id'      => 'nullable|exists:tours,id',
            'link_url'     => 'nullable|url|max:500',
            'button_text'  => 'nullable|string|max:50',
            'order'        => 'nullable|integer|min:0',
            'is_active'    => 'boolean',
            'is_popup'     => 'boolean',
            'start_date'   => 'nullable|date',
            'end_date'     => 'nullable|date|after_or_equal:start_date',
        ]);

        $data['is_active']   = $request->has('is_active');
        $data['is_popup']    = $request->has('is_popup');
        $data['order']       = $request->input('order', 0);
        $data['button_text'] = $request->input('button_text') ?: 'Lihat Paket';

        if ($request->hasFile('image')) {
            if ($banner->image && Storage::disk('public')->exists($banner->image)) {
                Storage::disk('public')->delete($banner->image);
            }
            $data['image'] = $request->file('image')->store('banners', 'public');
        }

        if ($request->hasFile('mobile_image')) {
            if ($banner->mobile_image && Storage::disk('public')->exists($banner->mobile_image)) {
                Storage::disk('public')->delete($banner->mobile_image);
            }
            $data['mobile_image'] = $request->file('mobile_image')->store('banners', 'public');
        }

        $banner->update($data);

        return redirect()->route('admin.banners.index')
            ->with('success', 'Banner slide promo berhasil diperbarui!');
    }

    public function destroy(Banner $banner)
    {
        if ($banner->image && Storage::disk('public')->exists($banner->image)) {
            Storage::disk('public')->delete($banner->image);
        }

        if ($banner->mobile_image && Storage::disk('public')->exists($banner->mobile_image)) {
            Storage::disk('public')->delete($banner->mobile_image);
        }

        $banner->delete();

        return redirect()->route('admin.banners.index')
            ->with('success', 'Banner berhasil dihapus!');
    }

    public function toggle(Banner $banner)
    {
        $banner->is_active = !$banner->is_active;
        $banner->save();

        $statusText = $banner->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return back()->with('success', "Banner #{$banner->id} berhasil {$statusText}.");
    }
}
