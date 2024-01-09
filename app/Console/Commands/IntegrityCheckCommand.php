<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ExtendedProductList;
use App\Models\DataSeries;
use Illuminate\Support\Facades\DB;
use League\Csv\Writer;
use Storage;

class IntegrityCheckCommand extends Command
{
    protected $signature = 'integrity:check';
    protected $description = 'Perform integrity check on products and data series tables and export issues to CSV files.';

    public function handle()
    {
        $this->info('Starting integrity checks...');

        // Prepare CSV writers
        $identicalRecordsCsv = Writer::createFromString('');
        $identicalRecordsCsv->insertOne(['Product Code 1', 'Product Code 2']);

        $noDataSeriesCsv = Writer::createFromString('');
        $noDataSeriesCsv->insertOne(['Product Code']);

        // Check 1: Find identical records across different 'cod' values
        $this->checkForIdenticalRecords($identicalRecordsCsv);

        // Check 2: Flag 'cod' with no DataSeries records
        $this->flagNoDataSeriesRecords($noDataSeriesCsv);

        // Save CSV files
        Storage::disk('local')->put('identical_records.csv', $identicalRecordsCsv->toString());
        Storage::disk('local')->put('no_data_series.csv', $noDataSeriesCsv->toString());

        $this->info('Integrity checks completed. Check storage/app for CSV files.');
    }

    private function checkForIdenticalRecords($csvWriter)
    {
        $codesWithRecords = DataSeries::select('cod')->distinct()->pluck('cod');

        foreach ($codesWithRecords as $code) {
            $records = DataSeries::where('cod', $code)->get()->toArray();
            foreach ($codesWithRecords as $compareCode) {
                if ($code != $compareCode) {
                    $compareRecords = DataSeries::where('cod', $compareCode)->get()->toArray();
                    if ($this->recordsMatch($records, $compareRecords)) {
                        $csvWriter->insertOne([$code, $compareCode]);
                        $this->info("Identical records found for $code and $compareCode");
                    }
                }
            }
        }
    }

    private function flagNoDataSeriesRecords($csvWriter)
    {
        $allCodes = ExtendedProductList::pluck('Código_Produto');
        foreach ($allCodes as $code) {
            $count = DataSeries::where('cod', $code)->count();
            if ($count === 0) {
                $csvWriter->insertOne([$code]);
                $this->info("No DataSeries records found for $code");
            }
        }
    }

    private function recordsMatch($records1, $records2)
    {
        sort($records1);
        sort($records2);
        return $records1 === $records2;
    }
}
