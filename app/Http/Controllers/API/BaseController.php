<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\ProductType;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class BaseController extends Controller
{
    /**
     * Authorize that a product type belongs to the authenticated user.
     *
     * @param ProductType $productType
     * @return JsonResponse|null
     */
    protected function authorizeProductType(ProductType $productType): ?JsonResponse
    {
        if ($productType->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        return null;
    }
    
    /**
     * Standard success response.
     *
     * @param mixed $data
     * @param string $message
     * @param int $code
     * @return JsonResponse
     */
    protected function successResponse($data, string $message = 'Success', int $code = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $code);
    }
    
    /**
     * Standard error response.
     *
     * @param string $message
     * @param int $code
     * @return JsonResponse
     */
    protected function errorResponse(string $message, int $code = 400): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
        ], $code);
    }
} 