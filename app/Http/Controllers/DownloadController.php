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
        $selectedProduct = $data['product']; // Expecting the selected product's data
        $dataSeries = $data['dataSeries'];

        // Create a CSV file in memory
        $file = fopen('php://temp', 'w+');

        // Set the headers for proper UTF-8 encoding
        fputs($file, $bom =( chr(0xEF) . chr(0xBB) . chr(0xBF) ));

        // If a product has been selected, add its data to the CSV
        if ($selectedProduct) {
            // Manually add headers for the selected product data
            $productHeaders = ['Produto', 'Nome', 'Frequência', 'Primeira Data'];
            fputcsv($file, $productHeaders);
            fputcsv($file, $selectedProduct);
        }

        // Add a blank line for spacing between product data and data series
        fputcsv($file, []);

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
            ->header('Content-Disposition', 'attachment; filename="visible-data.csv"')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');

        return $response;
    }

    public function downloadPDF(Request $request)
    {
        Log::info('DownloadController: downloadPDF method called');

        $data = json_decode($request->getContent(), true);

        if (empty($data['product'])) {
            Log::error('DownloadController: downloadPDF method called with empty product data');
            abort(400, "Bad Request: No product data provided");
        }

        // Extract the 'longo', 'Local', 'Subproduto', and 'freq' from the product data
        $selectedProduct = [
            'longo' => $data['product'][18], // Replace the index with the correct one if necessary
            'Produto' => $data['product'][2], // 'Código_Produto' at index 1
            'Local' => $data['product'][4], // 'Local' at index 4
            'Subproduto' => $data['product'][3], // 'Subproduto' at index 2
            'Frequência' => $data['product'][28], // 'freq' at index 27
        ];

        $dataSeries = $data['dataSeries'] ?? [];

        // Generate the PDF with the desired product details and existing data series logic
        $pdf = PDF::loadView('pdf_view', [
            'selectedProduct' => $selectedProduct,
            'dataSeries' => array_map(function ($series) {
                return [
                    'cod' => $series['cod'],
                    'data' => $series['data'],
                    'ult' => $series['ult'],
                    'mini' => $series['mini'],
                    'maxi' => $series['maxi'],
                    'abe' => $series['abe'],
                    'volumes' => $series['volumes'],
                    // 'med' and 'aju' are intentionally excluded
                ];
            }, $dataSeries)
        ]);

        return $pdf->download('visible-data.pdf');
    }

}
