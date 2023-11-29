// DropdownFilter.js
// Function to update filters and fetch filtered products
function updateFilters() {
    console.log("[DropdownFilter] Updating filters");
    const produto = document.getElementById('produto-select').value;
    const subproduto = document.getElementById('subproduct-select').value;
    const local = document.getElementById('local-select').value;
    const freq = document.getElementById('freq-select').value;
    const proprietario = document.getElementById('proprietario-select').value;

    console.log(`[DropdownFilter] Filter parameters - Produto: ${produto}, Subproduto: ${subproduto}, Local: ${local}, Frequência: ${freq}, Proprietário: ${proprietario}`);

    fetch('/filter-products', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
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
    let tableBody = document.getElementById('products-table-body');
    tableBody.innerHTML = products.map(product => `
        <tr onclick="selectProduct(${product.id})">
            <td>${product.Código_Produto}</td>
            <td>${product.descr}</td>
            <td>${product.inserido}</td>
            <td>${product.alterado}</td>
        </tr>
    `).join('');
    console.log("[DropdownFilter] Products table updated");
}

// Function to populate dropdowns
function populateDropdowns(products) {
    const uniqueValues = (products, key) => [...new Set(products.map(product => product[key]))];

    const populateDropdown = (dropdownId, values) => {
        const dropdown = document.getElementById(dropdownId);
        values.forEach(value => {
            dropdown.add(new Option(value, value));
        });
    };

    populateDropdown('produto-select', uniqueValues(products, 'Código_Produto'));
    populateDropdown('subproduto-select', uniqueValues(products, 'Subproduto'));
    populateDropdown('local-select', uniqueValues(products, 'Local'));
    populateDropdown('freq-select', uniqueValues(products, 'Freq'));

    // Special logic for 'proprietario' dropdown
    const proprietarioOptions = uniqueValues(products, 'bolsa').map(value => value === 2 ? 'Sim' : 'Não');
    populateDropdown('proprietario-select', [...new Set(proprietarioOptions)]);
}

// This function should be called after products table is populated
// For now, it's called here for demonstration purposes
document.addEventListener('DOMContentLoaded', function () {
    // Assuming products data is available here as 'productsData'
    // populateDropdowns(productsData);
});
