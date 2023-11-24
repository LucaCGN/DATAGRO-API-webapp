<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\ExtendedProductList;

class ProductsTable extends Component
{
    use WithPagination;

    public $products;

    public function mount()
    {
        $this->products = ExtendedProductList::paginate(10);
    }

    public function render()
    {
        return view('livewire.products-table', [
            'products' => $this->products
        ]);
    }
}
