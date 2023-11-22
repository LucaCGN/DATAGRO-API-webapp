<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use League\Csv\Reader; // You might need to install the league/csv package.

class ExtendedProductListTableSeeder extends Seeder
{
    public function run()
    {
        $csv = Reader::createFromPath('C:\Users\lnonino\OneDrive - DATAGRO\Documentos\GitHub\DataAgro\Laravel-API-WebAPP\datagro-webapp\database\products_list.csv', 'r');
        $csv->setHeaderOffset(0);

        foreach ($csv as $record) {
            DB::table('extended_product_list_tables')->insert([
                'Código_Produto' => $record['Código_Produto'],
                'Classificação' => $record['Classificação'],
                'Subproduto' => $record['Subproduto'],
                'Local' => $record['Local'],
            ]);
        }
    }
}
