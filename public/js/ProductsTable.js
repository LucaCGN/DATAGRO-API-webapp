// ProductsTable.js

console.log('ProductsTable.js loaded');

// Variable to keep track of the currently selected product code
let selectedProductCode = null;

// Attaching loadProducts to window to make it globally accessible
window.loadProducts = function(page = 1) {
    console.log(`Fetching products for page: ${page}`);
    fetch(`/products?page=${page}`)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            console.log('Products fetched successfully');
            return response.json();
        })
        .then(response => {
            window.currentPage = response.current_page;
            window.totalPages = response.last_page;
            console.log(`Current page: ${window.currentPage}, Total pages: ${window.totalPages}`);
            populateProductsTable(response.data);
            renderPagination();
        })
        .catch(error => {
            console.error("Failed to load pwroducts", error);
        });
};

document.addEventListener('DOMContentLoaded', function () {
    console.log('DOM fully loaded and parsed');
    window.loadProducts();
});

function populateProductsTable(products) {
    console.log('Populating products table with products:', products);
    let tableBody = document.getElementById('products-table-body');
    if (!tableBody) {
        console.error("Table body not found");
        return;
    }
    tableBody.innerHTML = products.map(product => `
        <tr>
            <td><input type="radio" name="productSelect" value="${product.Código_Produto}" ${selectedProductCode === product.Código_Produto ? 'checked' : ''} onchange="selectProduct('${product.Código_Produto}')"></td>
            <td>${product.Código_Produto}</td>
            <td>${product.descr}</td>
            <td>${product.inserido}</td>
            <td>${product.alterado}</td>
        </tr>
    `).join('');
}

function selectProduct(productCode) {
    console.log(`Product selected: ${productCode}`);
    selectedProductCode = productCode;
    console.log("Product selection updated to:", selectedProductCode);
}

function renderPagination() {
    console.log('Rendering pagination controls');
    const paginationDiv = document.getElementById('products-pagination');
    if (!paginationDiv) {
        console.error("Pagination div not found");
        return;
    }

    let html = '';
    if (window.currentPage > 1) {
        html += `<button onclick="window.loadProducts(${window.currentPage - 1})">Previous</button>`;
    }
    html += `<span>Page ${window.currentPage} of ${window.totalPages}</span>`;
    if (window.currentPage < window.totalPages) {
        html += `<button onclick="window.loadProducts(${window.currentPage + 1})">Next</button>`;
    }

    paginationDiv.innerHTML = html;
    console.log("Pagination rendered with HTML:", html);
}

// Add logic for integrating with dropdown filters
function setupDropdownFilters() {
    console.log("[ProductsTable] Setting up dropdown filters");

    // Fetch dropdown data from the server
    fetch('/api/get-dropdown-data')
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error while fetching dropdown data! Status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            // Assuming populateDropdowns is a global function from DropdownFilter.js
            populateDropdowns(data);
            console.log("[ProductsTable] Dropdowns populated with server data");
        })
        .catch(error => {
            console.error("Failed to fetch dropdown data", error);
        });
}

// Ensure this is called after the DOM is fully loaded
document.addEventListener('DOMContentLoaded', function () {
    console.log('DOM fully loaded and parsed');
    window.loadProducts();
    setupDropdownFilters(); // Setup dropdown filters after loading products
});
