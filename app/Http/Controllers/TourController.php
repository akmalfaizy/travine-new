<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Tour;

class TourController extends Controller
{
    public function category(Category $category)
    {
        $tours = Tour::where('category_id', $category->id)
                     ->where('is_active', true)
                     ->latest()
                     ->paginate(12);

        return view('tours.category', compact('category', 'tours'));
    }

    public function show(Tour $tour)
    {
        abort_if(!$tour->is_active, 404);
        $relatedTours = Tour::where('category_id', $tour->category_id)
                            ->where('id', '!=', $tour->id)
                            ->where('is_active', true)
                            ->take(3)
                            ->get();

        return view('tours.show', compact('tour', 'relatedTours'));
    }
}
