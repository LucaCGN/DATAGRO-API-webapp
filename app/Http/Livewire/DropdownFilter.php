<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\ExtendedProductList;

class DropdownFilter extends Component
{
    public $selectedClassificacao = null;
    public $selectedSubproduto = null;
    public $selectedLocal = null;

    public function updated($propertyName)
    {
        $this->emit('filterChanged', $this->selectedClassificacao, $this->selectedSubproduto, $this->selectedLocal);
    }

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
            'classifications' => $this->getClassificationsProperty(),
            'subproducts' => $this->getSubproductsProperty(),
            'locations' => $this->getLocationsProperty(),
        ]);
    }
}
