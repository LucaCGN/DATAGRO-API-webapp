function updateFilters() {
    console.log("Updating filters");  // Log the function call
    const classification = document.getElementById('classification-select').value;
    const subproduct = document.getElementById('subproduct-select').value;
    const local = document.getElementById('local-select').value;

    console.log(`Filter parameters - Classification: ${classification}, Subproduct: ${subproduct}, Local: ${local}`);  // Log filter parameters

    fetch(`/filter-products?classification=${classification}&subproduct=${subproduct}&local=${local}`)
        .then(response => {
            if (!response.ok) {
                console.error(`Error fetching filtered products: ${response.statusText}`);
            }
            return response.json();
        })
        .then(data => {
            console.log("Filter products API Response:", data);  // Log API response
            updateProductsTable(data.products);
        })
        .catch(error => console.error("Filter products API Error:", error)); // Log any fetch errors
}

document.getElementById('classification-select').addEventListener('change', () => {
    console.log("Classification select changed");  // Log select change
    updateFilters();
});
document.getElementById('subproduct-select').addEventListener('change', () => {
    console.log("Subproduct select changed");  // Log select change
    updateFilters();
});
document.getElementById('local-select').addEventListener('change', () => {
    console.log("Local select changed");  // Log select change
    updateFilters();
});

function updateProductsTable(products) {
    console.log("Updating products table with products:", products);  // Log the products being rendered
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
