// DropdownFilter.js

// Convert frequency codes to full words at the top level so it's accessible by all functions
const freqToWord = {
    'D': 'Diário',
    'W': 'Semanal',
    'M': 'Mensal',
    'A': 'Anual'
};

window.populateDropdowns = function(data) {
    console.log("Populating dropdowns with data", data);

    // Helper function to create a new option element
    const createOption = (value, text) => {
        const option = document.createElement('option');
        option.value = value;
        option.textContent = text || value;
        return option;
    };

    // Helper function to create a placeholder option
    const createPlaceholderOption = (placeholder) => {
        const option = document.createElement('option');
        option.value = '';
        option.textContent = placeholder;
        option.disabled = true; // Disable the placeholder option
        option.selected = true; // Set the placeholder option as selected by default
        option.hidden = true; // Hide the placeholder option
        return option;
    };

    // Define the dropdowns and their corresponding placeholder text
    const dropdowns = {
        'Classificação-select': {
            element: document.getElementById('Classificação-select'),
            placeholder: 'Produto'
        },
        'subproduto-select': {
            element: document.getElementById('subproduto-select'),
            placeholder: 'Subproduto'
        },
        'local-select': {
            element: document.getElementById('local-select'),
            placeholder: 'Local'
        },
        'freq-select': {
            element: document.getElementById('freq-select'),
            placeholder: 'Frequência',
            data: data['freq'].map(code => ({
                value: code, // the actual value to be sent to the backend
                text: freqToWord[code] || code // the text to show the user
            }))
        },
        'proprietario-select': {
            element: document.getElementById('proprietario-select'),
            placeholder: 'Proprietário'
        }
    };

        // Special handling for 'freq-select' dropdown to convert codes to words
        const freqDropdown = dropdowns['freq-select'].element;
        freqDropdown.innerHTML = ''; // Clear existing options
        freqDropdown.appendChild(createPlaceholderOption(dropdowns['freq-select'].placeholder));

        dropdowns['freq-select'].data.forEach(({value, text}) => {
            freqDropdown.appendChild(createOption(value, text));
        });

        // Set the selected value for 'freq-select' if one exists
        if (window.currentFilters && window.currentFilters['freq']) {
            // Map the frequency code to its word representation for the dropdown
            const freqWord = freqToWord[window.currentFilters['freq']] || window.currentFilters['freq'];
            freqDropdown.value = freqWord; // Set the dropdown to show the word to the user
        }

        // Add options to other dropdowns, excluding the 'freq' dropdown
        Object.entries(dropdowns).forEach(([key, dropdownInfo]) => {
            if (key !== 'freq-select') {
            const filterKey = key.replace('-select', '');
            const { element, placeholder } = dropdownInfo;

            // Clear previous options and append the placeholder option to the dropdown
            element.innerHTML = '';
            element.appendChild(createPlaceholderOption(placeholder));

            // Get the data for the current dropdown and add options
            data[filterKey].forEach(value => {
                element.appendChild(createOption(value, value));
            });

            // Set the selected value if it exists in currentFilters
            if (window.currentFilters && window.currentFilters[filterKey]) {
                const selectedValue = window.currentFilters[filterKey];
                const selectedOption = Array.from(element.options).find(option => option.value === selectedValue);
                if (selectedOption) {
                    element.value = selectedValue;
                } else {
                    // The selected value is not in the options list, append it
                    element.appendChild(createOption(selectedValue, selectedValue));
                    element.value = selectedValue;
                }
            }
        }
    });
};






window.updateFilters = async function() {
    console.log("[DropdownFilter] Starting filter update process");

    // Fetch current filter values from the DOM
    const ClassificaçãoElement = document.getElementById('Classificação-select');
    const subprodutoElement = document.getElementById('subproduto-select');
    const localElement = document.getElementById('local-select');
    const freqElement = document.getElementById('freq-select');
    const proprietarioElement = document.getElementById('proprietario-select');

    const Classificação = ClassificaçãoElement.value || null;
    const subproduto = subprodutoElement.value || null;
    const local = localElement.value || null;
    let proprietario = proprietarioElement.value || null;

    console.log("[DropdownFilter] Retrieved values from DOM elements:", {
        Classificação,
        subproduto,
        local,
        freq: freqElement.value,
        proprietario
    });

    // Retrieve the frequency value and convert it back to code if it's not the placeholder
    let freq = freqElement.options[freqElement.selectedIndex].value;
    if (freq && freqToWord[freq]) {
        freq = Object.keys(freqToWord).find(key => freqToWord[key] === freq) || freq;
    }


    // Check if the displayed text for 'proprietario' is the placeholder and set it to null if so
    if (proprietarioElement.selectedIndex === 0) {
        proprietario = null;
    }

    // Log current filter values
    console.log("[DropdownFilter] Filter values before removing nulls:", {
        Classificação,
        subproduto,
        local,
        freq,
        proprietario
    });

    // Prepare the filters to be applied, removing any that are null or empty
    const filterValues = {
        Classificação,
        subproduto,
        local,
        freq,
        proprietario
    };

    // Remove any filters that are null or empty
    Object.keys(filterValues).forEach(key => {
        if (filterValues[key] == null || filterValues[key] === '') {
            delete filterValues[key];
        }
    });

    console.log("[DropdownFilter] Filter values after removing nulls:", filterValues);

    // Update the window.currentFilters with the new values
    window.currentFilters = { ...window.currentFilters, ...filterValues };

    console.log("[DropdownFilter] Filter values after removing nulls:", window.currentFilters);

    try {
        // If current filters haven't changed, no need to update
        if (JSON.stringify(filterValues) === JSON.stringify(window.previousFilterValues)) {
            console.log("[DropdownFilter] No filter changes detected, skipping update");
            return;
        }

        // Store the current filters as previous filters to prevent duplicate calls
        window.previousFilterValues = { ...filterValues };

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

        if (!updateResponse.ok) {
            throw new Error(`HTTP error! status: ${updateResponse.status}`);
        }

        const updatedFilters = await updateResponse.json();
        if(updatedFilters) {
            window.populateDropdowns(updatedFilters);
            const filteredData = await fetchFilteredData(window.currentFilters);
            window.populateProductsTable(filteredData.data);
        } else {
            console.error("[DropdownFilter] Updated filters response is undefined.");
        }
    } catch (error) {
        console.error("[DropdownFilter] Error:", error);
    }
};

