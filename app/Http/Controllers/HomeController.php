<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\News;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $portfolios  = \App\Models\Portfolio::latest()->get();
        $categories  = Category::where('is_active', true)->withCount('activeTours')->get();
        $latestNews  = News::published()->latest('published_at')->take(3)->get();
        $banners     = \App\Models\Banner::active()->ordered()->with('tour')->get();
        $popupBanner = $banners->firstWhere('is_popup', true);

        return view('home', compact('portfolios', 'categories', 'latestNews', 'banners', 'popupBanner'));
    }
}
