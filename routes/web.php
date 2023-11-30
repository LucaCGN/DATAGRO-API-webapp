<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\DataSeriesController;
use App\Http\Controllers\DownloadController;
use Illuminate\Support\Facades\Log;
use App\Models\ExtendedProductList;
use App\Models\DataSeries;
use App\Http\Controllers\FilterController;

Route::get('/', function () {
    $products = ExtendedProductList::all();
    return view('app', compact('products'));
});

// Products related routes
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{page}/{perPage}', [ProductController::class, 'paginate']);

// Data Series related routes
Route::get('/data-series/{productId}', [DataSeriesController::class, 'show']);
Route::get('/data-series/{productId}/{page}/{perPage}', [DataSeriesController::class, 'paginate']);

// Download routes
Route::get('/download/csv', [DownloadController::class, 'downloadCSV']);
Route::get('/download/pdf', [DownloadController::class, 'downloadPDF']);


// Add a route to handle CSRF token generation
Route::get('/csrf-token', function() {
    return csrf_token();
});

// New route for fetching dropdown data
Route::get('/api/get-dropdown-data', [FilterController::class, 'getDropdownData']);

// Filter products
Route::post('/filter-products', [ProductController::class, 'filter']); // Changed to POST
