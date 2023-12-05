
function downloadPDF() {
    console.log("[DownloadButtons] Initiating PDF download");

    // Fetch products data from the products table
    const productsData = Array.from(document.querySelectorAll('#products-table tbody tr')).map(row => {
        return Array.from(row.querySelectorAll('td:not(:first-child)')).map(cell => cell.textContent.trim());
    });

    // Fetch data series from the data series table
    const dataSeriesData = Array.from(document.querySelectorAll('#data-series-table tbody tr')).map(row => {
        return Array.from(row.querySelectorAll('td')).map(cell => cell.textContent.trim());
    });

    // Prepare the data payload for the request
    const data = { products: productsData, dataSeries: dataSeriesData };

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



function downloadVisibleCSV() {
    // Gather data from the products table
    const productsData = Array.from(document.querySelectorAll('#products-table tbody tr')).map(row => {
        return Array.from(row.querySelectorAll('td:not(:first-child)')).map(cell => cell.textContent.trim());
    });

    // Gather data from the data series table
    const dataSeriesData = Array.from(document.querySelectorAll('#data-series-table tbody tr')).map(row => {
        return Array.from(row.querySelectorAll('td')).map(cell => cell.textContent.trim());
    });

    // Prepare the data to send
    const data = { products: productsData, dataSeries: dataSeriesData };

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

// Event listener for the download button
document.addEventListener('DOMContentLoaded', function () {
    const csvVisibleBtn = document.getElementById('download-csv-btn'); // Corrected to match the button's ID
    if (csvVisibleBtn) {
        csvVisibleBtn.addEventListener('click', downloadVisibleCSV);
    }
});


// Corrected Event listeners for download buttons
document.addEventListener('DOMContentLoaded', function () {
    console.log("[DownloadButtons] Setting up event listeners for download buttons");

    // This should match the ID of the button for downloading the visible CSV
    const csvVisibleBtn = document.getElementById('download-csv-btn');
    if (csvVisibleBtn) {
        csvVisibleBtn.addEventListener('click', downloadVisibleCSV);
    }

    // Leave the rest of your code as is for the PDF download
    const pdfBtn = document.getElementById('download-pdf-btn');
    if (pdfBtn) {
        pdfBtn.addEventListener('click', downloadPDF);
    }
});
