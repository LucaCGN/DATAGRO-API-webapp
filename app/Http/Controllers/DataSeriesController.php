<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DataSeries;
use Illuminate\Support\Facades\Log;

class DataSeriesController extends Controller
{
    /**
     * Display a listing of the data series for a given product code.
     *
     * @param string $productCode
     * @return \Illuminate\Http\Response
     */
    public function show($productCode)
    {
        Log::info("Entering DataSeriesController::show with productCode: {$productCode}");
        try {
            // Fetch the data series based on the product code
            $dataSeries = DataSeries::where('cod', $productCode)->get();

            Log::info('DataSeries Retrieved: ' . $dataSeries->count());
            return response()->json($dataSeries);
        } catch (\Exception $e) {
            Log::error("Error in DataSeriesController::show: " . $e->getMessage());
            return response()->json(['error' => 'Server Error'], 500);
        }
    }

    /**
     * Display a paginated listing of the data series for a given product code.
     *
     * @param string $productCode
     * @param int $page
     * @param int $perPage
     * @return \Illuminate\Http\Response
     */
    public function paginate($productCode, $page, $perPage)
    {
        Log::info("Entering DataSeriesController::paginate with productCode: {$productCode}, page: {$page}, perPage: {$perPage}");
        try {
            // Fetch the paginated data series based on the product code
            $dataSeries = DataSeries::where('cod', $productCode)
                                    ->paginate($perPage, ['*'], 'page', $page);

            Log::info('Paginated DataSeries Retrieved');
            return response()->json($dataSeries);
        } catch (\Exception $e) {
            Log::error("Error in DataSeriesController::paginate: " . $e->getMessage());
            return response()->json(['error' => 'Server Error'], 500);
        }
    }
}
