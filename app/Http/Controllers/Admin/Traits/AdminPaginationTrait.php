<?php

namespace App\Http\Controllers\Admin\Traits;

trait AdminPaginationTrait
{
    /**
     * Get validated per_page value for admin pagination.
     * Clamps between 5-100 to prevent DoS attacks.
     */
    protected function getPerPage(int $default = 15): int
    {
        $userDefault = auth()->user()->admin_per_page ?? $default;
        $perPage = (int) request('per_page', $userDefault);

        return max(5, min($perPage, 100));
    }
}
