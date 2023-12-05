// DropdownFilter.js

// Convert frequency codes to full words at the top level so it's accessible by all functions
const freqToWord = {
    'D': 'Diário',
    'W': 'Semanal',
    'M': 'Mensal',
    'A': 'Anual'
};

// Function to dynamically populate dropdowns with selection and correct placeholder text
window.populateDropdowns = function(data) {
    console.log("Populating dropdowns with data", data);

    // Verify that data for each dropdown is an array and log if not
    if (!Array.isArray(data['Classificação']) || !Array.isArray(data.subproduto) ||
        !Array.isArray(data.local) || !Array.isArray(data.freq) ||
        !Array.isArray(data.proprietario)) {
        console.error("Expected data for dropdowns to be an array", data);
        return;
    }

    const createOption = (value, text) => {
        const option = document.createElement('option');
        option.value = value;
        option.textContent = text || value;
        return option;
    };

    const createNullOption = (placeholder) => createOption('', placeholder);

    // Define the dropdowns
    const dropdowns = {
        'Classificação-select': document.getElementById('Classificação-select'),
        'subproduto-select': document.getElementById('subproduto-select'),
        'local-select': document.getElementById('local-select'),
        'freq-select': document.getElementById('freq-select'),
        'proprietario-select': document.getElementById('proprietario-select')
    };

    // Reset dropdowns to ensure they are clear before adding new options
    Object.values(dropdowns).forEach(dropdown => dropdown.innerHTML = '');

    // Add options to dropdowns
    Object.entries(dropdowns).forEach(([key, dropdown]) => {
        const filterKey = key.replace('-select', '');
        dropdown.appendChild(createNullOption(`Select ${filterKey}...`));

        let optionsArray = data[filterKey];

        // If current filter value is not in options, prepend it to the options array
        if (window.currentFilters[filterKey] && !optionsArray.includes(window.currentFilters[filterKey])) {
            optionsArray = [window.currentFilters[filterKey], ...optionsArray];
        }

        optionsArray.forEach(value => {
            dropdown.appendChild(createOption(value));
        });

        // Set the selected value if it exists in currentFilters
        if (window.currentFilters[filterKey]) {
            dropdown.value = window.currentFilters[filterKey];
        }
    });
};


window.updateFilters = async function() {
    console.log("[DropdownFilter] Starting filter update process");

    // Fetch current filter values from the DOM once
    const ClassificaçãoElement = document.getElementById('Classificação-select');
    const subprodutoElement = document.getElementById('subproduto-select');
    const localElement = document.getElementById('local-select');
    const freqElement = document.getElementById('freq-select');
    const proprietarioElement = document.getElementById('proprietario-select');

    const Classificação = ClassificaçãoElement.value || null;
    const subproduto = subprodutoElement.value || null;
    const local = localElement.value || null;
    let freq = freqElement.value || null;
    let proprietario = proprietarioElement.value || null;

    // Convert frequency and owner values to codes if needed
    freq = Object.keys(freqToWord).find(key => freqToWord[key] === freq) || freq; // Map from full word to code
    proprietario = proprietario === 'Sim' ? 2 : (proprietario === 'Não' ? 1 : null); // Convert to number

    // Log current filter values
    console.log("[DropdownFilter] Current filter values:", {
        Classificação,
        subproduto,
        local,
        freq,
        proprietario
    });

    // Prepare the filters to be applied, removing any that are null
    const filterValues = { Classificação, subproduto, local, freq, proprietario };
    Object.keys(filterValues).forEach(key => filterValues[key] == null && delete filterValues[key]);

    // Update the window.currentFilters before fetching updated filter options
    window.currentFilters = { ...window.currentFilters, ...filterValues };
    console.log("[DropdownFilter] Filter values after removing nulls:", filterValues);

    try {
        // Send the selected filters and get updated options for other filters
        const updateResponse = await fetch('/api/filters/updated', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify(window.currentFilters)
        });

        // Handle non-ok response
        if (!updateResponse.ok) {
            throw new Error(`HTTP error! status: ${updateResponse.status}`);
        }

        // Get and populate dropdowns with updated filter options
        const updatedFilters = await updateResponse.json();
        window.populateDropdowns(updatedFilters);

        // Fetch and update the products table with the filtered data
        const filterResponse = await fetch('/api/filter-products', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify(window.currentFilters)
        });

        // Handle non-ok response
        if (!filterResponse.ok) {
            throw new Error(`HTTP error! status: ${filterResponse.status}`);
        }

        // Populate the products table with the filtered data
        const filteredData = await filterResponse.json();
        await window.populateProductsTable(filteredData.data);

    } catch (error) {
        console.error("[DropdownFilter] Error:", error);
    }
 };






window.resetFilters = function() {
    console.log("[DropdownFilter] Resetting filters");

    // Define the IDs of the dropdown elements
    const dropdownIds = [
        'Classificação-select',
        'subproduto-select',
        'local-select',
        'freq-select',
        'proprietario-select'
    ];

    // Reset each dropdown to its default state
    dropdownIds.forEach(id => {
        const dropdown = document.getElementById(id);
        if (dropdown) {
            dropdown.selectedIndex = 0; // This sets the dropdown back to the first option, typically "Select..."
        }
    });

    // Clear the current filters
    window.currentFilters = {};

    // Fetch initial filter options and reset the products table
    if (typeof window.getInitialFilterOptions === "function") {
        window.getInitialFilterOptions().then(initialFilters => {
            window.populateDropdowns(initialFilters);
        });
    } else {
        console.error("getInitialFilterOptions function is not defined.");
    }

    window.populateProductsTable([]);

    console.log("[DropdownFilter] Filters have been reset");
};



window.getInitialFilterOptions = async function() {
    console.log("[DropdownFilter] Fetching initial filter options");
    try {
        const response = await fetch('/api/initial-filter-options', {
            method: 'GET',
            headers: {
                'Accept': 'application/json'
            }
        });
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        const initialOptions = await response.json();
        window.populateDropdowns(initialOptions);
        console.log("[DropdownFilter] Initial filter options fetched and dropdowns populated");
    } catch (error) {
        console.error("[DropdownFilter] Error fetching initial filter options:", error);
    }
};

// Make sure to initialize filters and attach event listeners once on load
document.addEventListener('DOMContentLoaded', (function() {
    let executed = false;
    return function() {
        if (!executed) {
            executed = true;

            // Fetch and populate initial filter options
            window.getInitialFilterOptions();

            // Attach event listener to the reset button
            const resetButton = document.getElementById('reset-filters-btn');
            if (resetButton) {
                resetButton.addEventListener('click', function() {
                    window.resetFilters();
                    // If you want to update filters after reset, uncomment the following line:
                    window.updateFilters();
                });
                console.log("[DropdownFilter] Reset button event listener attached");
            } else {
                console.error("[DropdownFilter] Reset button not found");
            }

            // Attach event listeners to filter dropdowns
            const filters = ['Classificação-select', 'subproduto-select', 'local-select', 'freq-select', 'proprietario-select'];
            filters.forEach(filterId => {
                const filterElement = document.getElementById(filterId);
                if (filterElement) {
                    filterElement.addEventListener('change', window.updateFilters);
                    console.log(`[DropdownFilter] Event listener added for: ${filterId}`);
                } else {
                    console.error(`[DropdownFilter] Filter element not found: ${filterId}`);
                }
            });
        }
    };
})());
