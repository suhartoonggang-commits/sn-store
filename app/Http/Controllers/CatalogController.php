<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'category' => ['nullable', 'string', 'max:255'],
        ]);
        $searchQuery = trim($filters['q'] ?? '');
        $categorySlug = $filters['category'] ?? null;

        $categories = Category::query()
            ->where('is_active', true)
            ->withCount(['products' => fn (Builder $query) => $query->where('is_active', true)])
            ->orderBy('name')
            ->get();

        $products = Product::query()
            ->with('category')
            ->where('is_active', true)
            ->when($searchQuery !== '', function (Builder $query) use ($searchQuery): void {
                $query->where(function (Builder $query) use ($searchQuery): void {
                    $query->where('name', 'like', "%{$searchQuery}%")
                        ->orWhere('description', 'like', "%{$searchQuery}%");
                });
            })
            ->when($categorySlug, fn (Builder $query) => $query->whereHas(
                'category',
                fn (Builder $query) => $query->where('slug', $categorySlug)->where('is_active', true)
            ))
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->paginate(12)
            ->withQueryString();

        return view('pages.public.products', [
            'categories' => $categories,
            'products' => $products,
            'searchQuery' => $searchQuery,
            'selectedCategory' => $categorySlug,
        ]);
    }
}
