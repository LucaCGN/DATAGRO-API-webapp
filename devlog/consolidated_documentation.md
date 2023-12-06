## public/css/app.css
```
/* Main application styles */
body {
    font-family: 'Arial', sans-serif;
    color: #4f4f4f;
    background-color: #f8f8f8;
}

/* Header styling with the logo aligned to the left and the background set to white */
header {
    display: flex;
    align-items: center;
    padding: 15px; /* Reduced padding to reduce total height */
    background-color: #8dbf42; /* Set background color */
}

/* Ensure the logo fits properly, aligned to the left, with adjusted size */
header img {
    height: auto; /* Keep the height auto to maintain aspect ratio */
    width: auto; /* Width set to auto to adjust with height */
    max-height: 60px; /* Example fixed max-height, adjust to your preference */
    order: -1; /* This ensures that the logo comes first within the flex container */
}

/* Updated title styling with flex-grow to center the title text, bold and white color */
header h1 {
    flex-grow: 1;
    text-align: center;
    color: #ffffff; /* Set text color to white */
    font-weight: bold; /* Make the font bold */
    margin: 0; /* Keep the margin 0 to align properly */
    font-size: 2em; /* You can adjust the font size if necessary */
}

/* Ensure the logo fits properly, aligned to the left, with adjusted size */
header img {
    height: auto; /* Keep the height auto to maintain aspect ratio */
    width: auto; /* Width set to auto to adjust with height */
    max-height: 60px; /* Example fixed max-height, adjust to your preference */
    order: -1; /* This ensures that the logo comes first within the flex container */
}


/* Footer styling with logo and links */
footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px;
    box-shadow: 0 -2px 4px rgba(0, 0, 0, 0.1);
    background-color: #fff;
}

.footer-logo {
    height: 50px;
    margin-right: auto;
}

.footer-links h2 {
    margin-top: 0;
    color: var(--brand-brown);
}

.footer-links ul {
    list-style: none;
    padding: 0;
    margin: 0;
}

.footer-links ul li {
    padding: 5px 0;
}

.footer-links ul li a {
    color: #4f4f4f;
    text-decoration: none;
}

.footer-links ul li a:hover {
    text-decoration: underline;
}

/* Branding colors */
:root {
    --brand-green: #8dbf42;
    --brand-brown: #8B4513;
}

/* Styling for the "selected-product-name" field */
.selected-product-display {
    font-weight: bold;
    font-style: italic;
    margin: 20px 0;
    padding: 10px;
    background-color: #f2f2f2;
    text-align: center;
    display: block;
}

/* Filter dropdown and buttons styling */
.filter-dropdown,
.button {
    background-color: var(--brand-green);
    color: #fff;
    padding: 10px 20px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

/* Specific styling for "Reset Filters" button */
#reset-filters-btn {
    background-color: transparent;
    color: var(--brand-brown);
    font-weight: bold;
    padding: 10px 20px;
    border: 2px solid var(--brand-brown);
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

/* Darken button on hover */
.filter-dropdown:hover,
.button:hover,
#reset-filters-btn:hover {
    background-color: var(--brand-brown);
    color: #fff;
}

/* DataSeries Table Styling */
.responsive-table table {
    width: 100%;
    max-width: 100%;
    border-collapse: collapse;
}

.responsive-table th,
.responsive-table td {
    border: 1px solid #ddd; /* Style as per your design */
    padding: 8px; /* Adjust padding as needed */
    text-align: left; /* Aligns text to the left */
    min-width: 120px; /* Ensures a minimum width for columns to avoid too much compression */
}

/* Title Styling */
h1 {
    color: var(--brand-brown);
    font-weight: bold;
    margin-bottom: 20px;
}

/* Responsive design adjustments */
@media screen and (max-width: 600px) {
    footer {
        flex-direction: column;
        align-items: center;
    }

    footer img.logo {
        margin-bottom: 10px;
    }

    footer .links {
        flex-direction: column;
        align-items: center;
    }

    .responsive-table {
        overflow-x: auto;
    }

    .responsive-table td,
    .responsive-table th {
        padding: 4px; /* Reduces padding on smaller screens for more space */
        min-width: 80px; /* Reduces minimum width on smaller screens */
    }

    .filter-dropdown,
    .button,
    #reset-filters-btn {
        width: 100%;
        margin-bottom: 10px;
    }
}

/* Consistent vertical rhythm and spacing */
* {
    box-sizing: border-box;
}

/* Spacing and alignment for the main content */
.main-content {
    margin: 15px;
    display: flex;
    flex-direction: column;
    align-items: flex-start;
}

.tables-container {
    width: 100%;
}

/* Align filters and buttons to the left */
.filters-container,
.download-buttons-container {
    display: flex;
    justify-content: flex-start;
    gap: 10px;
    margin-top: 15px;
    margin-bottom: 15px; /* Adjust this value to match the desired spacing */
}

@media screen and (max-width: 600px) {
    .filters-container,
    .download-buttons-container {
        flex-direction: column;
    }
}

.tables-container {
    display: flex;
    flex-direction: column;
    margin-bottom: 20px; /* Adjust this value to match the footer's top margin */
}

.responsive-table td, .responsive-table th {
    min-width: 120px; /* or any other appropriate value */
}

.tables-container {
    max-width: 100%;
    overflow-x: auto; /* for horizontal scrolling if necessary */
}

.responsive-table th:nth-child(1),
.responsive-table td:nth-child(1) {
    width: 10%; /* Adjust the percentage based on your design */
}
/* Repeat for each column as necessary */


```
## resources/views/partials/data-series-table.blade.php
```
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
                <th>cab</th>
            </tr>
        </thead>
        <tbody id="data-series-body">
            <!-- Data populated by DataSeriesTable.js -->
        </tbody>
    </table>
</div>
<script>console.log('[data-series-table.blade.php] Data series table view loaded');</script>


```
## resources/views/partials/download-buttons.blade.php
```
<div class="animated download-buttons-container">
    <button id="download-csv-btn" class="button">Download CSV</button>
    <button id="download-pdf-btn" class="button">Download PDF</button>
</div>
<script>console.log('[download-buttons.blade.php] Download buttons view loaded');</script>

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
    <header>
        <h1>DATAGRO Markets - Data Preview Platform</h1>
        <img src="{{ asset('images/Logo - Quadrado.png') }}" alt="Datagro Logo">
    </header>

    <main class="main-content">
        @include('partials.dropdown-filter') <!-- Ensure this partial does not contain conflicting styles -->
        <div class="tables-container">
            @include('partials.products-table', ['products' => $products])
            @include('partials.data-series-table')
        </div>
        @include('partials.download-buttons')
    </main>



<footer>
    <img src="{{ asset('images/Logo - Banner Médio - Markets - 2.png') }}" alt="Datagro Markets" class="footer-logo">
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
## public/js/DataSeriesTable.js
```
// Function to load data series for a product with pagination
window.loadDataSeries = function(productCode) {
    console.log(`Initiating fetch to /data-series/${productCode}`);

    fetch(`/data-series/${productCode}`)
        .then(response => {
            console.log("Raw fetch response:", response);
            if (!response.ok) {
                console.error(`[DataSeriesTable] Error fetching data series: ${response.statusText}`);
                throw new Error(`Error fetching data series: ${response.statusText}`);
            }
            return response.json();
        })
        .then(data => {
            console.log("[DataSeriesTable] DataSeries API Response received:", data);
            let tableBody = document.getElementById('data-series-body');
            window.globalDataSeries = data;
            tableBody.innerHTML = data.slice(0, 10).map(series => `
                <tr>
                    <td>${series.cod}</td>
                    <td>${series.data}</td>
                    <td>${series.ult}</td>
                    <td>${series.mini}</td>
                    <td>${series.maxi}</td>
                    <td>${series.abe}</td>
                    <td>${series.volumes}</td>
                    <td>${series.cab}</td>
                </tr>
            `).join('');
            console.log("[DataSeriesTable] Data series successfully rendered in table");
        })
        .catch(error => {
            console.error("Fetch request failed: ", error);
        });
};

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
    public function downloadVisibleCSV(Request $request)
    {
        Log::info('DownloadController: downloadVisibleCSV method called');

        // Decode the JSON data received from the frontend
        $data = json_decode($request->getContent(), true);
        $products = $data['products'];
        $dataSeries = $data['dataSeries'];

        // Create a CSV file in memory
        $file = fopen('php://temp', 'w+');

        // Set the headers for proper UTF-8 encoding
        fputs($file, $bom =( chr(0xEF) . chr(0xBB) . chr(0xBF) ));

        // Manually add headers for the products table
        $productHeaders = ['Produto', 'Nome', 'Frequência', 'Primeira Data'];
        fputcsv($file, $productHeaders);

        // Add product rows
        foreach ($products as $product) {
            fputcsv($file, $product);
        }

        // Add a separator line and a blank line for spacing
        fputcsv($file, array_fill(0, count($productHeaders), ''));
        fputcsv($file, array_fill(0, count($productHeaders), ''));

        // Manually add headers for the data series table
        $dataSeriesHeaders = ['Cod', 'data', 'ult', 'mini', 'maxi', 'abe', 'volumes', 'med', 'aju'];
        fputcsv($file, $dataSeriesHeaders);

        // Add data series rows
        foreach ($dataSeries as $series) {
            fputcsv($file, $series);
        }

        // Reset the file pointer to the start
        rewind($file);

        // Build the CSV from the file pointer
        $csv = stream_get_contents($file);
        fclose($file);

        // Create a response and add headers for file download
        $response = response($csv)
            ->header('Content-Type', 'text/csv; charset=UTF-8')
            ->header('Content-Disposition', 'attachment; filename="api-preview-data.csv"')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');

        return $response;
    }

    public function downloadPDF(Request $request)
    {
        Log::info('DownloadController: downloadPDF method called');

        $data = json_decode($request->getContent(), true);

        if($data === null) {
            Log::error('DownloadController: downloadPDF method called without proper data');
            abort(400, "Bad Request: No data provided");
        }

        $products = $data['products'] ?? [];
        $dataSeries = $data['dataSeries'] ?? [];

        Log::info('Products:', $products);
        Log::info('Data Series:', $dataSeries);

        $pdf = PDF::loadView('pdf_view', ['products' => $products, 'dataSeries' => $dataSeries]);

        return $pdf->download('visible-data.pdf');
    }
}

