## public/js/DataSeriesTable.js
```php
// Function to load data series for a product with pagination
function loadDataSeries(productId, page = 1, perPage = 10) {
    console.log(`Loading data series for productId: ${productId}, page: ${page}, perPage: ${perPage}`);
    fetch(`/data-series/${productId}?page=${page}&perPage=${perPage}`)
        .then(response => {
            if (!response.ok) {
                throw new Error(`Error fetching data series: ${response.statusText}`);
            }
            return response.json();
        })
        .then(data => {
            console.log("DataSeries API Response:", data);
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

            renderPagination(data.pagination, (newPage) => loadDataSeries(productId, newPage));
        })
        .catch(error => console.error("DataSeries API Error:", error));
}

// Function to render pagination for data series
function renderPagination(paginationData, updateFunction) {
    console.log("Rendering pagination with data:", paginationData);
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
}

```

## public/js/DownloadButtons.js
```php
// Functions to handle download actions
function downloadCSV() {
    console.log("Downloading CSV file");
    window.location.href = '/download/csv';
}

function downloadPDF() {
    console.log("Downloading PDF file");
    window.location.href = '/download/pdf';
}

// Event listeners for download buttons
document.addEventListener('DOMContentLoaded', function () {
    const csvBtn = document.getElementById('download-csv-btn');
    const pdfBtn = document.getElementById('download-pdf-btn');

    if (csvBtn) {
        csvBtn.addEventListener('click', function() {
            console.log("Download CSV button clicked");
            downloadCSV();
        });
    }

    if (pdfBtn) {
        pdfBtn.addEventListener('click', function() {
            console.log("Download PDF button clicked");
            downloadPDF();
        });
    }
});

```

## public/js/DropdownFilter.js
```php
// Function to update filters and fetch filtered products
function updateFilters() {
    console.log("Updating filters");
    const produto = document.getElementById('produto-select').value;
    const subproduto = document.getElementById('subproduct-select').value;
    const local = document.getElementById('local-select').value;
    const freq = document.getElementById('freq-select').value;
    const proprietario = document.getElementById('proprietario-select').value;

    console.log(`Filter parameters - Produto: ${produto}, Subproduto: ${subproduto}, Local: ${local}, Frequência: ${freq}, Proprietário: ${proprietario}`);

    fetch(`/filter-products?produto=${produto}&subproduto=${subproduto}&local=${local}&freq=${freq}&proprietario=${proprietario}`)
        .then(response => {
            if (!response.ok) {
                console.error(`Error fetching filtered products: ${response.statusText}`);
            }
            return response.json();
        })
        .then(data => {
            console.log("Filter products API Response:", data);
            updateProductsTable(data.products);
        })
        .catch(error => console.error("Filter products API Error:", error));
}

// Function to update the products table based on filters
function updateProductsTable(products) {
    console.log("Updating products table with products:", products);
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

// Event listeners for dropdown filters
document.addEventListener('DOMContentLoaded', function () {
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
    console.log(`Fetching products for page: ${page}, perPage: ${perPage}`);
    fetch(`/products?page=${page}&perPage=${perPage}`)
        .then(response => {
            if (!response.ok) {
                throw new Error(`Error fetching products: ${response.statusText}`);
            }
            return response.json();
        })
        .then(data => {
            console.log("Products API Response:", data);
            populateProductsTable(data.data);
            renderPagination(data.pagination);
        })
        .catch(error => console.error("Products API Error:", error));
}

// Function to populate products table
function populateProductsTable(products) {
    console.log("Populating products table with:", products);
    let tableBody = document.getElementById('products-table-body');
    tableBody.innerHTML = products.map(product => `
        <tr onclick="selectProduct(${product.id})">
            <td>${product.Código_Produto}</td>
            <td>${product.descr}</td>
            <td>${product.inserido}</td>
            <td>${product.alterado}</td>
        </tr>
    `).join('');
}

// Function to render pagination controls for products
function renderPagination(paginationData) {
    console.log("Rendering pagination with data:", paginationData);
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
}

document.addEventListener('DOMContentLoaded', () => {
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

```

## resources/views/partials/download-buttons.blade.php
```php
<!DOCTYPE html>
<div class="animated">
    <button class="button" id="download-csv-btn">Download CSV</button>
    <button class="button" id="download-pdf-btn">Download PDF</button>
</div>
<script src="{{ asset('js/download-buttons.js') }}"></script>

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

```

## resources/views/partials/products-table.blade.php
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
      @include('partials.products-table')
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
</body>
</html>

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
      @include('partials.products-table')
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
</body>
</html>

```

## routes\web.php
```php
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\DataSeriesController;
use App\Http\Controllers\DownloadController;
use Illuminate\Support\Facades\Log;

Route::get('/', function () {
    Log::info('Navigating to the home page.');
    return view('app');
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

