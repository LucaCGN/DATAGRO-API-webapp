import argparse
import luigi
from tasks.usda_pipeline.fetch_current_year_data import FetchUSDAData
from tasks.usda_pipeline.consolidate_csvs import ConsolidateFetchedData
from tasks.usda_pipeline.insert_unique_value import InsertUniqueData
from tasks.comex_pipeline.fetch_current_year_data import DownloadCurrentYearData
from tasks.comex_pipeline.apply_filters import FilterData
from tasks.comex_pipeline.add_columns_from_aux import MergeAuxiliaryData
from tasks.comex_pipeline.insert_exp_imp_data import UpdateMasterTable
from tasks.indec_pipeline.fetch_monthly_data import FetchMonthlyData
from tasks.indec_pipeline.apply_filters_and_merges import ApplyFiltersAndMerges
from tasks.indec_pipeline.update_master_table import UpdateMasterTable as UpdateINDECMasterTable

class USDAETLPipeline(luigi.WrapperTask):
    """
    Wrapper task for the USDA ETL pipeline.
    """
    def requires(self):
        return [
            FetchUSDAData(),
            ConsolidateFetchedData(),
            InsertUniqueData()
        ]

class ComexETLPipeline(luigi.WrapperTask):
    """
    Wrapper task for the COMEX ETL pipeline.
    """
    def requires(self):
        return [
            DownloadCurrentYearData(data_type='EXP'),
            DownloadCurrentYearData(data_type='IMP'),
            FilterData(data_type='EXP'),
            FilterData(data_type='IMP'),
            MergeAuxiliaryData(data_type='EXP'),
            MergeAuxiliaryData(data_type='IMP'),
            UpdateMasterTable(data_type='EXP'),
            UpdateMasterTable(data_type='IMP')
        ]

class INDECPipeline(luigi.WrapperTask):
    """
    Wrapper task for the INDEC ETL pipeline.
    """
    def requires(self):
        return [
            FetchMonthlyData(),
            ApplyFiltersAndMerges(),
            UpdateINDECMasterTable()
        ]

def main():
    parser = argparse.ArgumentParser(description="Run ETL pipelines")
    parser.add_argument('--pipeline', help='Specify which pipeline to run', choices=['USDA', 'COMEX', 'INDEC', 'ALL'])

    args = parser.parse_args()

    if args.pipeline == 'USDA':
        luigi.run(main_task_cls=USDAETLPipeline)
    elif args.pipeline == 'COMEX':
        luigi.run(main_task_cls=ComexETLPipeline)
    elif args.pipeline == 'INDEC':
        luigi.run(main_task_cls=INDECPipeline)
    elif args.pipeline == 'ALL':
        luigi.run(main_task_cls=MainETLPipeline)
    else:
        print("Please specify a valid pipeline: USDA, COMEX, INDEC, or ALL")

if __name__ == '__main__':
    main()
