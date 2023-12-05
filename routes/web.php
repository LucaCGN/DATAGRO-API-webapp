<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\DataSeriesController;
use App\Http\Controllers\DownloadController;
use App\Http\Controllers\FilterController;
use App\Http\Controllers\LoginController;
use App\Models\ExtendedProductList;

Route::get('/', function () {
    $products = ExtendedProductList::all();
    return view('app', compact('products'));
})->middleware('auth');

// Products related routes
Route::get('/products', [ProductController::class, 'index']); // For initial load and pagination without filters

// Updated POST route for filtered products
Route::post('/api/filter-products', [ProductController::class, 'index']); // Assuming 'index' is the correct method

// Data Series related routes
Route::get('/data-series/{productId}', [DataSeriesController::class, 'show']);
Route::get('/data-series/{productId}/{page}/{perPage}', [DataSeriesController::class, 'paginate']);

// Download routes
Route::post('/download/visible-csv', [DownloadController::class, 'downloadVisibleCSV']);
Route::post('/download/visible-pdf', [DownloadController::class, 'downloadPDF']);


// CSRF token generation
Route::get('/csrf-token', function() {
    return csrf_token();
});

// New GET route for initial filter options as expected in JS
Route::get('/api/initial-filter-options', [FilterController::class, 'getInitialFilterOptions']);

// Ensure this POST route is as per the JavaScript expectations
Route::post('/api/filters/updated', [FilterController::class, 'getUpdatedFilterOptions']);

// Login Routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
