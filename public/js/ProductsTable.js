// ProductsTable.js

console.log('ProductsTable.js loaded');

let selectedProductCode = null;
window.currentFilters = {};
window.showingAllRecords = false;

// Function to load products based on the current page and filters
window.loadProducts = async function(page = 1, filters = window.currentFilters) {
    console.log(`Fetching products for page: ${page} with filters`, filters);

    const hasFilters = Object.keys(filters).length > 0;
    const query = new URLSearchParams({ ...filters, page }).toString();
    const url = hasFilters ? `/api/filter-products` : `/products?page=${page}`;
    const method = hasFilters ? 'POST' : 'GET';

    const headers = {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
    };

    console.log("[ProductsTable] Sending request to:", url, " with method:", method, " and headers:", headers);

    try {
        const response = await fetch(url, {
            method: method,
            headers: headers,
            body: hasFilters ? JSON.stringify(filters) : null
        });

        console.log("[ProductsTable] Response received with status:", response.status);

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();
        console.log("[ProductsTable] Products data received:", data);

        window.populateProductsTable(data.data || []);
        window.currentPage = data.current_page;
        window.totalPages = data.last_page;
        window.renderPagination();
    } catch (error) {
        console.error("Failed to load products", error);
        window.populateProductsTable([]); // Show empty state or handle error appropriately
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
        tableBody.innerHTML = products.map(product => `
            <tr>
                <td><input type="radio" name="productSelect" value="${product['Código_Produto']}" onchange="selectProduct('${product['Código_Produto']}')"></td>
                <td>${product.Classificação}</td>
                <td>${product.longo}</td>
                <td>${product.freq}</td>
                <td>${product.alterado}</td>
            </tr>
        `).join('');
        console.log("[ProductsTable] Products table populated with products.");
    } else {
        // Show a message or an empty state if there are no products
        tableBody.innerHTML = `<tr><td colspan="5">No products found.</td></tr>`;
        console.log("[ProductsTable] No products found message displayed.");
    }
};

// Pagination rendering function
window.renderPagination = function() {
    console.log("[ProductsTable] Rendering pagination.");

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
    console.log("[ProductsTable] Pagination rendered.");
};

// Setting up dropdown filters
window.setupDropdownFilters = async function() {
    console.log("[ProductsTable] Setting up dropdown filters");

    try {
        const response = await fetch('/api/filters', {
            method: 'GET',
            headers: { 'Accept': 'application/json' }
        });

        console.log("[ProductsTable] Fetching filter data with response status:", response.status);

        if (!response.ok) {
            console.error(`[ProductsTable] HTTP error while fetching dropdown data! Status: ${response.status}`);
            return;
        }

        const data = await response.json();
        console.log("[ProductsTable] Dropdown filters set up with data:", JSON.stringify(data, null, 2));
        window.populateDropdowns(data);
    } catch (error) {
        console.error("[ProductsTable] Failed to fetch dropdown data", error);
    }
};


document.addEventListener('DOMContentLoaded', function () {
    console.log("[ProductsTable] DOMContentLoaded - Starting to load products and set up filters.");
    window.loadProducts();
    window.setupDropdownFilters();
});


window.selectProduct = function(productCode) {
    console.log("Selected product code: ", productCode);
    window.loadDataSeries(productCode);
};

// New function to update dropdowns based on current filters
window.updateDropdowns = async function(currentFilters) {
    console.log("[ProductsTable] Updating dropdowns based on current filters", currentFilters);

    // Remove any null or empty filter values
    const nonNullFilters = Object.fromEntries(Object.entries(currentFilters).filter(([_, v]) => v != null));
    console.log("[ProductsTable] Filters after removing nulls:", nonNullFilters);

    try {
        const response = await fetch('/api/filters/updated', {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(nonNullFilters)
        });

        console.log("[ProductsTable] Fetching updated dropdown data with response status:", response.status);

        if (!response.ok) {
            throw new Error(`HTTP error while fetching updated dropdown data! Status: ${response.status}`);
        }

        const data = await response.json();
        window.populateDropdowns(data);
        console.log("[ProductsTable] Dropdowns updated with new data:", data);
    } catch (error) {
        console.error("Failed to fetch updated dropdown data", error);
    }
}

// Ensure that on document ready we reset the flag
document.addEventListener('DOMContentLoaded', function () {
    window.showingAllRecords = false; // Initialize to false
    window.loadProducts();
    setupDropdownFilters();
});
