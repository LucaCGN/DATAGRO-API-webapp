<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ExtendedProductList;
use App\Models\DataSeries;

class ExportDataToCsv extends Command
{
    protected $signature = 'export:data-to-csv';
    protected $description = 'Export tables to CSV files';

    public function handle()
    {
        $this->exportTableToCsv('extended_product_list_tables', ExtendedProductList::all());
        $this->exportTableToCsv('data_series_tables', DataSeries::all());
        $this->info('Export completed successfully.');
    }

    private function exportTableToCsv($tableName, $data)
    {
        if ($data->isEmpty()) {
            $this->info("No data to export for table: $tableName");
            return;
        }

        $csvHeader = implode(',', array_keys($data->first()->getAttributes())) . "\n";
        $csvContent = $data->reduce(function ($csvLine, $item) {
            return $csvLine . implode(',', array_values($item->getAttributes())) . "\n";
        }, $csvHeader);

        $fileName = $tableName . '_' . now()->format('Y_m_d_His') . '.csv';
        file_put_contents(storage_path('app/' . $fileName), $csvContent);
        $this->info("Exported $tableName to $fileName");
    }
}
