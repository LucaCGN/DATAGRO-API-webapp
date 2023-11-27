<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\ExtendedProductList;
use Symfony\Component\HttpFoundation\Response;

class DownloadButtons extends Component
{
    public function downloadCSV()
    {
        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=products.csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $products = ExtendedProductList::all();
        $columns = array_keys($products->first()->getAttributes());

        $callback = function() use ($products, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($products as $product) {
                fputcsv($file, $product->getAttributes());
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function downloadPDF()
    {
        // To implement PDF download, use a library like dompdf
        // Generate a PDF from a view or directly from HTML
        // Then return a download response with the PDF file
    }

    public function render()
    {
        return view('livewire.download-buttons');
    }
}
