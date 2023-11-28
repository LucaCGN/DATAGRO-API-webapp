// Functions to handle download actions
function downloadCSV() {
    console.log("[DownloadButtons] Initiating CSV download");
    window.location.href = '/download/csv';
}

function downloadPDF() {
    console.log("[DownloadButtons] Initiating PDF download");
    window.location.href = '/download/pdf';
}

// Event listeners for download buttons
document.addEventListener('DOMContentLoaded', function () {
    console.log("[DownloadButtons] Setting up event listeners for download buttons");
    const csvBtn = document.getElementById('download-csv-btn');
    const pdfBtn = document.getElementById('download-pdf-btn');

    if (csvBtn) {
        csvBtn.addEventListener('click', function() {
            console.log("[DownloadButtons] CSV Download button clicked");
            downloadCSV();
        });
    }

    if (pdfBtn) {
        pdfBtn.addEventListener('click', function() {
            console.log("[DownloadButtons] PDF Download button clicked");
            downloadPDF();
        });
    }
});
