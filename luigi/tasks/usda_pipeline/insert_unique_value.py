import pandas as pd
import luigi
from luigi import LocalTarget
from tasks.usda_pipeline.consolidate_csvs import ConsolidateFetchedData

class InsertUniqueData(luigi.Task):
    consolidated_file = luigi.Parameter(default='data/usda/processed/consolidated_data.csv')
    master_table_file = luigi.Parameter(default='data/usda/processed/master_table.csv')

    def requires(self):
        return ConsolidateFetchedData()

    def output(self):
        return LocalTarget(self.master_table_file)

    def complete(self):
     # Always return False to indicate that the task is never complete
        return False

    def run(self):
        # Load the consolidated data
        consolidated_df = pd.read_csv(self.input().path)

        # Check if the master table exists, load it or create an empty DataFrame with the same columns
        if self.output().exists():
            master_df = pd.read_csv(self.output().path)
        else:
            master_df = pd.DataFrame()

        # Standardize the column names by removing spaces
        consolidated_df.columns = [col.replace(' ', '') for col in consolidated_df.columns]
        master_df.columns = [col.replace(' ', '') for col in master_df.columns]

        # Convert attribute names to match master table's column names
        consolidated_df['Attribute'] = consolidated_df['Attribute'].str.replace(' ', '')

        # Create a unique identifier for each row in both dataframes
        consolidated_df['UniqueID'] = consolidated_df['Country'] + '_' + consolidated_df['MarketYear'].astype(str) + '_' + consolidated_df['CalendarYear'].astype(str) + '_' + consolidated_df['Month'].astype(str)
        master_df['UniqueID'] = master_df['Country'] + '_' + master_df['MarketYear'].astype(str) + '_' + master_df['CalendarYear'].astype(str) + '_' + master_df['Month'].astype(str)

        # Identify new unique rows to add to the master table
        new_unique_ids = set(consolidated_df['UniqueID']) - set(master_df.get('UniqueID', []))

        # Prepare data for new rows
        new_rows = []
        for unique_id in new_unique_ids:
            country, market_year, calendar_year, month = unique_id.split('_')
            new_row = {'Country': country, 'MarketYear': market_year, 'CalendarYear': calendar_year, 'Month': month}
            # Initialize the rest of the columns with None
            for col in master_df.columns.difference(['Country', 'MarketYear', 'CalendarYear', 'Month']):
                new_row[col] = None

            # Fill the data from the consolidated DataFrame
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
        master_df.to_csv(self.output().path, index=False)
