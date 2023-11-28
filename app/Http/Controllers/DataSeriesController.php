<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DataSeries;

class DataSeriesController extends Controller
{
   public function show($productId)
   {
      $dataSeries = DataSeries::where('extended_product_list_id', $productId)->get();
      return response()->json($dataSeries);
   }

   public function paginate($productId, $page, $perPage)
   {
      $dataSeries = DataSeries::where('extended_product_list_id', $productId)->paginate($perPage);
      return response()->json($dataSeries);
   }
}
