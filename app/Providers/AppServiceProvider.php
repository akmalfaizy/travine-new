<?php

namespace App\Providers;

use App\Models\Category;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        // Share active categories with all views (for dynamic navbar)
        View::composer('*', function ($view) {
            $navCategories = Category::where('is_active', true)->withCount('activeTours')->get();
            $view->with('navCategories', $navCategories);
        });
    }
}
