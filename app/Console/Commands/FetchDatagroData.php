<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\ExtendedProductList;
use App\Models\DataSeries;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use League\Csv\Reader;

class FetchDatagroData extends Command
{
    protected $signature = 'fetch:datagro-data';
    protected $description = 'Fetch data from Datagro API';

    public function handle()
    {
        Log::info("Command Started: Fetching Datagro Data");

        $products = ExtendedProductList::all();
        Log::info("Total Products to Process: " . $products->count());

        foreach ($products as $product) {
            $this->info("Processing product: " . $product->Código_Produto);
            Log::info("Processing Product: " . $product->Código_Produto);

            $additionalDataResponse = $this->fetchProductData($product->Código_Produto);
            Log::info("API Response for Product Data: ", (array) $additionalDataResponse);

            $additionalData = $additionalDataResponse[0] ?? null; // Accessing the first element of the response

            if ($additionalData) {
                Log::info("Attempting to Update Product: " . $product->Código_Produto);
                $product->update([
                    'bolsa' => $additionalData['bolsa'] ?? null,
                    'roda' => $additionalData['roda'] ?? null,
                    'fonte' => $additionalData['fonte'] ?? null,
                    'tav' => $additionalData['tav'] ?? null,
                    'subtav' => $additionalData['subtav'] ?? null,
                    'decimais' => $additionalData['decimais'] ?? null,
                    'correlatos' => $additionalData['correlatos'] ?? null,
                    'empresa' => $additionalData['empresa'] ?? null,
                    'contrato' => $additionalData['contrato'] ?? null,
                    'subproduto_id' => $additionalData['subproduto'] ?? null,
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
                ]);
                Log::info("Updated Product: " . $product->Código_Produto, $additionalData);
            } else {
                Log::warning("No Additional Data for Product: " . $product->Código_Produto);
            }

            $startDate = $product->inserido ? Carbon::createFromFormat('Y-m-d H:i:s', $product->inserido)->format('Ymd') : '20230101';
            $endDate = $product->alterado ? Carbon::createFromFormat('Y-m-d H:i:s', $product->alterado)->format('Ymd') : '20231201';
            $dataSeries = $this->fetchDataSeries($product->Código_Produto, $startDate, $endDate);

            if (!empty($dataSeries)) {
                foreach ($dataSeries as $data) {
                    Log::info("Creating Data Series Entry with data: " . json_encode($data));

                    DataSeries::create([
                        'extended_product_list_id' => $product->id,
                        'cod' => $product->Código_Produto,
                        'data' => $data['data'] ?? null,
                        'ult' => $data['ult'] ?? null,
                        'mini' => $data['mini'] ?? null,
                        'maxi' => $data['maxi'] ?? null,
                        'abe' => $data['abe'] ?? null,
                        'volumes' => $data['volumes'] ?? null,
                        'cab' => $data['cab'] ?? null,
                        'med' => $data['med'] ?? null,
                        'aju' => $data['aju'] ?? null,
                    ]);

                    Log::info("Data Series Entry Created for Product: " . $product->Código_Produto);
            }
                $product->update(['Fetch_Status' => 'Success']);
            } else {
                foreach ([["20230101", "20230108"], ["20230601", "20230701"]] as $dateRange) {
                    $dataSeries = $this->fetchDataSeries($product->Código_Produto, $dateRange[0], $dateRange[1]);
                    if (!empty($dataSeries)) {
                        foreach ($dataSeries as $data) {
                            DataSeries::create([
                                'extended_product_list_id' => $product->id,
                                'cod' => $product->Código_Produto,
                                'data' => $data['data'] ?? null,
                                'ult' => $data['ult'] ?? null,
                                'mini' => $data['mini'] ?? null,
                                'maxi' => $data['maxi'] ?? null,
                                'abe' => $data['abe'] ?? null,
                                'volumes' => $data['volumes'] ?? null,
                                'cab' => $data['cab'] ?? null,
                                'med' => $data['med'] ?? null,
                                'aju' => $data['aju'] ?? null,
                            ]);

                        }
                                    $product->update(['Fetch_Status' => 'Success']);
                        Log::info("Data Series Fetched and Stored for Product: " . $product->Código_Produto);
                    } else {
                        Log::warning("Data Series Fetching Failed for Product: " . $product->Código_Produto);
                        $product->update(['Fetch_Status' => 'Failed']);
                    }
                }

                Log::info('Data fetching and updating completed.');
            }
        }

        Log::info('Data fetching and updating completed.');
    }


    private function fetchProductData($productCode)
    {
        $url = "https://precos.api.datagro.com/cad/";
        $response = Http::withOptions(['verify' => false])->retry(5, 3000)->get($url, ['a' => $productCode, 'x' => 'j']);

        if ($response->successful()) {
            Log::info("Successful API Response for Product Data: " . $productCode);
            return $response->json();
        } else {
            Log::error("Failed to Fetch Product Data: {$productCode}, Status Code: " . $response->status());
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

        $maxRetries = 5; // Number of retries
        $retryDelay = 3000; // Delay in milliseconds

        for ($attempt = 0; $attempt <= $maxRetries; $attempt++) {
            try {
                $response = Http::withOptions(['verify' => false])
                                ->get($url, $params);

                if ($response->successful()) {
                    Log::info("Successful API Response for Data Series: " . $productCode);
                    $csvData = $response->body(); // Get CSV data as string
                    return $this->parseCsvData($csvData); // Parse and return the data
                } else {
                    Log::error("Failed to Fetch Data Series: {$productCode}, Status Code: " . $response->status());
                }
            } catch (\Exception $e) {
                Log::error("Request Exception for {$productCode}: " . $e->getMessage());
            }

            // If max retries reached, log and break
            if ($attempt == $maxRetries) {
                Log::error("Max retries reached for {$productCode}");
                break;
            }

            // Delay before retrying
            usleep($retryDelay * 1000);
        }

        return null; // Return null in case of failure
    }



    private function parseCsvData($csvData)
    {
        // Manually define headers as the CSV does not have headers
        $headers = ['cod', 'data', 'ult', 'mini', 'maxi', 'abe', 'volumes', 'cab', 'med', 'aju'];

        $csv = Reader::createFromString($csvData);
        $csv->setHeaderOffset(null); // No headers in the actual CSV
        $records = $csv->getRecords($headers);

        $parsedData = [];
        foreach ($records as $record) {
            // Remove the last two columns (0 and null)
            array_pop($record);
            array_pop($record);

            Log::info("Record after removing last two columns: " . json_encode($record));

            // Handling date parsing
            $record['data'] = $this->parseDateForDataSeries($record['data']);

            $parsedData[] = $record;
        }

        return $parsedData;
    }


    private function parseDateForDataSeries($dateString)
    {
        Log::info("Parsing date string: {$dateString}");

        // Handle empty, null, or placeholder dates
        if (empty($dateString) || $dateString === '0000-00-00' || $dateString === '0000-00-00 00:00:00') {
            Log::warning("Invalid or placeholder date encountered: {$dateString}");
            return null;
        }

        // Attempt to parse the date string
        try {
            // If the date string includes time, extract only the date part
            if (strpos($dateString, ' ') !== false) {
                $dateString = explode(' ', $dateString)[0];
            }

            $formattedDate = Carbon::createFromFormat('Y-m-d', $dateString)->format('Y-m-d');
            Log::info("Formatted date: {$formattedDate}");
            return $formattedDate;
        } catch (\Exception $e) {
            Log::error("Invalid date format for string: {$dateString}. Error: " . $e->getMessage());
            return null;
        }
    }


}
