// DropdownFilter.js

// Convert frequency codes to full words at the top level so it's accessible by all functions
const freqToWord = {
    'D': 'Diário',
    'W': 'Semanal',
    'M': 'Mensal',
    'A': 'Anual'
};

// Function to dynamically populate dropdowns with null selection
window.populateDropdowns = function(data) {
    console.log("[DropdownFilter] Populating dropdowns with products data", JSON.stringify(data, null, 2));

    // Define null option HTML
    const nullOptionHTML = '<option value="">Select...</option>';

    const dropdowns = {
        'classificacao-select': [nullOptionHTML].concat(data.classificacao),
        'subproduto-select': [nullOptionHTML].concat(data.subproduto),
        'local-select': [nullOptionHTML].concat(data.local),
        'freq-select': [nullOptionHTML].concat(data.freq.map(code => freqToWord[code] || code)),
        'proprietario-select': [nullOptionHTML].concat(data.proprietario.map(item => item === 'Sim' ? 'Sim' : 'Não'))

    };

    Object.entries(dropdowns).forEach(([dropdownId, values]) => {
        const dropdown = document.getElementById(dropdownId);
        if (dropdown) {
            // Ensure only one 'Select...' option is present
            dropdown.innerHTML = '';
            dropdown.appendChild(document.createElement('option')).setAttribute('value', '');

            // Append the rest of the options
            values.slice(1).forEach(value => {
                const option = document.createElement('option');
                option.value = value;
                option.textContent = value;
                dropdown.appendChild(option);
            });

            console.log(`[DropdownFilter] Dropdown populated: ${dropdownId} with values:`, values);
        } else {
            console.error(`[DropdownFilter] Dropdown not found: ${dropdownId}`);
        }
    });
};



// Function to handle filter changes and fetch filtered products
window.updateFilters = async function() {
    console.log("[DropdownFilter] Starting filter update process");

    const classificacao = document.getElementById('classificacao-select').value || null;
    const subproduto = document.getElementById('subproduto-select').value || null;
    const local = document.getElementById('local-select').value || null;
    const freqValue = document.getElementById('freq-select').value || null;
    const proprietarioValue = document.getElementById('proprietario-select').value || null;

    console.log("[DropdownFilter] Current filter values:", {
        classificacao,
        subproduto,
        local,
        freqValue,
        proprietarioValue
    });

    const bolsa = proprietarioValue === 'Sim' ? 2 : (proprietarioValue === 'Não' ? 1 : null);
    const freq = freqValue ? Object.keys(freqToWord).find(key => freqToWord[key] === freqValue) : null;

    const filterValues = { classificacao, subproduto, local, freq, bolsa };

    console.log("[DropdownFilter] Filters to be applied:", filterValues);

    Object.keys(filterValues).forEach(key => filterValues[key] == null && delete filterValues[key]);

    console.log("[DropdownFilter] Filter values after removing nulls:", filterValues);

    window.currentFilters = filterValues;

    console.log("[DropdownFilter] Stored current filters:", window.currentFilters);

    try {
        console.log("[DropdownFilter] Sending request to filter products with filters:", filterValues);

        const response = await fetch('/api/filter-products', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify(filterValues)
        });

        console.log("[DropdownFilter] Received response status:", response.status);

        if (!response.ok) {
            console.error(`[DropdownFilter] HTTP error! status: ${response.status}`);
            return;
        }

        const data = await response.json();
        console.log("[DropdownFilter] Filtered products received:", JSON.stringify(data, null, 2));
        await window.populateProductsTable(data.data);
    } catch (error) {
        console.error("[DropdownFilter] Filter products API Error:", error);
    }
};


// Event listeners for each filter dropdown
document.addEventListener('DOMContentLoaded', function () {
    const filters = ['classificacao-select', 'subproduto-select', 'local-select', 'freq-select', 'proprietario-select'];
    filters.forEach(filterId => {
        const filterElement = document.getElementById(filterId);
        if (filterElement) {
            filterElement.addEventListener('change', window.updateFilters);
            console.log(`[DropdownFilter] Event listener added for: ${filterId}`);
        } else {
            console.error(`[DropdownFilter] Filter element not found: ${filterId}`);
        }
    });
});
