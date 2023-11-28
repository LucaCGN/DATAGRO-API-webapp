import os

# Define the base path of your Laravel project
base_path = 'C:/Users/lnonino/OneDrive - DATAGRO/Documentos/GitHub/DataAgro/Laravel-API-WebAPP/datagro-webapp'

# List of relative paths of the files to be included in the document
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
#    'app/Models/DataSeries.php',
#    'app/Models/ExtendedProductList.php',
#    'app/Console/Commands/FetchDatagroData.php',
#    'database/wmigrations/2023_03_01_120001_create_data_series_table.php',
#    'database/migrations/2023_03_01_120000_create_extended_product_list_table.php',
]

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
        md_file.write(f"## {relative_path}\n```php\n{content}\n```\n\n")

print(f"Documentation consolidated into {output_md_file}")
