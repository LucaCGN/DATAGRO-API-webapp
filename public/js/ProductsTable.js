// Function to fetch and populate products with pagination
function fetchAndPopulateProducts(page = 1, perPage = 10) {
    console.log(`Fetching products for page: ${page}, perPage: ${perPage}`);
    fetch(`/products?page=${page}&perPage=${perPage}`)
        .then(response => {
            if (!response.ok) {
                console.error(`Error fetching products: ${response.statusText}`);
            }
            return response.json();
        })
        .then(data => {
            console.log("Products API Response:", data);
            populateProductsTable(data.products);
            renderPagination(data.pagination);
        })
        .catch(error => console.error("Products API Error:", error));
}

// Function to populate products table
function populateProductsTable(products) {
    console.log("Populating products table with:", products);
    let tableBody = document.getElementById('products-table-body');
    tableBody.innerHTML = products.map(product => `
        <tr onclick="selectProduct(${product.id})">
            <td>${product.Código_Produto}</td>
            <td>${product.descr}</td>
            <td>${product.inserido}</td>
            <td>${product.alterado}</td>
        </tr>
    `).join('');
}

// Function to render pagination controls
function renderPagination(paginationData) {
    console.log("Rendering pagination with data:", paginationData);
    let paginationDiv = document.getElementById('products-pagination');
    paginationDiv.innerHTML = '';

    if (paginationData.currentPage > 1) {
        let prevButton = document.createElement('button');
        prevButton.textContent = 'Previous';
        prevButton.onclick = () => fetchAndPopulateProducts(paginationData.currentPage - 1);
        paginationDiv.appendChild(prevButton);
    }

    if (paginationData.currentPage < paginationData.lastPage) {
        let nextButton = document.createElement('button');
        nextButton.textContent = 'Next';
        nextButton.onclick = () => fetchAndPopulateProducts(paginationData.currentPage + 1);
        paginationDiv.appendChild(nextButton);
    }
}

document.addEventListener('DOMContentLoaded', () => {
    fetchAndPopulateProducts();
});
