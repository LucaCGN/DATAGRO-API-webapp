## public/js/DropdownFilter.js
```
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
            // Ensure 'Sim' and 'Não' are correctly displayed for 'proprietario'
            if (filterKey === 'proprietario') {
                dropdown.appendChild(createOption(value, value));
            } else {
                dropdown.appendChild(createOption(value));
            }
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

    // Get the displayed text for 'proprietario', which should be 'Sim' or 'Não'
    let proprietario = proprietarioElement.options[proprietarioElement.selectedIndex].text;

    // Convert frequency to code if needed
    freq = Object.keys(freqToWord).find(key => freqToWord[key] === freq) || freq;

    // Log current filter values
    console.log("[DropdownFilter] Current filter values:", {
        Classificação,
        subproduto,
        local,
        freq,
        proprietario // Log the text value that will be sent to the backend
    });

    // Prepare the filters to be applied, removing any that are null
    const filterValues = {
        Classificação,
        subproduto,
        local,
        freq,
        proprietario // Use the text value for 'proprietario'
    };

    // Remove any filters that are null or empty
    Object.keys(filterValues).forEach(key => filterValues[key] == null && delete filterValues[key]);

    // Update the window.currentFilters with the new values
    window.currentFilters = { ...window.currentFilters, ...filterValues };

    console.log("[DropdownFilter] Filter values after removing nulls:", window.currentFilters);

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

        if (!updateResponse.ok) {
            throw new Error(`HTTP error! status: ${updateResponse.status}`);
        }

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

        if (!filterResponse.ok) {
            throw new Error(`HTTP error! status: ${filterResponse.status}`);
        }

        const filteredData = await filterResponse.json();
        await window.populateProductsTable(filteredData.data);

    } catch (error) {
        console.error("[DropdownFilter] Error:", error);
    }
};






window.resetFilters = function() {
    console.log("[DropdownFilter] Resetting filters");

    // Clear the current filters
    window.currentFilters = {};

    // Fetch initial filter options and reset the products table
    if (typeof window.getInitialFilterOptions === "function") {
        window.getInitialFilterOptions().then(initialFilters => {
            if (initialFilters && typeof initialFilters === 'object') {
                // Reset each dropdown to its default state after confirming we have the initial filter options
                const dropdownIds = [
                    'Classificação-select',
                    'subproduto-select',
                    'local-select',
                    'freq-select',
                    'proprietario-select'
                ];

                dropdownIds.forEach(id => {
                    const dropdown = document.getElementById(id);
                    if (dropdown) {
                        dropdown.selectedIndex = 0; // This sets the dropdown back to the first option, typically "Select..."
                    }
                });

                window.populateDropdowns(initialFilters);
                window.populateProductsTable([]); // Reset the products table
            } else {
                console.error("Failed to fetch initial filter options or received undefined.");
            }
        });
    } else {
        console.error("getInitialFilterOptions function is not defined.");
    }

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

```
## app/Http/Controllers/FilterController.php
```
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ExtendedProductList;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\QueryException;

class FilterController extends Controller
{
    // This method can be used to fetch initial filter options for the dropdowns
    public function getInitialFilterOptions()
    {
        Log::info('[FilterController] Fetching initial filter options');
        try {
            // Fetch distinct values for each filter option from the database
            $ClassificaçãoOptions = ExtendedProductList::distinct()->pluck('Classificação');
            $subprodutoOptions = ExtendedProductList::distinct()->pluck('Subproduto');
            $localOptions = ExtendedProductList::distinct()->pluck('Local');
            $freqOptions = ExtendedProductList::distinct()->pluck('freq');
            $bolsaOptions = ExtendedProductList::distinct()->pluck('bolsa');

            // Map 'bolsa' to 'proprietario' for frontend representation
            $proprietarioOptions = $bolsaOptions->map(function ($item) {
                return $item == 2 ? 'Sim' : 'Não'; // Ensure we return 'Sim'/'Não' instead of numeric values
            })->unique()->values();

            Log::info('[FilterController] Initial filter options fetched', [
                'Classificação' => $ClassificaçãoOptions,
                'subproduto' => $subprodutoOptions,
                'local' => $localOptions,
                'freq' => $freqOptions,
                'proprietario' => $proprietarioOptions,
            ]);

            // Return the filter options as a JSON response
            return response()->json([
                'Classificação' => $ClassificaçãoOptions,
                'subproduto' => $subprodutoOptions,
                'local' => $localOptions,
                'freq' => $freqOptions,
                'proprietario' => $proprietarioOptions,
            ]);
        } catch (QueryException $e) {
            Log::error('[FilterController] Database query exception: ' . $e->getMessage());
            return response()->json(['error' => 'Database query exception'], 500);
        } catch (\Exception $e) {
            Log::error('[FilterController] General exception: ' . $e->getMessage());
            return response()->json(['error' => 'General exception'], 500);
        }
    }

    // Method to fetch updated filter options based on current selections
    public function getUpdatedFilterOptions(Request $request)
    {
        Log::info('[FilterController] Fetching updated filter options with request: ', $request->all());
        try {
            // Initialize the base query
            $query = ExtendedProductList::query();

            // Apply filters based on the provided selections in the request
            // Apply filters based on the provided selections in the request
            foreach ($request->all() as $key => $value) {
                if (!empty($value)) {
                    // Convert 'proprietario' filter from frontend to 'bolsa' for the database query
                    if ($key === 'proprietario') {
                        if ($value === 'Sim') {
                            $query->where('bolsa', 2);
                        } elseif ($value === 'Não') { // Corrected typo here
                            $query->where('bolsa', '<>', 2);
                        }
                        Log::info("Applied filter for 'bolsa' with value: {$value}");
                    } else {
                        $query->where($key, $value);
                        Log::info("Applied filter for '{$key}' with value: {$value}");
                    }
                }
            }

            // Log the SQL query
            Log::debug('[FilterController] SQL Query: ' . $query->toSql());

            // Fetch the distinct values for the filters that are not currently selected
            $data = [
                'Classificação' => $request->filled('Classificação') ? [] : $query->distinct()->pluck('Classificação')->all(),
                'subproduto' => $request->filled('subproduto') ? [] : $query->distinct()->pluck('Subproduto')->all(),
                'local' => $request->filled('local') ? [] : $query->distinct()->pluck('Local')->all(),
                'freq' => $request->filled('freq') ? [] : $query->distinct()->pluck('freq')->all(),
                // Fetch 'bolsa' options and map to 'proprietario' for frontend representation
                'proprietario'  => $request->filled('proprietario') ? [] : $query->distinct()->pluck('bolsa')->map(function ($item) {
                    return $item == 2 ? 'Sim' : 'Não'; // Convert back to 'Sim'/'Não' for the frontend
                })->unique()->values()->all(),
            ];

            Log::info('[FilterController] Updated filter options fetched', $data);

            return response()->json($data);
        } catch (QueryException $e) {
            Log::error('[FilterController] Database query exception: ' . $e->getMessage());
            return response()->json(['error' => 'Database query exception'], 500);
        } catch (\Exception $e) {
            Log::error('[FilterController] General exception: ' . $e->getMessage());
            return response()->json(['error' => 'General exception'], 500);
        }
    }
}

```
## app/Http/Controllers/ProductController.php
```
<?php

// ProductController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ExtendedProductList;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        Log::info('ProductController: index method called', $request->all());
        $perPage = 10;

        try {
            $query = ExtendedProductList::query();

            // Adjusted logic for 'proprietario' filter conversion
            if ($request->filled('proprietario')) {
                if ($request->input('proprietario') === 'Sim') {
                    $query->where('bolsa', 2);
                    Log::info("Applying filter: bolsa with value: 2");
                } elseif ($request->input('proprietario') === 'Não') {
                    $query->where('bolsa', '<>', 2);
                    Log::info("Applying filter: bolsa with values not equal to 2");
                }
            }

            // Handle other filters
            $filters = $request->only(['Classificação', 'subproduto', 'local', 'freq']);
            foreach ($filters as $key => $value) {
                if (!is_null($value) && $value !== '') {
                    $query->where($key, $value);
                    Log::info("Applying filter: {$key} with value: {$value}");
                }
            }

            $products = $query->paginate($perPage);

            Log::info('Products fetched successfully with applied filters', ['count' => $products->count()]);
            return response()->json($products);
        } catch (\Exception $e) {
            Log::error('Error fetching products', ['message' => $e->getMessage()]);
            return response()->json(['error' => 'Error fetching products'], 500);
        }
    }
}

```
