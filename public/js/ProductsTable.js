// Function to fetch and populate products with pagination
function fetchAndPopulateProducts(page = 1, perPage = 10) {
    console.log(`Fetching products for page: ${page}, perPage: ${perPage}`); // Log the function call
    fetch(`/products?page=${page}&perPage=${perPage}`)
        .then(response => {
            if (!response.ok) {
                console.error(`Error fetching products: ${response.statusText}`);
            }
            return response.json();
        })
        .then(data => {
            console.log("Products API Response:", data); // Log API response
            populateProductsTable(data.products);
            renderPagination(data.pagination);
        })
        .catch(error => console.error("Products API Error:", error)); // Log any fetch errors
}

// Function to populate products table
function populateProductsTable(products) {
    console.log("Populating products table with:", products); // Log the products being rendered
    let tableBody = document.getElementById('products-table-body');
    tableBody.innerHTML = products.map(product => `
        <tr onclick="selectProduct(${product.id})">
            <td>${product.nome}</td>
            <td>${product.freq}</td>
            <td>${product.inserido}</td>
            <td>${product.alterado}</td>
        </tr>
    `).join('');
}

// Function to render pagination controls
function renderPagination(paginationData) {
    console.log("Rendering pagination with data:", paginationData); // Log pagination data
    let paginationDiv = document.getElementById('products-pagination');
    paginationDiv.innerHTML = '';

    // Create previous button if needed
    if (paginationData.currentPage > 1) {
        let prevButton = document.createElement('button');
        prevButton.textContent = 'Previous';
        prevButton.onclick = () => {
            console.log(`Previous page button clicked, going to page ${paginationData.currentPage - 1}`); // Log button click
            fetchAndPopulateProducts(paginationData.currentPage - 1);
        };
        paginationDiv.appendChild(prevButton);
    }

    // Create next button if needed
    if (paginationData.currentPage < paginationData.lastPage) {
        let nextButton = document.createElement('button');
        nextButton.textContent = 'Next';
        nextButton.onclick = () => {
            console.log(`Next page button clicked, going to page ${paginationData.currentPage + 1}`); // Log button click
            fetchAndPopulateProducts(paginationData.currentPage + 1);
        };
        paginationDiv.appendChild(nextButton);
    }
}
