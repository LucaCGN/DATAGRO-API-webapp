import sys
import os
import fitz
import re
from zipfile import ZipFile

# Function to extract invoice numbers and split the PDF
def split_pdf(pdf_path, output_dir):
    pdf_document = fitz.open(pdf_path)
    invoice_numbers = []

    for page_number in range(len(pdf_document)):
        page = pdf_document[page_number]
        page_text = page.get_text()
        match = re.search(r'N\.\s*(\d+)', page_text)
        if match:
            invoice_number = match.group(1)
            invoice_numbers.append(invoice_number)
            output_file_name = f"{invoice_number}.pdf"
            invoice_pdf = fitz.open()
            invoice_pdf.insert_pdf(pdf_document, from_page=page_number, to_page=page_number)
            invoice_pdf.save(os.path.join(output_dir, output_file_name))
            invoice_pdf.close()

    pdf_document.close()
    return invoice_numbers

# Function to create a ZIP file from the output directory
def create_zip_from_directory(directory_path, zip_path):
    with ZipFile(zip_path, 'w') as zipf:
        for root, dirs, files in os.walk(directory_path):
            for file in files:
                zipf.write(os.path.join(root, file), file)

# Main execution
if __name__ == "__main__":
    input_pdf_path = sys.argv[1]
    base_name = os.path.splitext(os.path.basename(input_pdf_path))[0]
    output_directory = os.path.join('/tmp', base_name)

    # Create a directory to store individual PDFs
    if not os.path.exists(output_directory):
        os.makedirs(output_directory)

    # Split the PDF
    split_pdf(input_pdf_path, output_directory)

    # Create a ZIP file containing all the split PDFs
    zip_file_path = f"{output_directory}.zip"
    create_zip_from_directory(output_directory, zip_file_path)

    # Output the path to the ZIP file for Laravel to capture
    print(zip_file_path)
