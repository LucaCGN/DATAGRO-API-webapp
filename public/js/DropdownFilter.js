// DropdownFilter.js

// Function to dynamically populate dropdowns
function populateDropdowns(productsData) {
    console.log("[DropdownFilter] Populating dropdowns with products data");

    const getUniqueValues = (data, key) => [...new Set(data.map(item => item[key]))];

    // Populate each dropdown
    const dropdowns = {
        'produto-select': getUniqueValues(productsData, 'Código_Produto'),
        'subproduto-select': getUniqueValues(productsData, 'Subproduto'),
        'local-select': getUniqueValues(productsData, 'Local'),
        'freq-select': getUniqueValues(productsData, 'Freq'),
        // 'proprietario-select': Add logic if needed
    };

    Object.entries(dropdowns).forEach(([dropdownId, values]) => {
        const dropdown = document.getElementById(dropdownId);
        if (dropdown) {
            values.forEach(value => {
                dropdown.add(new Option(value, value));
            });
            console.log(`[DropdownFilter] Dropdown populated: ${dropdownId}`);
        } else {
            console.error(`[DropdownFilter] Dropdown not found: ${dropdownId}`);
        }
    });
}

// Function to handle filter changes and fetch filtered products
function updateFilters() {
    console.log("[DropdownFilter] Updating filters");

    const produto = document.getElementById('produto-select').value;
    const subproduto = document.getElementById('subproduto-select').value;
    const local = document.getElementById('local-select').value;
    const freq = document.getElementById('freq-select').value;
    const proprietario = document.getElementById('proprietario-select').value;

    console.log(`[DropdownFilter] Filter parameters: Produto: ${produto}, Subproduto: ${subproduto}, Local: ${local}, Frequência: ${freq}, Proprietário: ${proprietario}`);

    fetch('/filter-products', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ produto, subproduto, local, freq, proprietario })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        console.log("[DropdownFilter] Filtered products received:", data.products);
        if (data.products) {
            populateProductsTable(data.products);
        } else {
            console.error("[DropdownFilter] No products received after filter update");
        }
    })
    .catch(error => {
        console.error("[DropdownFilter] Filter products API Error:", error);
    });
}

// Setting up event listeners for each filter
document.addEventListener('DOMContentLoaded', function () {
    const filters = ['produto-select', 'subproduto-select', 'local-select', 'freq-select', 'proprietario-select'];
    filters.forEach(filterId => {
        const filterElement = document.getElementById(filterId);
        if (filterElement) {
            filterElement.addEventListener('change', () => {
                console.log(`[DropdownFilter] Filter changed: ${filterId}`);
                updateFilters();
            });
            console.log(`[DropdownFilter] Event listener added for: ${filterId}`);
        } else {
            console.error(`[DropdownFilter] Filter element not found: ${filterId}`);
        }
    });

    // Initialize dropdowns with static or prefetched data if needed
    // fetch('/api/get-dropdown-data').then(...).then(data => populateDropdowns(data));
});
