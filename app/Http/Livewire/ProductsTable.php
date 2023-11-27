<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\ExtendedProductList;

class ProductsTable extends Component
{
    use WithPagination;

    public $currentPage = 1;

    public function getProductsProperty()
    {
        return ExtendedProductList::query()
            ->paginate(10, ['*'], 'page', $this->currentPage);
    }

    public function render()
    {
        return view('livewire.products-table', [
            'products' => $this->getProductsProperty()
        ]);
    }
}
