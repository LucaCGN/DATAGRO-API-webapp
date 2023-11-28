<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DataSeries;
use Illuminate\Support\Facades\Log;

class DataSeriesController extends Controller
{
   public function show($productId)
   {
      Log::info("DataSeriesController: show method called for productId {$productId}");
      $dataSeries = DataSeries::where('extended_product_list_id', $productId)->get();
      Log::info('DataSeries Retrieved: ' . $dataSeries->count());
      return response()->json($dataSeries);
   }

   public function paginate($productId, $page, $perPage)
   {
      Log::info("DataSeriesController: paginate method called for productId {$productId} with page {$page} and perPage {$perPage}");
      $dataSeries = DataSeries::where('extended_product_list_id', $productId)->paginate($perPage);
      Log::info('Paginated DataSeries Retrieved');
      return response()->json($dataSeries);
   }
}
