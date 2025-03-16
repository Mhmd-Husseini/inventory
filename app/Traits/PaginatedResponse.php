<?php

namespace App\Traits;

use Illuminate\Pagination\LengthAwarePaginator;

trait PaginatedResponse
{
    /**
     * The default number of items per page.
     *
     * @var int
     */
    protected $perPage = 15;

    /**
     * Generate a paginated response.
     *
     * @param LengthAwarePaginator $paginator
     * @return array
     */
    protected function paginatedResponse(LengthAwarePaginator $paginator): array
    {
        return [
            'data' => $paginator->items(),
            'pagination' => [
                'total' => $paginator->total(),
                'per_page' => $paginator->perPage(),
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'from' => $paginator->firstItem(),
                'to' => $paginator->lastItem(),
            ],
        ];
    }
} 