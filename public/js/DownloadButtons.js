// Functions to handle download actions
function downloadCSV() {
    console.log("Downloading CSV file");
    window.location.href = '/download/csv';
}

function downloadPDF() {
    console.log("Downloading PDF file");
    window.location.href = '/download/pdf';
}

// Ensure the DOM is fully loaded before adding event listeners
document.addEventListener('DOMContentLoaded', function () {
    if (document.getElementById('download-csv-btn')) {
        document.getElementById('download-csv-btn').addEventListener('click', function() {
            console.log("Download CSV button clicked");
            downloadCSV();
        });
    }

    if (document.getElementById('download-pdf-btn')) {
        document.getElementById('download-pdf-btn').addEventListener('click', function() {
            console.log("Download PDF button clicked");
            downloadPDF();
        });
    }
});
