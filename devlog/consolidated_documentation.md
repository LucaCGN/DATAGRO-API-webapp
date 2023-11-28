## public\js\DataSeriesTable.js
```php
function loadDataSeries(productId, page = 1, perPage = 10) {
    console.log(`Loading data series for productId: ${productId}, page: ${page}, perPage: ${perPage}`);  // Log the function call
    fetch(`/data-series/${productId}?page=${page}&perPage=${perPage}`)
        .then(response => {
            if (!response.ok) {
                console.error(`Error fetching data series: ${response.statusText}`);
            }
            return response.json();
        })
        .then(data => {
            console.log("DataSeries API Response:", data);  // Log API response
            let tableBody = document.getElementById('data-series-body');
            tableBody.innerHTML = data.series.map(series => `
                <tr>
                    <td>${series.name}</td>
                    <td>${series.value}</td>
                    <td>${series.date}</td>
                </tr>
            `).join('');

            renderPagination(data.pagination, (newPage) => loadDataSeries(productId, newPage));
        })
        .catch(error => console.error("DataSeries API Error:", error)); // Log any fetch errors
}

function renderPagination(paginationData, updateFunction) {
    console.log("Rendering pagination with data:", paginationData);  // Log pagination data
    let paginationDiv = document.getElementById('data-series-pagination');
    paginationDiv.innerHTML = '';

    if (paginationData.currentPage > 1) {
        let prevButton = document.createElement('button');
        prevButton.textContent = 'Previous';
        prevButton.onclick = () => {
            console.log(`Previous page button clicked, going to page ${paginationData.currentPage - 1}`);  // Log button click
            updateFunction(paginationData.currentPage - 1);
        };
        paginationDiv.appendChild(prevButton);
    }

    if (paginationData.currentPage < paginationData.lastPage) {
        let nextButton = document.createElement('button');
        nextButton.textContent = 'Next';
        nextButton.onclick = () => {
            console.log(`Next page button clicked, going to page ${paginationData.currentPage + 1}`);  // Log button click
            updateFunction(paginationData.currentPage + 1);
        };
        paginationDiv.appendChild(nextButton);
    }
}

```

## public\js\DownloadButtons.js
```php
function downloadCSV() {
    console.log("Downloading CSV file");  // Log the function call
    window.location.href = '/download/csv';
}

function downloadPDF() {
    console.log("Downloading PDF file");  // Log the function call
    window.location.href = '/download/pdf';
}

document.getElementById('download-csv-btn').addEventListener('click', () => {
    console.log("Download CSV button clicked");  // Log button click
    downloadCSV();
});
document.getElementById('download-pdf-btn').addEventListener('click', () => {
    console.log("Download PDF button clicked");  // Log button click
    downloadPDF();
});

```

## public\js\DropdownFilter.js
```php
function updateFilters() {
    console.log("Updating filters");  // Log the function call
    const classification = document.getElementById('classification-select').value;
    const subproduct = document.getElementById('subproduct-select').value;
    const local = document.getElementById('local-select').value;

    console.log(`Filter parameters - Classification: ${classification}, Subproduct: ${subproduct}, Local: ${local}`);  // Log filter parameters

    fetch(`/filter-products?classification=${classification}&subproduct=${subproduct}&local=${local}`)
        .then(response => {
            if (!response.ok) {
                console.error(`Error fetching filtered products: ${response.statusText}`);
            }
            return response.json();
        })
        .then(data => {
            console.log("Filter products API Response:", data);  // Log API response
            updateProductsTable(data.products);
        })
        .catch(error => console.error("Filter products API Error:", error)); // Log any fetch errors
}

document.getElementById('classification-select').addEventListener('change', () => {
    console.log("Classification select changed");  // Log select change
    updateFilters();
});
document.getElementById('subproduct-select').addEventListener('change', () => {
    console.log("Subproduct select changed");  // Log select change
    updateFilters();
});
document.getElementById('local-select').addEventListener('change', () => {
    console.log("Local select changed");  // Log select change
    updateFilters();
});

function updateProductsTable(products) {
    console.log("Updating products table with products:", products);  // Log the products being rendered
    let tableBody = document.getElementById('products-table-body');
    tableBody.innerHTML = products.map(product => `
        <tr onclick="selectProduct(${product.id})">
            <td>${product.nome}</td>
            <td>${product.freq}</td>
            <td>${product.inserido}</td>
            <td>${product.alterado}</td>
        </tr>
    `).join('');
}

```

