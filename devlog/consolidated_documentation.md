## app/Http/Controllers/ProductController.php
```
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
        $products = ExtendedProductList::paginate(10); // Default to 10 per page
        Log::info('Products Retrieved: ' . $products->count());
        return response()->json($products);
    }

    public function paginate($page, $perPage)
    {
        Log::info("ProductController: paginate method called with page {$page} and perPage {$perPage}");
        $products = ExtendedProductList::paginate($perPage, ['*'], 'page', $page);
        Log::info('Paginated Products Retrieved');
        return response()->json($products);
    }

    // Adjusted to handle POST requests for filtering
    public function filter(Request $request)
    {
        // Ensure your method is equipped to handle the request appropriately
        $query = ExtendedProductList::query();

        if ($request->filled('produto')) {
            $query->where('Código_Produto', 'like', '%' . $request->produto . '%');
        }
        if ($request->filled('subproduto')) {
            $query->where('Subproduto', 'like', '%' . $request->subproduto . '%');
        }
        // Add other filters similarly...

        $products = $query->paginate(10); // Or the perPage value sent from the front-end
        return response()->json($products);
    }
}

```
## resources/views/partials/dropdown-filter.blade.php
```
<!DOCTYPE html>
<div class="animated" id="dropdown-filter">
    <select class="button" id="produto-select">
        <!-- Options will be populated via JavaScript -->
    </select>
    <select class="button" id="subproduto-select">
        <!-- Options will be populated via JavaScript -->
    </select>
    <select class="button" id="local-select">
        <!-- Options will be populated via JavaScript -->
    </select>
    <select class="button" id="freq-select">
        <!-- Options will be populated via JavaScript -->
    </select>
    <select class="button" id="proprietario-select">
        <!-- Options will be populated via JavaScript -->
    </select>
</div>
<script src="{{ asset('js/DropdownFilter.js') }}"></script>
<script>console.log('[dropdown-filter.blade.php] Dropdown filter view loaded');</script>

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
        // Assuming these are the fields for your dropdowns
        $produto = ExtendedProductList::distinct('Código_Produto')->pluck('Código_Produto', 'id');
        $subproduto = ExtendedProductList::distinct('Subproduto')->pluck('Subproduto', 'id');
        $local = ExtendedProductList::distinct('Local')->pluck('Local', 'id');
        $freq = ExtendedProductList::distinct('Freq')->pluck('Freq', 'id');
        $proprietario = ExtendedProductList::distinct('Proprietario')->pluck('Proprietario', 'id');

        return response()->json([
            'produto' => $produto,
            'subproduto' => $subproduto,
            'local' => $local,
            'freq' => $freq,
            'proprietario' => $proprietario
        ]);
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
## resources/views/partials/download-buttons.blade.php
```
<!DOCTYPE html>
<div class="animated">
    <button class="button" id="download-csv-btn">Download CSV</button>
    <button class="button" id="download-pdf-btn">Download PDF</button>
</div>
<script src="{{ asset('js/DownloadButtons.js') }}"></script>
<script>console.log('[download-buttons.blade.php] Download buttons view loaded');</script>

```
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
## public/js/ProductsTable.js
```
// ProductsTable.js

// Assume this function is provided the products data when called
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

    // Call populateDropdowns from DropdownFilter.js after the table is populated
    populateDropdowns(products);
}

function selectProduct(productId) {
    // Implementation of selectProduct
    console.log("[ProductsTable] Product selected with ID:", productId);
    // Additional logic for product selection
}

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
## resources/views/partials/products-table.blade.php
```
<!-- Products Table -->
<div class="responsive-table animated" id="products-table">
    <table>
        <thead>
            <tr>
                <th>Select</th>
                <th>Código_Produto</th>
                <th>Descr</th>
                <th>Data Inserido</th>
                <th>Data Alterado</th>
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
<script src="{{ asset('js/ProductsTable.js') }}"></script>

```
## public/js/DropdownFilter.js
```
// DropdownFilter.js
// Function to update filters and fetch filtered products
function updateFilters() {
    console.log("[DropdownFilter] Updating filters");
    const produto = document.getElementById('produto-select').value;
    const subproduto = document.getElementById('subproduct-select').value;
    const local = document.getElementById('local-select').value;
    const freq = document.getElementById('freq-select').value;
    const proprietario = document.getElementById('proprietario-select').value;

    console.log(`[DropdownFilter] Filter parameters - Produto: ${produto}, Subproduto: ${subproduto}, Local: ${local}, Frequência: ${freq}, Proprietário: ${proprietario}`);

    fetch('/filter-products', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            produto: produto,
            subproduto: subproduto,
            local: local,
            freq: freq,
            proprietario: proprietario
        })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
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
    let tableBody = document.getElementById('products-table-body');
    tableBody.innerHTML = products.map(product => `
        <tr onclick="selectProduct(${product.id})">
            <td>${product.Código_Produto}</td>
            <td>${product.descr}</td>
            <td>${product.inserido}</td>
            <td>${product.alterado}</td>
        </tr>
    `).join('');
    console.log("[DropdownFilter] Products table updated");
}

// Function to populate dropdowns
function populateDropdowns(products) {
    const uniqueValues = (products, key) => [...new Set(products.map(product => product[key]))];

    const populateDropdown = (dropdownId, values) => {
        const dropdown = document.getElementById(dropdownId);
        values.forEach(value => {
            dropdown.add(new Option(value, value));
        });
    };

    populateDropdown('produto-select', uniqueValues(products, 'Código_Produto'));
    populateDropdown('subproduto-select', uniqueValues(products, 'Subproduto'));
    populateDropdown('local-select', uniqueValues(products, 'Local'));
    populateDropdown('freq-select', uniqueValues(products, 'Freq'));

    // Special logic for 'proprietario' dropdown
    const proprietarioOptions = uniqueValues(products, 'bolsa').map(value => value === 2 ? 'Sim' : 'Não');
    populateDropdown('proprietario-select', [...new Set(proprietarioOptions)]);
}

// This function should be called after products table is populated
// For now, it's called here for demonstration purposes
document.addEventListener('DOMContentLoaded', function () {
    // Assuming products data is available here as 'productsData'
    // populateDropdowns(productsData);
});

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
<script src="{{ asset('js/DataSeriesTable.js') }}"></script>
<script>console.log('[data-series-table.blade.php] Data series table view loaded');</script>

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
## routes/web.php
```
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


// Add a route to handle CSRF token generation
Route::get('/csrf-token', function() {
    return csrf_token();
});

// New route for fetching dropdown data
Route::get('/api/get-dropdown-data', [FilterController::class, 'getDropdownData']);

// Filter products
Route::post('/filter-products', [ProductController::class, 'filter']); // Changed to POST

```
