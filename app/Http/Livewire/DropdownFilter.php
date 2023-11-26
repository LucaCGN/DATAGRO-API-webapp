<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\ExtendedProductList;

class DropdownFilter extends Component
{
    public $selectedClassificacao = null;
    public $selectedSubproduto = null;
    public $selectedLocal = null;

    public $classifications = [];
    public $subproducts = [];
    public $locations = [];

    public function mount()
    {
        $this->classifications = ExtendedProductList::distinct()->pluck('Classificação')->toArray();
        $this->subproducts = ExtendedProductList::distinct()->pluck('Subproduto')->toArray();
        $this->locations = ExtendedProductList::distinct()->pluck('Local')->toArray();
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