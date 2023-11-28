## public/js/DataSeriesTable.js
```php
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
function renderPagination(paginationData, updateFunction) {
    console.log("[DataSeriesTable] Rendering pagination with data:", paginationData);
    let paginationDiv = document.getElementById('data-series-pagination');
    paginationDiv.innerHTML = '';

    if (paginationData.current_page > 1) {
        let prevButton = document.createElement('button');
        prevButton.textContent = 'Previous';
        prevButton.onclick = () => updateFunction(paginationData.current_page - 1);
        paginationDiv.appendChild(prevButton);
    }

    if (paginationData.current_page < paginationData.last_page) {
        let nextButton = document.createElement('button');
        nextButton.textContent = 'Next';
        nextButton.onclick = () => updateFunction(paginationData.current_page + 1);
        paginationDiv.appendChild(nextButton);
    }
    console.log("[DataSeriesTable] Pagination controls rendered");
}

```

## public/js/DownloadButtons.js
```php
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

## public/js/DropdownFilter.js
```php
// Function to update filters and fetch filtered products
function updateFilters() {
    console.log("[DropdownFilter] Updating filters");
    const produto = document.getElementById('produto-select').value;
    const subproduto = document.getElementById('subproduct-select').value;
    const local = document.getElementById('local-select').value;
    const freq = document.getElementById('freq-select').value;
    const proprietario = document.getElementById('proprietario-select').value;

    console.log(`[DropdownFilter] Filter parameters - Produto: ${produto}, Subproduto: ${subproduto}, Local: ${local}, Frequência: ${freq}, Proprietário: ${proprietario}`);

    fetch(`/filter-products?produto=${produto}&subproduto=${subproduto}&local=${local}&freq=${freq}&proprietario=${proprietario}`)
        .then(response => {
            if (!response.ok) {
                console.error(`[DropdownFilter] Error fetching filtered products: ${response.statusText}`);
            }
            return response.json();
        })
        .then(data => {
            console.log("[DropdownFilter] Filter products API Response:", data);
            updateProductsTable(data.products);
        })
        .catch(error => {
            console.error("[DropdownFilter] Filter products API Error:", error);
        });
}

// Function to update the products table based on filters
function updateProductsTable(products) {
    console.log("[DropdownFilter] Updating products table with products:", products);
    let tableBody = document.getElementById('products-table-body');
    tableBody.innerHTML = products.map(product => `
        <tr onclick="selectProduct(${product.id})">
            <td>${product.nome}</td>
            <td>${product.freq}</td>
            <td>${product.inserido}</td>
            <td>${product.alterado}</td>
        </tr>
    `).join('');
    console.log("[DropdownFilter] Products table updated");
}

// Event listeners for dropdown filters
document.addEventListener('DOMContentLoaded', function () {
    console.log("[DropdownFilter] Setting up filter dropdown event listeners");
    const filters = ['produto-select', 'subproduct-select', 'local-select', 'freq-select', 'proprietario-select'];
    filters.forEach(filterId => {
        const filterElement = document.getElementById(filterId);
        if (filterElement) {
            filterElement.addEventListener('change', updateFilters);
        }
    });
});

```

## public/js/ProductsTable.js
```php
// Function to fetch and populate products with pagination
function fetchAndPopulateProducts(page = 1, perPage = 10) {
    console.log(`[ProductsTable] Fetching products for page: ${page}, perPage: ${perPage}`);
    fetch(`/products?page=${page}&perPage=${perPage}`)
        .then(response => {
            if (!response.ok) {
                console.error(`[ProductsTable] Error fetching products: ${response.statusText}`);
                throw new Error(`Error fetching products: ${response.statusText}`);
            }
            return response.json();
        })
        .then(data => {
            console.log("[ProductsTable] Products API Response received:", data);
            populateProductsTable(data.data);
            renderPagination(data.pagination);
        })
        .catch(error => {
            console.error("[ProductsTable] Products API Error:", error);
        });
}

