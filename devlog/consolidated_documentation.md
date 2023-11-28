## public/js/DataSeriesTable.js
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

// Ensure the DOM is fully loaded before adding event listeners
document.addEventListener('DOMContentLoaded', function () {
    if (document.getElementById('download-csv-btn')) {
        document.getElementById('download-csv-btn').addEventListener('click', function() {
            console.log("Download CSV button clicked");
            downloadCSV();
        });
    }

    if (document.getElementById('download-pdf-btn')) {
        document.getElementById('download-pdf-btn').addEventListener('click', function() {
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
    const classification = document.getElementById('classification-select').value;
    const subproduct = document.getElementById('subproduct-select').value;
    const local = document.getElementById('local-select').value;

    console.log(`Filter parameters - Classification: ${classification}, Subproduct: ${subproduct}, Local: ${local}`);

    fetch(`/filter-products?classification=${classification}&subproduct=${subproduct}&local=${local}`)
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

// Function to update the products table
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

// Ensure the DOM is fully loaded before adding event listeners
document.addEventListener('DOMContentLoaded', function () {
    if (document.getElementById('classification-select')) {
        document.getElementById('classification-select').addEventListener('change', updateFilters);
    }

    if (document.getElementById('subproduct-select')) {
        document.getElementById('subproduct-select').addEventListener('change', updateFilters);
    }

    if (document.getElementById('local-select')) {
        document.getElementById('local-select').addEventListener('change', updateFilters);
    }
});

```

## public/js/ProductsTable.js
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

