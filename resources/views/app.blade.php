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
  <script>console.log('[app.blade.php] Main application view loaded');</script>
</body>
</html>
