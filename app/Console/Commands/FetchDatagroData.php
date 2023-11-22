<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\ExtendedProductList;
use App\Models\DataSeries;
use Illuminate\Support\Carbon;

class FetchDatagroData extends Command
{
    protected $signature = 'fetch:datagro-data';
    protected $description = 'Fetch data from Datagro API';

    public function handle()
    {
        $products = ExtendedProductList::all();
        foreach ($products as $product) {
            $this->info("Processing product: " . $product->Código_Produto);
            $additionalData = $this->fetchProductData($product->Código_Produto);

            if ($additionalData) {
                // Assuming subproduto in API response maps to subproduto_id in your database
                $subprodutoId = $additionalData['subproduto'] ?? null;

                $product->update([
                    // 'cod' is not needed as it's the same as Código_Produto
                    'bolsa' => $additionalData['bolsa'] ?? null,
                    'roda' => $additionalData['roda'] ?? null,
                    'fonte' => $additionalData['fonte'] ?? null,
                    'tav' => $additionalData['tav'] ?? null,
                    'subtav' => $additionalData['subtav'] ?? null,
                    'decimais' => $additionalData['decimais'] ?? null,
                    'correlatos' => $additionalData['correlatos'] ?? null,
                    'empresa' => $additionalData['empresa'] ?? null,
                    'contrato' => $additionalData['contrato'] ?? null,
                    'subproduto_id' => $subprodutoId,
                    'entcode' => $additionalData['entcode'] ?? null,
                    'nome' => $additionalData['nome'] ?? null,
                    'longo' => $additionalData['longo'] ?? null,
                    'descr' => $additionalData['descr'] ?? null,
                    'codf' => $additionalData['codf'] ?? null,
                    'bd' => $additionalData['bd'] ?? null,
                    'palavras' => $additionalData['palavras'] ?? null,
                    'habilitado' => $additionalData['habilitado'] ?? null,
                    'lote' => $additionalData['lote'] ?? null,
                    'rep' => $additionalData['rep'] ?? null,
                    'vln' => $additionalData['vln'] ?? null,
                    'dia' => $additionalData['dia'] ?? null,
                    'freq' => $additionalData['freq'] ?? null,
                    'dex' => $additionalData['dex'] ?? null,
                    'inserido' => $additionalData['inserido'] ?? null,
                    'alterado' => $additionalData['alterado'] ?? null,
                    // 'Fetch_Status' is to be updated after data series fetch attempt
                ]);
            }

            $startDate = $product->inserido ? Carbon::createFromFormat('Y-m-d H:i:s', $product->inserido)->format('Ymd') : '20230101';
            $endDate = $product->alterado ? Carbon::createFromFormat('Y-m-d H:i:s', $product->alterado)->format('Ymd') : '20231201';
            $dataSeries = $this->fetchDataSeries($product->Código_Produto, $startDate, $endDate);

            if (!empty($dataSeries)) {
                foreach ($dataSeries as $data) {
                    DataSeries::create([
                        'extended_product_list_id' => $product->id,
                        'cod' => $data['cod'],
                        'data' => Carbon::createFromFormat('Y-m-d', $data['data'])->format('Y-m-d'),
                        'ult' => $data['ult'],
                        'mini' => $data['mini'],
                        'maxi' => $data['maxi'],
                        'abe' => $data['abe'],
                        'volumes' => $data['volumes'],
                        'cab' => $data['cab'],
                        'med' => $data['med'],
                        'aju' => $data['aju'],
                    ]);
                }
                $product->update(['Fetch_Status' => 'Success']);
            } else {
                // Try alternative dates if the initial fetch returns empty
                foreach ([["20230101", "20230108"], ["20230601", "20230701"]] as $dateRange) {
                    $dataSeries = $this->fetchDataSeries($product->Código_Produto, $dateRange[0], $dateRange[1]);
                    if (!empty($dataSeries)) {
                        foreach ($dataSeries as $data) {
                            DataSeries::create([
                                'extended_product_list_id' => $product->id,
                                // ...data series fields...
                            ]);
                        }
                        $product->update(['Fetch_Status' => 'Success']);
                        break;
                    }
                }
                if (empty($dataSeries)) {
                    $product->update(['Fetch_Status' => 'Failed']);
                }
            }
        }

        $this->info('Data fetching and updating completed.');
    }


    private function fetchProductData($productCode)
    {
        $url = "https://precos.api.datagro.com/cad/";
        $response = Http::withOptions(['verify' => false])->retry(5, 3000)->get($url, ['a' => $productCode, 'x' => 'j']);

        if ($response->successful()) {
            return $response->json();
        } else {
            $this->error("Failed to fetch data for product code {$productCode}. Status code: " . $response->status());
            return null;
        }
    }

    private function fetchDataSeries($productCode, $startDate, $endDate)
    {
        $url = "https://precos.api.datagro.com/dados/";
        $params = [
            'a' => $productCode,
            'i' => $startDate,
            'f' => $endDate,
            'x' => 'c'
        ];

        $response = Http::withOptions(['verify' => false])->retry(5, 3000)->get($url, $params);

        if ($response->successful()) {
            return $response->json();
        } else {
            $this->error("Failed to fetch data series for product code {$productCode}. Status code: " . $response->status());
            return null;
        }
    }
}
