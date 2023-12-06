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

    // Helper function to create a new option element
    const createOption = (value, text) => {
        const option = document.createElement('option');
        option.value = value;
        option.textContent = text || value;
        return option;
    };

    // Helper function to create a placeholder option
    const createNullOption = (placeholder) => {
        const option = createOption('', placeholder);
        option.disabled = true; // Disable the placeholder option
        option.selected = true; // Set the placeholder option as selected by default
        return option;
    };

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
        dropdown.appendChild(createNullOption(`Select ${filterKey}...`)); // Create and append the placeholder

        let optionsArray = data[filterKey];

        // Prepend the current filter value to the options array if it's not present
        if (window.currentFilters[filterKey] && !optionsArray.includes(window.currentFilters[filterKey])) {
            optionsArray = [window.currentFilters[filterKey], ...optionsArray];
        }

        optionsArray.forEach(value => {
            // Append actual options to the dropdown
            dropdown.appendChild(createOption(value));
        });

        // Set the selected value if it exists in currentFilters and is not the placeholder
        if (window.currentFilters[filterKey] && optionsArray.includes(window.currentFilters[filterKey])) {
            dropdown.value = window.currentFilters[filterKey];
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

    let freq = freqElement.value;
    console.log("[DropdownFilter] Initial freq value:", freq);




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

