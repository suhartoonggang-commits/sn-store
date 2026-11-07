<?php

namespace App\Support;

use Illuminate\Http\JsonResponse;

class ApiResponse
{
    public static function success(
        mixed $data = null,
        string $message = 'Permintaan berhasil diproses.',
        int $code = 200
    ): JsonResponse {
        return self::make($code, 'success', $message, $data);
    }

    public static function error(
        string $message,
        int $code = 400,
        mixed $data = null
    ): JsonResponse {
        return self::make($code, 'error', $message, $data);
    }

    private static function make(int $code, string $status, string $message, mixed $data): JsonResponse
    {
        return response()->json([
            'code' => $code,
            'status' => $status,
            'message' => $message,
            'data' => $data,
        ], $code);
    }
}
