<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ExtendedProductList;
use Illuminate\Support\Facades\Log;
use PDF;

class DownloadController extends Controller
{
    public function downloadVisibleCSV(Request $request)
    {
        Log::info('DownloadController: downloadVisibleCSV method called');

        // Decode the JSON data received from the frontend
        $data = json_decode($request->getContent(), true);
        $products = $data['products'];
        $dataSeries = $data['dataSeries'];

        // Create a CSV file in memory
        $file = fopen('php://temp', 'w+');

        // Set the headers for proper UTF-8 encoding
        fputs($file, $bom =( chr(0xEF) . chr(0xBB) . chr(0xBF) ));

        // Manually add headers for the products table
        $productHeaders = ['Produto', 'Nome', 'Frequência', 'Primeira Data'];
        fputcsv($file, $productHeaders);

        // Add product rows
        foreach ($products as $product) {
            fputcsv($file, $product);
        }

        // Add a separator line and a blank line for spacing
        fputcsv($file, array_fill(0, count($productHeaders), ''));
        fputcsv($file, array_fill(0, count($productHeaders), ''));

        // Manually add headers for the data series table
        $dataSeriesHeaders = ['Cod', 'data', 'ult', 'mini', 'maxi', 'abe', 'volumes', 'med', 'aju'];
        fputcsv($file, $dataSeriesHeaders);

        // Add data series rows
        foreach ($dataSeries as $series) {
            fputcsv($file, $series);
        }

        // Reset the file pointer to the start
        rewind($file);

        // Build the CSV from the file pointer
        $csv = stream_get_contents($file);
        fclose($file);

        // Create a response and add headers for file download
        $response = response($csv)
            ->header('Content-Type', 'text/csv; charset=UTF-8')
            ->header('Content-Disposition', 'attachment; filename="api-preview-data.csv"')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');

        return $response;
    }


}
