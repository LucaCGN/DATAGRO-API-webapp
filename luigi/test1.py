import csv
import os
from collections import defaultdict

# File paths
ipv_list_path = 'C:/Users/lnonino/OneDrive - DATAGRO/Documentos/GitHub/DataAgro/Laravel-API-WebAPP/datagro-webapp/luigi/data/usda/processed/ipv_list.csv'
master_table_path = 'C:/Users/lnonino/OneDrive - DATAGRO/Documentos/GitHub/DataAgro/Laravel-API-WebAPP/datagro-webapp/luigi/data/usda/processed/consolidated_master_table.csv'
output_base_path = 'C:/Users/lnonino/OneDrive - DATAGRO/Documentos/GitHub/DataAgro/Laravel-API-WebAPP/datagro-webapp/luigi/ipv_files/usda'

# Ensure output directory exists
if not os.path.exists(output_base_path):
    os.makedirs(output_base_path)

# Function to standardize commodity code formatting
def standardize_commodity_code(code):
    if '.' in code:
        return code.split('.')[0]
    return code

# Function to convert MarketYear to yyyy-mm-dd format
def convert_to_date_format(year):
    return f"{year}-01-01"

# Grouping attributes and codes by country and commodity code
attributes_by_country_commodity = defaultdict(list)
codes_by_country_commodity = {}
with open(ipv_list_path, mode='r') as ipv_list_file:
    ipv_list_reader = csv.reader(ipv_list_file, delimiter=';')
    next(ipv_list_reader)  # Skip header

    for row in ipv_list_reader:
        if len(row) < 4:
            continue
        country, commodity_code, attribute, code = row
        commodity_code = standardize_commodity_code(commodity_code.strip())
        key = (country, commodity_code)
        if attribute not in attributes_by_country_commodity[key]:
            attributes_by_country_commodity[key].append(attribute)
        codes_by_country_commodity[key] = code  # Assuming one code per country-commodity combination

# Read the master table
with open(master_table_path, mode='r') as file:
    master_reader = csv.DictReader(file)
    master_data = list(master_reader)

# Process each country-commodity combination
for (country, commodity_code), attributes in attributes_by_country_commodity.items():
    code = codes_by_country_commodity[(country, commodity_code)]
    output_file_path = os.path.join(output_base_path, f'{code}.csv')
    with open(output_file_path, mode='w', newline='') as file:
        writer = csv.writer(file)
        headers = ['CÓDIGO DATAGRO', 'MarketYear'] + attributes
        writer.writerow(headers)

        filtered_data = sorted(
            [r for r in master_data if r['Country'] == country and standardize_commodity_code(r['CommodityCode']) == commodity_code],
            key=lambda x: x['MarketYear']
        )

        for year in set(r['MarketYear'] for r in filtered_data):
            row_data = [code, convert_to_date_format(year)] + [next((r[attribute] for r in filtered_data if r['MarketYear'] == year and attribute in r), '') for attribute in attributes]
            writer.writerow(row_data)

        print(f"File saved for {code} at {output_file_path}")
