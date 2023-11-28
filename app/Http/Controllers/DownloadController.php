<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ExtendedProductList;
use Illuminate\Support\Facades\Log;
use PDF;

class DownloadController extends Controller
{
   public function downloadCSV()
   {
      Log::info('DownloadController: downloadCSV method called');
      $headers = array(
          "Content-type" => "text/csv",
          "Content-Disposition" => "attachment; filename=file.csv",
          "Pragma" => "no-cache",
          "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
          "Expires" => "0"
      );

      $products = ExtendedProductList::all()->toArray();
      $file_name = 'products.csv';
      $file_path = public_path($file_name);

      Log::info('Creating CSV file for download');
      $file_open = fopen($file_path, 'w');
      $content = array_keys($products[0]);
      fputcsv($file_open, $content);
      foreach ($products as $product) {
          fputcsv($file_open, $product);
      }
      fclose($file_open);

      Log::info('CSV file created and ready for download');
      return response()->download($file_path, $file_name, $headers);
   }

   public function downloadPDF()
   {
      Log::info('DownloadController: downloadPDF method called');
      $products = ExtendedProductList::all();
      Log::info('Creating PDF file for download');
      $pdf = PDF::loadView('products.pdf', compact('products'));
      return $pdf->download('products.pdf');
   }
}
