<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Throwable;

class ProductService
{
    /**
     * @return LengthAwarePaginator<int, Product>
     */
    public function getAll(): LengthAwarePaginator
    {
        return Product::query()
            ->with('category')
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->paginate(10);
    }

    public function count(): int
    {
        return Product::query()->count();
    }

    /**
     * @param  array{
     *     category_id: int|string,
     *     name: string,
     *     description?: ?string,
     *     price: int|float|string,
     *     stock: int|string,
     *     is_active: bool|int|string,
     *     image: UploadedFile
     * }  $attributes
     */
    public function create(array $attributes): Product
    {
        $imagePath = $this->storeImage($attributes['image']);
        unset($attributes['image']);

        try {
            return DB::transaction(fn (): Product => Product::query()->create([
                ...$attributes,
                'image_path' => $imagePath,
            ]));
        } catch (Throwable $exception) {
            $this->deleteImage($imagePath);

            throw $exception;
        }
    }

    /**
     * @param  array{
     *     category_id: int|string,
     *     name: string,
     *     description?: ?string,
     *     price: int|float|string,
     *     stock: int|string,
     *     is_active: bool|int|string,
     *     image?: ?UploadedFile
     * }  $attributes
     */
    public function update(Product $product, array $attributes): void
    {
        $image = $attributes['image'] ?? null;
        unset($attributes['image']);

        $newImagePath = $image ? $this->storeImage($image) : null;
        $previousImagePath = $product->image_path;

        try {
            DB::transaction(function () use ($attributes, $newImagePath, $product): void {
                $product->update([
                    ...$attributes,
                    ...($newImagePath === null ? [] : ['image_path' => $newImagePath]),
                ]);
            });
        } catch (Throwable $exception) {
            if ($newImagePath !== null) {
                $this->deleteImage($newImagePath);
            }

            throw $exception;
        }

        if ($newImagePath !== null && $previousImagePath !== null) {
            $this->deleteImage($previousImagePath);
        }
    }

    public function delete(Product $product): void
    {
        $imagePath = $product->image_path;

        DB::transaction(function () use ($product): void {
            $product->delete();
        });

        if ($imagePath !== null) {
            $this->deleteImage($imagePath);
        }
    }

    private function storeImage(UploadedFile $image): string
    {
        $path = $image->store('products', 'public');

        if ($path === false) {
            throw new RuntimeException('Gambar produk gagal disimpan.');
        }

        return $path;
    }

    private function deleteImage(string $path): void
    {
        if (! Storage::disk('public')->delete($path)) {
            throw new RuntimeException('Gambar produk gagal dihapus dari penyimpanan.');
        }
    }
}
