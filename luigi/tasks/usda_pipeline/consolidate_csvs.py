# .\usda_pipeline\consolidate_csvs.py
import luigi
import os
import pandas as pd
import json
from tasks.usda_pipeline.fetch_current_year_data import FetchUSDAData

class ConsolidateFetchedData(luigi.Task):
    data_dir = 'data/usda/raw'
    consolidated_file = 'data/usda/processed/consolidated_data.csv'

    def requires(self):
        # This task depends on FetchUSDAData to be completed.
        return FetchUSDAData()

    def output(self):
        return luigi.LocalTarget(self.consolidated_file)

    def run(self):
        all_data = []
        for file_name in os.listdir(self.data_dir):
            file_path = os.path.join(self.data_dir, file_name)
            try:
                with open(file_path, 'r') as file:
                    data = json.load(file)
                    # Process and structure the JSON data
                    for entry in data:
                        processed_entry = {
                            'Country': entry.get('CountryName', '').strip(),  # Added strip() here
                            'MarketYear': entry.get('MarketYear', ''),
                            'CalendarYear': entry.get('CalendarYear', ''),
                            'Month': entry.get('Month', ''),
                            'Attribute': entry.get('AttributeDescription', ''),
                            'Value': entry.get('Value', ''),
                            'CommodityCode': file_name.split('_')[0]
                        }
                        all_data.append(processed_entry)

            except json.JSONDecodeError as e:
                print(f"Error loading JSON from {file_path}: {e}")
            except Exception as e:
                print(f"Unexpected error with file {file_path}: {e}")

        # Create DataFrame and save to CSV
        df = pd.DataFrame(all_data)
        df.to_csv(self.output().path, index=False)


if __name__ == '__main__':
    luigi.run()
