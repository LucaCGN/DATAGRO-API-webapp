// Function to fetch and populate products with pagination
function fetchAndPopulateProducts(page = 1, perPage = 10) {
    console.log(`[ProductsTable] Fetching products for page: ${page}, perPage: ${perPage}`);
    fetch(`/products?page=${page}&perPage=${perPage}`)
        .then(response => response.json())
        .then(data => {
            console.log("[ProductsTable] Products API Response received:", data);
            populateProductsTable(data.data);
            renderPagination(data);
        })
        .catch(error => console.error("[ProductsTable] Products API Error:", error));
}

// Function to populate products table
function populateProductsTable(products) {
    console.log("[ProductsTable] Populating products table with:", products);
    let tableBody = document.getElementById('products-table-body');
    tableBody.innerHTML = products.map(product => `
        <tr>
            <td><input type="checkbox" name="selectedProduct" value="${product.id}"></td>
            <td>${product.Código_Produto}</td>
            <td>${product.descr}</td>
            <td>${product.inserido}</td>
            <td>${product.alterado}</td>
        </tr>
    `).join('');
    console.log("[ProductsTable] Products table populated");
}

// Function to render pagination controls for products
function renderPagination(paginationData) {
    console.log("[ProductsTable] Rendering pagination controls with data:", paginationData);
    let paginationDiv = document.getElementById('products-pagination');
    paginationDiv.innerHTML = '';

    // Previous button
    if (paginationData.current_page > 1) {
        paginationDiv.innerHTML += `<button onclick="fetchAndPopulateProducts(${paginationData.current_page - 1})">Previous</button>`;
    }

    // Page numbers
    for (let page = 1; page <= paginationData.last_page; page++) {
        paginationDiv.innerHTML += `<button onclick="fetchAndPopulateProducts(${page})">${page}</button>`;
    }

    // Next button
    if (paginationData.current_page < paginationData.last_page) {
        paginationDiv.innerHTML += `<button onclick="fetchAndPopulateProducts(${paginationData.current_page + 1})">Next</button>`;
    }
    console.log("[ProductsTable] Pagination controls rendered");
}

document.addEventListener('DOMContentLoaded', () => {
    console.log("[ProductsTable] DOMContentLoaded - starting product fetching");
    fetchAndPopulateProducts();
});
