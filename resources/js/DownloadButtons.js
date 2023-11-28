function downloadCSV() {
    console.log("Downloading CSV file");  // Log the function call
    window.location.href = '/download/csv';
}

function downloadPDF() {
    console.log("Downloading PDF file");  // Log the function call
    window.location.href = '/download/pdf';
}

document.getElementById('download-csv-btn').addEventListener('click', () => {
    console.log("Download CSV button clicked");  // Log button click
    downloadCSV();
});
document.getElementById('download-pdf-btn').addEventListener('click', () => {
    console.log("Download PDF button clicked");  // Log button click
    downloadPDF();
});
