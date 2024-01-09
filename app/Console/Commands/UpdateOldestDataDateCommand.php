<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ExtendedProductList;
use App\Models\DataSeries;

class UpdateOldestDataDateCommand extends Command
{
    protected $signature = 'update:oldest-data-date';
    protected $description = 'Update inserido in the extended_product_list_tables';

    public function handle()
    {
        $this->info('Starting update of inserido column...');

        $products = ExtendedProductList::all();
        foreach ($products as $product) {
            $oldestDate = DataSeries::where('cod', $product->Código_Produto)
                                     ->oldest('data')
                                     ->value('data');

            // Update the inserido field regardless of whether an oldest date was found
            $product->inserido = $oldestDate; // This will be null if no records are found
            $product->save();

            if ($oldestDate) {
                $this->info("Updated inserido for product code: {$product->Código_Produto} to {$oldestDate}");
            } else {
                $this->info("No data series records found for product code: {$product->Código_Produto}. inserido set to NULL.");
            }
        }

        $this->info('Update of inserido column completed.');
    }
}
