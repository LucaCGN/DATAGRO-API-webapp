<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ExtendedProductList;
use App\Models\DataSeries;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use League\Csv\Reader;
use League\Csv\Statement;
use Carbon\Carbon;

class UpdateDataSeriesCommand extends Command
{
    protected $signature = 'update:data-series';
    protected $description = 'Update Data Series Table Only';

    public function handle()
    {
        Log::info("Command Started: Updating Data Series Table");

        $products = ExtendedProductList::all();
        Log::info("Total Products for Data Series Update: " . $products->count());

        foreach ($products as $product) {
            $this->info("Updating Data Series for product: " . $product->Código_Produto);
            Log::info("Updating Data Series for Product: " . $product->Código_Produto);

            $freqParam = $this->getFrequencyParam($product->freq);
            $dateIntervals = $this->getDateIntervals($freqParam);

            foreach ($dateIntervals as $interval) {
                $dataSeries = $this->fetchDataSeries($product->Código_Produto, $interval['start'], $interval['end'], $freqParam);
                if (!empty($dataSeries)) {
                    $this->updateDataSeries($dataSeries, $product->id);
                    break; // Break the loop if data is found
                }
            }

            Log::info('Data Series update for Product: ' . $product->Código_Produto . ' completed.');
        }

        Log::info('All Data Series updates completed.');
    }

    private function getFrequencyParam($frequency)
    {
        return match ($frequency) {
            'D' => 'd',
            'S' => 's',
            'M' => 'm',
            'A' => 'a',
            default => 'X', // Default to daily if frequency is not recognized
        };
    }

    private function getDateIntervals($freqParam)
    {
        return match ($freqParam) {
            'd' => [
                ['start' => '20230101', 'end' => '20230201'],
                ['start' => '20220101', 'end' => '20220201'],
                ['start' => '20210101', 'end' => '20210201'],
            ],
            's' => [
                ['start' => '20230101', 'end' => '20230701'],
                ['start' => '20220101', 'end' => '20220701'],
                ['start' => '20210101', 'end' => '20210701'],
            ],
            'm' => [
                ['start' => '20230101', 'end' => '20230701'],
                ['start' => '20220101', 'end' => '20220701'],
                ['start' => '20210101', 'end' => '20210701'],
            ],
            'a' => [
                ['start' => '20210101', 'end' => '20230101'],
                ['start' => '20180101', 'end' => '20210101'],
                ['start' => '20150101', 'end' => '20180101'],
            ],
            default => [],
        };
    }

    private function fetchDataSeries($productCode, $startDate, $endDate, $freqParam) {
        $url = "https://precos.api.datagro.com/dados/";
        Log::info("Fetching Data Series for product code {$productCode} with params: StartDate = {$startDate}, EndDate = {$endDate}, FreqParam = {$freqParam}");

        $response = Http::withOptions(['verify' => false])->get($url, [
            'a' => $productCode,
            'i' => $startDate,
            'f' => $endDate,
            'p' => $freqParam,
            'x' => 'c'
        ]);

        if ($response->successful()) {
            $csvData = $response->body();
            Log::info("Raw CSV Data for product code {$productCode}: " . $csvData);
            return $this->parseCsvData($csvData);
        } else {
            Log::error("Failed to fetch Data Series for product code {$productCode}: " . $response->status());
            Log::error("Response Body: " . $response->body());
            return [];
        }
    }



    private function parseCsvData($csvData)
    {
        $headers = ['cod', 'data', 'ult', 'mini', 'maxi', 'abe', 'volumes', 'cab', 'med', 'aju'];

        $csv = Reader::createFromString($csvData);
        $csv->setHeaderOffset(null); // No headers in the actual CSV
        $records = Statement::create()->process($csv);

        $parsedData = [];
        foreach ($records as $record) {
            // Make sure we have the same number of elements in both arrays
            $record = array_slice($record, 0, count($headers));
            $recordWithKeys = array_combine($headers, $record);

            // Convert empty strings to null and parse dates
            foreach ($recordWithKeys as $key => &$value) {
                $value = $value === '' ? null : $value;
                if ($key === 'data') {
                    $value = $this->parseDateForDataSeries($value);
                }
            }
            unset($value); // Unset reference to last element

            // Check if required fields are not null
            if (isset($recordWithKeys['cod'], $recordWithKeys['data'])) {
                Log::info("Parsed CSV Record: " . json_encode($recordWithKeys));
                $parsedData[] = $recordWithKeys;
            } else {
                Log::error("CSV record does not match expected format: " . json_encode($record));
            }
        }

        return $parsedData;
    }

    private function parseDateForDataSeries($dateString)
    {
        if (empty($dateString) || $dateString === '0000-00-00' || $dateString === '0000-00-00 00:00:00') {
            return null;
        }

        try {
            return Carbon::createFromFormat('Y-m-d', $dateString)->format('Y-m-d');
        } catch (\Exception $e) {
            Log::error("Invalid date format for string: {$dateString}. Error: " . $e->getMessage());
            return null;
        }
    }






    private function updateDataSeries($dataSeries, $productId) {
        if (empty($dataSeries)) {
            Log::info("No data to update for product ID {$productId}");
            return;
        }

        foreach ($dataSeries as $data) {
            try {
                $result = DataSeries::updateOrCreate(
                    ['cod' => $data['cod'], 'data' => $data['data']],
                    array_merge($data, ['extended_product_list_id' => $productId])
                );
                Log::info("Updated or Created Data Series Record: " . json_encode($result));
            } catch (\Exception $e) {
                Log::error("Error updating Data Series: " . $e->getMessage());
            }
        }
    }
}
