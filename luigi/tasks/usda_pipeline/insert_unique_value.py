# .\usda_pipeline\insert_unique_data.py
import pandas as pd
import luigi
import os
from tasks.usda_pipeline.consolidate_csvs import ConsolidateFetchedData

class InsertUniqueData(luigi.Task):
    consolidated_file = 'data/usda/processed/consolidated_data.csv'
    master_table_file = 'data/usda/processed/master_table.csv'

    def requires(self):
        # This task depends on ConsolidateFetchedData to be completed.
        return ConsolidateFetchedData()

    def output(self):
        return luigi.LocalTarget(self.master_table_file)

    def run(self):
        # Load the consolidated data and master table
        consolidated_df = pd.read_csv(self.consolidated_file)
        master_df = pd.read_csv(self.master_table_file) if os.path.exists(self.master_table_file) else pd.DataFrame()

        # Identify the unique rows
        unique_rows = consolidated_df[~consolidated_df.set_index(['Country', 'MarketYear', 'CalendarYear', 'Month', 'CommodityCode']).index.isin(master_df.set_index(['Country', 'MarketYear', 'CalendarYear', 'Month', 'CommodityCode']).index)]

        # Append the unique rows to the master table
        updated_master_df = pd.concat([master_df, unique_rows], ignore_index=True)

        # Save the updated master table
        updated_master_df.to_csv(self.output().path, index=False)

if __name__ == '__main__':
    luigi.run()