```
## public/js/DownloadButtons.js
```

function downloadPDF() {
    console.log("[DownloadButtons] Initiating PDF download");

    // Fetch products data from the products table
    const productsData = Array.from(document.querySelectorAll('#products-table tbody tr')).map(row => {
        return Array.from(row.querySelectorAll('td:not(:first-child)')).map(cell => cell.textContent.trim());
    });

    // Fetch data series from the data series table
    const dataSeriesData = Array.from(document.querySelectorAll('#data-series-table tbody tr')).map(row => {
        return Array.from(row.querySelectorAll('td')).map(cell => cell.textContent.trim());
    });

    // Prepare the data payload for the request
    const data = { products: productsData, dataSeries: dataSeriesData };

    // Make a POST request to the server to generate and download the PDF
    fetch('/download/visible-pdf', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(data)
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok ' + response.statusText);
        }
        return response.blob();
    })
    .then(blob => {
        // Create a blob URL from the response
        const url = window.URL.createObjectURL(blob);

        // Create a link element, use it to download the file, and remove it
        const a = document.createElement('a');
        a.style.display = 'none';
        a.href = url;
        a.download = 'visible-data.pdf';
        document.body.appendChild(a);
        a.click();

        // Clean up by revoking the object URL and removing the link element
        window.URL.revokeObjectURL(url);
        document.body.removeChild(a);
    })
    .catch((error) => console.error('Error:', error));
}




