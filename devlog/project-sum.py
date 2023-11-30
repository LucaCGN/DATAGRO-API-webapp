
import os

# Define the base path of your Laravel project
base_path = 'C:/Users/lnonino/OneDrive - DATAGRO/Documentos/GitHub/DataAgro/Laravel-API-WebAPP/datagro-webapp'

file_paths = [
    'public/js/DataSeriesTable.js',
    'public/js/DownloadButtons.js',
    'public/js/DropdownFilter.js',
    'public/js/ProductsTable.js',
    'resources/views/partials/data-series-table.blade.php',
    'resources/views/partials/download-buttons.blade.php',
    'resources/views/partials/dropdown-filter.blade.php',
    'resources/views/partials/products-table.blade.php',
    'resources/views/app.blade.php',
    'public/css/app.css',
    'app/Models/DataSeries.php',
    'app/Models/ExtendedProductList.php',
    'app/Console/Commands/FetchDatagroData.php',
    'routes/web.php',
    'app/Http/Controllers/DownloadController.php',
    'app/Http/Controllers/ProductController.php',
    'app/Http/Controllers/DataSeriesController.php',
    'app/Http/Controllers/Controller.php',
    'app/Http/Controllers/FilterController.php',
    'app/Http/Controllers/LoginController.php',
    'resources/views/auth/login.blade.php'
]

# Define preset groups of files
file_groups = {
    'controllers': [
        'app/Http/Controllers/DownloadController.php',
        'app/Http/Controllers/ProductController.php',
        'app/Http/Controllers/DataSeriesController.php',
        'app/Http/Controllers/Controller.php',
        'app/Http/Controllers/FilterController.php'
    ],
    'resources': [
        'resources/views/partials/data-series-table.blade.php',
        'resources/views/partials/download-buttons.blade.php',
        'resources/views/partials/dropdown-filter.blade.php',
        'resources/views/partials/products-table.blade.php',
        'resources/views/app.blade.php',
    ],
    'routes': [
        'routes/web.php',
    ],
    'public': [
        'public/js/DataSeriesTable.js',
        'public/js/DownloadButtons.js',
        'public/js/DropdownFilter.js',
        'public/js/ProductsTable.js',
        'public/css/app.css',
    ],
    'models': [
        'app/Models/DataSeries.php',
        'app/Models/ExtendedProductList.php',
    ],
    'backend': [
        'database/seeders/ExtendedProductListTableSeeder.php',
        'database/migrations/2023_03_01_120001_create_data_series_table.php',
        'database/migrations/2023_03_01_120000_create_extended_product_list_table.php',
        'app/Console/Commands/FetchDatagroData.php',
    ],
    'products-table': [
        'app/Models/ExtendedProductList.php',  
        'resources/views/partials/products-table.blade.php',
        'app/Http/Controllers/ProductController.php',
        'public/js/ProductsTable.js'
    ],
    'dropdown-filter': [
        'public/js/DropdownFilter.js',
        'app/Models/ExtendedProductList.php',
        'resources/views/partials/dropdown-filter.blade.php',
        'app\Http\Controllers\FilterController.php'
    ],
    'templates-css': [ 
        'resources/views/partials/data-series-table.blade.php',
        'resources/views/partials/download-buttons.blade.php',
        'resources/views/partials/dropdown-filter.blade.php',
        'resources/views/partials/products-table.blade.php',
        'resources/views/auth/login.blade.php',
        'public/css/app.css',
    ],
    
    'all': file_paths  # The 'all' group contains all the files
}

# Ask the user which group to process
group_names = input("Which group(s) do you want to process? Separate multiple groups with ';': ")

# Split the input into individual group names
group_names = group_names.split(';')

# Combine the files from the selected groups and remove duplicates
file_paths = []
for group_name in group_names:
    group_files = file_groups.get(group_name.strip())
    if group_files is not None:
        file_paths.extend(group_files)
file_paths = list(set(file_paths))  # Remove duplicates

if not file_paths:
    print(f"No such group: {group_names}")
    exit(1)

# Path to the output Markdown file
output_md_file = 'consolidated_documentation.md'

# Create and open the output Markdown file
with open(output_md_file, 'w') as md_file:
    print("Starting documentation consolidation...")
    for relative_path in file_paths:
        # Construct the full path
        full_path = os.path.join(base_path, relative_path)
        print(f"Processing file: {full_path}")

        # Check if the file exists
        if not os.path.exists(full_path):
            print(f"File not found: {full_path}")
            continue

        # Read the file content
        with open(full_path, 'r') as file:
            content = file.read()

        # Write the file path and content to the Markdown file
        md_file.write(f"## {relative_path}\n```\n{content}\n```\n")
    print("Documentation consolidation completed.")


