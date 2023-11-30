// ProductsTable.js

console.log('ProductsTable.js loaded');

let selectedProductCode = null;

window.loadProducts = function(page = 1) {
    console.log(`Fetching products for page: ${page}`);
    fetch(`/products?page=${page}`)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(response => {
            window.currentPage = response.current_page;
            window.totalPages = response.last_page;
            populateProductsTable(response.data);
            renderPagination();
        })
        .catch(error => {
            console.error("Failed to load products", error);
        });
};

document.addEventListener('DOMContentLoaded', function () {
    window.loadProducts();
});

function populateProductsTable(products) {
    let tableBody = document.getElementById('products-table-body');
    if (!tableBody) {
        console.error("Table body not found");
        return;
    }

    tableBody.innerHTML = products.map(product => `
        <tr>
            <td><input type="radio" name="productSelect" value="${product.Classificação}" onchange="selectProduct('${product.Classificação}')"></td>
            <td>${product.Classificação}</td>
            <td>${product.longo}</td>
            <td>${product.freq}</td>
            <td>${product.alterado}</td>
        </tr>
    `).join('');
}

function selectProduct(productCode) {
    selectedProductCode = productCode;
}

function renderPagination() {
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
}


// Add logic for integrating with dropdown filters
function setupDropdownFilters() {
    console.log("[ProductsTable] Setting up dropdown filters");

    // Fetch dropdown data from the server
    fetch('/api/get-dropdown-data')
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error while fetching dropdown data! Status: ${response.status}`); // Corrected this line
            }
            return response.json();
        })
        .then(data => {
            populateDropdowns(data);
            console.log("[ProductsTable] Dropdowns populated with server data");
        })
        .catch(error => {
            console.error("Failed to fetch dropdown data", error);
        });
}

document.addEventListener('DOMContentLoaded', function () {
    console.log('DOM fully loaded and parsed');
    window.loadProducts();
    setupDropdownFilters(); // Setup dropdown filters after loading products
});
