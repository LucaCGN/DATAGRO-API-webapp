<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Filterable Product Table</title>
    <!-- Link to your CSS file -->
    <link href="path_to_your_css_file.css" rel="stylesheet" type="text/css">
</head>
<body>
    <div id="filter-container">
        <!-- Container for the dropdown filters -->
        <div class="filters-container">
            <select id="Classificação-select" class="filter-dropdown">
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
            <button id="reset-filters-btn">Limpar Filtros</button>
        </div>
        <!-- Search box -->
        <input type="text" id="search-box" placeholder="Pesquisar..." oninput="applySearchFilter()">
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
