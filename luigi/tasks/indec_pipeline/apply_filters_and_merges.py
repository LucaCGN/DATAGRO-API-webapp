import luigi
import pandas as pd
import os
from tasks.indec_pipeline.fetch_monthly_data import FetchMonthlyData

# from datetime import datetime  # Commented out for dynamic logic

class ApplyFiltersAndMerges(luigi.Task):
    """
    Task to filter the raw data based on the current month and merge it with auxiliary data.
    """

    def requires(self):
        return FetchMonthlyData()

    def output(self):
        # Dynamic logic (commented out):
        # current_year = datetime.now().year
        # current_month = datetime.now().month
        # Hardcoded values for testing:
        current_year = 2023
        current_month = 12

        return luigi.LocalTarget(f'data/indec/raw/EXP_{current_year}_{current_month}.csv')

    def run(self):
        # Load raw data
        raw_data_path = self.input().path
        df = pd.read_csv(raw_data_path, delimiter=';', encoding='latin1')

        # Filter data for the current month (commented out dynamic part):
        # current_month = datetime.now().month
        # Hardcoded current month for testing:
        current_month = 12
        df = df[df['MES'] == current_month]

        # Load auxiliary tables
        aux_ncm_path = 'data/aux_tables/aux_1.csv'
        aux_country_path = 'data/aux_tables/aux_17.csv'

        aux_ncm = pd.read_csv(aux_ncm_path, sep=',', encoding='latin1')
        # Adjusted delimiter for aux_country to match file format:
        aux_country = pd.read_csv(aux_country_path, sep=',', encoding='latin1')

        # Merge with auxiliary tables
        df = df.merge(aux_ncm[['CO_NCM', 'NO_NCM_POR']], on='CO_NCM', how='left')
        df = df.merge(aux_country[['ARG_PAIS_CO', 'ARG_PAIS_NOME']], on='ARG_PAIS_CO', how='left')

        # Handle missing values
        df['NO_NCM_POR'].fillna('Unknown', inplace=True)
        df['ARG_PAIS_NOME'].fillna('Unknown', inplace=True)

        # Save the enriched data
        df.to_csv(self.output().path, index=False, sep=',')

if __name__ == "__main__":
    luigi.run(['ApplyFiltersAndMerges', '--local-scheduler'])
