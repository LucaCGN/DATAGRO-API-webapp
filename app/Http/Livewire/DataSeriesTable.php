<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\DataSeries;

class DataSeriesTable extends Component
{
   public $dataSeries = [];
   protected $listeners = ['productSelected' => 'onProductSelected'];

   public function onProductSelected($productId)
   {
       $this->dataSeries = DataSeries::where('extended_product_list_id', $productId)->get();
   }

   public function render()
   {
       return view('livewire.data-series-table', [
           'dataSeries' => $this->dataSeries
       ]);
   }
}
