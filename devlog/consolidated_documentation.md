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
  <script type="module" src="{{ asset('js/DropdownFilter.js') }}"></script>
  <script type="module" src="{{ asset('js/ProductsTable.js') }}"></script>
  <script type="module" src="{{ asset('js/DataSeriesTable.js') }}"></script>
  <script type="module" src="{{ asset('js/DownloadButtons.js') }}"></script>
  <script>console.log('[app.blade.php] Main application view loaded');</script>
</body>
</html>

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

