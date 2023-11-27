<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\ExtendedProductList;

class ProductsTable extends Component
{
    use WithPagination;

    public $selectedClassificacao = null;
    public $selectedSubproduto = null;
    public $selectedLocal = null;
    public $selectedProductId = null;

    protected $listeners = ['filterChanged' => 'onFilterChange'];

    public function onFilterChange($selectedClassificacao, $selectedSubproduto, $selectedLocal)
    {
        $this->selectedClassificacao = $selectedClassificacao;
        $this->selectedSubproduto = $selectedSubproduto;
        $this->selectedLocal = $selectedLocal;
    }

    public function selectProduct($productId)
    {
        $this->selectedProductId = $productId;
        // Trigger event or load data series based on selected product
        $this->emit('productSelected', $productId);
    }

    public function getProductsProperty()
    {
        $query = ExtendedProductList::query();

        if ($this->selectedClassificacao) {
            $query->where('Classificação', $this->selectedClassificacao);
        }
        if ($this->selectedSubproduto) {
            $query->where('Subproduto', $this->selectedSubproduto);
        }
        if ($this->selectedLocal) {
            $query->where('Local', $this->selectedLocal);
        }

        return $query->paginate(10);
    }

    public function render()
    {
        return view('livewire.products-table', [
            'products' => $this->getProductsProperty()
        ]);
    }
}
