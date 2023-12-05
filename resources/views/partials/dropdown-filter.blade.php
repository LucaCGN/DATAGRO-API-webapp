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
