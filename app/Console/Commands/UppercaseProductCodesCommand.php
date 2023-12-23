<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ExtendedProductList;

class UppercaseProductCodesCommand extends Command
{
    protected $signature = 'uppercase:product-codes';
    protected $description = 'Convert Código_Produto fields to uppercase';

    public function handle()
    {
        ExtendedProductList::all()->each(function ($product) {
            $product->Código_Produto = strtoupper($product->Código_Produto);
            $product->save();
        });

        $this->info('All product codes have been converted to uppercase.');
    }
}
