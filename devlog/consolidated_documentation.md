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
    <!-- Pagination Controls populated by ProductsTable.js -->
</div>

```
## public/css/app.css
```
/* Main application styles */
body {
    font-family: 'Arial', sans-serif;
    color: #4f4f4f;
    background-color: #f8f8f8;
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
    border: 1px solid #ddd;
    padding: 8px;
    text-align: left;
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
}

@media screen and (max-width: 600px) {
    .filters-container,
    .download-buttons-container {
        flex-direction: column;
    }
}

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
                <th>med</th>
                <th>aju</th>
            </tr>
        </thead>
        <tbody id="data-series-body">
            <!-- Data populated by DataSeriesTable.js -->
        </tbody>
    </table>
</div>
<script>console.log('[data-series-table.blade.php] Data series table view loaded');</script>


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
    <header style="display: flex; justify-content: center; align-items: center; padding: 10px;">
        <h1>API Mercado Físico - SALES TOOL</h1>
        <img src="{{ asset('images/Logo - Quadrado.png') }}" alt="Datagro Logo" style="height: 50px;">
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
    <div class="footer-links">
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
