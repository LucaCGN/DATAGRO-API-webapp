import os
import pandas as pd
import luigi
from datetime import datetime, timedelta
from tasks.comex_pipeline.add_columns_from_aux import MergeAuxiliaryData

class UpdateMasterTable(luigi.Task):
    data_type = luigi.Parameter()  # 'EXP' or 'IMP'

    def requires(self):
        return MergeAuxiliaryData(data_type=self.data_type)

    def output(self):
        file_name = 'exports' if self.data_type == 'EXP' else 'imports'
        return luigi.LocalTarget(f'data/comex/processed/{file_name}_master_table.csv')

    def complete(self):
        # Check if the output file exists
        if not self.output().exists():
            return False

        # Get the modification time of the file
        modification_time = datetime.fromtimestamp(os.path.getmtime(self.output().path))

        # If the file has been updated in the last 5 minutes, consider the task complete
        if datetime.now() - modification_time < timedelta(minutes=5):
            return True

        # If the file is older than 5 minutes, the task is not complete
        return False

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
