import luigi
import requests
from io import BytesIO
from zipfile import ZipFile
import pandas as pd
import os
# from datetime import datetime  # Dynamic logic commented out for testing

class FetchMonthlyData(luigi.Task):
    """
    Task to download and process monthly data from INDEC.
    Downloads data for the current year and processes it.
    """

    def output(self):
        # Dynamic logic commented out:
        # current_year = datetime.now().year
        # current_month = datetime.now().month - 1  # Assuming data is one month behind
        # if current_month == 0:  # Handle December of the previous year
        #     current_year -= 1
        #     current_month = 12
        # return luigi.LocalTarget(f'data/indec/raw/EXP_{current_year}_{current_month:02d}.csv')

        # Hardcoded values for testing:
        return luigi.LocalTarget('data/indec/raw/EXP_2023_12.csv')

    def run(self):
        # Dynamic logic commented out:
        # current_year = datetime.now().year
        # current_month = datetime.now().month - 1  # Assuming data is one month behind
        # if current_month == 0:  # Handle December of the previous year
        #     current_year -= 1
        #     current_month = 12
        # url = f"https://comex.indec.gov.ar/files/zips/exports_{current_year}_M.zip"

        # Hardcoded values for testing:
        url = "https://comex.indec.gov.ar/files/zips/exports_2023_M.zip"
        output_dir = self.output().path

        # Create the output directory if it doesn't exist
        os.makedirs(os.path.dirname(output_dir), exist_ok=True)

        # Download and extract data
        response = requests.get(url)
        response.raise_for_status()

        with ZipFile(BytesIO(response.content)) as zip_file:
            for file in zip_file.namelist():
                # Adjusted hardcoded logic for testing:
                if 'expom23.csv' in file or 'exponm23.csv' in file or 'expopm23.csv' in file:
                    # Extract and rename the file
                    zip_file.extract(file, os.path.dirname(output_dir))
                    new_file_path = os.path.join(os.path.dirname(output_dir), f"EXP_2023_12.csv")
                    os.rename(os.path.join(os.path.dirname(output_dir), file), new_file_path)

                    # Read the CSV without headers, skip the first row, and remove initial spaces
                    df = pd.read_csv(new_file_path, delimiter=';', header=None, encoding='latin1', skiprows=1, skipinitialspace=True)

                    # Strip whitespace from all DataFrame elements
                    df = df.applymap(lambda x: x.strip() if isinstance(x, str) else x)
                    # Rename columns based on their positions
                    df.columns = ['ANO', 'MES', 'CO_NCM', 'ARG_PAIS_CO', 'PNET', 'FOB']

                    # Save the modified CSV
                    df.to_csv(new_file_path, index=False, sep=';')
                    break  # Break after handling the first relevant file

if __name__ == "__main__":
    luigi.run(['FetchMonthlyData', '--local-scheduler'])
