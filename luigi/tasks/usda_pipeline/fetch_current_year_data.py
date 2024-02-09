import requests
import datetime
import luigi
import json
import os

class FetchUSDAData(luigi.Task):
    """
    Luigi Task to fetch USDA data for the current, previous, and next market years for predefined commodity codes.
    """
    api_key = luigi.Parameter()
    data_dir = 'data/usda/raw'
    commodity_codes = [
            '0440000',  # Corn
            '2231000',  # Oilseed, Copra
            '2223000',  # Oilseed, Cottonseed
            '2232000',  # Oilseed, Palm Kernel
            '2221000',  # Oilseed, Peanut
            '2226000',  # Oilseed, Rapeseed
            '2222000',  # Oilseed, Soybean
            '2224000',  # Oilseed, Sunflowerseed
            '4232000',  # Oil, Soybean
            '0813100',  # Meal, Soybean
            '0410000',  # Wheat
            '4233000',  # Oil, Cottonseed
            '4235000',  # Oil, Olive
            '4243000',  # Oil, Palm
            '4244000',  # Oil, Palm Kernel
            '4234000',  # Oil, Peanut
            '4239100',  # Oil, Rapeseed
            '4236000',  # Oil, Sunflowerseed
            '4242000',  # Oil, Coconut
            '0813700',  # Meal, Copra
            '0813300',  # Meal, Cottonseed
            '0814200',  # Meal, Fish
            '0813800',  # Meal, Palm Kernel
            '0813200',  # Meal, Peanut
            '0813600',  # Meal, Rapeseed
            '0813500',  # Meal, Sunflowerseed
    ]
    api_key = '697486e5-932d-46d3-804a-388452a19d70'

    def requires(self):
        os.makedirs(self.data_dir, exist_ok=True)
        return []

    def output(self):
        current_year = datetime.datetime.now().year
        years = [current_year - 1, current_year, current_year + 1]  # Include previous, current, and next year
        return [luigi.LocalTarget(f'{self.data_dir}/{code}_data_{year}.json')
                for code in self.commodity_codes
                for year in years]

    def run(self):
        api_endpoint = "https://apps.fas.usda.gov/PSDOnlineDataServices/api/CommodityData/GetCommodityDataByYear"
        headers = {'Accept': 'application/json', 'API_KEY': self.api_key}

        for target in self.output():
            commodity_code, year = target.path.split('/')[-1].split('_')[0], target.path.split('_')[-1].split('.')[0]
            response = requests.get(f"{api_endpoint}?commodityCode={commodity_code}&marketYear={year}", headers=headers)

            if response.status_code == 200:
                # Overwrite the file if it exists
                with open(target.path, 'w') as file:
                    json.dump(response.json(), file)
            else:
                # Create an empty file for failed requests
                with open(target.path, 'w') as file:
                    pass
                # Log the failure but continue execution
                print(f"Failed to fetch data for {commodity_code}, market year {year}")


if __name__ == '__main__':
    luigi.run()