// Function to populate products table
function populateProductsTable(products) {
    console.log("[ProductsTable] Populating products table with:", products);
    let tableBody = document.getElementById('products-table-body');
    tableBody.innerHTML = products.map(product => `
        <tr onclick="selectProduct(${product.id})">
            <td>${product.Código_Produto}</td>
            <td>${product.descr}</td>
            <td>${product.inserido}</td>
            <td>${product.alterado}</td>
        </tr>
    `).join('');
    console.log("[ProductsTable] Products table populated");
}

// Function to render pagination controls for products
function renderPagination(paginationData) {
    console.log("[ProductsTable] Rendering pagination controls with data:", paginationData);
    let paginationDiv = document.getElementById('products-pagination');
    paginationDiv.innerHTML = '';

    if (paginationData.current_page > 1) {
        let prevButton = document.createElement('button');
        prevButton.textContent = 'Previous';
        prevButton.onclick = () => fetchAndPopulateProducts(paginationData.current_page - 1);
        paginationDiv.appendChild(prevButton);
    }

    if (paginationData.current_page < paginationData.last_page) {
        let nextButton = document.createElement('button');
        nextButton.textContent = 'Next';
        nextButton.onclick = () => fetchAndPopulateProducts(paginationData.current_page + 1);
        paginationDiv.appendChild(nextButton);
    }
    console.log("[ProductsTable] Pagination controls rendered");
}

document.addEventListener('DOMContentLoaded', () => {
    console.log("[ProductsTable] DOMContentLoaded - starting product fetching");
    fetchAndPopulateProducts();
});

```

## resources/views/partials/data-series-table.blade.php
```php
<!DOCTYPE html>
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
    <div id="data-series-pagination" class="pagination-controls">
        <!-- Pagination Controls -->
    </div>
</div>
<script src="{{ asset('js/data-series.js') }}"></script>
<script>console.log('[data-series-table.blade.php] Data series table view loaded');</script>

```

## resources/views/partials/download-buttons.blade.php
```php
<!DOCTYPE html>
<div class="animated">
    <button class="button" id="download-csv-btn">Download CSV</button>
    <button class="button" id="download-pdf-btn">Download PDF</button>
</div>
<script src="{{ asset('js/download-buttons.js') }}"></script>
<script>console.log('[download-buttons.blade.php] Download buttons view loaded');</script>

```

## resources/views/partials/dropdown-filter.blade.php
```php
<!DOCTYPE html>
<div class="animated" id="dropdown-filter">
    <select class="button" id="produto-select">
        <!-- Produto options populated server-side -->
    </select>
    <select class="button" id="subproduto-select">
        <!-- SubProduto options populated server-side -->
    </select>
    <select class="button" id="local-select">
        <!-- Local options populated server-side -->
    </select>
    <select class="button" id="freq-select">
        <!-- Frequência options populated server-side -->
    </select>
    <select class="button" id="proprietario-select">
        <!-- Proprietário options populated server-side -->
    </select>
</div>
<script src="{{ asset('js/dropdown-filter.js') }}"></script>
<script>console.log('[dropdown-filter.blade.php] Dropdown filter view loaded');</script>