## public\js\ProductsTable.js
```php
// Function to fetch and populate products with pagination
function fetchAndPopulateProducts(page = 1, perPage = 10) {
    console.log(`Fetching products for page: ${page}, perPage: ${perPage}`); // Log the function call
    fetch(`/products?page=${page}&perPage=${perPage}`)
        .then(response => {
            if (!response.ok) {
                console.error(`Error fetching products: ${response.statusText}`);
            }
            return response.json();
        })
        .then(data => {
            console.log("Products API Response:", data); // Log API response
            populateProductsTable(data.products);
            renderPagination(data.pagination);
        })
        .catch(error => console.error("Products API Error:", error)); // Log any fetch errors
}

// Function to populate products table
function populateProductsTable(products) {
    console.log("Populating products table with:", products); // Log the products being rendered
    let tableBody = document.getElementById('products-table-body');
    tableBody.innerHTML = products.map(product => `
        <tr onclick="selectProduct(${product.id})">
            <td>${product.nome}</td>
            <td>${product.freq}</td>
            <td>${product.inserido}</td>
            <td>${product.alterado}</td>
        </tr>
    `).join('');
}

// Function to render pagination controls
function renderPagination(paginationData) {
    console.log("Rendering pagination with data:", paginationData); // Log pagination data
    let paginationDiv = document.getElementById('products-pagination');
    paginationDiv.innerHTML = '';

    // Create previous button if needed
    if (paginationData.currentPage > 1) {
        let prevButton = document.createElement('button');
        prevButton.textContent = 'Previous';
        prevButton.onclick = () => {
            console.log(`Previous page button clicked, going to page ${paginationData.currentPage - 1}`); // Log button click
            fetchAndPopulateProducts(paginationData.currentPage - 1);
        };
        paginationDiv.appendChild(prevButton);
    }

    // Create next button if needed
    if (paginationData.currentPage < paginationData.lastPage) {
        let nextButton = document.createElement('button');
        nextButton.textContent = 'Next';
        nextButton.onclick = () => {
            console.log(`Next page button clicked, going to page ${paginationData.currentPage + 1}`); // Log button click
            fetchAndPopulateProducts(paginationData.currentPage + 1);
        };
        paginationDiv.appendChild(nextButton);
    }
}

```

## resources/views/partials/data-series-table.blade.php
```php
<div class="responsive-table animated" id="data-series-table">
    <table>
        <thead>
            <tr>
                <th>Data Series Name</th>
                <th>Value</th>
                <th>Date</th>
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

```

## resources/views/partials/download-buttons.blade.php
```php
<!-- Updated to remove Livewire syntax -->
<div class="animated">
    <button class="button" id="download-csv-btn">Download CSV</button>
    <button class="button" id="download-pdf-btn">Download PDF</button>
</div>
<script src="{{ asset('js/download-buttons.js') }}"></script>

```

## resources/views/partials/dropdown-filter.blade.php
```php
<!-- Updated to remove Livewire syntax -->
<div class="animated" id="dropdown-filter">
    <!-- Dropdown filters -->
    <select class="button" id="classification-select">
        <!-- Options populated server-side -->
    </select>

    <select class="button" id="subproduct-select">
        <!-- Options populated server-side -->
    </select>

    <select class="button" id="local-select">
        <!-- Options populated server-side -->
    </select>
</div>
<script src="{{ asset('js/dropdown-filter.js') }}"></script>

```

## resources/views/partials/products-table.blade.php
```php
<div class="responsive-table animated" id="products-table">
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Frequency</th>
                <th>Insert Date</th>
                <th>Last Update</th>
            </tr>
        </thead>
        <tbody id="products-table-body">
            <!-- Data populated by ProductsTable.js -->
        </tbody>
    </table>
    <div id="products-pagination" class="pagination-controls">
        <!-- Pagination Controls -->
    </div>
</div>
<script src="{{ asset('js/products-table.js') }}"></script>

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
      <!-- Main content goes here -->
  </main>

  <footer>
      <!-- Footer content goes here -->
  </footer>

  <!-- Include the JavaScript files -->
  <script type="module" src="{{ asset('js/app.js') }}"></script>
  <script type="module" src="{{ asset('js/DropdownFilter.js') }}"></script>
  <script type="module" src="{{ asset('js/ProductsTable.js') }}"></script>
  <script type="module" src="{{ asset('js/DataSeriesTable.js') }}"></script>
  <script type="module" src="{{ asset('js/DownloadButtons.js') }}"></script>
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

## database/migrations/2023_03_01_120001_create_data_series_table.php
```php
<?php

// Filename: 2023_03_01_120001_create_data_series_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('data_series_tables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('extended_product_list_id')->constrained('extended_product_list_tables')->onDelete('cascade');
            $table->string('cod');
            $table->date('data');
            $table->decimal('ult', 8, 2)->nullable();
            $table->decimal('mini', 8, 2)->nullable();
            $table->decimal('maxi', 8, 2)->nullable();
            $table->decimal('abe', 8, 2)->nullable();
            $table->integer('volumes')->nullable();
            $table->integer('cab')->nullable();
            $table->decimal('med', 8, 2)->nullable();
            $table->integer('aju')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('data_series_tables');
    }
};

```

## database/migrations/2023_03_01_120000_create_extended_product_list_table.php
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('extended_product_list_tables', function (Blueprint $table) {
            $table->id();
            $table->string('Código_Produto')->unique();
            $table->string('Classificação')->nullable();
            $table->string('Subproduto')->nullable();
            $table->string('Local')->nullable();
            $table->string('Fetch_Status')->nullable();
            $table->integer('bolsa')->nullable();
            $table->integer('roda')->nullable();
            $table->integer('fonte')->nullable();
            $table->string('tav')->nullable();
            $table->string('subtav')->nullable();
            $table->integer('decimais')->nullable();
            $table->integer('correlatos')->nullable();
            $table->string('empresa')->nullable();
            $table->string('contrato')->nullable();
            $table->integer('subproduto_id')->nullable();
            $table->string('entcode')->nullable();
            $table->string('nome')->nullable();
            $table->string('longo')->nullable();
            $table->text('descr')->nullable();
            $table->string('codf')->nullable();
            $table->string('bd')->nullable();
            $table->text('palavras')->nullable();
            $table->integer('habilitado')->nullable();
            $table->integer('lote')->nullable();
            $table->integer('rep')->nullable();
            $table->integer('vln')->nullable();
            $table->integer('dia')->nullable();
            $table->string('freq')->nullable();
            $table->string('dex')->nullable();
            $table->dateTime('inserido')->nullable();
            $table->dateTime('alterado')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('extended_product_list_tables');
    }
};


```

