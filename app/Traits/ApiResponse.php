<?php

namespace App\Traits;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;

trait ApiResponse
{
    /**
     * Standard success JSON response.
     */
    protected function successResponse(
        mixed $data = null,
        string $message = 'Success',
        int $code = 200,
        ?array $meta = null
    ): JsonResponse {
        $response = [
            'success' => true,
            'message' => $message,
            'data'    => $data,
            'errors'  => null,
        ];

        if ($meta !== null) {
            $response['meta'] = $meta;
        }

        return response()->json($response, $code);
    }

    /**
     * Standard error JSON response.
     */
    protected function errorResponse(
        string $message = 'Error',
        int $code = 400,
        mixed $errors = null
    ): JsonResponse {
        return response()->json([
            'success' => false,
            'message' => $message,
            'data'    => null,
            'errors'  => $errors,
        ], $code);
    }

    /**
     * Standard paginated JSON response.
     */
    protected function paginatedResponse(
        LengthAwarePaginator $paginator,
        ?string $resourceClass = null,
        string $message = 'Data retrieved successfully'
    ): JsonResponse {
        $items = $paginator->items();
        if ($resourceClass && is_subclass_of($resourceClass, JsonResource::class)) {
            $items = $resourceClass::collection($items);
        }

        return $this->successResponse(
            data: $items,
            message: $message,
            meta: [
                'current_page' => $paginator->currentPage(),
                'last_page'    => $paginator->lastPage(),
                'per_page'     => $paginator->perPage(),
                'total'        => $paginator->total(),
            ]
        );
    }
}
