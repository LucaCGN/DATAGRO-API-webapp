import csv
import os

# File paths
headers_adjustment_path = 'C:/Users/lnonino/OneDrive - DATAGRO/Documentos/GitHub/DataAgro/Laravel-API-WebAPP/datagro-webapp/luigi/data/usda/processed/headers_adjustment.csv'
output_directory_path = 'C:/Users/lnonino/OneDrive - DATAGRO/Documentos/GitHub/DataAgro/Laravel-API-WebAPP/datagro-webapp/luigi/ipv_files/usda'

# Read header adjustments
header_replacements = {}
with open(headers_adjustment_path, mode='r') as file:
    reader = csv.reader(file, delimiter=';')
    next(reader)  # Skip header
    for row in reader:
        old_header, new_header = row
        header_replacements[old_header.replace(',', '')] = new_header

# Function to replace headers based on the header_replacements dictionary
def replace_headers(headers):
    return [header_replacements.get(header, header) for header in headers]

# Process each CSV file in the output directory
for filename in os.listdir(output_directory_path):
    if filename.endswith('.csv') and filename != '.gitkeep':
        file_path = os.path.join(output_directory_path, filename)

        # Read the current CSV file
        with open(file_path, mode='r', newline='') as file:
            reader = csv.reader(file)
            headers = next(reader)
            data = list(reader)

        # Replace headers
        new_headers = replace_headers(headers)

        # Write the adjusted data back to the CSV file
        with open(file_path, mode='w', newline='') as file:
            writer = csv.writer(file)
            writer.writerow(new_headers)
            writer.writerows(data)

        print(f"Processed and updated headers for {filename}")