async function fetchFilteredData(filters) {
    const response = await fetch('/api/filter-products', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify(filters)
    });

    if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
    }

    return await response.json();
}


// Updated resetFilters function
// Updated resetFilters function
window.resetFilters = async function() {
    console.log("[DropdownFilter] Resetting filters");

    // Clear the current filters and previous filters to ensure a clean state
    window.currentFilters = {};
    window.previousFilterValues = {};

    // Reset the selected product and clear the DataSeries view
    window.selectedProductCode = null;
    clearDataSeriesView(); // Clear the DataSeries table
    updateSelectedProductName(); // Update the display to show the placeholder message

    // Fetch initial filter options and reset the products table
    try {
        const initialFilters = await window.getInitialFilterOptions();

        // Check for a valid response before attempting to reset dropdowns and products table
        if (initialFilters && typeof initialFilters === 'object') {
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
                    dropdown.selectedIndex = 0; // This sets the dropdown back to the first option, which is assumed to be the placeholder
                }
            });

            // Reset the products table
            window.populateProductsTable([]);

            // Re-populate dropdowns with initial filter options
            window.populateDropdowns(initialFilters);
        } else {
            console.error("[DropdownFilter] Failed to fetch initial filter options or received undefined.");
        }
    } catch (error) {
        console.error("[DropdownFilter] Error resetting filters:", error);
    } finally {
        console.log("[DropdownFilter] Filters have been reset");
    }
};

// Add the clearDataSeriesView and updateSelectedProductName function definitions if not already present
function clearDataSeriesView() {
    let dataSeriesBody = document.getElementById('data-series-body');
    if (dataSeriesBody) {
        dataSeriesBody.innerHTML = '';
    }
    console.log("[DataSeriesTable] Data series view cleared.");
}

function updateSelectedProductName() {
    let productNameDisplay = document.getElementById('selected-product-name');
    if (productNameDisplay) {
        productNameDisplay.textContent = 'Please select a product in the table above';
    }
}



window.getInitialFilterOptions = async function() {
    console.log("[DropdownFilter] Fetching initial filter options");

    // Check if initial filter options are already cached to prevent unnecessary fetches
    if (window.cachedInitialOptions) {
        console.log("[DropdownFilter] Using cached initial filter options");
        window.populateDropdowns(window.cachedInitialOptions);
        return;
    }

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

        // Validate the initial options and ensure 'proprietario' is handled correctly
        if (initialOptions && typeof initialOptions === 'object') {
            // If 'proprietario' is not an array or doesn't contain the expected options, log and handle the error
            if (!Array.isArray(initialOptions.proprietario) ||
                !initialOptions.proprietario.includes('Sim') ||
                !initialOptions.proprietario.includes('Não')) {
                console.error("[DropdownFilter] Invalid 'proprietario' options:", initialOptions.proprietario);
                // Default 'proprietario' to an empty array to prevent further errors
                initialOptions.proprietario = [];
            }

            // Cache the initial options for future use
            window.cachedInitialOptions = initialOptions;

            window.populateDropdowns(initialOptions);
            console.log("[DropdownFilter] Initial filter options fetched and dropdowns populated");
        } else {
            throw new Error("Invalid initial filter options received.");
        }
    } catch (error) {
        console.error("[DropdownFilter] Error fetching initial filter options:", error);
    }
};


// This function will ensure that the code inside will only be executed once the DOM is fully loaded
document.addEventListener('DOMContentLoaded', function() {
    // Only execute this block of code once
    if (window.hasInitialized) {
        return;
    }
    window.hasInitialized = true;

    // Fetch and populate initial filter options
    window.getInitialFilterOptions();

    // Attach an event listener to the reset button
    const resetButton = document.getElementById('reset-filters-btn');
    if (resetButton) {
        resetButton.addEventListener('click', function() {
            window.resetFilters();
            window.updateFilters(); // Now we are sure that updateFilters should be called after reset
        });
        console.log("[DropdownFilter] Reset button event listener attached");
    } else {
        console.error("[DropdownFilter] Reset button not found");
    }

    // Attach event listeners to filter dropdowns
    const filters = [
        'Classificação-select',
        'subproduto-select',
        'local-select',
        'freq-select',
        'proprietario-select'
    ];

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

