import os
import luigi
import pandas as pd
import datetime
from tasks.comex_pipeline.fetch_current_year_data import DownloadCurrentYearData

class FilterData(luigi.Task):
    data_type = luigi.Parameter()  # 'EXP' or 'IMP'
    ncm_codes_to_keep = [
        '10051000', '10059010', '10059090', '12010010', '12010090',
        '12011000', '12019000', '15071000', '15079011', '15079019',
        '15079090', '23040010', '23040090', '10011100', '10011090',
        '10011900', '10019010', '10019090', '10019100', '10019900',
        '11010010', '11010020'
    ]

    def requires(self):
        return DownloadCurrentYearData(data_type=self.data_type)

    def output(self):
        today = datetime.datetime.today()
        current_year = today.year if today.month > 1 else today.year - 1
        return luigi.LocalTarget(f'data/comex/filtered/{self.data_type}_filtered_{current_year}.csv')

    def run(self):
        today = datetime.datetime.today()
        last_month_end = datetime.datetime(today.year, today.month, 1) - datetime.timedelta(days=1)
        last_month, last_year = last_month_end.month, last_month_end.year if today.month > 1 else today.year - 1

        # Read the input file
        df = pd.read_csv(self.input().path, delimiter=';', quotechar='"', encoding='utf-8')

        df_filtered = df[(df['CO_NCM'].astype(str).isin(self.ncm_codes_to_keep)) &
                         (df['CO_ANO'] == last_year) &
                         (df['CO_MES'] == last_month)]

        df_filtered.to_csv(self.output().path, index=False)

if __name__ == '__main__':
    luigi.build([FilterData(data_type='EXP'), FilterData(data_type='IMP')], local_scheduler=True)
