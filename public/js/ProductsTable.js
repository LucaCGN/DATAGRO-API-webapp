// ProductsTable.js

console.log('ProductsTable.js loaded');

let selectedProductCode = null;
window.currentFilters = {};
window.showingAllRecords = false;

// Function to load products based on the current page and filters
window.loadProducts = async function(page = 1, filters = window.currentFilters) {
    console.log(`Fetching products for page: ${page} with filters`, filters);

    // Building the query URL
    const query = new URLSearchParams(filters).toString();
    const url = `/products?page=${page}&${query}`;

    // Headers for the fetch call
    const headers = {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
    };

    // Fetching products
    console.log("[ProductsTable] Fetching products with URL:", url);
    try {
        const response = await fetch(url, {
            method: 'GET',
            headers: headers,
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();
        console.log("[ProductsTable] Products data received:", data);
        populateProductsTable(data.data || []);
        renderPagination(data.current_page, data.last_page);
    } catch (error) {
        console.error("[ProductsTable] Failed to load products", error);
        populateProductsTable([]); // Show empty state or handle error appropriately
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

    // Reset selected product and clear DataSeries view if products list changes
    if (selectedProductCode !== null) {
        selectedProductCode = null;
        clearDataSeriesView(); // Function to clear DataSeries view
    }

    // Clear the table before populating new data
    tableBody.innerHTML = '';

    // Only populate if there are products
    if (products.length > 0) {
        tableBody.innerHTML = products.map(product => {
            // Convert the frequency code to the corresponding word
            const freqWord = freqToWord[product.freq] || product.freq;

            return `
                <tr>
                   <td><input type="radio" name="productSelect" value="${product['Código_Produto']}" onchange="selectProduct('${product['Código_Produto']}')"></td>
                   <td>${product.Classificação}</td>
                   <td>${product.longo}</td>
                   <td>${freqWord}</td>
                   <td>${product.alterado}</td>
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


window.selectProduct = function(productCode) {
    console.log("Selected product code: ", productCode);
    if (selectedProductCode !== productCode) {
        selectedProductCode = productCode;
        window.loadDataSeries(productCode);
    } else {
        selectedProductCode = null;
        clearDataSeriesView(); // Clear DataSeries if the same product is unselected
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
        if (selectedProductCode) {
            // Find the selected product from the loaded products array
            const selectedProduct = window.loadedProducts.find(product => product['Código_Produto'] === selectedProductCode);
            if (selectedProduct) {
                productNameDisplay.textContent = `DataSeries for: ${selectedProduct.longo}`;
            }
        } else {
            productNameDisplay.textContent = 'Please select a product in the table above';
        }
    }
}

document.addEventListener('DOMContentLoaded', function () {
    console.log("[ProductsTable] Page loaded - Starting to load products.");
    loadProducts();
    updateSelectedProductName(); // Ensure placeholder is displayed initially
});

