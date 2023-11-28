// Functions to handle download actions
function downloadCSV() {
    console.log("Downloading CSV file");
    window.location.href = '/download/csv';
}

function downloadPDF() {
    console.log("Downloading PDF file");
    window.location.href = '/download/pdf';
}

// Event listeners for download buttons
document.addEventListener('DOMContentLoaded', function () {
    const csvBtn = document.getElementById('download-csv-btn');
    const pdfBtn = document.getElementById('download-pdf-btn');

    if (csvBtn) {
        csvBtn.addEventListener('click', function() {
            console.log("Download CSV button clicked");
            downloadCSV();
        });
    }

    if (pdfBtn) {
        pdfBtn.addEventListener('click', function() {
            console.log("Download PDF button clicked");
            downloadPDF();
        });
    }
});
