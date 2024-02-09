import pandas as pd
import os

# Define the paths to the files
consolidated_file = 'C:/Users/lnonino/OneDrive - DATAGRO/Documentos/GitHub/DataAgro/Laravel-API-WebAPP/datagro-webapp/luigi/data/usda/processed/consolidated_data.csv'
master_table_file = 'C:/Users/lnonino/OneDrive - DATAGRO/Documentos/GitHub/DataAgro/Laravel-API-WebAPP/datagro-webapp/luigi/data/usda/processed/master_table.csv'


# Function to load data from a CSV file
def load_data(file_path):
    if os.path.exists(file_path):
        return pd.read_csv(file_path)
    else:
        raise FileNotFoundError(f"File not found: {file_path}")

# Load the consolidated data and master table
try:
    consolidated_df = load_data(consolidated_file)
    master_df = load_data(master_table_file)
except FileNotFoundError as e:
    print(e)
    exit()

# Standardize the column names for consistency
consolidated_df.columns = [col.strip().replace(' ', '') for col in consolidated_df.columns]
master_df.columns = [col.strip().replace(' ', '') for col in master_df.columns]

# Convert attribute names to match master table's column names
consolidated_df['Attribute'] = consolidated_df['Attribute'].str.replace(' ', '')

# Create a unique identifier for each row in both dataframes
consolidated_df['UniqueID'] = consolidated_df['Country'] + '_' + consolidated_df['MarketYear'].astype(str) + '_' + consolidated_df['CalendarYear'].astype(str) + '_' + consolidated_df['Month'].astype(str)
master_df['UniqueID'] = master_df['Country'] + '_' + master_df['MarketYear'].astype(str) + '_' + master_df['CalendarYear'].astype(str) + '_' + master_df['Month'].astype(str)

# Identify new unique rows to add to the master table
new_unique_ids = set(consolidated_df['UniqueID']) - set(master_df['UniqueID'])

# Prepare data for new rows
new_rows = []
for unique_id in new_unique_ids:
    # Initialize a new row with identifying columns filled and others as None
    country, market_year, calendar_year, month = unique_id.split('_')
    new_row = {'Country': country, 'MarketYear': market_year, 'CalendarYear': calendar_year, 'Month': month}
    # Initialize the rest of the columns with None
    for col in master_df.columns.difference(['Country', 'MarketYear', 'CalendarYear', 'Month']):
        new_row[col] = None

    # For the current unique_id, fill the data from the consolidated DataFrame
    consolidated_rows = consolidated_df[consolidated_df['UniqueID'] == unique_id]
    for _, cons_row in consolidated_rows.iterrows():
        attribute = cons_row['Attribute']
        value = cons_row['Value']
        if attribute in new_row:
            new_row[attribute] = value

    new_rows.append(new_row)

# Append new rows to master_df
new_rows_df = pd.DataFrame(new_rows, columns=master_df.columns)
master_df = pd.concat([master_df, new_rows_df], ignore_index=True)

# Remove the UniqueID column as it was only needed for matching purposes
master_df.drop(columns=['UniqueID'], inplace=True)

# Save the updated master table
try:
    master_df.to_csv(master_table_file, index=False)
    print("Master table updated successfully.")
except Exception as e:
    print(f"Error saving the master table: {e}")