```

## resources/views/partials/products-table.blade.php
```php
<div class="responsive-table animated" id="products-table">
    <table>
        <thead>
            <tr>
                <th>Código_Produto</th>
                <th>Descr</th>
                <th>Data Inserido</th>
                <th>Data Alterado</th>
            </tr>
        </thead>
        <tbody id="products-table-body">
            @foreach($products as $product)
                <tr onclick="selectProduct({{ $product->id }})">
                    <td>{{ $product->Código_Produto }}</td>
                    <td>{{ $product->descr }}</td>
                    <td>{{ $product->inserido }}</td>
                    <td>{{ $product->alterado }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <div id="products-pagination" class="pagination-controls">
        <!-- Pagination Controls -->
    </div>
</div>
<script src="{{ asset('js/products-table.js') }}"></script>
<script>console.log('[products-table.blade.php] Products table view loaded');</script>

```

## resources/views/app.blade.php
```php
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Datagro Comercial Team Web Application</title>
  <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body>
  <header>
      <!-- Header content goes here -->
  </header>

  <main>
      @include('partials.dropdown-filter')
      @include('partials.products-table', ['products' => $products])
      @include('partials.data-series-table')
      @include('partials.download-buttons')
  </main>

  <footer>
      <!-- Footer content goes here -->
  </footer>

  <!-- Include the JavaScript files -->
  <script type="module" src="{{ asset('js/DropdownFilter.js') }}"></script>
  <script type="module" src="{{ asset('js/ProductsTable.js') }}"></script>
  <script type="module" src="{{ asset('js/DataSeriesTable.js') }}"></script>
  <script type="module" src="{{ asset('js/DownloadButtons.js') }}"></script>
  <script>console.log('[app.blade.php] Main application view loaded');</script>
</body>
</html>

```

## public/css/app.css
```php
body {
    font-family: Arial, sans-serif;
    color: #ffffff;
    background-color: #002d04;
 }

 header {
    background-color: #002d04;
    color: #ffffff;
    padding: 10px;
    text-align: center;
 }

 main {
    margin: 15px;
 }

 footer {
    background-color: #002d04;
    color: #ffffff;
    text-align: center;
    padding: 10px;
 }

 .button {
    background-color: #2ecb13;
    color: #ffffff;
    padding: 10px 20px;
    text-align: center;
    text-decoration: none;
    display: inline-block;
    font-size: 16px;
    margin: 4px 2px;
    cursor: pointer;
    border: 1px solid #ffffff;
    border-radius: 5px;
    transition: background-color 0.3s;
 }

 .button:hover {
    background-color: #002d04;
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
    background-color: #2ecb13;
    color: #ffffff;
    border: 1px solid #ffffff;
    border-radius: 5px;
    cursor: pointer;
 }

 .pagination-controls button:hover {
    background-color: #002d04;
 }

```

## app/Models/DataSeries.php
```php
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
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExtendedProductList extends Model
{
    protected $table = 'extended_product_list_tables';
    protected $fillable = ['Código_Produto', 'Classificação', 'Subproduto', 'Local', 'Fetch_Status',
    'bolsa', 'roda', 'fonte', 'tav', 'subtav', 'decimais', 'correlatos',
    'empresa', 'contrato', 'subproduto_id', 'entcode', 'nome', 'longo', 'descr',
    'codf', 'bd', 'palavras', 'habilitado', 'lote', 'rep', 'vln', 'dia',
    'freq', 'dex', 'inserido', 'alterado'];
    public $timestamps = true;

    protected $with = ['dataSeries']; // Eager loading

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

## app/Console/Commands/FetchDatagroData.php
```php
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

## routes\web.php
```php
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\DataSeriesController;
use App\Http\Controllers\DownloadController;
use Illuminate\Support\Facades\Log;
use App\Models\ExtendedProductList;
use App\Models\DataSeries;

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

// Filter products
Route::get('/filter-products', [ProductController::class, 'filter']);

```

## app\Http\Controllers\DownloadController.php
```php
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

## app\Http\Controllers\ProductController.php
```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ExtendedProductList;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    public function index()
    {
        Log::info('ProductController: index method called');
        $products = ExtendedProductList::all();
        Log::info('Products Retrieved: ' . $products->count());
        return view('partials.products-table')->with('products', $products);
    }

    public function paginate($page, $perPage)
    {
        Log::info("ProductController: paginate method called with page {$page} and perPage {$perPage}");
        $products = ExtendedProductList::paginate($perPage);
        Log::info('Paginated Products Retrieved');
        return view('partials.products-table')->with('products', $products);
    }

    public function filter(Request $request)
    {
        Log::info("ProductController: filter method called with request: ", $request->all());
        $products = ExtendedProductList::where('Código_Produto', $request->Código_Produto)
                    ->orWhere('descr', $request->descr)
                    ->get();
        Log::info('Filtered Products Retrieved: ' . $products->count());
        return view('partials.products-table')->with('products', $products);
    }
}

```

## app\Http\Controllers\DataSeriesController.php
```php
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

## app\Http\Controllers\Controller.php
```php
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

