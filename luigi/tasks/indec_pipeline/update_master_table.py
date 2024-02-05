import luigi
import pandas as pd
import os
from datetime import datetime
from tasks.indec_pipeline.apply_filters_and_merges import ApplyFiltersAndMerges  # Importing ApplyFiltersAndMerges task


class UpdateMasterTable(luigi.Task):
    """
    Task to update the master export table with new monthly data.
    """

    def requires(self):
        return ApplyFiltersAndMerges()

    def output(self):
        return luigi.LocalTarget('data/indec/processed/master_exp_table.csv')

    def run(self):
        # Load enhanced monthly data
        monthly_data_path = self.input().path
        monthly_data = pd.read_csv(monthly_data_path, delimiter=';', encoding='latin1')

        # Load the existing master table
        master_table_path = self.output().path
        if os.path.exists(master_table_path):
            master_table = pd.read_csv(master_table_path, delimiter=';', encoding='latin1')
        else:
            master_table = pd.DataFrame(columns=monthly_data.columns)

        # Append new data and remove duplicates
        updated_master_table = pd.concat([master_table, monthly_data]).drop_duplicates()

        # Save the updated master table
        updated_master_table.to_csv(master_table_path, index=False, sep=';')

if __name__ == "__main__":
    luigi.run(['UpdateMasterTable', '--local-scheduler'])
