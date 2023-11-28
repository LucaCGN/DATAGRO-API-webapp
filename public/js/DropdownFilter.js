// Function to update filters and fetch filtered products
function updateFilters() {
    console.log("Updating filters");
    const classification = document.getElementById('classification-select').value;
    const subproduct = document.getElementById('subproduct-select').value;
    const local = document.getElementById('local-select').value;

    console.log(`Filter parameters - Classification: ${classification}, Subproduct: ${subproduct}, Local: ${local}`);

    fetch(`/filter-products?classification=${classification}&subproduct=${subproduct}&local=${local}`)
        .then(response => {
            if (!response.ok) {
                console.error(`Error fetching filtered products: ${response.statusText}`);
            }
            return response.json();
        })
        .then(data => {
            console.log("Filter products API Response:", data);
            updateProductsTable(data.products);
        })
        .catch(error => console.error("Filter products API Error:", error));
}

// Function to update the products table
function updateProductsTable(products) {
    console.log("Updating products table with products:", products);
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

// Ensure the DOM is fully loaded before adding event listeners
document.addEventListener('DOMContentLoaded', function () {
    if (document.getElementById('classification-select')) {
        document.getElementById('classification-select').addEventListener('change', updateFilters);
    }

    if (document.getElementById('subproduct-select')) {
        document.getElementById('subproduct-select').addEventListener('change', updateFilters);
    }

    if (document.getElementById('local-select')) {
        document.getElementById('local-select').addEventListener('change', updateFilters);
    }
});
