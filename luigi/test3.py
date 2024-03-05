import csv
import os

# File paths
output_directory_path = 'C:/Users/lnonino/OneDrive - DATAGRO/Documentos/GitHub/DataAgro/Laravel-API-WebAPP/datagro-webapp/luigi/ipv_files/usda'

# Process each CSV file in the output directory
for filename in os.listdir(output_directory_path):
    if filename.endswith('.csv') and filename != '.gitkeep':
        file_path = os.path.join(output_directory_path, filename)

        # Read the current CSV file with default encoding
        with open(file_path, mode='r', newline='') as file:
            reader = csv.reader(file)
            headers = next(reader)
            data = list(reader)

        # Override the first header with 'Code'
        headers[0] = 'Code'

        # Write the adjusted data back to the CSV file
        with open(file_path, mode='w', newline='') as file:
            writer = csv.writer(file)
            writer.writerow(headers)
            writer.writerows(data)

        print(f"Processed and updated the first header for {filename}")
