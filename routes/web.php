<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\DataSeriesController;
use App\Http\Controllers\DownloadController;
use App\Http\Controllers\FilterController;
use App\Models\ExtendedProductList;

Route::get('/', function () {
    $products = ExtendedProductList::all();
    return view('app', compact('products'));
});

// Products related routes
Route::get('/products', [ProductController::class, 'index']); // For initial load and pagination without filters
Route::match(['get', 'post'], '/filter-products', [ProductController::class, 'index']); // For filtered requests, allow both GET and POST

// Data Series related routes
Route::get('/data-series/{productId}', [DataSeriesController::class, 'show']);
Route::get('/data-series/{productId}/{page}/{perPage}', [DataSeriesController::class, 'paginate']);

// Download routes
Route::get('/download/csv', [DownloadController::class, 'downloadCSV']);
Route::get('/download/pdf', [DownloadController::class, 'downloadPDF']);

// CSRF token generation
Route::get('/csrf-token', function() {
    return csrf_token();
});

// Fetching dropdown data
Route::get('/api/get-dropdown-data', [FilterController::class, 'getDropdownData']);

// Login Route
Route::get('/login', 'Auth\LoginController@showLoginForm')->name('login');
Route::post('/login', 'Auth\LoginController@login');

