// ProductsTable.js

console.log('ProductsTable.js loaded');

let selectedProductCode = null;
window.currentFilters = {};
window.showingAllRecords = false;

// Modify loadProducts function to fetch all products without pagination
window.loadProducts = async function(filters = window.currentFilters) {
    console.log(`Fetching products with filters`, filters);

    const query = new URLSearchParams(filters).toString();
    const searchParam = filters.search ? `&search=${encodeURIComponent(filters.search)}` : '';
    const url = `/products?${query}${searchParam}`;

    const headers = {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
    };

    try {
        const response = await fetch(url, { method: 'GET', headers: headers });
        if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
        const data = await response.json();
        populateProductsTable(data.data || []);
    } catch (error) {
        console.error("[ProductsTable] Failed to load products", error);
        populateProductsTable([]);
    }
};


// Populate the products table
window.populateProductsTable = function(products) {
    console.log("[ProductsTable] Populating products table with data:", products);

    let tableBody = document.getElementById('products-table-body');
    if (!tableBody) {
        console.error("Table body not found");
        return;
    }

    // Clear the table before populating new data
    tableBody.innerHTML = '';

    // Only populate if there are products
    if (products.length > 0) {
        tableBody.innerHTML = products.map(product => {
            // Convert the frequency code to the corresponding word
            const freqWord = freqToWord[product.freq] || product.freq;

            // Extract only the date part from the 'alterado' value
            const dateOnly = product.alterado.split(' ')[0]; // Splits the string by space and takes the first part

            return `
                <tr>
                   <td><input type="radio" name="productSelect" value="${product['Código_Produto']}" onchange="selectProduct('${product['Código_Produto']}')"></td>
                   <td>${product.Classificação}</td>
                   <td>${product.longo}</td>
                   <td>${freqWord}</td>
                   <td>${dateOnly}</td> <!-- Display only the date part -->
                </tr>
            `;
        }).join('');
        console.log("[ProductsTable] Products table populated with products.");
    } else {
        // Show a message or an empty state if there are no products
        tableBody.innerHTML = `<tr><td colspan="5">No products found.</td></tr>`;
        console.log("[ProductsTable] No products found message displayed.");
    }

    window.loadedProducts = products;
};






window.selectProduct = function(productCode) {
    console.log("Selected product code: ", productCode);
    if (window.selectedProductCode !== productCode) {
        window.selectedProductCode = productCode;
        window.selectedProductCodeExport = productCode; // Set the export code
        window.loadDataSeries(productCode);
    } else {
        window.selectedProductCode = null;
        window.selectedProductCodeExport = null; // Clear the export code
        clearDataSeriesView();
    }
    updateSelectedProductName(); // Update the display of the selected product name
};



function clearDataSeriesView() {
    let dataSeriesBody = document.getElementById('data-series-body');
    if (dataSeriesBody) {
        dataSeriesBody.innerHTML = '';
    }
    console.log("[DataSeriesTable] Data series view cleared.");
}

function updateSelectedProductName() {
    let productNameDisplay = document.getElementById('selected-product-name');
    if (productNameDisplay) {
        if (window.selectedProductCode) {
            const selectedProduct = window.loadedProducts.find(product => product['Código_Produto'] === window.selectedProductCode);
            productNameDisplay.textContent = selectedProduct ? `DataSeries for: ${selectedProduct.longo}` : 'Product not found';
        } else {
            productNameDisplay.textContent = 'Por favor selecione um produto na tabela acima';
        }
    }
}


document.addEventListener('DOMContentLoaded', function () {
    console.log("[ProductsTable] Page loaded - Starting to load products.");
    loadProducts();
    updateSelectedProductName(); // Ensure placeholder is displayed initially
});

window.applySearchFilter = async function() {
    const searchTerm = document.getElementById('search-box').value;
    await loadProducts({...window.currentFilters, search: searchTerm});
};
