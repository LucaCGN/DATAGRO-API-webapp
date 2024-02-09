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


use App\Http\Controllers\LuigiPipelineController;

// Luigi Pipeline Routes
Route::get('/pipeline/usda', [LuigiPipelineController::class, 'triggerUSDA']);
Route::get('/pipeline/comex', [LuigiPipelineController::class, 'triggerCOMEX']);
Route::get('/pipeline/indec', [LuigiPipelineController::class, 'triggerINDEC']);
Route::get('/pipeline/all', [LuigiPipelineController::class, 'triggerAllPipelines']);


Route::get('/test-user', function () {
    $output = [];
    exec('whoami', $output);
    return $output[0];
});


Route::get('/test-env', function () {
    $output = [];
    exec('env', $output);
    return implode('<br>', $output);
});

// test exec
Route::get('/test-exec', [LuigiPipelineController::class, 'testExec']);
Route::get('/test-python-script1', [LuigiPipelineController::class, 'executeTestScriptSystem']);
Route::get('/test-python-script2', [LuigiPipelineController::class, 'executeTestScriptProcOpen']);


// Luigi Pipeline Routes
Route::get('/pipeline/usda/data', [DataFetchController::class, 'fetchUSDAall']);
Route::get('/pipeline/comex/exp', [DataFetchController::class, 'fetchCOMEXexp']);
Route::get('/pipeline/comex/imp', [DataFetchController::class, 'fetchCOMEXimp']);
Route::get('/pipeline/indec/exp', [DataFetchController::class, 'fetchINDECexp']);
