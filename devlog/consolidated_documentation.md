## app/Http/Controllers/ProductController.php
```
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ExtendedProductList;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        Log::info('ProductController: index method called', $request->all());
        $perPage = 10;

        try {
            $query = ExtendedProductList::query();

            // Dynamically apply filters if provided in the request, skip if null
            $filters = $request->only(['classificacao', 'subproduto', 'local', 'freq', 'bolsa']);
            foreach ($filters as $key => $value) {
                if (!is_null($value) && $value !== '') {
                    $query->where($key, $value);
                }
            }

            // Paginate the query result
            $products = $query->paginate($perPage, ['*'], 'page', $request->get('page', 1));

            Log::info('Products fetched successfully', ['count' => $products->count()]);
            return response()->json($products);
        } catch (\Exception $e) {
            Log::error('Error fetching products', ['message' => $e->getMessage()]);
            return response()->json(['error' => 'Error fetching products'], 500);
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

// Function to dynamically populate dropdowns with null selection
window.populateDropdowns = function(data) {
    console.log("[DropdownFilter] Populating dropdowns with products data", JSON.stringify(data, null, 2));

    const nullOptionHTML = '<option value="">Select...</option>';
    const dropdowns = {
        'classificacao-select': [nullOptionHTML].concat(data.classificacao),
        'subproduto-select': [nullOptionHTML].concat(data.subproduto),
        'local-select': [nullOptionHTML].concat(data.local),
        'freq-select': [nullOptionHTML].concat(data.freq.map(code => freqToWord[code] || code)),
        'proprietario-select': [nullOptionHTML].concat(data.proprietario.map(item => item === 2 ? 'Sim' : 'Não'))
    };

    Object.entries(dropdowns).forEach(([dropdownId, values]) => {
        const dropdown = document.getElementById(dropdownId);
        if (dropdown) {
            console.log(`[DropdownFilter] Processing Dropdown: ${dropdownId}`);
            const html = values.map(value => `<option value="${value}">${value}</option>`).join('');
            dropdown.innerHTML = html;
            console.log(`[DropdownFilter] Dropdown populated: ${dropdownId} with values:`, values);
        } else {
            console.error(`[DropdownFilter] Dropdown not found: ${dropdownId}`);
        }
    });
};


// Function to handle filter changes and fetch filtered products
window.updateFilters = async function() {
    console.log("[DropdownFilter] Starting filter update process");

    const classificacao = document.getElementById('classificacao-select').value || null;
    const subproduto = document.getElementById('subproduto-select').value || null;
    const local = document.getElementById('local-select').value || null;
    const freqValue = document.getElementById('freq-select').value || null;
    const proprietarioValue = document.getElementById('proprietario-select').value || null;

    console.log("[DropdownFilter] Current filter values:", {
        classificacao,
        subproduto,
        local,
        freqValue,
        proprietarioValue
    });

    const bolsa = proprietarioValue === 'Sim' ? 2 : (proprietarioValue === 'Não' ? 1 : null);
    const freq = freqValue ? Object.keys(freqToWord).find(key => freqToWord[key] === freqValue) : null;

    const filterValues = { classificacao, subproduto, local, freq, bolsa };

    console.log("[DropdownFilter] Filters to be applied:", filterValues);

    Object.keys(filterValues).forEach(key => filterValues[key] == null && delete filterValues[key]);

    console.log("[DropdownFilter] Filter values after removing nulls:", filterValues);

    window.currentFilters = filterValues;

    console.log("[DropdownFilter] Stored current filters:", window.currentFilters);

    try {
        console.log("[DropdownFilter] Sending request to filter products with filters:", filterValues);

        const response = await fetch('/api/filter-products', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify(filterValues)
        });

        console.log("[DropdownFilter] Received response status:", response.status);

        if (!response.ok) {
            console.error(`[DropdownFilter] HTTP error! status: ${response.status}`);
            return;
        }

        const data = await response.json();
        console.log("[DropdownFilter] Filtered products received:", JSON.stringify(data, null, 2));
        await window.populateProductsTable(data.data);
    } catch (error) {
        console.error("[DropdownFilter] Filter products API Error:", error);
    }
};


// Event listeners for each filter dropdown
document.addEventListener('DOMContentLoaded', function () {
    const filters = ['classificacao-select', 'subproduto-select', 'local-select', 'freq-select', 'proprietario-select'];
    filters.forEach(filterId => {
        const filterElement = document.getElementById(filterId);
        if (filterElement) {
            filterElement.addEventListener('change', window.updateFilters);
            console.log(`[DropdownFilter] Event listener added for: ${filterId}`);
        } else {
            console.error(`[DropdownFilter] Filter element not found: ${filterId}`);
        }
    });
});

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
    public function getInitialFilterData()
    {
        Log::info('[FilterController] Fetching initial filter data');
        try {
            $classificacao = ExtendedProductList::distinct()->pluck('Classificação');
            $subproduto = ExtendedProductList::distinct()->pluck('Subproduto');
            $local = ExtendedProductList::distinct()->pluck('Local');
            $freq = ExtendedProductList::distinct()->pluck('freq');
            $bolsa = ExtendedProductList::distinct()->pluck('bolsa');

            $proprietario = $bolsa->map(function ($item) {
                return $item == 2 ? 'Sim' : 'Não';
            })->unique()->values();

            Log::info('[FilterController] Filter data fetched successfully', [
                'classificacao' => $classificacao,
                'subproduto' => $subproduto,
                'local' => $local,
                'freq' => $freq,
                'proprietario' => $proprietario,
            ]);

            return response()->json([
                'classificacao' => $classificacao,
                'subproduto' => $subproduto,
                'local' => $local,
                'freq' => $freq,
                'proprietario' => $proprietario,
            ]);
        } catch (\Exception $e) {
            Log::error('[FilterController] Error fetching initial filter data: ' . $e’sgetMessage());
            return response()->json(['error' => 'Error fetching initial filter data'], 500);
        }
    }

    public function getDropdownData(Request $request)
    {
        Log::info('[FilterController] Fetching dropdown data');
        try {
            $query = ExtendedProductList::query();

            // Apply the filters if any are provided in the request
            foreach ($request->all() as $key => $value) {
                if (!empty($value)) {
                    $query->where($key, $value);
                }
            }

            // Fetch the data for dropdowns applying distinct to avoid duplicates
            $classificacao = $query->distinct()->pluck('Classificação');
            $subproduto = $query->distinct()->pluck('Subproduto');
            $local = $query->distinct()->pluck('Local');
            $freq = $query->distinct()->pluck('freq');
            $proprietario = $query->distinct()->pluck('bolsa');

            return response()->json([
                'classificacao' => $classificacao,
                'subproduto' => $subproduto,
                'local' => $local,
                'freq' => $freq,
                'proprietario' => $proprietario->mapWithKeys(function ($item) {
                    return [$item => $item == 2 ? 'sim' : 'nao'];
                }),
            ]);
        } catch (\Exception $e) {
            Log::error('[FilterController] Error fetching dropdown data: ' . $e->getMessage());
            return response()->json(['error' => 'Error fetching dropdown data'], 500);
        }
    }
    public function getUpdatedFilterOptions(Request $request)
    {
        Log::info('[FilterController] Fetching updated filter options based on current selections');
        try {
            // Initialize the base query
            $query = ExtendedProductList::query();

            // Dynamically build the query based on provided filters, except for the one being updated
            $filters = $request->all();
            foreach ($filters as $key => $value) {
                // Skip empty filters
                if (!empty($value)) {
                    $query->where($key, $value);
                }
            }

            // Fetch the distinct values for each filter, excluding the keys present in the request
            $data = [
                'classificacao' => $request->has('classificacao') ? [] : $query->distinct()->pluck('Classificação'),
                'subproduto' => $request->has('subproduto') ? [] : $query->distinct()->pluck('Subproduto'),
                'local' => $request->has('local') ? [] : $query->distinct()->pluck('Local'),
                'freq' => $request->has('freq') ? [] : $query->distinct()->pluck('freq'),
            ];

            // Special handling for 'proprietario' based on 'bolsa'
            if (!$request->has('proprietario')) {
                $bolsaValues = $query->distinct()->pluck('bolsa');
                $data['proprietario'] = $bolsaValues->mapWithKeys(function ($item) {
                    return [$item => $item == 2 ? 'Sim' : 'Não'];
                });
            } else {
                $data['proprietario'] = [];
            }

            return response()->json($data);
        } catch (\Exception $e) {
            Log::error('[FilterController] Error fetching updated filter options: ' . $e->getMessage());
            return response()->json(['error' => 'Error fetching updated filter options'], 500);
        }
    }

}


```
## public/js/ProductsTable.js
```
// ProductsTable.js

console.log('ProductsTable.js loaded');

let selectedProductCode = null;
window.currentFilters = {};
window.showingAllRecords = false;

// Function to load products based on the current page and filters
window.loadProducts = async function(page = 1, filters = window.currentFilters) {
    console.log(`Fetching products for page: ${page} with filters`, filters);

    const hasFilters = Object.keys(filters).length > 0;
    const query = new URLSearchParams({ ...filters, page }).toString();
    const url = hasFilters ? `/api/filter-products` : `/products?page=${page}`;
    const method = hasFilters ? 'POST' : 'GET';

    const headers = {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
    };

    console.log("[ProductsTable] Sending request to:", url, " with method:", method, " and headers:", headers);

    try {
        const response = await fetch(url, {
            method: method,
            headers: headers,
            body: hasFilters ? JSON.stringify(filters) : null
        });

        console.log("[ProductsTable] Response received with status:", response.status);

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();
        console.log("[ProductsTable] Products data received:", data);

        window.populateProductsTable(data.data || []);
        window.currentPage = data.current_page;
        window.totalPages = data.last_page;
        window.renderPagination();
    } catch (error) {
        console.error("Failed to load products", error);
        window.populateProductsTable([]); // Show empty state or handle error appropriately
    }
};

// Populate the products table
window.populateProductsTable = function(products) {
    console.log("[ProductsTable] Populating products table with data:", products);

    let tableBody = document.getElementById('products-table-body');
    if (!tableBody) {
        console.error("Table body not found");
        return;
    }

    // Clear the table before populating new data
    tableBody.innerHTML = '';

    // Only populate if there are products
    if (products.length > 0) {
        tableBody.innerHTML = products.map(product => `
            <tr>
                <td><input type="radio" name="productSelect" value="${product['Código_Produto']}" onchange="selectProduct('${product['Código_Produto']}')"></td>
                <td>${product.Classificação}</td>
                <td>${product.longo}</td>
                <td>${product.freq}</td>
                <td>${product.alterado}</td>
            </tr>
        `).join('');
        console.log("[ProductsTable] Products table populated with products.");
    } else {
        // Show a message or an empty state if there are no products
        tableBody.innerHTML = `<tr><td colspan="5">No products found.</td></tr>`;
        console.log("[ProductsTable] No products found message displayed.");
    }
};

// Pagination rendering function
window.renderPagination = function() {
    console.log("[ProductsTable] Rendering pagination.");

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
    console.log("[ProductsTable] Pagination rendered.");
};

// Setting up dropdown filters
window.setupDropdownFilters = async function() {
    console.log("[ProductsTable] Setting up dropdown filters");

    try {
        const response = await fetch('/api/filters', {
            method: 'GET',
            headers: { 'Accept': 'application/json' }
        });

        console.log("[ProductsTable] Fetching filter data with response status:", response.status);

        if (!response.ok) {
            console.error(`[ProductsTable] HTTP error while fetching dropdown data! Status: ${response.status}`);
            return;
        }

        const data = await response.json();
        console.log("[ProductsTable] Dropdown filters set up with data:", JSON.stringify(data, null, 2));
        window.populateDropdowns(data);
    } catch (error) {
        console.error("[ProductsTable] Failed to fetch dropdown data", error);
    }
};


document.addEventListener('DOMContentLoaded', function () {
    console.log("[ProductsTable] DOMContentLoaded - Starting to load products and set up filters.");
    window.loadProducts();
    window.setupDropdownFilters();
});

// New function to update dropdowns based on current filters
window.updateDropdowns = async function(currentFilters) {
    console.log("[ProductsTable] Updating dropdowns based on current filters", currentFilters);

    // Remove any null or empty filter values
    const nonNullFilters = Object.fromEntries(Object.entries(currentFilters).filter(([_, v]) => v != null));
    console.log("[ProductsTable] Filters after removing nulls:", nonNullFilters);

    try {
        const response = await fetch('/api/filters/updated', {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(nonNullFilters)
        });

        console.log("[ProductsTable] Fetching updated dropdown data with response status:", response.status);

        if (!response.ok) {
            throw new Error(`HTTP error while fetching updated dropdown data! Status: ${response.status}`);
        }

        const data = await response.json();
        window.populateDropdowns(data);
        console.log("[ProductsTable] Dropdowns updated with new data:", data);
    } catch (error) {
        console.error("Failed to fetch updated dropdown data", error);
    }
};

```
