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

// Function to handle filter changes and fetch filtered products
function updateFilters() {
    console.log("[DropdownFilter] Updating filters");

    // Existing filter values retrieval
    const classificacao = document.getElementById('classificacao-select').value;
    const subproduto = document.getElementById('subproduto-select').value;
    const local = document.getElementById('local-select').value;
    const freqValue = document.getElementById('freq-select').value;

    // Convert 'Proprietário' back to 'bolsa' value
    const proprietarioValue = document.getElementById('proprietario-select').value;
    const bolsa = proprietarioValue === 'sim' ? 2 : (proprietarioValue === 'nao' ? 1 : '');

    // Convert frequency word to code
    const freq = Object.keys(freqToWord).find(key => freqToWord[key] === freqValue);

    console.log(`[DropdownFilter] Filter parameters: Classificação: ${classificacao}, Subproduto: ${subproduto}, Local: ${local}, Frequência: ${freq}, Bolsa: ${bolsa}`);

    const requestBody = JSON.stringify({ classificacao, subproduto, local, freq, bolsa }); // Use 'bolsa' instead of 'proprietario'
    console.log(`[DropdownFilter] AJAX request body: ${requestBody}`);

    fetch('/filter-products', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: requestBody
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        console.log("[DropdownFilter] Filtered products received:", data);
        if (data && data.data && Array.isArray(data.data)) {
            window.populateProductsTable(data.data);
        } else {
            console.error("[DropdownFilter] No products received or invalid data format after filter update", data);
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
