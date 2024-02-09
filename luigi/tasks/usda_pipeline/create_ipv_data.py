# .\usda_pipeline\create_ipv_data.py
import pandas as pd
import luigi

class ReverseTransformData(luigi.Task):
    master_table_file = 'data/usda/processed/master_table.csv'
    reverse_consolidated_file = 'data/usda/processed/reverse_consolidated_data.csv'

    def requires(self):
        return []

    def output(self):
        return luigi.LocalTarget(self.reverse_consolidated_file)

    def run(self):
        # Load the master table
        master_df = pd.read_csv(self.master_table_file)

        # Define the columns that will remain fixed (not melted)
        id_vars = ['Country', 'Market Year', 'Calendar Year', 'Month']

        # Melt the DataFrame
        melted_df = master_df.melt(id_vars=id_vars, var_name='Attribute', value_name='Value')

        # Create 'MarketYear' column from 'Market Year' and so on for other columns
        melted_df['MarketYear'] = melted_df['Market Year']
        melted_df['CalendarYear'] = melted_df['Calendar Year']
        melted_df['CommodityCode'] = melted_df['Attribute'].apply(lambda x: '0410000')  # Replace with your logic for CommodityCode

        # Select the relevant columns and order them as required
        final_df = melted_df[['Country', 'MarketYear', 'CalendarYear', 'Month', 'Attribute', 'Value', 'CommodityCode']]

        # Save the reverse-transformed data
        final_df.to_csv(self.output().path, index=False)

if __name__ == '__main__':
    luigi.run()
