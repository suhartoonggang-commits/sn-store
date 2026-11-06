<?php

namespace App\Providers;

use App\Models\Category;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\View as ViewInstance;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('pages.public.*', function (ViewInstance $view): void {
            $view->with('navigationCategories', Category::query()
                ->where('is_active', true)
                ->withCount(['products' => fn (Builder $query) => $query->where('is_active', true)])
                ->orderBy('name')
                ->get());
        });
    }
}
