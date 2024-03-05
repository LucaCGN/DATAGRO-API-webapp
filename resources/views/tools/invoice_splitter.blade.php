<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice Splitter Tool</title>
    <!-- Link to existing app.css for styling -->
    <link rel="stylesheet" href="css/tools.css">
</head>
<body>

<div class="container">
    <h2>Invoice Splitter Tool</h2>
    <form id="invoice-split-form" enctype="multipart/form-data">
        <div class="form-group">
            <label for="pdfUpload">Upload Invoice PDF:</label>
            <input type="file" id="pdfUpload" name="pdf" accept=".pdf" required>
        </div>
        <button type="submit" class="btn btn-primary">Split PDF</button>
        <div id="upload-progress" style="display:none;">
            <label for="fileProgress">Uploading and processing:</label>
            <progress id="fileProgress" value="0" max="100"></progress>
        </div>
    </form>
</div>

<script src="/public/js/InvoiceSplitter.js"></script>

</body>
</html>
