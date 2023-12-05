## app/Http/Controllers/FilterController.php
```
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ExtendedProductList;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\QueryException;

class FilterController extends Controller
{
    // This method can be used to fetch initial filter options for the dropdowns
    public function getInitialFilterOptions()
    {
        Log::info('[FilterController] Fetching initial filter options');
        try {
            // Fetch distinct values for each filter option from the database
            $classificacaoOptions = ExtendedProductList::distinct()->pluck('Classificação');
            $subprodutoOptions = ExtendedProductList::distinct()->pluck('Subproduto');
            $localOptions = ExtendedProductList::distinct()->pluck('Local');
            $freqOptions = ExtendedProductList::distinct()->pluck('freq');
            $bolsaOptions = ExtendedProductList::distinct()->pluck('bolsa');

            // Map 'bolsa' to 'proprietario' for frontend representation
            $proprietarioOptions = $bolsaOptions->map(function ($item) {
                return $item == 2 ? 'Sim' : 'Não'; // Ensure we return 'Sim'/'Não' instead of numeric values
            })->unique()->values();

            Log::info('[FilterController] Initial filter options fetched', [
                'classificacao' => $classificacaoOptions,
                'subproduto' => $subprodutoOptions,
                'local' => $localOptions,
                'freq' => $freqOptions,
                'proprietario' => $proprietarioOptions,
            ]);

            // Return the filter options as a JSON response
            return response()->json([
                'classificacao' => $classificacaoOptions,
                'subproduto' => $subprodutoOptions,
                'local' => $localOptions,
                'freq' => $freqOptions,
                'proprietario' => $proprietarioOptions,
            ]);
        } catch (QueryException $e) {
            Log::error('[FilterController] Database query exception: ' . $e->getMessage());
            return response()->json(['error' => 'Database query exception'], 500);
        } catch (\Exception $e) {
            Log::error('[FilterController] General exception: ' . $e->getMessage());
            return response()->json(['error' => 'General exception'], 500);
        }
    }

    // Method to fetch updated filter options based on current selections
    public function getUpdatedFilterOptions(Request $request)
    {
        Log::info('[FilterController] Fetching updated filter options with request: ', $request->all());
        try {
            // Initialize the base query
            $query = ExtendedProductList::query();

            // Apply filters based on the provided selections in the request
            foreach ($request->all() as $key => $value) {
                if (!empty($value)) {
                    // Convert 'proprietario' filter from frontend to 'bolsa' for the database query
                    if ($key === 'proprietario') {
                        $value = $value === 'Sim' ? 2 : 'Não';
                        $query->where('bolsa', $value);
                        Log::info("Applied filter for 'bolsa' with value: {$value}");
                    } else {
                        $query->where($key, $value);
                        Log::info("Applied filter for '{$key}' with value: {$value}");
                    }
                }
            }

            // Log the SQL query
            Log::debug('[FilterController] SQL Query: ' . $query->toSql());

            // Fetch the distinct values for the filters that are not currently selected
            $data = [
                'classificacao' => $request->filled('classificacao') ? [] : $query->distinct()->pluck('Classificação')->all(),
                'subproduto' => $request->filled('subproduto') ? [] : $query->distinct()->pluck('Subproduto')->all(),
                'local' => $request->filled('local') ? [] : $query->distinct()->pluck('Local')->all(),
                'freq' => $request->filled('freq') ? [] : $query->distinct()->pluck('freq')->all(),
                // Fetch 'bolsa' options and map to 'proprietario' for frontend representation
                'proprietario'  => $request->filled('proprietario') ? [] : $query->distinct()->pluck('bolsa')->map(function ($item) {
                    return $item == 2 ? 'Sim' : 'Não'; // Convert back to 'Sim'/'Não' for the frontend
                })->unique()->values()->all(),
            ];

            Log::info('[FilterController] Updated filter options fetched', $data);

            return response()->json($data);
        } catch (QueryException $e) {
            Log::error('[FilterController] Database query exception: ' . $e->getMessage());
            return response()->json(['error' => 'Database query exception'], 500);
        } catch (\Exception $e) {
            Log::error('[FilterController] General exception: ' . $e->getMessage());
            return response()->json(['error' => 'General exception'], 500);
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

```
## resources/views/partials/dropdown-filter.blade.php
```
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Filterable Product Table</title>
    <!-- Add any additional head content here (e.g., CSS links) -->
</head>
<body>
    <div id="filter-container">
        <!-- Dropdowns for each filter category -->
        <select id="classificacao-select" class="filter-dropdown">
            <option value="">Select Classificação...</option>
            <!-- Options will be populated dynamically -->
        </select>
        <select id="subproduto-select" class="filter-dropdown">
            <option value="">Select Subproduto...</option>
            <!-- Options will be populated dynamically -->
        </select>
        <select id="local-select" class="filter-dropdown">
            <option value="">Select Local...</option>
            <!-- Options will be populated dynamically -->
        </select>
        <select id="freq-select" class="filter-dropdown">
            <option value="">Select Frequência...</option>
            <!-- Options will be populated dynamically -->
        </select>
        <select id="proprietario-select" class="filter-dropdown">
            <option value="">Select Proprietário...</option>
            <!-- Options will be populated dynamically -->
        </select>

        <!-- Reset Button -->
        <button id="reset-filters-btn">Reset Filters</button>
    </div>

    <!-- Container for the products table or other display elements -->
    <div id="products-table">
        <!-- Table or other elements will be populated dynamically -->
    </div>

    <!-- JavaScript Files -->
    <script src="js/DropdownFilter.js"></script>
    <!-- Include other JS files or scripts here -->
</body>
</html>

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

    // Building the query URL
    const query = new URLSearchParams(filters).toString();
    const url = `/products?page=${page}&${query}`;

    // Headers for the fetch call
    const headers = {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
    };

    // Fetching products
    console.log("[ProductsTable] Fetching products with URL:", url);
    try {
        const response = await fetch(url, {
            method: 'GET',
            headers: headers,
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();
        console.log("[ProductsTable] Products data received:", data);
        populateProductsTable(data.data || []);
        renderPagination(data.current_page, data.last_page);
    } catch (error) {
        console.error("[ProductsTable] Failed to load products", error);
        populateProductsTable([]); // Show empty state or handle error appropriately
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


window.selectProduct = function(productCode) {
    console.log("Selected product code: ", productCode);
    window.loadDataSeries(productCode);
};


// Ensure that on document ready we reset the flag
document.addEventListener('DOMContentLoaded', function () {
    console.log("[ProductsTable] Page loaded - Starting to load products.");
    loadProducts();
});

```
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

            // Convert 'proprietario' filter from frontend to 'bolsa' for the database query
            if ($request->filled('proprietario')) {
                $bolsaValue = $request->input('proprietario') === 'Sim' ? 2 : ($request->input('proprietario') === 'Não' ? 1 : null);
                if (!is_null($bolsaValue)) {
                    $query->where('bolsa', $bolsaValue);
                    Log::info("Applying filter: bolsa with value: {$bolsaValue}");
                }
            }

            // Handle other filters
            $filters = $request->only(['classificacao', 'subproduto', 'local', 'freq']);
            foreach ($filters as $key => $value) {
                if (!is_null($value) && $value !== '') {
                    $query->where($key, $value);
                    Log::info("Applying filter: {$key} with value: {$value}");
                }
            }

            $products = $query->paginate($perPage);

            Log::info('Products fetched successfully with applied filters', ['count' => $products->count()]);
            return response()->json($products);
        } catch (\Exception $e) {
            Log::error('Error fetching products', ['message' => $e->getMessage()]);
            return response()->json(['error' => 'Error fetching products'], 500);
        }
    }
}

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

