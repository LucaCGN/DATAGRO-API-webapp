<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ClearDataSeriesTable extends Command
{
    protected $signature = 'clear:data-series';
    protected $description = 'Clear the data_series table';

    public function handle()
    {
        $this->info('Clearing the data_series table...');

        // Truncate the data_series table
        DB::table('data_series_tables')->truncate();

        $this->info('The data_series table has been cleared.');
    }
}
