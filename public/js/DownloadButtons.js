
function downloadVisibleCSV() {
    // Use the selectedProductCode to gather only the selected product's data
    const selectedProductRow = window.loadedProducts.find(product => product['Código_Produto'] === window.selectedProductCode);
    const selectedProductData = selectedProductRow ?
        Object.values(selectedProductRow).slice(1) : []; // Exclude the product code from the data

    const dataSeriesData = Array.from(document.querySelectorAll('#data-series-table tbody tr')).map(row => {
        return Array.from(row.querySelectorAll('td')).map(cell => cell.textContent.trim());
    });

    // Prepare the data to send
    const data = { product: selectedProductData, dataSeries: dataSeriesData };

    // Send the data to the server using fetch API
    fetch('/download/visible-csv', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(data)
    })
    .then(response => response.blob())
    .then(blob => {
        // Create a link element, use it to download the file and remove it
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.style.display = 'none';
        a.href = url;
        a.download = 'visible-data.csv';
        document.body.appendChild(a);
        a.click();
        window.URL.revokeObjectURL(url);
        document.body.removeChild(a);
    })
    .catch((error) => console.error('Error:', error));
}





function downloadPDF() {
    console.log("[DownloadButtons] Initiating PDF download");

    // Check if a product has been selected
    if (!window.selectedProductCodeExport) {
        console.error('No product selected for export');
        alert('Please select a product before downloading the PDF.');
        return; // Exit the function if no product is selected for export
    }

    // Fetch only the selected product's data using the export code
    const selectedProductRow = window.loadedProducts.find(product => product['Código_Produto'] === window.selectedProductCodeExport);

    if (!selectedProductRow) {
        console.error('Selected product not found in loaded products for export');
        alert('Selected product data could not be found for export. Please try again.');
        return; // Exit the function if selected product data is not found for export
    }

    // Log the selected product for debugging purposes
    console.log('Selected product data:', selectedProductRow);

    // Extract the selected product's data including the 'Código_Produto'
    // Map the product object to an array of its values
    const selectedProductData = Object.values(selectedProductRow);

    // Log the extracted data for debugging purposes
    console.log('Extracted product data for PDF:', selectedProductData);

    // Fetch data series from the data series table with only the desired columns
    const dataSeriesData = Array.from(document.querySelectorAll('#data-series-table tbody tr')).map(row => {
        const cells = Array.from(row.querySelectorAll('td'));
        return {
            cod: cells[0].textContent.trim(),
            data: cells[1].textContent.trim(),
            ult: cells[2].textContent.trim(),
            mini: cells[3].textContent.trim(),
            maxi: cells[4].textContent.trim(),
            abe: cells[5].textContent.trim(),
            volumes: cells[6].textContent.trim()
            // 'med' and 'aju' are intentionally excluded
        };
    });

    // Log the data series for debugging purposes
    console.log('Data series for PDF:', dataSeriesData);

    // Prepare the data payload for the request
    const data = { product: selectedProductData, dataSeries: dataSeriesData };

    // Make a POST request to the server to generate and download the PDF
    fetch('/download/visible-pdf', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(data)
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok ' + response.statusText);
        }
        return response.blob();
    })
    .then(blob => {
        // Create a blob URL from the response
        const url = window.URL.createObjectURL(blob);

        // Create a link element, use it to download the file, and remove it
        const a = document.createElement('a');
        a.style.display = 'none';
        a.href = url;
        a.download = 'visible-data.pdf';
        document.body.appendChild(a);
        a.click();

        // Clean up by revoking the object URL and removing the link element
        window.URL.revokeObjectURL(url);
        document.body.removeChild(a);
    })
    .catch((error) => console.error('Error:', error));
}

// Event listener for the download button
document.addEventListener('DOMContentLoaded', function () {
    const csvVisibleBtn = document.getElementById('download-csv-btn'); // Corrected to match the button's ID
    if (csvVisibleBtn) {
        csvVisibleBtn.addEventListener('click', downloadVisibleCSV);
    }
});


console.log('DownloadButtons.js loaded');

document.addEventListener('DOMContentLoaded', function () {
    console.log("[DownloadButtons] DOM fully loaded and parsed");

    // Binding for CSV download button
    const csvVisibleBtn = document.getElementById('download-csv-btn');
    if (csvVisibleBtn) {
        console.log("[DownloadButtons] CSV button found");
        csvVisibleBtn.addEventListener('click', downloadVisibleCSV);
    } else {
        console.log("[DownloadButtons] CSV button NOT found");
    }

    // Binding for PDF download button
    const pdfBtn = document.getElementById('download-pdf-btn');
    if (pdfBtn) {
        console.log("[DownloadButtons] PDF button found");
        pdfBtn.addEventListener('click', downloadPDF);
    } else {
        console.log("[DownloadButtons] PDF button NOT found");
    }
});
