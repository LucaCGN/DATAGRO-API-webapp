<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\ExtendedProductList;

class DropdownFilter extends Component
{
    public $selectedClassificacao = null;
    public $selectedSubproduto = null;
    public $selectedLocal = null;

    public function getClassificationsProperty()
    {
        return ExtendedProductList::distinct()->pluck('Classificação')->toArray();
    }

    public function getSubproductsProperty()
    {
        return ExtendedProductList::distinct()->pluck('Subproduto')->toArray();
    }

    public function getLocationsProperty()
    {
        return ExtendedProductList::distinct()->pluck('Local')->toArray();
    }

    public function render()
    {
        return view('livewire.dropdown-filter', [
            'classifications' => $this->classifications,
            'subproducts' => $this->subproducts,
            'locations' => $this->locations,
        ]);
    }
}
