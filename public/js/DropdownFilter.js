// DropdownFilter.js

// Convert frequency codes to full words at the top level so it's accessible by all functions
const freqToWord = {
    'D': 'Diário',
    'W': 'Semanal',
    'M': 'Mensal',
    'A': 'Anual'
};

// Function to dynamically populate dropdowns
window.populateDropdowns = function(data) {
    console.log("[DropdownFilter] Populating dropdowns with products data");

    const getUniqueValues = (values) => [...new Set(values)];

    // Populate each dropdown
    const dropdowns = {
        'classificacao-select': getUniqueValues(Object.values(data.classificacao)),
        'subproduto-select': getUniqueValues(Object.values(data.subproduto)),
        'local-select': getUniqueValues(Object.values(data.local)),
        'freq-select': getUniqueValues(Object.values(data.freq).map(code => freqToWord[code] || code)),
        'proprietario-select': getUniqueValues(Object.values(data.proprietario))
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
};

// Function to handle filter changes and fetch filtered products
function updateFilters() {
    console.log("[DropdownFilter] Updating filters");

    const classificacao = document.getElementById('classificacao-select').value;
    const subproduto = document.getElementById('subproduto-select').value;
    const local = document.getElementById('local-select').value;
    const freqValue = document.getElementById('freq-select').value;
    const proprietario = document.getElementById('proprietario-select').value;

    // Find the key for the selected frequency word
    const freq = Object.keys(freqToWord).find(key => freqToWord[key] === freqValue);

    console.log(`[DropdownFilter] Filter parameters: Classificação: ${classificacao}, Subproduto: ${subproduto}, Local: ${local}, Frequência: ${freq}, Proprietário: ${proprietario}`);

    fetch('/filter-products', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ classificacao, subproduto, local, freq, proprietario })
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
    const filters = ['classificacao-select', 'subproduto-select', 'local-select', 'freq-select', 'proprietario-select'];
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
});