// Function to dynamically populate dropdowns with selection and correct placeholder text
window.populateDropdowns = function(data) {
    console.log("Populating dropdowns with data", data);

    // Verify that data for each dropdown is an array and log if not
    if (!Array.isArray(data.classificacao) || !Array.isArray(data.subproduto) ||
        !Array.isArray(data.local) || !Array.isArray(data.freq) ||
        !Array.isArray(data.proprietario)) {
        console.error("Expected data for dropdowns to be an array", data);
        return;
    }

    const createNullOption = (placeholder) => {
        const nullOption = document.createElement('option');
        nullOption.value = '';
        nullOption.textContent = placeholder;
        return nullOption;
    };

    // Populate dropdowns with options
    const dropdowns = {
        'classificacao-select': document.getElementById('classificacao-select'),
        'subproduto-select': document.getElementById('subproduto-select'),
        'local-select': document.getElementById('local-select'),
        'freq-select': document.getElementById('freq-select'),
        'proprietario-select': document.getElementById('proprietario-select')
    };

    // Reset dropdowns to ensure they are clear before adding new options
    Object.values(dropdowns).forEach(dropdown => dropdown.innerHTML = '');

    // Add options to dropdowns
    Object.entries(dropdowns).forEach(([key, dropdown]) => {
        if (dropdown) {
            dropdown.appendChild(createNullOption(`Select ${key.replace('-select', '')}...`));
            data[key.replace('-select', '')].forEach(value => {
                const option = document.createElement('option');
                option.value = value;
                option.textContent = value;
                dropdown.appendChild(option);
            });
        } else {
            console.error(`Dropdown not found: ${key}`);
        }
    });

    // Check that the initial values are selected in the dropdowns
    Object.keys(dropdowns).forEach(key => {
        if (window.currentFilters[key.replace('-select', '')]) {
            document.getElementById(key).value = window.currentFilters[key.replace('-select', '')];
        }
    });
};

