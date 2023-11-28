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
        <!-- Include the updated Blade templates for each component -->
        @include('partials.dropdown-filter')
        @include('partials.products-table')
        @include('partials.data-series-table')
        @include('partials.download-buttons')
    </main>

    <footer>
        <!-- Footer content goes here -->
    </footer>

    <!-- Include the JavaScript files -->
    <script src="{{ asset('js/dropdown-filter.js') }}"></script>
    <script src="{{ asset('js/products-table.js') }}"></script>
    <script src="{{ asset('js/data-series.js') }}"></script>
    <script src="{{ asset('js/download-buttons.js') }}"></script>
</body>
</html>
