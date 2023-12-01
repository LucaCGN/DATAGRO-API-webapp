## resources/views/partials/dropdown-filter.blade.php
```
<!DOCTYPE html>
<div class="animated" id="dropdown-filter">
    <div class="animated" id="dropdown-filter" style="display: flex; flex-direction: column;">
        <label for="classificacao-select">Classificação</label>
        <select class="button" id="classificacao-select">
            <!-- Options will be populated via JavaScript -->
        </select>

        <label for="subproduto-select">Subproduto</label>
        <select class="button" id="subproduto-select">
            <!-- Options will be populated via JavaScript -->
        </select>

        <label for="local-select">Local</label>
        <select class="button" id="local-select">
            <!-- Options will be populated via JavaScript -->
        </select>

        <label for="freq-select">Frequência</label>
        <select class="button" id="freq-select">
            <!-- Options will be populated via JavaScript -->
        </select>

        <label for="proprietario-select">Proprietário</label>
        <select class="button" id="proprietario-select">
            <!-- Options will be populated via JavaScript -->
        </select>
    </div>
</div>
<script>console.log('[dropdown-filter.blade.php] Dropdown filter view loaded');</script>

```
## resources/views/partials/products-table.blade.php
```
<!-- Products Table -->
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
## public/css/app.css
```
/* Main application styles */
body {
    font-family: Arial, sans-serif;
    color: #4f4f4f; /* Dark grey for text, softer than pure white */
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
}

.tables-container {
    margin-left: 20px;
}

#dropdown-filter {
    display: flex;
    flex-direction: column;
    gap: 10px;
    margin-bottom: 20px;
}

/* Removed all login page specific styles */

```
## resources/views/partials/data-series-table.blade.php
```
<!-- Data Series Table -->
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
<div id="data-series-pagination" class="pagination-controls">
    <!-- Pagination Controls populated by DataSeriesTable.js -->
</div>
<script>console.log('[data-series-table.blade.php] Data series table view loaded');</script>

```
## resources/views/partials/download-buttons.blade.php
```
<!DOCTYPE html>
<div class="animated">
    <button class="button" id="download-csv-btn">Download CSV</button>
    <button class="button" id="download-pdf-btn">Download PDF</button>
</div>
<script>console.log('[download-buttons.blade.php] Download buttons view loaded');</script>

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
