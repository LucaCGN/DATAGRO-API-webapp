<?php

namespace App\Utilities;

class LivewirePaginationManager
{
    /**
     * Custom method to manage pagination.
     *
     * This method can be extended to include custom pagination logic
     * specific to the needs of your Livewire components.
     *
     * @param mixed $query
     * @param int $perPage
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function paginate($query, $perPage = 15)
    {
        // Implement custom pagination logic here
        // Return the paginated result
        return $query->paginate($perPage);
    }
}
