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

// Updated route for filtered products, only POST requests
Route::post('/api/filter-products', [ProductController::class, 'index']);

// Data Series related routes
Route::get('/data-series/{productId}', [DataSeriesController::class, 'show']);
Route::get('/data-series/{productId}/{page}/{perPage}', [DataSeriesController::class, 'paginate']);

// Download routes
Route::post('/download/visible-csv', [DownloadController::class, 'downloadVisibleCSV']);

// CSRF token generation
Route::get('/csrf-token', function() {
    return csrf_token();
});

// New route for initial filter data
Route::get('/api/filters', [FilterController::class, 'getInitialFilterData']);

// New route for updated filter options based on selections
Route::post('/api/filters/updated', [FilterController::class, 'getUpdatedFilterOptions']);

// Login Route
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
