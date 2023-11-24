<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\DataSeries;

class DataSeriesTable extends Component
{
    public $dataSeries = [];
    protected $listeners = ['productSelected' => 'onProductSelected'];

    public function mount()
    {
        $this->dataSeries = DataSeries::all();
    }

    public function onProductSelected($productId)
    {
        // Assuming there's a relation between products and data series defined in the models
        $this->dataSeries = DataSeries::where('product_id', $productId)->get();
    }

    public function render()
    {
        return view('livewire.data-series-table', [
            'dataSeries' => $this->dataSeries
        ]);
    }
}
