<?php

namespace App\Providers;

use App\Models\Category;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        // Enforce HTTPS in production
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        // Share active categories with all views (for dynamic navbar)
        View::composer('*', function ($view) {
            try {
                if (!Schema::hasTable('categories')) {
                    $view->with('navCategories', collect());
                    return;
                }

                $navCategories = Category::query()
                    ->where('is_active', true)
                    ->withCount('activeTours')
                    ->get();

                $view->with('navCategories', $navCategories);
            } catch (\Throwable $e) {
                $view->with('navCategories', collect());
            }
        });
    }
}
