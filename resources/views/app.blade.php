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
