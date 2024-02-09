<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DataFetchController extends Controller
{
    public function fetchUSDAall()
    {
        // Set the path to your CSV file
        $filePath = storage_path('app/luigi/data/usda/processed/master_table.csv');

        // Check if the file exists
        if (!file_exists($filePath)) {
            abort(404, 'File not found');
        }

        // Return the file as a download
        return response()->download($filePath, 'master_table.csv', [
            'Content-Type' => 'text/csv',
        ]);
    }

}
