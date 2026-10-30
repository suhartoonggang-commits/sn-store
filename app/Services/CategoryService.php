<?php

namespace App\Services;

use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;

class CategoryService
{
    /**
     * @return Collection<int, Category>
     */
    public function getAll(): Collection
    {
        return Category::query()
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->get();
    }

    /**
     * @return Collection<int, Category>
     */
    public function getAllByName(): Collection
    {
        return Category::query()
            ->orderBy('name')
            ->get();
    }

    public function count(): int
    {
        return Category::query()->count();
    }

    /**
     * @param  array{name: string, description?: ?string, is_active: bool|string|int}  $attributes
     */
    public function create(array $attributes): Category
    {
        $attributes['slug'] = $this->uniqueSlug($attributes['name']);

        return Category::query()->create($attributes);
    }

    /**
     * @param  array{name: string, description?: ?string, is_active: bool|string|int}  $attributes
     */
    public function update(Category $category, array $attributes): void
    {
        $attributes['slug'] = $this->uniqueSlug($attributes['name'], $category);

        $category->update($attributes);
    }

    public function delete(Category $category): void
    {
        $category->delete();
    }

    private function uniqueSlug(string $name, ?Category $category = null): string
    {
        $baseSlug = Str::slug($name) ?: 'category';
        $slug = $baseSlug;
        $suffix = 2;

        while (Category::query()
            ->where('slug', $slug)
            ->when($category, fn ($query) => $query->where($category->getKeyName(), '!=', $category->getKey()))
            ->exists()) {
            $slug = "{$baseSlug}-{$suffix}";
            $suffix++;
        }

        return $slug;
    }
}
