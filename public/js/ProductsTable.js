// ProductsTable.js

// Function to populate products table
function populateProductsTable(products) {
    console.log("[ProductsTable] Populating products table with:", products);
    let tableBody = document.getElementById('products-table-body');
    tableBody.innerHTML = products.map(product => `
        <tr onclick="selectProduct(${product.id})">
            <td>${product.Código_Produto}</td>
            <td>${product.descr}</td>
            <td>${product.inserido}</td>
            <td>${product.alterado}</td>
        </tr>
    `).join('');
    console.log("[ProductsTable] Products table populated");
}

// Assuming selectProduct function exists to handle the row selection
function selectProduct(productId) {
    // Implementation of selectProduct
    console.log("[ProductsTable] Product selected with ID:", productId);
    // You might want to add some logic here to handle the product selection
}

// The DOMContentLoaded event listener and updateFilters call have been removed from this file.
