## resources/views/partials/data-series-table.blade.php
```
<div class="responsive-table animated" id="data-series-table">
    <table>
        <thead>
            <tr>
                <th>Cod</th>
                <th>data</th>
                <th>ult</th>
                <th>mini</th>
                <th>maxi</th>
                <th>abe</th>
                <th>volumes</th>
                <th>med</th>
                <th>aju</th>
            </tr>
        </thead>
        <tbody id="data-series-body">
            <!-- Data populated by DataSeriesTable.js -->
        </tbody>
    </table>
</div>
<script>console.log('[data-series-table.blade.php] Data series table view loaded');</script>


```
## public/css/login.css
```
/* login.css - specific styles for the login page to match the main application's design */

.login-page {
    font-family: Arial, sans-serif;
    background-color: #f8f8f8; /* Light grey background to match the main app */
    color: #4f4f4f; /* Dark grey for text */
    height: 100vh; /* Full viewport height */
    display: flex;
    flex-direction: column;
    justify-content: space-between; /* Distribute space between header, main and footer */
}

.login-header, .login-footer {
    background-color: #fff; /* White background */
    padding: 10px 20px; /* Padding for larger screens */
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); /* Soft shadow for depth */
    text-align: center; /* Center text for smaller screens */
}

.login-container {
    flex-grow: 1; /* Flex grow to take available space */
    display: flex;
    flex-direction: column;
    justify-content: center; /* Center vertically */
    align-items: center; /* Center horizontally */
}

.login-box {
    background-color: #fff; /* White background */
    padding: 20px;
    border-radius: 10px; /* Rounded corners */
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); /* Shadow for depth */
    width: 100%;
    max-width: 320px; /* Max width for the box */
}

.input-group label, .input-group input {
    display: block;
    width: 100%;
    margin-bottom: 10px; /* Space between the inputs */
}

.input-group input {
    padding: 10px;
    border: 1px solid #ddd; /* Light border color */
    border-radius: 5px; /* Rounded borders for inputs */
}

.button.login-button {
    width: 100%; /* Full width button */
    padding: 10px; /* Padding for the button */
    margin-top: 20px; /* Space from the last input group */
}

.error-messages ul {
    list-style-type: none;
    color: red;
    padding: 0;
    margin-top: 10px;
}

/* Footer links style */
.login-footer ul {
    list-style-type: none;
    padding: 0;
    margin: 0;
    display: flex;
    justify-content: center;
}

.login-footer ul li a {
    color: #8dbf42; /* Green color to match the main app */
    text-decoration: none;
}

.login-footer ul li a:hover {
    text-decoration: underline; /* Underline on hover */
}

@media screen and (max-width: 600px) {
    .login-header, .login-footer {
        padding: 10px; /* Smaller padding on smaller screens */
    }
}

```
## public/css/app.css
```
/* Main application styles */
body {
    font-family: Arial, sans-serif;
    color: #4f4f4f; /* Dark grey for text */
    background-color: #f8f8f8; /* Light grey background for a clean look */
}

header {
    background-color: #fff; /* White background for header */
    color: #4f4f4f; /* Dark grey for text */
    padding: 10px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); /* Soft shadow for depth */
}

main {
    margin: 15px;
}

footer {
    background-color: #fff; /* White background for footer */
    color: #4f4f4f; /* Dark grey for text */
    text-align: center;
    padding: 10px;
    box-shadow: 0 -2px 4px rgba(0, 0, 0, 0.1); /* Soft shadow for depth */
}

.button {
    background-color: #8dbf42; /* Muted green for buttons */
    color: #fff;
    padding: 10px 20px;
    text-align: center;
    text-decoration: none;
    display: inline-block;
    font-size: 16px;
    margin: 4px 2px;
    cursor: pointer;
    border: none; /* Removed border for a cleaner look */
    border-radius: 5px;
    transition: background-color 0.3s;
}

.button:hover {
    background-color: #6e9830; /* Darker green on hover */
}

@media screen and (max-width: 600px) {
    body {
        font-size: 18px;
    }

    header, footer {
        text-align: center;
        padding: 10px 20px;
    }

    .responsive-table {
        overflow-x: auto;
    }
}

.animated {
    animation: fadeIn 1s ease-in;
}

@keyframes fadeIn {
    0% {opacity: 0;}
    100% {opacity: 1;}
}

.pagination-controls {
    text-align: center;
    padding: 10px;
}

.pagination-controls button {
    margin: 0 5px;
    padding: 5px 10px;
    background-color: #8dbf42; /* Muted green for buttons */
    color: #fff;
    border: none; /* Removed border for a cleaner look */
    border-radius: 5px;
    cursor: pointer;
}

.pagination-controls button:hover {
    background-color: #6e9830; /* Darker green on hover */
}

/* Styles for the filters to be positioned to the left of the tables */
.content {
    display: flex;
    flex-wrap: wrap; /* Ensure wrapping on smaller screens */
}

.tables-container {
    margin-left: 20px;
}

/* Filter container and group styles */
.filters-container {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
    margin-bottom: 20px;
}

.filter-group {
    margin-bottom: 10px;
}

.filter-group label,
.filter-group select {
    width: 100%; /* Full width for mobile, adjust as needed */
    margin-bottom: 5px; /* Vertical margin for spacing */
}

.filter-group select {
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 5px;
    background-color: #fff;
    color: #4f4f4f;
}

/* Table styles for consistency */
.responsive-table table {
    border-collapse: collapse;
    width: 100%;
}

.responsive-table th,
.responsive-table td {
    border: 1px solid #ddd; /* Light grey border */
    padding: 8px;
    text-align: left;
}

/* Download button container styles */
.download-buttons-container {
    display: flex;
    justify-content: space-between;
    margin-top: 20px;
}

.download-buttons-container button {
    background-color: #8dbf42; /* Muted green */
    color: #fff;
    padding: 10px 20px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
}

.download-buttons-container button:hover {
    background-color: #6e9830; /* Darker green on hover */
}

@media screen and (max-width: 600px) {
    .filters-container {
        flex-direction: column; /* Stack filters vertically on small screens */
    }

    .filter-group {
        width: 100%; /* Full width for filter groups on small screens */
    }

    .content,
    .tables-container {
        margin-left: 0;
    }

    .download-buttons-container {
        flex-direction: column; /* Stack buttons vertically on small screens */
    }

    .download-buttons-container button {
        width: 100%; /* Full width for buttons on small screens */
        margin-bottom: 10px; /* Space between stacked buttons */
    }
}

```
## resources/views/partials/products-table.blade.php
```
<div class="responsive-table animated" id="products-table">
    <table>
        <thead>
            <tr>
                <th> [Carregar Data Series] </th>
                <th>Produto  ('Classificação')  </th>
                <th>Nome ('longo')  </th>
                <th>Frequência</th>
                <th>Primeira Data</th>
            </tr>
        </thead>
        <tbody id="products-table-body">
            <!-- Data populated by ProductsTable.js -->
        </tbody>
    </table>
</div>
<div id="products-pagination" class="pagination-controls">
    <!-- Pagination Controls populated by ProductsTable.js -->
</div>

```
## resources/views/partials/dropdown-filter.blade.php
```
<div class="animated filters-container">
    <div class="filter-group">
        <label for="classificacao-select">Classificação</label>
        <select id="classificacao-select">
            <!-- Options will be populated via JavaScript -->
        </select>
    </div>

    <div class="filter-group">
        <label for="subproduto-select">Subproduto</label>
        <select id="subproduto-select">
            <!-- Options will be populated via JavaScript -->
        </select>
    </div>

    <div class="filter-group">
        <label for="local-select">Local</label>
        <select id="local-select">
            <!-- Options will be populated via JavaScript -->
        </select>
    </div>

    <div class="filter-group">
        <label for="freq-select">Frequência</label>
        <select id="freq-select">
            <!-- Options will be populated via JavaScript -->
        </select>
    </div>

    <div class="filter-group">
        <label for="proprietario-select">Proprietário</label>
        <select id="proprietario-select">
            <!-- Options will be populated via JavaScript -->
        </select>
    </div>
</div>
<script>console.log('[dropdown-filter.blade.php] Dropdown filter view loaded');</script>

```
## resources/views/auth/login.blade.php
```
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login - Markets Team Data Tools</title>
  <link href="{{ asset('css/login.css') }}" rel="stylesheet"> <!-- Link to the new login.css -->
</head>
<body class="login-page">
  <header class="login-header">
    <img src="{{ asset('images/Logo - Quadrado.png') }}" alt="Datagro Logo">
    <h2>Markets Team Data Tools</h2>
  </header>

  <main class="login-container">
    <div class="login-box">
      <form method="POST" action="{{ route('login') }}" class="login-form">
          @csrf
          <div class="input-group">
              <label for="email">Login:</label>
              <input type="text" name="email" id="email" required autofocus>
          </div>
          <div class="input-group">
              <label for="password">Password:</label>
              <input type="password" name="password" id="password" required>
          </div>
          <button type="submit" class="button login-button">Login</button> <!-- Modified class name -->
          @if ($errors->any())
              <div class="error-messages">
                  <ul>
                      @foreach ($errors->all() as $error)
                          <li>{{ $error }}</li>
                      @endforeach
                  </ul>
              </div>
          @endif
      </form>
    </div>
  </main>

  <footer class="login-footer"> <!-- Modified class name -->
    <img src="{{ asset('images/Logo - Banner Médio - Markets - 2.png') }}" alt="Datagro Markets Logo">
    <div>
      <h2>DATAGRO LINKS</h2>
      <ul>
          <li><a href="https://www.datagro.com/en/" target="_blank">www.datagro.com</a></li>
          <li><a href="https://portal.datagro.com/" target="_blank">portal.datagro.com</a></li>
          <li><a href="https://www.linkedin.com/company/datagro" target="_blank">Datagro LinkedIn</a></li>
      </ul>
    </div>
  </footer>

  <script>console.log('[login.blade.php] Login view loaded');</script>
</body>
</html>

```
## resources/views/partials/download-buttons.blade.php
```
<div class="animated download-buttons-container">
    <button id="download-csv-btn" class="button">Download CSV</button>
    <button id="download-pdf-btn" class="button">Download PDF</button>
</div>
<script>console.log('[download-buttons.blade.php] Download buttons view loaded');</script>

```
