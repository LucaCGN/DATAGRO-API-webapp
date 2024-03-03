document.addEventListener('DOMContentLoaded', function () {
    var form = document.getElementById('invoice-split-form');
    var fileInput = document.getElementById('pdfUpload');
    var progressBar = document.getElementById('fileProgress');

    form.onsubmit = function (event) {
        event.preventDefault();
        // Client-side validation
        var file = fileInput.files[0];
        if (file.type !== 'application/pdf') {
            alert('Please upload a PDF file.');
            return;
        }

        // Prepare form data
        var formData = new FormData();
        formData.append('pdf', file);

        // AJAX request to upload the file
        var xhr = new XMLHttpRequest();
        xhr.open('POST', '/nf-splitter', true);

        // Progress on transfers from the server to the client (downloads)
        xhr.onprogress = function (event) {
            if (event.lengthComputable) {
                var percentComplete = (event.loaded / event.total) * 100;
                progressBar.value = percentComplete;
            }
        };

        xhr.onloadstart = function (event) {
            progressBar.style.display = 'block';
            progressBar.value = 0;
        };

        xhr.onloadend = function (event) {
            progressBar.value = event.loaded; // event.loaded is the same as event.total
        };

        // When request is complete
        xhr.onload = function () {
            if (xhr.status === 200) {
                alert('File successfully uploaded. Your download will start shortly.');
                window.location.href = xhr.responseText; // Assuming the responseText contains the path to the ZIP file
            } else {
                alert('Error occurred when trying to upload the file.');
            }
            progressBar.style.display = 'none';
        };

        // Send the request
        xhr.send(formData);
    };
});
