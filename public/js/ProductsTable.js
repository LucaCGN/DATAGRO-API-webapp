// Function to fetch and populate products with pagination
function fetchAndPopulateProducts(page = 1, perPage = 10) {
    console.log(`[ProductsTable] Fetching products for page: ${page}, perPage: ${perPage}`);
    fetch(`/products?page=${page}&perPage=${perPage}`)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log("[ProductsTable] Products API Response received:", data);
            populateProductsTable(data.data);
            renderPagination(data); // Call renderPagination with the fetched data
        })
        .catch(error => {
            console.error("[ProductsTable] Products API Error:", error);
        });
}

// Global variable to track the currently selected product ID
let selectedProductId = null;

// Function to handle row selection
function selectProduct(productId) {
    // Check if we're unselecting the current product
    if (selectedProductId === productId) {
        selectedProductId = null;
        document.getElementById(`product-checkbox-${productId}`).checked = false;
    } else {
        // Unselect any previously selected checkbox
        if (selectedProductId !== null) {
            document.getElementById(`product-checkbox-${selectedProductId}`).checked = false;
        }
        selectedProductId = productId;
    }

    // Perform any additional logic needed when a product is selected
    console.log(`Product ${productId} selected`);
}

// Function to populate products table
function populateProductsTable(products) {
    console.log("[ProductsTable] Populating products table with:", products);
    let tableBody = document.getElementById('products-table-body');
    tableBody.innerHTML = products.map(product => `
        <tr>
            <td><input type="checkbox" id="product-checkbox-${product.id}" name="selectedProduct" ${
                selectedProductId === product.id ? 'checked' : ''
            } onclick="selectProduct(${product.id})"></td>
            <td>${product.Código_Produto}</td>
            <td>${product.descr}</td>
            <td>${product.inserido}</td>
            <td>${product.alterado}</td>
        </tr>
    `).join('');
    console.log("[ProductsTable] Products table populated");
}


// Function to update the current page indicator
function updateCurrentPageIndicator(currentPage, lastPage) {
    let currentPageIndicator = document.getElementById('current-page-indicator');
    if (currentPageIndicator) {
        currentPageIndicator.textContent = `Page ${currentPage} of ${lastPage}`;
    }
}

document.addEventListener('DOMContentLoaded', () => {
    fetchAndPopulateProducts();
});


// Function to render pagination controls for products
function renderPagination(paginationData) {
    let paginationDiv = document.getElementById('products-pagination');
    paginationDiv.innerHTML = ''; // Clear existing pagination controls

    // Previous button
    if (paginationData.current_page > 1) {
        paginationDiv.innerHTML += `<button onclick="fetchAndPopulateProducts(${paginationData.current_page - 1}, ${paginationData.per_page})">Previous</button>`;
    }

    // Current Page Indicator
    paginationDiv.innerHTML += `<span>Page ${paginationData.current_page} of ${paginationData.last_page}</span>`;

    // Next button
    if (paginationData.current_page < paginationData.last_page) {
        paginationDiv.innerHTML += `<button onclick="fetchAndPopulateProducts(${paginationData.current_page + 1}, ${paginationData.per_page})">Next</button>`;
    }
}
