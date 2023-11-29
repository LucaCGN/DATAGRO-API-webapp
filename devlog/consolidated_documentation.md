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
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log("[ProductsTable] Products API Response received:", data);
            populateProductsTable(data.data);
            renderPagination(data);  // Call renderPagination with the fetched data
        })
        .catch(error => {
            console.error("[ProductsTable] Products API Error:", error);
        });
}

// Global variable to track the currently selected product ID
let selectedProductId = null;

// Function to handle row selection
function selectProduct(productId) {
    // Check if we're unselecting the current product
    if (selectedProductId === productId) {
        selectedProductId = null;
        document.getElementById(`product-checkbox-${productId}`).checked = false;
    } else {
        // Unselect any previously selected checkbox
        if (selectedProductId !== null) {
            document.getElementById(`product-checkbox-${selectedProductId}`).checked = false;
        }
        selectedProductId = productId;
    }

    // Perform any additional logic needed when a product is selected
    console.log(`Product ${productId} selected`);
}

// Function to populate products table
function populateProductsTable(products) {
    console.log("[ProductsTable] Populating products table with:", products);
    let tableBody = document.getElementById('products-table-body');
    tableBody.innerHTML = products.map(product => `
        <tr>
            <td><input type="checkbox" id="product-checkbox-${product.id}" name="selectedProduct" ${
                selectedProductId === product.id ? 'checked' : ''
            } onclick="selectProduct(${product.id})"></td>
            <td>${product.Código_Produto}</td>
            <td>${product.descr}</td>
            <td>${product.inserido}</td>
            <td>${product.alterado}</td>
        </tr>
    `).join('');
    console.log("[ProductsTable] Products table populated");
}


// Function to fetch and populate products with pagination
function fetchAndPopulateProducts(page = 1, perPage = 10) {
    console.log(`[ProductsTable] Fetching products for page: ${page}, perPage: ${perPage}`);
    fetch(`/products?page=${page}&perPage=${perPage}`)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log("[ProductsTable] Products API Response received:", data);
            populateProductsTable(data.data);
            renderPagination(data);  // Call renderPagination with the fetched data
        })
        .catch(error => {
            console.error("[ProductsTable] Products API Error:", error);
        });
}

// Function to update the current page indicator
function updateCurrentPageIndicator(currentPage, lastPage) {
    let currentPageIndicator = document.getElementById('current-page-indicator');
    if (currentPageIndicator) {
        currentPageIndicator.textContent = `Page ${currentPage} of ${lastPage}`;
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
<script src="{{ asset('js/DataSeriesTable.js') }}"></script>
<script>console.log('[data-series-table.blade.php] Data series table view loaded');</script>

```

## resources/views/partials/download-buttons.blade.php
```php
<!DOCTYPE html>
<div class="animated">
    <button class="button" id="download-csv-btn">Download CSV</button>
    <button class="button" id="download-pdf-btn">Download PDF</button>
</div>
<script src="{{ asset('js/DownloadButtons.js') }}"></script>
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
<script src="{{ asset('js/DropdownFilter.js') }}"></script>
<script>console.log('[dropdown-filter.blade.php] Dropdown filter view loaded');</script>

```

## resources/views/partials/products-table.blade.php
```php
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
    <div id="products-pagination" class="pagination-controls">
        <!-- Pagination Controls populated by ProductsTable.js -->
    </div>
</div>
<script src="{{ asset('js/ProductsTable.js') }}"></script>

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

    public function filter(Request $request)
    {
        Log::info("ProductController: filter method called with request: ", $request->all());
        $products = ExtendedProductList::where('Código_Produto', $request->Código_Produto)
                    ->orWhere('descr', $request->descr)
                    ->paginate(10); // Assuming default 10 per page for filters as well
        Log::info('Filtered Products Retrieved: ' . $products->count());
        return response()->json($products);
    }
}

```

