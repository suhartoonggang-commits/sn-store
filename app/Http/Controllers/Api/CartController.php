<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\CartService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class CartController extends Controller
{
    public function show(Request $request, CartService $cartService): JsonResponse
    {
        return ApiResponse::success(
            data: $cartService->getCartData($request->user()),
            message: 'Data cart berhasil diambil.',
        );
    }

    public function store(Request $request, Product $product, CartService $cartService): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'quantity' => ['sometimes', 'integer', 'min:1'],
        ]);

        if ($validator->fails()) {
            return ApiResponse::error(
                message: 'Jumlah produk tidak valid.',
                code: 422,
                data: $validator->errors(),
            );
        }

        try {
            $cartItem = $cartService->addProduct(
                $request->user(),
                $product,
                (int) ($validator->validated()['quantity'] ?? 1),
            );
        } catch (ValidationException $exception) {
            $errors = $exception->errors();

            return ApiResponse::error(
                message: collect($errors)->flatten()->first() ?? 'Produk tidak dapat ditambahkan ke cart.',
                code: 422,
                data: $errors,
            );
        }

        return ApiResponse::success(
            data: $cartService->getCartData($request->user()),
            message: $cartItem->wasRecentlyCreated
                ? 'Produk berhasil ditambahkan ke cart.'
                : 'Jumlah produk di cart berhasil diperbarui.',
            code: $cartItem->wasRecentlyCreated ? 201 : 200,
        );
    }
}
