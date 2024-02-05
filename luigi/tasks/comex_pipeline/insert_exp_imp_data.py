import os
import pandas as pd
import luigi
from tasks.comex_pipeline.add_columns_from_aux import MergeAuxiliaryData

class UpdateMasterTable(luigi.Task):
    data_type = luigi.Parameter()  # 'EXP' or 'IMP'

    def requires(self):
        return MergeAuxiliaryData(data_type=self.data_type)

    def output(self):
        return luigi.LocalTarget(f'data/comex/processed/{self.data_type}_master_table.csv')

    def run(self):
        merged_data_path = self.input().path
        master_table_path = self.output().path

        # Ensure the output directory exists
        os.makedirs(os.path.dirname(master_table_path), exist_ok=True)

        # Read the merged data
        merged_data = pd.read_csv(merged_data_path)

        # Check if the master table exists
        if os.path.exists(master_table_path):
            master_table = pd.read_csv(master_table_path)
        else:
            # If not, create an empty DataFrame with the same columns as merged_data
            master_table = pd.DataFrame(columns=merged_data.columns)

        # Append data
        updated_master_table = pd.concat([master_table, merged_data], ignore_index=True)
        updated_master_table.to_csv(master_table_path, index=False)
        print(f"{self.data_type} data updated successfully in the master table.")
