<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use App\Http\Livewire\DropdownFilter;
use App\Http\Livewire\DataSeriesTable;
use App\Http\Livewire\DownloadButtons;
use App\Http\Livewire\ProductsTable;

class LivewireServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Livewire::component('dropdown-filter', DropdownFilter::class);
        Livewire::component('data-series-table', DataSeriesTable::class);
        Livewire::component('download-buttons', DownloadButtons::class);
        Livewire::component('products-table', ProductsTable::class);
    }
}
