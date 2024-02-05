import datetime
import luigi
import requests
import os

class DownloadCurrentYearData(luigi.Task):
    data_type = luigi.Parameter()  # 'EXP' or 'IMP'

    def output(self):
        # Original logic:
        # current_year = datetime.datetime.now().year
        # return luigi.LocalTarget(f'data/comex/raw/{self.data_type}_{current_year}.csv')

        # Testing logic with hardcoded year:
        return luigi.LocalTarget(f'data/comex/raw/{self.data_type}_2023.csv')

    def run(self):
        base_url = "https://balanca.economia.gov.br/balanca/bd/comexstat-bd/ncm/"
        # Original logic:
        # current_year = datetime.datetime.now().year
        # url = f"{base_url}{self.data_type}_{current_year}.csv"

        # Testing logic with hardcoded year:
        url = f"{base_url}{self.data_type}_2023.csv"

       # Making the request
        response = requests.get(url, verify=False, stream=True)
        print(f"Requesting data from URL: {url}")
        print(f"Status code: {response.status_code}")

        if response.status_code == 200:
            # Ensure the directory exists before writing the file
            os.makedirs(os.path.dirname(self.output().path), exist_ok=True)

            # Open the output file in binary write mode
            with open(self.output().path, 'wb') as f:
                content_length = 0
                for chunk in response.iter_content(chunk_size=8192):
                    if chunk:
                        f.write(chunk)
                        content_length += len(chunk)
                print(f"Total content written to file: {content_length} bytes")
            if content_length == 0:
                print(f"Warning: The file {self.output().path} is empty.")
        else:
            print(f"Failed to download data from {url}")

if __name__ == '__main__':
    luigi.run(['DownloadCurrentYearData', '--data-type', 'EXP', '--local-scheduler'])
