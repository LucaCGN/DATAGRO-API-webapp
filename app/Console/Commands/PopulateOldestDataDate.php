<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ExtendedProductList;
use App\Models\DataSeries;

class PopulateOldestDataDate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:populate-oldest-data-date';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Populate the oldest data date in the Extended Product List';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $products = ExtendedProductList::all();
        foreach ($products as $product) {
            $oldestDate = DataSeries::where('extended_product_list_id', $product->id)
                                    ->orderBy('data', 'asc')
                                    ->first()
                                    ->data ?? null;

            if ($oldestDate) {
                $product->update(['oldest_data_date' => $oldestDate]);
                $this->info("Updated product {$product->id} with date {$oldestDate}");
            }
        }

        $this->info("All products updated successfully.");
    }
}
