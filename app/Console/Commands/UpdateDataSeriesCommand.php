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
                ['start' => '19840101', 'end' => '19840201'],
                ['start' => '19860101', 'end' => '19860201'],
                ['start' => '19880101', 'end' => '19880201'],
                ['start' => '19900101', 'end' => '19900201'],
                ['start' => '19920101', 'end' => '19920201'],
                ['start' => '19940101', 'end' => '19940201'],
                ['start' => '19960101', 'end' => '19960201'],
                ['start' => '19980101', 'end' => '19980201'],
                ['start' => '20000101', 'end' => '20000201'],
                ['start' => '20020101', 'end' => '20020201'],
                ['start' => '20040101', 'end' => '20040201'],
                ['start' => '20060101', 'end' => '20060201'],
                ['start' => '20080101', 'end' => '20080201'],
                ['start' => '20100101', 'end' => '20100201'],
                ['start' => '20120101', 'end' => '20120201'],
                ['start' => '20140101', 'end' => '20140201'],
                ['start' => '20160101', 'end' => '20160201'],
                ['start' => '20180101', 'end' => '20180201'],
                ['start' => '20200101', 'end' => '20200201'],
                ['start' => '20220101', 'end' => '20220201'],
            ],
            's' => [
                ['start' => '19840101', 'end' => '19840601'],
                ['start' => '19860101', 'end' => '19860601'],
                ['start' => '19880101', 'end' => '19880601'],
                ['start' => '19900101', 'end' => '19900601'],
                ['start' => '19920101', 'end' => '19920601'],
                ['start' => '19940101', 'end' => '19940601'],
                ['start' => '19960101', 'end' => '19960601'],
                ['start' => '19980101', 'end' => '19980601'],
                ['start' => '20000101', 'end' => '20000601'],
                ['start' => '20020101', 'end' => '20020601'],
                ['start' => '20040101', 'end' => '20040601'],
                ['start' => '20060101', 'end' => '20060601'],
                ['start' => '20080101', 'end' => '20080601'],
                ['start' => '20100101', 'end' => '20100601'],
                ['start' => '20120101', 'end' => '20120601'],
                ['start' => '20140101', 'end' => '20140601'],
                ['start' => '20160101', 'end' => '20160601'],
                ['start' => '20180101', 'end' => '20180601'],
                ['start' => '20200101', 'end' => '20200601'],
                ['start' => '20220101', 'end' => '20220601'],
            ],
            'm' => [
                ['start' => '19840101', 'end' => '19841231'],
                ['start' => '19850101', 'end' => '19851231'],
                ['start' => '19860101', 'end' => '19861231'],
                ['start' => '19870101', 'end' => '19871231'],
                ['start' => '19880101', 'end' => '19881231'],
                ['start' => '19890101', 'end' => '19891231'],
                ['start' => '19900101', 'end' => '19901231'],
                ['start' => '19910101', 'end' => '19911231'],
                ['start' => '19920101', 'end' => '19921231'],
                ['start' => '19930101', 'end' => '19931231'],
                ['start' => '19940101', 'end' => '19941231'],
                ['start' => '19950101', 'end' => '19951231'],
                ['start' => '19960101', 'end' => '19961231'],
                ['start' => '19970101', 'end' => '19971231'],
                ['start' => '19980101', 'end' => '19981231'],
                ['start' => '19990101', 'end' => '19991231'],
                ['start' => '20000101', 'end' => '20001231'],
                ['start' => '20010101', 'end' => '20011231'],
                ['start' => '20020101', 'end' => '20021231'],
                ['start' => '20030101', 'end' => '20031231'],
            ],
            'a' => [
                ['start' => '19840101', 'end' => '19851231'],
                ['start' => '19860101', 'end' => '19871231'],
                ['start' => '19880101', 'end' => '19891231'],
                ['start' => '19900101', 'end' => '19911231'],
                ['start' => '19920101', 'end' => '19931231'],
                ['start' => '19940101', 'end' => '19951231'],
                ['start' => '19960101', 'end' => '19971231'],
                ['start' => '19980101', 'end' => '19991231'],
                ['start' => '20000101', 'end' => '20011231'],
                ['start' => '20020101', 'end' => '20031231'],
                ['start' => '20040101', 'end' => '20051231'],
                ['start' => '20060101', 'end' => '20071231'],
                ['start' => '20080101', 'end' => '20091231'],
                ['start' => '20100101', 'end' => '20111231'],
                ['start' => '20120101', 'end' => '20131231'],
                ['start' => '20140101', 'end' => '20151231'],
                ['start' => '20160101', 'end' => '20171231'],
                ['start' => '20180101', 'end' => '20191231'],
                ['start' => '20200101', 'end' => '20211231'],
                ['start' => '20220101', 'end' => '20231231'],
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