function downloadVisibleCSV() {
    // Gather data from the products table
    const productsData = Array.from(document.querySelectorAll('#products-table tbody tr')).map(row => {
        return Array.from(row.querySelectorAll('td:not(:first-child)')).map(cell => cell.textContent.trim());
    });

    // Gather data from the data series table
    const dataSeriesData = Array.from(document.querySelectorAll('#data-series-table tbody tr')).map(row => {
        return Array.from(row.querySelectorAll('td')).map(cell => cell.textContent.trim());
    });

    // Prepare the data to send
    const data = { products: productsData, dataSeries: dataSeriesData };

    // Send the data to the server using fetch API
    fetch('/download/visible-csv', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(data)
    })
    .then(response => response.blob())
    .then(blob => {
        // Create a link element, use it to download the file and remove it
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.style.display = 'none';
        a.href = url;
        a.download = 'visible-data.csv';
        document.body.appendChild(a);
        a.click();
        window.URL.revokeObjectURL(url);
        document.body.removeChild(a);
    })
    .catch((error) => console.error('Error:', error));
}

// Event listener for the download button
document.addEventListener('DOMContentLoaded', function () {
    const csvVisibleBtn = document.getElementById('download-csv-btn'); // Corrected to match the button's ID
    if (csvVisibleBtn) {
        csvVisibleBtn.addEventListener('click', downloadVisibleCSV);
    }
});


