## app/Models/DataSeries.php
```
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DataSeries extends Model
{
    protected $table = 'data_series_tables';

    protected $fillable = [
        'extended_product_list_id', 'cod', 'data', 'ult', 'mini', 'maxi',
        'abe', 'volumes', 'cab', 'med', 'aju'
    ];

    public $timestamps = true;

    // Define the relationship with ExtendedProductList
    public function extendedProductList()
    {
        return $this->belongsTo(ExtendedProductList::class, 'extended_product_list_id');
    }
}

```
## app/Models/ExtendedProductList.php
```
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExtendedProductList extends Model
{
    protected $table = 'extended_product_list_tables';
    protected $fillable = [
        'Código_Produto', 'Classificação', 'Subproduto', 'Local', 'Fetch_Status',
        'bolsa', 'roda', 'fonte', 'tav', 'subtav', 'decimais', 'correlatos',
        'empresa', 'contrato', 'subproduto_id', 'entcode', 'nome', 'longo', 'descr',
        'codf', 'bd', 'palavras', 'habilitado', 'lote', 'rep', 'vln', 'dia',
        'freq', 'dex', 'inserido', 'alterado'
    ];
    public $timestamps = true;

    public function dataSeries()
    {
        return $this->hasOne(DataSeries::class, 'extended_product_list_id');
    }

    public function scopeActive($query)
    {
        return $query->where('active', 1);
    }
}

```
## public/js/DownloadButtons.js
```
// Functions to handle download actions
function downloadCSV() {
    console.log("[DownloadButtons] Initiating CSV download");
    window.location.href = '/download/csv';
}

function downloadPDF() {
    console.log("[DownloadButtons] Initiating PDF download");
    window.location.href = '/download/pdf';
}

// Event listeners for download buttons
document.addEventListener('DOMContentLoaded', function () {
    console.log("[DownloadButtons] Setting up event listeners for download buttons");
    const csvBtn = document.getElementById('download-csv-btn');
    const pdfBtn = document.getElementById('download-pdf-btn');

    if (csvBtn) {
        csvBtn.addEventListener('click', function() {
            console.log("[DownloadButtons] CSV Download button clicked");
            downloadCSV();
        });
    }

    if (pdfBtn) {
        pdfBtn.addEventListener('click', function() {
            console.log("[DownloadButtons] PDF Download button clicked");
            downloadPDF();
        });
    }
});

```
## app/Http/Controllers/DownloadController.php
```
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

```
## app/Http/Controllers/Controller.php
```
<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
}

```
## app/Http/Controllers/ProductController.php
```
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ExtendedProductList;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Builder;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        Log::info('ProductController: index method called', $request->all());
        $perPage = 10;

        try {
            $products = ExtendedProductList::query()
                ->when($request->filled('classificacao'), function (Builder $query) use ($request) {
                    $query->where('Classificação', $request->classificacao);
                })
                ->when($request->filled('subproduto'), function (Builder $query) use ($request) {
                    $query->where('Subproduto', $request->subproduto);
                })
                ->when($request->filled('local'), function (Builder $query) use ($request) {
                    $query->where('Local', $request->local);
                })
                ->when($request->filled('freq'), function (Builder $query) use ($request) {
                    $query->where('freq', $request->freq);
                })
                ->when($request->filled('bolsa'), function (Builder $query) use ($request) {
                    $query->where('bolsa', $request->bolsa);
                })
                ->paginate($perPage, ['*'], 'page', $request->get('page', 1));

            Log::info('Products fetched successfully', ['count' => $products->count()]);
            return response()->json($products);
        } catch (\Exception $e) {
            Log::error('Error fetching products', ['message' => $e->getMessage()]);
            return response()->json(['error' => 'Error fetching products'], 500);
        }
    }
}

```
## public/css/app.css
```
body {
    font-family: Arial, sans-serif;
    color: #4f4f4f; /* Dark grey for text, softer than pure white */
    background-color: #f8f8f8; /* Light grey background for a clean look */
}

header {
    background-color: #fff; /* White background for header */
    color: #4f4f4f; /* Dark grey for text */
    padding: 10px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); /* Soft shadow for depth */
}

main {
    margin: 15px;
}

footer {
    background-color: #fff; /* White background for footer */
    color: #4f4f4f; /* Dark grey for text */
    text-align: center;
    padding: 10px;
    box-shadow: 0 -2px 4px rgba(0, 0, 0, 0.1); /* Soft shadow for depth */
}

.button {
    background-color: #8dbf42; /* Muted green for buttons */
    color: #fff;
    padding: 10px 20px;
    text-align: center;
    text-decoration: none;
    display: inline-block;
    font-size: 16px;
    margin: 4px 2px;
    cursor: pointer;
    border: none; /* Removed border for a cleaner look */
    border-radius: 5px;
    transition: background-color 0.3s;
}

.button:hover {
    background-color: #6e9830; /* Darker green on hover */
}

 @media screen and (max-width: 600px) {
    body {
        font-size: 18px;
    }

    header, footer {
        text-align: left;
        padding: 10px 20px;
    }

    /* Responsive table styles */
    table {
        width: 100%;
        border-collapse: collapse;
    }

    th, td {
        padding: 10px;
        text-align: left;
    }

    /* Scrollable table on small screens */
    .responsive-table {
        overflow-x: auto;
    }
 }

 /* Add animation classes */
 .animated {
    animation: fadeIn 1s ease-in;
 }

 @keyframes fadeIn {
    0% {opacity: 0;}
    100% {opacity: 1;}
 }

 /* Pagination styles */
 .pagination-controls {
    text-align: center;
    padding: 10px;
 }

 .pagination-controls button {
    margin: 0 5px;
    padding: 5px 10px;
    background-color: #8dbf42; /* Muted green for buttons */
    color: #fff;
    border: none; /* Removed border for a cleaner look */
    border-radius: 5px;
    cursor: pointer;
}

.pagination-controls button:hover {
    background-color: #6e9830; /* Darker green on hover */
}

```
## resources/views/partials/dropdown-filter.blade.php
```
<!DOCTYPE html>
<div class="animated" id="dropdown-filter">
    <label for="classificacao-select">Classificação</label>
    <select class="button" id="classificacao-select">
        <!-- Options will be populated via JavaScript -->
    </select>

    <label for="subproduto-select">Subproduto</label>
    <select class="button" id="subproduto-select">
        <!-- Options will be populated via JavaScript -->
    </select>

    <label for="local-select">Local</label>
    <select class="button" id="local-select">
        <!-- Options will be populated via JavaScript -->
    </select>

    <label for="freq-select">Frequência</label>
    <select class="button" id="freq-select">
        <!-- Options will be populated via JavaScript -->
    </select>

    <label for="proprietario-select">Proprietário</label>
    <select class="button" id="proprietario-select">
        <!-- Options will be populated via JavaScript -->
    </select>
</div>
<script>console.log('[dropdown-filter.blade.php] Dropdown filter view loaded');</script>

```
## resources/views/partials/download-buttons.blade.php
```
<!DOCTYPE html>
<div class="animated">
    <button class="button" id="download-csv-btn">Download CSV</button>
    <button class="button" id="download-pdf-btn">Download PDF</button>
</div>
<script>console.log('[download-buttons.blade.php] Download buttons view loaded');</script>

```
## app/Console/Commands/FetchDatagroData.php
```
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\ExtendedProductList;
use App\Models\DataSeries;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use League\Csv\Reader;

class FetchDatagroData extends Command
{
    protected $signature = 'fetch:datagro-data';
    protected $description = 'Fetch data from Datagro API';

    public function handle()
    {
        Log::info("Command Started: Fetching Datagro Data");

        $products = ExtendedProductList::all();
        Log::info("Total Products to Process: " . $products->count());

        foreach ($products as $product) {
            $this->info("Processing product: " . $product->Código_Produto);
            Log::info("Processing Product: " . $product->Código_Produto);

            $additionalDataResponse = $this->fetchProductData($product->Código_Produto);
            Log::info("API Response for Product Data: ", (array) $additionalDataResponse);

            $additionalData = $additionalDataResponse[0] ?? null; // Accessing the first element of the response

            if ($additionalData) {
                Log::info("Attempting to Update Product: " . $product->Código_Produto);
                $product->update([
                    'bolsa' => $additionalData['bolsa'] ?? null,
                    'roda' => $additionalData['roda'] ?? null,
                    'fonte' => $additionalData['fonte'] ?? null,
                    'tav' => $additionalData['tav'] ?? null,
                    'subtav' => $additionalData['subtav'] ?? null,
                    'decimais' => $additionalData['decimais'] ?? null,
                    'correlatos' => $additionalData['correlatos'] ?? null,
                    'empresa' => $additionalData['empresa'] ?? null,
                    'contrato' => $additionalData['contrato'] ?? null,
                    'subproduto_id' => $additionalData['subproduto'] ?? null,
                    'entcode' => $additionalData['entcode'] ?? null,
                    'nome' => $additionalData['nome'] ?? null,
                    'longo' => $additionalData['longo'] ?? null,
                    'descr' => $additionalData['descr'] ?? null,
                    'codf' => $additionalData['codf'] ?? null,
                    'bd' => $additionalData['bd'] ?? null,
                    'palavras' => $additionalData['palavras'] ?? null,
                    'habilitado' => $additionalData['habilitado'] ?? null,
                    'lote' => $additionalData['lote'] ?? null,
                    'rep' => $additionalData['rep'] ?? null,
                    'vln' => $additionalData['vln'] ?? null,
                    'dia' => $additionalData['dia'] ?? null,
                    'freq' => $additionalData['freq'] ?? null,
                    'dex' => $additionalData['dex'] ?? null,
                    'inserido' => $additionalData['inserido'] ?? null,
                    'alterado' => $additionalData['alterado'] ?? null,
                ]);
                Log::info("Updated Product: " . $product->Código_Produto, $additionalData);
            } else {
                Log::warning("No Additional Data for Product: " . $product->Código_Produto);
            }

            $startDate = $product->inserido ? Carbon::createFromFormat('Y-m-d H:i:s', $product->inserido)->format('Ymd') : '20230101';
            $endDate = $product->alterado ? Carbon::createFromFormat('Y-m-d H:i:s', $product->alterado)->format('Ymd') : '20231201';
            $dataSeries = $this->fetchDataSeries($product->Código_Produto, $startDate, $endDate);

            if (!empty($dataSeries)) {
                foreach ($dataSeries as $data) {
                    Log::info("Creating Data Series Entry with data: " . json_encode($data));

                    DataSeries::create([
                        'extended_product_list_id' => $product->id,
                        'cod' => $product->Código_Produto,
                        'data' => $data['data'] ?? null,
                        'ult' => $data['ult'] ?? null,
                        'mini' => $data['mini'] ?? null,
                        'maxi' => $data['maxi'] ?? null,
                        'abe' => $data['abe'] ?? null,
                        'volumes' => $data['volumes'] ?? null,
                        'cab' => $data['cab'] ?? null,
                        'med' => $data['med'] ?? null,
                        'aju' => $data['aju'] ?? null,
                    ]);

                    Log::info("Data Series Entry Created for Product: " . $product->Código_Produto);
            }
                $product->update(['Fetch_Status' => 'Success']);
            } else {
                foreach ([["20230101", "20230108"], ["20230601", "20230701"]] as $dateRange) {
                    $dataSeries = $this->fetchDataSeries($product->Código_Produto, $dateRange[0], $dateRange[1]);
                    if (!empty($dataSeries)) {
                        foreach ($dataSeries as $data) {
                            DataSeries::create([
                                'extended_product_list_id' => $product->id,
                                'cod' => $product->Código_Produto,
                                'data' => $data['data'] ?? null,
                                'ult' => $data['ult'] ?? null,
                                'mini' => $data['mini'] ?? null,
                                'maxi' => $data['maxi'] ?? null,
                                'abe' => $data['abe'] ?? null,
                                'volumes' => $data['volumes'] ?? null,
                                'cab' => $data['cab'] ?? null,
                                'med' => $data['med'] ?? null,
                                'aju' => $data['aju'] ?? null,
                            ]);

                        }
                                    $product->update(['Fetch_Status' => 'Success']);
                        Log::info("Data Series Fetched and Stored for Product: " . $product->Código_Produto);
                    } else {
                        Log::warning("Data Series Fetching Failed for Product: " . $product->Código_Produto);
                        $product->update(['Fetch_Status' => 'Failed']);
                    }
                }

                Log::info('Data fetching and updating completed.');
            }
        }

        Log::info('Data fetching and updating completed.');
    }


    private function fetchProductData($productCode)
    {
        $url = "https://precos.api.datagro.com/cad/";
        $response = Http::withOptions(['verify' => false])->retry(5, 3000)->get($url, ['a' => $productCode, 'x' => 'j']);

        if ($response->successful()) {
            Log::info("Successful API Response for Product Data: " . $productCode);
            return $response->json();
        } else {
            Log::error("Failed to Fetch Product Data: {$productCode}, Status Code: " . $response->status());
            return null;
        }
    }

    private function fetchDataSeries($productCode, $startDate, $endDate)
    {
        $url = "https://precos.api.datagro.com/dados/";
        $params = [
            'a' => $productCode,
            'i' => $startDate,
            'f' => $endDate,
            'x' => 'c'
        ];

        $maxRetries = 5; // Number of retries
        $retryDelay = 3000; // Delay in milliseconds

        for ($attempt = 0; $attempt <= $maxRetries; $attempt++) {
            try {
                $response = Http::withOptions(['verify' => false])
                                ->get($url, $params);

                if ($response->successful()) {
                    Log::info("Successful API Response for Data Series: " . $productCode);
                    $csvData = $response->body(); // Get CSV data as string
                    return $this->parseCsvData($csvData); // Parse and return the data
                } else {
                    Log::error("Failed to Fetch Data Series: {$productCode}, Status Code: " . $response->status());
                }
            } catch (\Exception $e) {
                Log::error("Request Exception for {$productCode}: " . $e->getMessage());
            }

            // If max retries reached, log and break
            if ($attempt == $maxRetries) {
                Log::error("Max retries reached for {$productCode}");
                break;
            }

            // Delay before retrying
            usleep($retryDelay * 1000);
        }

        return null; // Return null in case of failure
    }



    private function parseCsvData($csvData)
    {
        // Manually define headers as the CSV does not have headers
        $headers = ['cod', 'data', 'ult', 'mini', 'maxi', 'abe', 'volumes', 'cab', 'med', 'aju'];

        $csv = Reader::createFromString($csvData);
        $csv->setHeaderOffset(null); // No headers in the actual CSV
        $records = $csv->getRecords($headers);

        $parsedData = [];
        foreach ($records as $record) {
            // Remove the last two columns (0 and null)
            array_pop($record);
            array_pop($record);

            Log::info("Record after removing last two columns: " . json_encode($record));

            // Handling date parsing
            $record['data'] = $this->parseDateForDataSeries($record['data']);

            $parsedData[] = $record;
        }

        return $parsedData;
    }


    private function parseDateForDataSeries($dateString)
    {
        Log::info("Parsing date string: {$dateString}");

        // Handle empty, null, or placeholder dates
        if (empty($dateString) || $dateString === '0000-00-00' || $dateString === '0000-00-00 00:00:00') {
            Log::warning("Invalid or placeholder date encountered: {$dateString}");
            return null;
        }

        // Attempt to parse the date string
        try {
            // If the date string includes time, extract only the date part
            if (strpos($dateString, ' ') !== false) {
                $dateString = explode(' ', $dateString)[0];
            }

            $formattedDate = Carbon::createFromFormat('Y-m-d', $dateString)->format('Y-m-d');
            Log::info("Formatted date: {$formattedDate}");
            return $formattedDate;
        } catch (\Exception $e) {
            Log::error("Invalid date format for string: {$dateString}. Error: " . $e->getMessage());
            return null;
        }
    }


}

```
## routes/web.php
```
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

```
## public/js/ProductsTable.js
```
// ProductsTable.js

console.log('ProductsTable.js loaded');

let selectedProductCode = null;
window.currentFilters = {};

window.loadProducts = function(page = 1, filters = window.currentFilters) {
    console.log(`Fetching products for page: ${page} with filters`, filters);
    const method = Object.keys(filters).length > 0 ? 'POST' : 'GET';
    const query = new URLSearchParams({ ...filters, page }).toString();
    const url = method === 'POST' ? '/filter-products' : `/products?page=${page}`;

    fetch(url, {
        method: method,
        headers: method === 'POST' ? {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        } : {},
        body: method === 'POST' ? JSON.stringify(filters) : null
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(response => {
        window.currentPage = response.current_page;
        window.totalPages = response.last_page;
        populateProductsTable(response.data || []); // Fallback to empty array if data is undefined
        renderPagination();
    })
    .catch(error => {
        console.error("Failed to load products", error);
        populateProductsTable([]); // Handle error by calling populateProductsTable with an empty array
    });
};

document.addEventListener('DOMContentLoaded', function () {
    window.loadProducts();
});


window.populateProductsTable = function(products, noMatches = false) {
    console.log("[ProductsTable] populateProductsTable called with products:", products);

    let tableBody = document.getElementById('products-table-body');
    if (!tableBody) {
        console.error("Table body not found");
        return;
    }

    if (products.length === 0 && noMatches) {
        // Show all products and display no matches message
        window.loadProducts();
        alert("No matches found with the current filters. Displaying all records.");
    } else {
        // Populate table with products
        tableBody.innerHTML = products.map(product => `
            <tr>
                <td><input type="radio" name="productSelect" value="${product.Classificação}" onchange="selectProduct('${product.Classificação}')"></td>
                <td>${product.Classificação}</td>
                <td>${product.longo}</td>
                <td>${product.freq}</td>
                <td>${product.alterado}</td>
            </tr>
        `).join('');
    }
};

function selectProduct(productCode) {
    selectedProductCode = productCode;
}

function renderPagination() {
    const paginationDiv = document.getElementById('products-pagination');
    if (!paginationDiv) {
        console.error("Pagination div not found");
        return;
    }

    let html = '';
    if (window.currentPage > 1) {
        html += `<button onclick="window.loadProducts(${window.currentPage - 1}, window.currentFilters)">Previous</button>`;
    }

    html += `<span>Page ${window.currentPage} of ${window.totalPages}</span>`;

    if (window.currentPage < window.totalPages) {
        html += `<button onclick="window.loadProducts(${window.currentPage + 1}, window.currentFilters)">Next</button>`;
    }

    paginationDiv.innerHTML = html;
}

function setupDropdownFilters() {
    console.log("[ProductsTable] Setting up dropdown filters");

    fetch('/api/get-dropdown-data')
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error while fetching dropdown data! Status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        populateDropdowns(data);
        console.log("[ProductsTable] Dropdowns populated with server data");
    })
    .catch(error => {
        console.error("Failed to fetch dropdown data", error);
    });
}

document.addEventListener('DOMContentLoaded', function () {
    console.log('DOM fully loaded and parsed');
    window.loadProducts();
    setupDropdownFilters();
});

```
## resources/views/partials/products-table.blade.php
```
<!-- Products Table -->
<div class="responsive-table animated" id="products-table">
    <table>
        <thead>
            <tr>
                <th> [Carregar Data Series] </th>
                <th>Produto  ('Classificação')  </th>
                <th>Nome ('longo')  </th>
                <th>Frequência</th>
                <th>Primeira Data</th>
            </tr>
        </thead>
        <tbody id="products-table-body">
            <!-- Data populated by ProductsTable.js -->
        </tbody>
    </table>
</div>
<div id="products-pagination" class="pagination-controls">
    <!-- Pagination Controls populated by ProductsTable.js -->
</div>


```
## app/Http/Controllers/FilterController.php
```
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ExtendedProductList;
use Illuminate\Support\Facades\Log;

class FilterController extends Controller
{
    public function getDropdownData()
    {
        Log::info('[FilterController] Fetching dropdown data');
        try {
            // Fetching 'Classificação' instead of 'Produto'
            $classificacao = ExtendedProductList::distinct('Classificação')->pluck('Classificação', 'id');
            Log::info('[FilterController] Classificação data: ' . json_encode($classificacao));

            $subproduto = ExtendedProductList::distinct('Subproduto')->pluck('Subproduto', 'id');
            Log::info('[FilterController] Subproduto data: ' . json_encode($subproduto));

            $local = ExtendedProductList::distinct('Local')->pluck('Local', 'id');
            Log::info('[FilterController] Local data: ' . json_encode($local));

            $freq = ExtendedProductList::distinct('freq')->pluck('freq', 'id');
            Log::info('[FilterController] freq data: ' . json_encode($freq));

            // Fetching 'bolsa' and converting to 'Proprietário' data
            $proprietario = ExtendedProductList::pluck('bolsa', 'id')
                ->mapWithKeys(function ($item, $key) {
                    return [$key => $item == 2 ? 'sim' : 'nao'];
                });
            Log::info('[FilterController] Proprietário data: ' . json_encode($proprietario));

            return response()->json([
                'classificacao' => $classificacao,
                'subproduto' => $subproduto,
                'local' => $local,
                'freq' => $freq,
                'proprietario' => $proprietario,
                // Add any other fields if necessary
            ]);
        } catch (\Exception $e) {
            Log::error('[FilterController] Error fetching dropdown data: ' . $e->getMessage());
            return response()->json(['error' => 'Error fetching dropdown data'], 500);
        }
    }
}

```
## public/js/DataSeriesTable.js
```
// Function to load data series for a product with pagination
function loadDataSeries(productId, page = 1, perPage = 10) {
    console.log(`[DataSeriesTable] Start loading data series for productId: ${productId}, page: ${page}, perPage: ${perPage}`);
    fetch(`/data-series/${productId}?page=${page}&perPage=${perPage}`)
        .then(response => {
            if (!response.ok) {
                console.error(`[DataSeriesTable] Error fetching data series: ${response.statusText}`);
                throw new Error(`Error fetching data series: ${response.statusText}`);
            }
            return response.json();
        })
        .then(data => {
            console.log("[DataSeriesTable] DataSeries API Response received:", data);
            let tableBody = document.getElementById('data-series-body');
            tableBody.innerHTML = data.data.map(series => `
                <tr>
                    <td>${series.cod}</td>
                    <td>${series.data}</td>
                    <td>${series.ult}</td>
                    <td>${series.mini}</td>
                    <td>${series.maxi}</td>
                    <td>${series.abe}</td>
                    <td>${series.volumes}</td>
                    <td>${series.med}</td>
                    <td>${series.aju}</td>
                </tr>
            `).join('');
            console.log("[DataSeriesTable] Data series successfully rendered in table");

            renderPagination(data.pagination, (newPage) => loadDataSeries(productId, newPage));
        })
        .catch(error => {
            console.error("[DataSeriesTable] DataSeries API Error:", error);
        });
}

// Function to render pagination for data series
// Ensure the renderPagination is specific for DataSeries and does not overlap with ProductsTable
function renderDataSeriesPagination(paginationData, productId) {
    let paginationDiv = document.getElementById('data-series-pagination');
    paginationDiv.innerHTML = ''; // Clear existing pagination controls

    // Previous button
    if (paginationData.current_page > 1) {
        paginationDiv.innerHTML += `<button onclick="loadDataSeries(${productId}, ${paginationData.current_page - 1}, ${paginationData.per_page})">Previous</button>`;
    }

    // Current Page Indicator
    paginationDiv.innerHTML += `<span>Page ${paginationData.current_page} of ${paginationData.last_page}</span>`;

    // Next button
    if (paginationData.current_page < paginationData.last_page) {
        paginationDiv.innerHTML += `<button onclick="loadDataSeries(${productId}, ${paginationData.current_page + 1}, ${paginationData.per_page})">Next</button>`;
    }
}

// Call this function wherever you need to render pagination for data series

```
## resources/views/partials/data-series-table.blade.php
```
<!-- Data Series Table -->
<div class="responsive-table animated" id="data-series-table">
    <table>
        <thead>
            <tr>
                <th>Cod</th>
                <th>data</th>
                <th>ult</th>
                <th>mini</th>
                <th>maxi</th>
                <th>abe</th>
                <th>volumes</th>
                <th>med</th>
                <th>aju</th>
            </tr>
        </thead>
        <tbody id="data-series-body">
            <!-- Data populated by DataSeriesTable.js -->
        </tbody>
    </table>
</div>
<div id="data-series-pagination" class="pagination-controls">
    <!-- Pagination Controls populated by DataSeriesTable.js -->
</div>
<script>console.log('[data-series-table.blade.php] Data series table view loaded');</script>

```
## resources/views/app.blade.php
```
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Datagro Comercial Team Web Application</title>
  <meta name="csrf-token" content="{{ csrf_token() }}"> <!-- CSRF token meta tag added -->
  <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body>
    <header style="display: flex; justify-content: space-between; align-items: center; padding: 10px;">
        <h1>API Mercado Físico - SALES TOOL</h1>
        <img src="{{ asset('images/Logo - Quadrado.png') }}" alt="Datagro Logo" style="height: 50px;">
    </header>

  <main>
      @include('partials.dropdown-filter')
      @include('partials.products-table', ['products' => $products])
      @include('partials.data-series-table')
      @include('partials.download-buttons')
  </main>

  <footer style="display: flex; justify-content: space-between; align-items: center; padding: 10px;">
    <img src="{{ asset('images/Logo - Banner Médio - Markets - 2.png') }}" alt="Datagro Markets" style="height: 50px;">
    <div>
        <h2>DATAGRO LINKS</h2>
        <ul>
            <li><a href="https://www.datagro.com/en/" target="_blank">www.datagro.com</a></li>
            <li><a href="https://portal.datagro.com/" target="_blank">portal.datagro.com</a></li>
            <li><a href="https://www.linkedin.com/company/datagro" target="_blank">Datagro LinkedIn</a></li>
        </ul>
    </div>
  </footer>


  <!-- Include the JavaScript files -->
  <script type="module" src="{{ asset('js/ProductsTable.js') }}"></script>
  <script type="module" src="{{ asset('js/DropdownFilter.js') }}"></script>
  <script type="module" src="{{ asset('js/DataSeriesTable.js') }}"></script>
  <script type="module" src="{{ asset('js/DownloadButtons.js') }}"></script>
  <script>console.log('[app.blade.php] Main application view loaded');</script>
</body>
</html>

```
## public/js/DropdownFilter.js
```
// DropdownFilter.js

// Convert frequency codes to full words at the top level so it's accessible by all functions
const freqToWord = {
    'D': 'Diário',
    'W': 'Semanal',
    'M': 'Mensal',
    'A': 'Anual'
};

// Function to dynamically populate dropdowns
window.populateDropdowns = function(data) {
    console.log("[DropdownFilter] Populating dropdowns with products data");

    const getUniqueValues = (values) => [...new Set(values)];

    // Populate each dropdown
    const dropdowns = {
        'classificacao-select': getUniqueValues(Object.values(data.classificacao)),
        'subproduto-select': getUniqueValues(Object.values(data.subproduto)),
        'local-select': getUniqueValues(Object.values(data.local)),
        'freq-select': getUniqueValues(Object.values(data.freq).map(code => freqToWord[code] || code)),
        'proprietario-select': getUniqueValues(Object.values(data.proprietario))
    };

    Object.entries(dropdowns).forEach(([dropdownId, values]) => {
        const dropdown = document.getElementById(dropdownId);
        if (dropdown) {
            values.forEach(value => {
                dropdown.add(new Option(value, value));
            });
            console.log(`[DropdownFilter] Dropdown populated: ${dropdownId}`);
        } else {
            console.error(`[DropdownFilter] Dropdown not found: ${dropdownId}`);
        }
    });
};

// Function to handle filter changes and fetch filtered products
function updateFilters() {
    console.log("[DropdownFilter] Updating filters");

    // Existing filter values retrieval
    const classificacao = document.getElementById('classificacao-select').value;
    const subproduto = document.getElementById('subproduto-select').value;
    const local = document.getElementById('local-select').value;
    const freqValue = document.getElementById('freq-select').value;

    // Convert 'Proprietário' back to 'bolsa' value
    const proprietarioValue = document.getElementById('proprietario-select').value;
    const bolsa = proprietarioValue === 'sim' ? 2 : (proprietarioValue === 'nao' ? 1 : '');

    // Convert frequency word to code
    const freq = Object.keys(freqToWord).find(key => freqToWord[key] === freqValue);

    console.log(`[DropdownFilter] Filter parameters: Classificação: ${classificacao}, Subproduto: ${subproduto}, Local: ${local}, Frequência: ${freq}, Bolsa: ${bolsa}`);

    const requestBody = JSON.stringify({ classificacao, subproduto, local, freq, bolsa });

    // Store current filters
    window.currentFilters = { classificacao, subproduto, local, freq, bolsa };

    console.log(`[DropdownFilter] AJAX request body: ${requestBody}`);

    fetch('/filter-products', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: requestBody
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        console.log("[DropdownFilter] Filtered products received:", data);
        if (data && data.data && Array.isArray(data.data)) {
            if (data.data.length === 0) {
                // No matches found, load all products and show alert
                window.loadProducts();
                alert("No matches found with the current filters. Displaying all records.");
            } else {
                window.populateProductsTable(data.data);
            }
        } else {
            console.error("[DropdownFilter] No products received or invalid data format after filter update", data);
        }
    })
    .catch(error => {
        console.error("[DropdownFilter] Filter products API Error:", error);
    });
}
// Setting up event listeners for each filter
document.addEventListener('DOMContentLoaded', function () {
    const filters = ['classificacao-select', 'subproduto-select', 'local-select', 'freq-select', 'proprietario-select'];
    filters.forEach(filterId => {
        const filterElement = document.getElementById(filterId);
        if (filterElement) {
            filterElement.addEventListener('change', () => {
                console.log(`[DropdownFilter] Filter changed: ${filterId}`);
                updateFilters();
            });
            console.log(`[DropdownFilter] Event listener added for: ${filterId}`);
        } else {
            console.error(`[DropdownFilter] Filter element not found: ${filterId}`);
        }
    });
});

```
## app/Http/Controllers/DataSeriesController.php
```
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

```
