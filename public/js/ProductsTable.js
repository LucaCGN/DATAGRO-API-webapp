// ProductsTable.js

console.log('ProductsTable.js loaded');

let selectedProductCode = null;
window.currentFilters = {};

// ProductsTable.js

window.loadProducts = function(page = 1, filters = window.currentFilters) {
    console.log(`Fetching products for page: ${page} with filters`, filters);

    const hasFilters = Object.keys(filters).some(key => filters[key]);
    const query = new URLSearchParams({ ...filters, page }).toString();
    const url = hasFilters ? `/filter-products?${query}` : `/products?page=${page}`;
    const method = hasFilters ? 'POST' : 'GET';

    // Define headers based on whether filters are applied
    const headers = hasFilters ? {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        'Content-Type': 'application/json',
        'Accept': 'application/json'
    } : {};

    // Use the defined headers in the fetch call
    fetch(url, {
        method: method,
        headers: headers,
        body: hasFilters ? JSON.stringify(filters) : null
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(response => {
        window.currentPage = response.current_page;
        window.totalPages = response.last_page;

        // If no matches and we're not already showing all records
        if (hasFilters && response.data.length === 0 && !window.showingAllRecords) {
            window.showingAllRecords = true; // Set a flag that we're now showing all records
            alert("No matches found with the current filters. Displaying all records.");
            window.loadProducts(); // Load all products without filters
        } else {
            populateProductsTable(response.data || []);
            window.showingAllRecords = false; // Reset the flag if we are applying filters
        }
        renderPagination();
    })
    .catch(error => {
        console.error("Failed to load products", error);
        populateProductsTable([]); // Show empty state or handle error appropriately
    });
};

document.addEventListener('DOMContentLoaded', function () {
    window.loadProducts();
});


window.populateProductsTable = function(products) {
    console.log("[ProductsTable] populateProductsTable called with products:", products);

    let tableBody = document.getElementById('products-table-body');
    if (!tableBody) {
        console.error("Table body not found");
        return;
    }

    // Check if we are already showing all records to prevent redundant loads
    if (products.length === 0 && !window.showingAllRecords) {
        // Display a message when there are no matches with current filters
        tableBody.innerHTML = `<tr><td colspan="5">No matches found. Showing all records.</td></tr>`;

        // Set the flag to indicate we are now showing all records
        window.showingAllRecords = true;

        // Call loadProducts to load all records without any filters
        window.loadProducts();
    } else {
        // If products exist or we are already showing all records, populate the table with products
        tableBody.innerHTML = products.map(product => `
        <tr>
            <td><input type="radio" name="productSelect" value="${product['Código_Produto']}" onchange="selectProduct('${product['Código_Produto']}')"></td>
            <td>${product.Classificação}</td>
            <td>${product.longo}</td>
            <td>${product.freq}</td>
            <td>${product.alterado}</td>
        </tr>
    `).join('');

        // Reset the flag if filters are applied and products are found
        window.showingAllRecords = products.length > 0;
    }
};

window.selectProduct = function(productCode) {
    console.log("Selected product code: ", productCode);
    window.loadDataSeries(productCode);
};


function renderPagination() {
    const paginationDiv = document.getElementById('products-pagination');
    if (!paginationDiv) {
        console.error("Pagination div not found");
        return;
    }

    let html = '';
    if (window.currentPage > 1) {
        html += `<button onclick="window.loadProducts(${window.currentPage - 1}, window.currentFilters)">Previous</button>`;
    }

    html += `<span>Page ${window.currentPage} of ${window.totalPages}</span>`;

    if (window.currentPage < window.totalPages) {
        html += `<button onclick="window.loadProducts(${window.currentPage + 1}, window.currentFilters)">Next</button>`;
    }

    paginationDiv.innerHTML = html;
}


function setupDropdownFilters() {
    console.log("[ProductsTable] Setting up dropdown filters");

    fetch('/api/get-dropdown-data')
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error while fetching dropdown data! Status: ${response.status}`);
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

// Ensure that on document ready we reset the flag
document.addEventListener('DOMContentLoaded', function () {
    window.showingAllRecords = false; // Initialize to false
    window.loadProducts();
    setupDropdownFilters();
});