// Corrected Event listeners for download buttons
document.addEventListener('DOMContentLoaded', function () {
    console.log("[DownloadButtons] Setting up event listeners for download buttons");

    // This should match the ID of the button for downloading the visible CSV
    const csvVisibleBtn = document.getElementById('download-csv-btn');
    if (csvVisibleBtn) {
        csvVisibleBtn.addEventListener('click', downloadVisibleCSV);
    }

    // Leave the rest of your code as is for the PDF download
    const pdfBtn = document.getElementById('download-pdf-btn');
    if (pdfBtn) {
        pdfBtn.addEventListener('click', downloadPDF);
    }
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
    /**
     * Display the first 10 entries of the data series for a given product code.
     *
     * @param string $productCode
     * @return \Illuminate\Http\Response
     */
    public function show($productCode)
    {
        Log::info("Entering DataSeriesController::show with productCode: {$productCode}");
        try {
            // Fetch only the first 10 data series entries based on the product code
            $dataSeries = DataSeries::where('cod', $productCode)->take(10)->get();

            Log::info('First 10 DataSeries entries retrieved.');
            return response()->json($dataSeries);
        } catch (\Exception $e) {
            Log::error("Error in DataSeriesController::show: " . $e->getMessage());
            return response()->json(['error' => 'Server Error'], 500);
        }
    }
}

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

    // Reset selected product and clear DataSeries view if products list changes
    if (selectedProductCode !== null) {
        selectedProductCode = null;
        clearDataSeriesView(); // Function to clear DataSeries view
    }

    // Clear the table before populating new data
    tableBody.innerHTML = '';

    // Only populate if there are products
    if (products.length > 0) {
        tableBody.innerHTML = products.map(product => {
            // Convert the frequency code to the corresponding word
            const freqWord = freqToWord[product.freq] || product.freq;

            return `
                <tr>
                   <td><input type="radio" name="productSelect" value="${product['Código_Produto']}" onchange="selectProduct('${product['Código_Produto']}')"></td>
                   <td>${product.Classificação}</td>
                   <td>${product.longo}</td>
                   <td>${freqWord}</td>
                   <td>${product.alterado}</td>
                </tr>
            `;
        }).join('');
        console.log("[ProductsTable] Products table populated with products.");
    } else {
        // Show a message or an empty state if there are no products
        tableBody.innerHTML = `<tr><td colspan="5">No products found.</td></tr>`;
        console.log("[ProductsTable] No products found message displayed.");
    }

    window.loadedProducts = products;
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
    if (selectedProductCode !== productCode) {
        selectedProductCode = productCode;
        window.loadDataSeries(productCode);
    } else {
        selectedProductCode = null;
        clearDataSeriesView(); // Clear DataSeries if the same product is unselected
    }
    updateSelectedProductName(); // Update the display of the selected product name
};


function clearDataSeriesView() {
    let dataSeriesBody = document.getElementById('data-series-body');
    if (dataSeriesBody) {
        dataSeriesBody.innerHTML = '';
    }
    console.log("[DataSeriesTable] Data series view cleared.");
}

function updateSelectedProductName() {
    let productNameDisplay = document.getElementById('selected-product-name');
    if (productNameDisplay) {
        if (selectedProductCode) {
            // Find the selected product from the loaded products array
            const selectedProduct = window.loadedProducts.find(product => product['Código_Produto'] === selectedProductCode);
            if (selectedProduct) {
                productNameDisplay.textContent = `DataSeries for: ${selectedProduct.longo}`;
            }
        } else {
            productNameDisplay.textContent = 'Por favor selecione um produto na tabela acima';
        }
    }
}

document.addEventListener('DOMContentLoaded', function () {
    console.log("[ProductsTable] Page loaded - Starting to load products.");
    loadProducts();
    updateSelectedProductName(); // Ensure placeholder is displayed initially
});


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
## resources/views/partials/products-table.blade.php
```
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
<div id="selected-product-name" class="selected-product-display"></div>

</div>

```
## app/Http/Controllers/ProductController.php
```
<?php

// ProductController.php

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

            // Adjusted logic for 'proprietario' filter conversion
            if ($request->filled('proprietario')) {
                if ($request->input('proprietario') === 'Sim') {
                    $query->where('bolsa', 2);
                    Log::info("Applying filter: bolsa with value: 2");
                } elseif ($request->input('proprietario') === 'Não') {
                    $query->where('bolsa', '<>', 2);
                    Log::info("Applying filter: bolsa with values not equal to 2");
                }
            }

            // Handle other filters
            $filters = $request->only(['Classificação', 'subproduto', 'local', 'freq']);
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
