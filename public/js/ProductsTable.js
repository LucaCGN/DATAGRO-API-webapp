// Function to fetch and populate products with pagination
function fetchAndPopulateProducts(page = 1, perPage = 10) {
    console.log(`[ProductsTable] Fetching products for page: ${page}, perPage: ${perPage}`);
    fetch(`/products?page=${page}&perPage=${perPage}`)
        .then(response => {
            if (!response.ok) {
                console.error(`[ProductsTable] Error fetching products: ${response.statusText}`);
                throw new Error(`Error fetching products: ${response.statusText}`);
            }
            return response.json();
        })
        .then(data => {
            console.log("[ProductsTable] Products API Response received:", data);
            populateProductsTable(data.data);
            renderPagination(data.pagination);
        })
        .catch(error => {
            console.error("[ProductsTable] Products API Error:", error);
        });
}

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

// Function to render pagination controls for products
function renderPagination(paginationData) {
    console.log("[ProductsTable] Rendering pagination controls with data:", paginationData);
    let paginationDiv = document.getElementById('products-pagination');
    paginationDiv.innerHTML = '';

    if (paginationData.current_page > 1) {
        let prevButton = document.createElement('button');
        prevButton.textContent = 'Previous';
        prevButton.onclick = () => fetchAndPopulateProducts(paginationData.current_page - 1);
        paginationDiv.appendChild(prevButton);
    }

    if (paginationData.current_page < paginationData.last_page) {
        let nextButton = document.createElement('button');
        nextButton.textContent = 'Next';
        nextButton.onclick = () => fetchAndPopulateProducts(paginationData.current_page + 1);
        paginationDiv.appendChild(nextButton);
    }
    console.log("[ProductsTable] Pagination controls rendered");
}

document.addEventListener('DOMContentLoaded', () => {
    console.log("[ProductsTable] DOMContentLoaded - starting product fetching");
    fetchAndPopulateProducts();
});
