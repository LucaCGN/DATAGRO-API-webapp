// Function to update filters and fetch filtered products
function updateFilters() {
    console.log("[DropdownFilter] Updating filters");
    const produto = document.getElementById('produto-select').value;
    const subproduto = document.getElementById('subproduct-select').value;
    const local = document.getElementById('local-select').value;
    const freq = document.getElementById('freq-select').value;
    const proprietario = document.getElementById('proprietario-select').value;

    console.log(`[DropdownFilter] Filter parameters - Produto: ${produto}, Subproduto: ${subproduto}, Local: ${local}, Frequência: ${freq}, Proprietário: ${proprietario}`);

    // Updated to handle CSRF token and content type
    fetch('/filter-products', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'), // Assumes a meta tag with the CSRF token is present
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            produto: produto,
            subproduto: subproduto,
            local: local,
            freq: freq,
            proprietario: proprietario
        })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        console.log("[DropdownFilter] Filter products API Response:", data);
        updateProductsTable(data.products);
    })
    .catch(error => {
        console.error("[DropdownFilter] Filter products API Error:", error);
    });
}

// Function to update the products table based on filters
function updateProductsTable(products) {
    console.log("[DropdownFilter] Updating products table with products:", products);
    let tableBody = document.getElementById('products-table-body');
    tableBody.innerHTML = products.map(product => `
        <tr onclick="selectProduct(${product.id})">
            <td>${product.nome}</td>
            <td>${product.freq}</td>
            <td>${product.inserido}</td>
            <td>${product.alterado}</td>
        </tr>
    `).join('');
    console.log("[DropdownFilter] Products table updated");
}

// Event listeners for dropdown filters
document.addEventListener('DOMContentLoaded', function () {
    console.log("[DropdownFilter] Setting up filter dropdown event listeners");
    const filters = ['produto-select', 'subproduct-select', 'local-select', 'freq-select', 'proprietario-select'];
    filters.forEach(filterId => {
        const filterElement = document.getElementById(filterId);
        if (filterElement) {
            filterElement.addEventListener('change', updateFilters);
        }
    });

    // Trigger filter update on load to populate the products table
    updateFilters();
});
