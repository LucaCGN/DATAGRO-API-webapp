import os
import pandas as pd

# Define the directory containing the CSV files
directory = "C:/Users/lnonino/OneDrive - DATAGRO/Documentos/GitHub/DataAgro/Laravel-API-WebAPP/datagro-webapp/luigi/ipv_files/usda"

# Define the path to the header replacements file
header_replacements_path = "C:/Users/lnonino/OneDrive - DATAGRO/Documentos/GitHub/DataAgro/Laravel-API-WebAPP/datagro-webapp/luigi/ipv_files/ipv_headers.csv"

# Load the header replacements as a dictionary, strip trailing spaces from the keys
header_replacements = pd.read_csv(header_replacements_path, sep=";", header=None).set_index(0).squeeze()
header_replacements.index = header_replacements.index.str.strip()
header_replacements = header_replacements.to_dict()

# Initialize a list to keep track of headers without matches
unmatched_headers = []

# Process each CSV file in the directory
for filename in os.listdir(directory):
    if filename.endswith(".csv"):
        file_path = os.path.join(directory, filename)

        # Load the CSV file
        df = pd.read_csv(file_path)

        # Remove trailing spaces from headers
        df.columns = df.columns.str.strip()

        # Replace headers using a dictionary comprehension
        df.rename(columns={col: header_replacements.get(col, col) for col in df.columns}, inplace=True)

        # Check for unmatched headers (set difference)
        unmatched = set(df.columns) - set(header_replacements.values())
        unmatched_headers.extend(unmatched)

        # Save the modified CSV file (consider backup first)
        df.to_csv(file_path, index=False)

# Remove duplicates from the unmatched headers list
unmatched_headers = list(set(unmatched_headers))

# Print the unmatched headers
print("Unmatched Headers:")
for header in unmatched_headers:
    print(header)
