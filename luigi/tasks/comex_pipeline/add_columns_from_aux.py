# .\comex_pipeline\add_columns_from_aux.py
import luigi
import pandas as pd
import datetime
import os
from tasks.comex_pipeline.apply_filters import FilterData  # Importing FilterData task

class MergeAuxiliaryData(luigi.Task):
    data_type = luigi.Parameter()  # 'EXP' or 'IMP'

    def requires(self):
        return FilterData(data_type=self.data_type)

    def output(self):
        current_year = datetime.datetime.now().year
        return luigi.LocalTarget(f'data/comex/merged/{self.data_type}_merged_{current_year}.csv')

    def run(self):
        # Ensure the output directory exists
        os.makedirs(os.path.dirname(self.output().path), exist_ok=True)

        # Read the main data
        df = pd.read_csv(self.input().path, delimiter=',', encoding='utf-8')
        # Merge with aux_1
        aux1_df = pd.read_csv('data/aux_tables/aux_1.csv', index_col='CO_NCM')
        df = pd.merge(df, aux1_df[['NO_NCM_POR']], on='CO_NCM', how='left')

        # Merge with aux_6
        aux6_df = pd.read_csv('data/aux_tables/aux_6.csv')
        df = pd.merge(df, aux6_df[['CO_UNID', 'SG_UNID']], on='CO_UNID', how='left')

        # Merge with aux_10
        aux10_df = pd.read_csv('data/aux_tables/aux_10.csv', index_col='CO_PAIS')
        df = pd.merge(df, aux10_df[['NO_PAIS']], on='CO_PAIS', how='left')

        # Merge with aux_12
        aux12_df = pd.read_csv('data/aux_tables/aux_12.csv')
        df = pd.merge(df, aux12_df[['SG_UF', 'NO_UF', 'NO_REGIAO']], left_on='SG_UF_NCM', right_on='SG_UF', how='left')
        df.drop('SG_UF', axis=1, inplace=True)

        # Merge with aux_14
        aux14_df = pd.read_csv('data/aux_tables/aux_14.csv', index_col='CO_VIA')
        df = pd.merge(df, aux14_df[['NO_VIA']], on='CO_VIA', how='left')

        # Merge with aux_15
        aux15_df = pd.read_csv('data/aux_tables/aux_15.csv', index_col='CO_URF')
        df = pd.merge(df, aux15_df[['NO_URF']], on='CO_URF', how='left')

        # Saving the final merged DataFrame
        df.to_csv(self.output().path, index=False)

if __name__ == '__main__':
    luigi.build([MergeAuxiliaryData(data_type='EXP'), MergeAuxiliaryData(data_type='IMP')], local_scheduler=True)