// Function to handle filter changes and fetch filtered products
window.updateFilters = async function() {
    console.log("[DropdownFilter] Starting filter update process");

    // Fetch current filter values from the DOM
    const classificacao = document.getElementById('classificacao-select').value || null;
    const subproduto = document.getElementById('subproduto-select').value || null;
    const local = document.getElementById('local-select').value || null;
    let freq = document.getElementById('freq-select').value || null;
    let proprietario = document.getElementById('proprietario-select').value || null;

    // Convert frequency and owner values to codes if needed
    freq = Object.keys(freqToWord).find(key => freqToWord[key] === freq) || freq; // Map from full word to code
    proprietario = proprietario === 'Sim' ? 2 : (proprietario === 'Não' ? 1 : null); // Convert to number

    // Log current filter values
    console.log("[DropdownFilter] Current filter values:", {
        classificacao,
        subproduto,
        local,
        freq,
        proprietario
    });

    // Prepare the filters to be applied, removing any that are null
    const filterValues = { classificacao, subproduto, local, freq, proprietario };
    Object.keys(filterValues).forEach(key => filterValues[key] == null && delete filterValues[key]);

    // Update the window.currentFilters before fetching updated filter options
    window.currentFilters = { ...window.currentFilters, ...filterValues };
   console.log("[DropdownFilter] Filter values after removing nulls:", filterValues);

    // Update the window.currentFilters before fetching updated filter options
    window.currentFilters = { ...window.currentFilters, ...filterValues };
    console.log("[DropdownFilter] updateFilters - About to update dropdowns with currentFilters:", window.currentFilters);

    try {
        // Send the selected filters and get updated options for other filters
        const updateResponse = await fetch('/api/filters/updated', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify(window.currentFilters)
        });

        // Handle non-ok response
        if (!updateResponse.ok) {
            throw new Error(`HTTP error! status: ${updateResponse.status}`);
        }

        // Get and populate dropdowns with updated filter options
        const updatedFilters = await updateResponse.json();
        window.populateDropdowns(updatedFilters);

        // Fetch and update the products table with the filtered data
        const filterResponse = await fetch('/api/filter-products', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify(window.currentFilters)
        });

        // Handle non-ok response
        if (!filterResponse.ok) {
            throw new Error(`HTTP error! status: ${filterResponse.status}`);
        }

        // Populate the products table with the filtered data
        const filteredData = await filterResponse.json();
        await window.populateProductsTable(filteredData.data);

    } catch (error) {
        console.error("[DropdownFilter] Error:", error);
    }
};





window.resetFilters = function() {
    console.log("[DropdownFilter] Resetting filters");

    // Define the IDs of the dropdown elements
    const dropdownIds = [
        'classificacao-select',
        'subproduto-select',
        'local-select',
        'freq-select',
        'proprietario-select'
    ];

    // Reset each dropdown to its default state
    dropdownIds.forEach(id => {
        const dropdown = document.getElementById(id);
        if (dropdown) {
            dropdown.selectedIndex = 0; // This sets the dropdown back to the first option, typically "Select..."
        }
    });

    // Clear the current filters
    window.currentFilters = {};

    // Fetch initial filter options and reset the products table
    if (typeof window.getInitialFilterOptions === "function") {
        window.getInitialFilterOptions().then(initialFilters => {
            window.populateDropdowns(initialFilters);
        });
    } else {
        console.error("getInitialFilterOptions function is not defined.");
    }

    window.populateProductsTable([]);

    console.log("[DropdownFilter] Filters have been reset");
};



window.getInitialFilterOptions = async function() {
    console.log("[DropdownFilter] Fetching initial filter options");
    try {
        const response = await fetch('/api/initial-filter-options', {
            method: 'GET',
            headers: {
                'Accept': 'application/json'
            }
        });
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        const initialOptions = await response.json();
        window.populateDropdowns(initialOptions);
        console.log("[DropdownFilter] Initial filter options fetched and dropdowns populated");
    } catch (error) {
        console.error("[DropdownFilter] Error fetching initial filter options:", error);
    }
};

// Make sure to initialize filters and attach event listeners once on load
document.addEventListener('DOMContentLoaded', (function() {
    let executed = false;
    return function() {
        if (!executed) {
            executed = true;

            // Fetch and populate initial filter options
            window.getInitialFilterOptions();

            // Attach event listener to the reset button
            const resetButton = document.getElementById('reset-filters-btn');
            if (resetButton) {
                resetButton.addEventListener('click', function() {
                    window.resetFilters();
                    // If you want to update filters after reset, uncomment the following line:
                    // window.updateFilters();
                });
                console.log("[DropdownFilter] Reset button event listener attached");
            } else {
                console.error("[DropdownFilter] Reset button not found");
            }

            // Attach event listeners to filter dropdowns
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
        }
    };
})());

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
