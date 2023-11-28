<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ExtendedProductList;
use App\Models\DataSeries;


class DownloadController extends Controller
{
   public function downloadCSV()
   {
      $headers = array(
          "Content-type"      => "text/csv",
          "Content-Disposition" => "attachment; filename=file.csv",
          "Pragma"            => "no-cache",
          "Cache-Control"     => "must-revalidate, post-check=0, pre-check=0",
          "Expires"           => "0"
      );

      $products = ExtendedProductList::all()->toArray();
      $file_name = 'products.csv';
      $file_path = public_path($file_name);
      $file_url = url($file_name);

      $file_open = fopen($file_path, 'w');
      $content = array_keys($products[0]);
      fputcsv($file_open, $content);
      foreach ($products as $product) {
          fputcsv($file_open, $product);
      }
      fclose($file_open);

      return response()->download($file_path, $file_name, $headers);
   }

   public function downloadPDF()
   {
      $products = ExtendedProductList::all();

      $pdf = PDF::loadView('products.pdf', compact('products'));
      return $pdf->download('products.pdf');
   }
}
