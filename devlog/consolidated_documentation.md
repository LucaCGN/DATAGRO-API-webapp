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
    let freq = freqElement.value;
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


```
## app/Models/DataSeries.php
```
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DataSeries extends Model
{
    protected $table = 'data_series_tables';

    protected $fillable = [
        'extended_product_list_id', 'cod', 'data', 'ult', 'mini', 'maxi',
        'abe', 'volumes', 'cab', 'med', 'aju'
    ];

    public $timestamps = true;

    // Define the relationship with ExtendedProductList
    public function extendedProductList()
    {
        return $this->belongsTo(ExtendedProductList::class, 'extended_product_list_id');
    }
}

```
## app/Console/Commands/FetchDatagroData.php
```
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\ExtendedProductList;
use App\Models\DataSeries;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use League\Csv\Reader;

class FetchDatagroData extends Command
{
    protected $signature = 'fetch:datagro-data';
    protected $description = 'Fetch data from Datagro API';

    public function handle()
    {
        Log::info("Command Started: Fetching Datagro Data");

        $products = ExtendedProductList::all();
        Log::info("Total Products to Process: " . $products->count());

        foreach ($products as $product) {
            $this->info("Processing product: " . $product->Código_Produto);
            Log::info("Processing Product: " . $product->Código_Produto);

            $additionalDataResponse = $this->fetchProductData($product->Código_Produto);
            Log::info("API Response for Product Data: ", (array) $additionalDataResponse);

            $additionalData = $additionalDataResponse[0] ?? null; // Accessing the first element of the response

            if ($additionalData) {
                Log::info("Attempting to Update Product: " . $product->Código_Produto);
                $product->update([
                    'bolsa' => $additionalData['bolsa'] ?? null,
                    'roda' => $additionalData['roda'] ?? null,
                    'fonte' => $additionalData['fonte'] ?? null,
                    'tav' => $additionalData['tav'] ?? null,
                    'subtav' => $additionalData['subtav'] ?? null,
                    'decimais' => $additionalData['decimais'] ?? null,
                    'correlatos' => $additionalData['correlatos'] ?? null,
                    'empresa' => $additionalData['empresa'] ?? null,
                    'contrato' => $additionalData['contrato'] ?? null,
                    'subproduto_id' => $additionalData['subproduto'] ?? null,
                    'entcode' => $additionalData['entcode'] ?? null,
                    'nome' => $additionalData['nome'] ?? null,
                    'longo' => $additionalData['longo'] ?? null,
                    'descr' => $additionalData['descr'] ?? null,
                    'codf' => $additionalData['codf'] ?? null,
                    'bd' => $additionalData['bd'] ?? null,
                    'palavras' => $additionalData['palavras'] ?? null,
                    'habilitado' => $additionalData['habilitado'] ?? null,
                    'lote' => $additionalData['lote'] ?? null,
                    'rep' => $additionalData['rep'] ?? null,
                    'vln' => $additionalData['vln'] ?? null,
                    'dia' => $additionalData['dia'] ?? null,
                    'freq' => $additionalData['freq'] ?? null,
                    'dex' => $additionalData['dex'] ?? null,
                    'inserido' => $additionalData['inserido'] ?? null,
                    'alterado' => $additionalData['alterado'] ?? null,
                ]);
                Log::info("Updated Product: " . $product->Código_Produto, $additionalData);
            } else {
                Log::warning("No Additional Data for Product: " . $product->Código_Produto);
            }

            $startDate = $product->inserido ? Carbon::createFromFormat('Y-m-d H:i:s', $product->inserido)->format('Ymd') : '20230101';
            $endDate = $product->alterado ? Carbon::createFromFormat('Y-m-d H:i:s', $product->alterado)->format('Ymd') : '20231201';
            $dataSeries = $this->fetchDataSeries($product->Código_Produto, $startDate, $endDate);

            if (!empty($dataSeries)) {
                foreach ($dataSeries as $data) {
                    Log::info("Creating Data Series Entry with data: " . json_encode($data));

                    DataSeries::create([
                        'extended_product_list_id' => $product->id,
                        'cod' => $product->Código_Produto,
                        'data' => $data['data'] ?? null,
                        'ult' => $data['ult'] ?? null,
                        'mini' => $data['mini'] ?? null,
                        'maxi' => $data['maxi'] ?? null,
                        'abe' => $data['abe'] ?? null,
                        'volumes' => $data['volumes'] ?? null,
                        'cab' => $data['cab'] ?? null,
                        'med' => $data['med'] ?? null,
                        'aju' => $data['aju'] ?? null,
                    ]);

                    Log::info("Data Series Entry Created for Product: " . $product->Código_Produto);
            }
                $product->update(['Fetch_Status' => 'Success']);
            } else {
                foreach ([["20230101", "20230108"], ["20230601", "20230701"]] as $dateRange) {
                    $dataSeries = $this->fetchDataSeries($product->Código_Produto, $dateRange[0], $dateRange[1]);
                    if (!empty($dataSeries)) {
                        foreach ($dataSeries as $data) {
                            DataSeries::create([
                                'extended_product_list_id' => $product->id,
                                'cod' => $product->Código_Produto,
                                'data' => $data['data'] ?? null,
                                'ult' => $data['ult'] ?? null,
                                'mini' => $data['mini'] ?? null,
                                'maxi' => $data['maxi'] ?? null,
                                'abe' => $data['abe'] ?? null,
                                'volumes' => $data['volumes'] ?? null,
                                'cab' => $data['cab'] ?? null,
                                'med' => $data['med'] ?? null,
                                'aju' => $data['aju'] ?? null,
                            ]);

                        }
                                    $product->update(['Fetch_Status' => 'Success']);
                        Log::info("Data Series Fetched and Stored for Product: " . $product->Código_Produto);
                    } else {
                        Log::warning("Data Series Fetching Failed for Product: " . $product->Código_Produto);
                        $product->update(['Fetch_Status' => 'Failed']);
                    }
                }

                Log::info('Data fetching and updating completed.');
            }
        }

        Log::info('Data fetching and updating completed.');
    }


    private function fetchProductData($productCode)
    {
        $url = "https://precos.api.datagro.com/cad/";
        $response = Http::withOptions(['verify' => false])->retry(5, 3000)->get($url, ['a' => $productCode, 'x' => 'j']);

        if ($response->successful()) {
            Log::info("Successful API Response for Product Data: " . $productCode);
            return $response->json();
        } else {
            Log::error("Failed to Fetch Product Data: {$productCode}, Status Code: " . $response->status());
            return null;
        }
    }

    private function fetchDataSeries($productCode, $startDate, $endDate)
    {
        $url = "https://precos.api.datagro.com/dados/";
        $params = [
            'a' => $productCode,
            'i' => $startDate,
            'f' => $endDate,
            'x' => 'c'
        ];

        $maxRetries = 5; // Number of retries
        $retryDelay = 3000; // Delay in milliseconds

        for ($attempt = 0; $attempt <= $maxRetries; $attempt++) {
            try {
                $response = Http::withOptions(['verify' => false])
                                ->get($url, $params);

                if ($response->successful()) {
                    Log::info("Successful API Response for Data Series: " . $productCode);
                    $csvData = $response->body(); // Get CSV data as string
                    return $this->parseCsvData($csvData); // Parse and return the data
                } else {
                    Log::error("Failed to Fetch Data Series: {$productCode}, Status Code: " . $response->status());
                }
            } catch (\Exception $e) {
                Log::error("Request Exception for {$productCode}: " . $e->getMessage());
            }

            // If max retries reached, log and break
            if ($attempt == $maxRetries) {
                Log::error("Max retries reached for {$productCode}");
                break;
            }

            // Delay before retrying
            usleep($retryDelay * 1000);
        }

        return null; // Return null in case of failure
    }



    private function parseCsvData($csvData)
    {
        // Manually define headers as the CSV does not have headers
        $headers = ['cod', 'data', 'ult', 'mini', 'maxi', 'abe', 'volumes', 'cab', 'med', 'aju'];

        $csv = Reader::createFromString($csvData);
        $csv->setHeaderOffset(null); // No headers in the actual CSV
        $records = $csv->getRecords($headers);

        $parsedData = [];
        foreach ($records as $record) {
            // Remove the last two columns (0 and null)
            array_pop($record);
            array_pop($record);

            Log::info("Record after removing last two columns: " . json_encode($record));

            // Handling date parsing
            $record['data'] = $this->parseDateForDataSeries($record['data']);

            $parsedData[] = $record;
        }

        return $parsedData;
    }


    private function parseDateForDataSeries($dateString)
    {
        Log::info("Parsing date string: {$dateString}");

        // Handle empty, null, or placeholder dates
        if (empty($dateString) || $dateString === '0000-00-00' || $dateString === '0000-00-00 00:00:00') {
            Log::warning("Invalid or placeholder date encountered: {$dateString}");
            return null;
        }

        // Attempt to parse the date string
        try {
            // If the date string includes time, extract only the date part
            if (strpos($dateString, ' ') !== false) {
                $dateString = explode(' ', $dateString)[0];
            }

            $formattedDate = Carbon::createFromFormat('Y-m-d', $dateString)->format('Y-m-d');
            Log::info("Formatted date: {$formattedDate}");
            return $formattedDate;
        } catch (\Exception $e) {
            Log::error("Invalid date format for string: {$dateString}. Error: " . $e->getMessage());
            return null;
        }
    }


}

```
## app/Models/ExtendedProductList.php
```
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExtendedProductList extends Model
{
    protected $table = 'extended_product_list_tables';
    protected $fillable = [
        'Código_Produto', 'Classificação', 'Subproduto', 'Local', 'Fetch_Status',
        'bolsa', 'roda', 'fonte', 'tav', 'subtav', 'decimais', 'correlatos',
        'empresa', 'contrato', 'subproduto_id', 'entcode', 'nome', 'longo', 'descr',
        'codf', 'bd', 'palavras', 'habilitado', 'lote', 'rep', 'vln', 'dia',
        'freq', 'dex', 'inserido', 'alterado', 'oldest_data_date',
    ];
    public $timestamps = true;

    public function dataSeries()
    {
        return $this->hasOne(DataSeries::class, 'extended_product_list_id');
    }

    public function scopeActive($query)
    {
        return $query->where('active', 1);
    }
}

```
## resources/views/partials/dropdown-filter.blade.php
```
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Filterable Product Table</title>
    <!-- Add any additional head content here (e.g., CSS links) -->
</head>
<body>
    <div id="filter-container">
        <!-- Dropdowns for each filter category -->
        <select id="Classificação-select" class="filter-dropdown">
            <option value="">Select Classificação...</option>
            <!-- Options will be populated dynamically -->
        </select>
        <select id="subproduto-select" class="filter-dropdown">
            <option value="">Select Subproduto...</option>
            <!-- Options will be populated dynamically -->
        </select>
        <select id="local-select" class="filter-dropdown">
            <option value="">Select Local...</option>
            <!-- Options will be populated dynamically -->
        </select>
        <select id="freq-select" class="filter-dropdown">
            <option value="">Select Frequência...</option>
            <!-- Options will be populated dynamically -->
        </select>
        <select id="proprietario-select" class="filter-dropdown">
            <option value="">Select Proprietário...</option>
            <!-- Options will be populated dynamically -->
        </select>

        <!-- Reset Button -->
        <button id="reset-filters-btn">Limpar Filtros</button>
    </div>

    <!-- Container for the products table or other display elements -->
    <div id="products-table">
        <!-- Table or other elements will be populated dynamically -->
    </div>

    <!-- JavaScript Files -->
    <script src="js/DropdownFilter.js"></script>
    <!-- Include other JS files or scripts here -->
</body>
</html>

```
## resources/views/partials/data-series-table.blade.php
```
<div class="responsive-table animated" id="data-series-table">
    <table>
        <thead>
            <tr>
                <th title="O código de identificação único do produto">Cod</th>
                <th title="Todas as segundas-feiras do período (ou o dia posterior, se não houver cotação na segunda)">data</th>
                <th title="O valor da última do último dia">abe</th>
                <th title="O menor valor de mini na semana">maxi</th>
                <th title="O maior valor de maxi na semana">mini</th>
                <th title="O valor de abe do primeiro dia da semana">ult</th>
                <th title="Neg, vtt, qtt, cng = a soma de todos os dias">volumes</th>
                <th title="O valor de cab do último dia da semana">cab</th>
            </tr>
        </thead>
        <tbody id="data-series-body">
            <!-- Data populated by DataSeriesTable.js -->
        </tbody>
    </table>
</div>


```
## app/Http/Controllers/DownloadController.php
```
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ExtendedProductList;
use Illuminate\Support\Facades\Log;
use PDF;

class DownloadController extends Controller
{
    public function downloadVisibleCSV(Request $request)
    {
        Log::info('DownloadController: downloadVisibleCSV method called');

        // Decode the JSON data received from the frontend
        $data = json_decode($request->getContent(), true);
        $dataSeries = $data['dataSeries'];

        // Create a CSV file in memory
        $file = fopen('php://temp', 'w+');

        // Set the headers for proper UTF-8 encoding
        fputs($file, $bom =( chr(0xEF) . chr(0xBB) . chr(0xBF) ));

        // Manually add headers for the data series table
        $dataSeriesHeaders = ['Cod', 'data', 'ult', 'mini', 'maxi', 'abe', 'volumes', 'med', 'aju'];
        fputcsv($file, $dataSeriesHeaders);

        // Add data series rows
        foreach ($dataSeries as $series) {
            fputcsv($file, $series);
        }

        // Reset the file pointer to the start
        rewind($file);

        // Build the CSV from the file pointer
        $csv = stream_get_contents($file);
        fclose($file);

        // Create a response and add headers for file download
        $response = response($csv)
            ->header('Content-Type', 'text/csv; charset=UTF-8')
            ->header('Content-Disposition', 'attachment; filename="visible-data.csv"')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');

        return $response;
    }

    public function downloadPDF(Request $request)
    {
        Log::info('DownloadController: downloadPDF method called');

        $data = json_decode($request->getContent(), true);

        if (empty($data['product'])) {
            Log::error('DownloadController: downloadPDF method called with empty product data');
            abort(400, "Bad Request: No product data provided");
        }

        // Extract the 'longo', 'Local', 'Subproduto', and 'freq' from the product data
        $selectedProduct = [
            'longo' => $data['product'][18], // Replace the index with the correct one if necessary
            'Produto' => $data['product'][2], // 'Código_Produto' at index 1
            'Local' => $data['product'][4], // 'Local' at index 4
            'Subproduto' => $data['product'][3], // 'Subproduto' at index 2
            'Frequência' => $data['product'][28], // 'freq' at index 27
        ];

        $dataSeries = $data['dataSeries'] ?? [];

        // Generate the PDF with the desired product details and existing data series logic
        $pdf = PDF::loadView('pdf_view', [
            'selectedProduct' => $selectedProduct,
            'dataSeries' => array_map(function ($series) {
                return [
                    'cod' => $series['cod'],
                    'data' => $series['data'],
                    'ult' => $series['ult'],
                    'mini' => $series['mini'],
                    'maxi' => $series['maxi'],
                    'abe' => $series['abe'],
                    'volumes' => $series['volumes'],
                    // 'med' and 'aju' are intentionally excluded
                ];
            }, $dataSeries)
        ]);

        return $pdf->download('visible-data.pdf');
    }

}

```
## app/Http/Controllers/DataSeriesController.php
```
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DataSeries;
use Illuminate\Support\Facades\Log;

class DataSeriesController extends Controller
{
    /**
     * Display the first 10 entries of the data series for a given product code.
     *
     * @param string $productCode
     * @return \Illuminate\Http\Response
     */
    public function show($productCode)
    {
        Log::info("Entering DataSeriesController::show with productCode: {$productCode}");
        try {
            // Fetch only the first 10 data series entries based on the product code
            $dataSeries = DataSeries::where('cod', $productCode)->take(10)->get();

            Log::info('First 10 DataSeries entries retrieved.');
            return response()->json($dataSeries);
        } catch (\Exception $e) {
            Log::error("Error in DataSeriesController::show: " . $e->getMessage());
            return response()->json(['error' => 'Server Error'], 500);
        }
    }
}

```
## resources/views/app.blade.php
```
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Datagro Comercial Team Web Application</title>
  <meta name="csrf-token" content="{{ csrf_token() }}"> <!-- CSRF token meta tag added -->
  <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body>
    <header>
        <h1>DATAGRO Markets - Data Preview Platform</h1>
        <img src="{{ asset('images/Logo - Quadrado.png') }}" alt="Datagro Logo">
    </header>

    <main class="main-content">
        @include('partials.dropdown-filter') <!-- Ensure this partial does not contain conflicting styles -->
        <div class="tables-container">
            @include('partials.products-table', ['products' => $products])
            @include('partials.data-series-table')
        </div>
        @include('partials.download-buttons')
    </main>



<footer>
    <img src="{{ asset('images/Logo - Banner Médio - Markets - 2.png') }}" alt="Datagro Markets" class="footer-logo">
</footer>



  <!-- Include the JavaScript files -->
  <script type="module" src="{{ asset('js/ProductsTable.js') }}"></script>
  <script type="module" src="{{ asset('js/DropdownFilter.js') }}"></script>
  <script type="module" src="{{ asset('js/DataSeriesTable.js') }}"></script>
  <script type="module" src="{{ asset('js/DownloadButtons.js') }}"></script>
  <script>console.log('[app.blade.php] Main application view loaded');</script>
</body>
</html>

```
## routes/web.php
```
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\DataSeriesController;
use App\Http\Controllers\DownloadController;
use App\Http\Controllers\FilterController;
use App\Http\Controllers\LoginController;
use App\Models\ExtendedProductList;

Route::get('/', function () {
    $products = ExtendedProductList::all();
    return view('app', compact('products'));
})->middleware('auth');

// Products related routes
Route::get('/products', [ProductController::class, 'index']); // For initial load and pagination without filters

// Updated POST route for filtered products
Route::post('/api/filter-products', [ProductController::class, 'index']); // Assuming 'index' is the correct method

// Data Series related routes
Route::get('/data-series/{productId}', [DataSeriesController::class, 'show']);
Route::get('/data-series/{productId}/{page}/{perPage}', [DataSeriesController::class, 'paginate']);

// Download routes
Route::post('/download/visible-csv', [DownloadController::class, 'downloadVisibleCSV']);
Route::post('/download/visible-pdf', [DownloadController::class, 'downloadPDF']);


// CSRF token generation
Route::get('/csrf-token', function() {
    return csrf_token();
});

// New GET route for initial filter options as expected in JS
Route::get('/api/initial-filter-options', [FilterController::class, 'getInitialFilterOptions']);

// Ensure this POST route is as per the JavaScript expectations
Route::post('/api/filters/updated', [FilterController::class, 'getUpdatedFilterOptions']);

// Login Routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);

```
## app/Http/Controllers/Controller.php
```
<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
}

```
## public/js/DownloadButtons.js
```

function downloadVisibleCSV() {
    // Use the selectedProductCode to gather only the selected product's data
    const selectedProductRow = window.loadedProducts.find(product => product['Código_Produto'] === window.selectedProductCode);
    const selectedProductData = selectedProductRow ?
        Object.values(selectedProductRow).slice(1) : []; // Exclude the product code from the data

    const dataSeriesData = Array.from(document.querySelectorAll('#data-series-table tbody tr')).map(row => {
        return Array.from(row.querySelectorAll('td')).map(cell => cell.textContent.trim());
    });

    // Prepare the data to send
    const data = { product: selectedProductData, dataSeries: dataSeriesData };

    // Send the data to the server using fetch API
    fetch('/download/visible-csv', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(data)
    })
    .then(response => response.blob())
    .then(blob => {
        // Create a link element, use it to download the file and remove it
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.style.display = 'none';
        a.href = url;
        a.download = 'visible-data.csv';
        document.body.appendChild(a);
        a.click();
        window.URL.revokeObjectURL(url);
        document.body.removeChild(a);
    })
    .catch((error) => console.error('Error:', error));
}





function downloadPDF() {
    console.log("[DownloadButtons] Initiating PDF download");

    // Check if a product has been selected
    if (!window.selectedProductCodeExport) {
        console.error('No product selected for export');
        alert('Please select a product before downloading the PDF.');
        return; // Exit the function if no product is selected for export
    }

    // Fetch only the selected product's data using the export code
    const selectedProductRow = window.loadedProducts.find(product => product['Código_Produto'] === window.selectedProductCodeExport);

    if (!selectedProductRow) {
        console.error('Selected product not found in loaded products for export');
        alert('Selected product data could not be found for export. Please try again.');
        return; // Exit the function if selected product data is not found for export
    }

    // Log the selected product for debugging purposes
    console.log('Selected product data:', selectedProductRow);

    // Extract the selected product's data including the 'Código_Produto'
    // Map the product object to an array of its values
    const selectedProductData = Object.values(selectedProductRow);

    // Log the extracted data for debugging purposes
    console.log('Extracted product data for PDF:', selectedProductData);

    // Fetch data series from the data series table with only the desired columns
    const dataSeriesData = Array.from(document.querySelectorAll('#data-series-table tbody tr')).map(row => {
        const cells = Array.from(row.querySelectorAll('td'));
        return {
            cod: cells[0].textContent.trim(),
            data: cells[1].textContent.trim(),
            ult: cells[2].textContent.trim(),
            mini: cells[3].textContent.trim(),
            maxi: cells[4].textContent.trim(),
            abe: cells[5].textContent.trim(),
            volumes: cells[6].textContent.trim()
            // 'med' and 'aju' are intentionally excluded
        };
    });

    // Log the data series for debugging purposes
    console.log('Data series for PDF:', dataSeriesData);

    // Prepare the data payload for the request
    const data = { product: selectedProductData, dataSeries: dataSeriesData };

    // Make a POST request to the server to generate and download the PDF
    fetch('/download/visible-pdf', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(data)
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok ' + response.statusText);
        }
        return response.blob();
    })
    .then(blob => {
        // Create a blob URL from the response
        const url = window.URL.createObjectURL(blob);

        // Create a link element, use it to download the file, and remove it
        const a = document.createElement('a');
        a.style.display = 'none';
        a.href = url;
        a.download = 'visible-data.pdf';
        document.body.appendChild(a);
        a.click();

        // Clean up by revoking the object URL and removing the link element
        window.URL.revokeObjectURL(url);
        document.body.removeChild(a);
    })
    .catch((error) => console.error('Error:', error));
}

// Event listener for the download button
document.addEventListener('DOMContentLoaded', function () {
    const csvVisibleBtn = document.getElementById('download-csv-btn'); // Corrected to match the button's ID
    if (csvVisibleBtn) {
        csvVisibleBtn.addEventListener('click', downloadVisibleCSV);
    }
});


console.log('DownloadButtons.js loaded');

document.addEventListener('DOMContentLoaded', function () {
    console.log("[DownloadButtons] DOM fully loaded and parsed");

    // Binding for CSV download button
    const csvVisibleBtn = document.getElementById('download-csv-btn');
    if (csvVisibleBtn) {
        console.log("[DownloadButtons] CSV button found");
        csvVisibleBtn.addEventListener('click', downloadVisibleCSV);
    } else {
        console.log("[DownloadButtons] CSV button NOT found");
    }

    // Binding for PDF download button
    const pdfBtn = document.getElementById('download-pdf-btn');
    if (pdfBtn) {
        console.log("[DownloadButtons] PDF button found");
        pdfBtn.addEventListener('click', downloadPDF);
    } else {
        console.log("[DownloadButtons] PDF button NOT found");
    }
});

```
## public/js/DataSeriesTable.js
```
// Function to load data series for a product with pagination
window.loadDataSeries = function(productCode) {
    console.log(`Initiating fetch to /data-series/${productCode}`);

    fetch(`/data-series/${productCode}`)
        .then(response => {
            console.log("Raw fetch response:", response);
            if (!response.ok) {
                console.error(`[DataSeriesTable] Error fetching data series: ${response.statusText}`);
                throw new Error(`Error fetching data series: ${response.statusText}`);
            }
            return response.json();
        })
        .then(data => {
            console.log("[DataSeriesTable] DataSeries API Response received:", data);
            let tableBody = document.getElementById('data-series-body');
            window.globalDataSeries = data;
            tableBody.innerHTML = data.slice(0, 10).map(series => `
                <tr>
                    <td>${series.cod}</td>
                    <td>${series.data}</td>
                    <td>${series.ult}</td>
                    <td>${series.mini}</td>
                    <td>${series.maxi}</td>
                    <td>${series.abe}</td>
                    <td>${series.volumes}</td>
                    <td>${series.cab}</td>
                </tr>
            `).join('');
            console.log("[DataSeriesTable] Data series successfully rendered in table");
        })
        .catch(error => {
            console.error("Fetch request failed: ", error);
        });
};

```
## public/css/app.css
```
/* Main application styles */
body {
    font-family: 'Arial', sans-serif;
    color: #4f4f4f;
    background-color: #f8f8f8;
}

/* Header styling with the logo aligned to the left and the background set to white */
header {
    display: flex;
    align-items: center;
    padding: 15px; /* Reduced padding to reduce total height */
    background-color: #8dbf42; /* Set background color */
}

/* Ensure the logo fits properly, aligned to the left, with adjusted size */
header img {
    height: auto; /* Keep the height auto to maintain aspect ratio */
    width: auto; /* Width set to auto to adjust with height */
    max-height: 60px; /* Example fixed max-height, adjust to your preference */
    order: -1; /* This ensures that the logo comes first within the flex container */
}

/* Updated title styling with flex-grow to center the title text, bold and white color */
header h1 {
    flex-grow: 1;
    text-align: center;
    color: #ffffff; /* Set text color to white */
    font-weight: bold; /* Make the font bold */
    margin: 0; /* Keep the margin 0 to align properly */
    font-size: 2em; /* You can adjust the font size if necessary */
}

/* Ensure the logo fits properly, aligned to the left, with adjusted size */
header img {
    height: auto; /* Keep the height auto to maintain aspect ratio */
    width: auto; /* Width set to auto to adjust with height */
    max-height: 60px; /* Example fixed max-height, adjust to your preference */
    order: -1; /* This ensures that the logo comes first within the flex container */
}


/* Footer styling with logo and links */
footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px;
    box-shadow: 0 -2px 4px rgba(0, 0, 0, 0.1);
    background-color: #fff;
}

.footer-logo {
    height: 50px;
    margin-right: auto;
}

.footer-links h2 {
    margin-top: 0;
    color: var(--brand-brown);
}

.footer-links ul {
    list-style: none;
    padding: 0;
    margin: 0;
}

.footer-links ul li {
    padding: 5px 0;
}

.footer-links ul li a {
    color: #4f4f4f;
    text-decoration: none;
}

.footer-links ul li a:hover {
    text-decoration: underline;
}

/* Branding colors */
:root {
    --brand-green: #8dbf42;
    --brand-brown: #8B4513;
}

/* Styling for the "selected-product-name" field */
.selected-product-display {
    font-weight: bold;
    font-style: italic;
    margin: 20px 0;
    padding: 10px;
    background-color: #f2f2f2;
    text-align: center;
    display: block;
}

/* Filter dropdown and buttons styling */
.filter-dropdown,
.button {
    background-color: var(--brand-green);
    color: #fff;
    padding: 10px 20px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

/* Specific styling for "Reset Filters" button */
#reset-filters-btn {
    background-color: transparent;
    color: var(--brand-brown);
    font-weight: bold;
    padding: 10px 20px;
    border: 2px solid var(--brand-brown);
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

/* Darken button on hover */
.filter-dropdown:hover,
.button:hover,
#reset-filters-btn:hover {
    background-color: var(--brand-brown);
    color: #fff;
}

/* DataSeries Table Styling */
.responsive-table table {
    width: 100%;
    max-width: 100%;
    border-collapse: collapse;
}
.responsive-table thead th {
    position: sticky;
    top: 0;
    background-color: white; /* or any other color that matches your design */
    z-index: 10; /* to ensure the header stays above the content when scrolling */
}

.responsive-table th,
.responsive-table td {
    border: 1px solid #ddd; /* Style as per your design */
    padding: 8px; /* Adjust padding as needed */
    text-align: left; /* Aligns text to the left */
    min-width: 120px; /* Ensures a minimum width for columns to avoid too much compression */
}

/* Title Styling */
h1 {
    color: var(--brand-brown);
    font-weight: bold;
    margin-bottom: 20px;
}

/* Responsive design adjustments */
@media screen and (max-width: 600px) {
    footer {
        flex-direction: column;
        align-items: center;
    }

    footer img.logo {
        margin-bottom: 10px;
    }

    footer .links {
        flex-direction: column;
        align-items: center;
    }

    .responsive-table {
        overflow-x: auto;
    }

    .responsive-table td,
    .responsive-table th {
        padding: 4px; /* Reduces padding on smaller screens for more space */
        min-width: 80px; /* Reduces minimum width on smaller screens */
    }

    .filter-dropdown,
    .button,
    #reset-filters-btn {
        width: 100%;
        margin-bottom: 10px;
    }
}

/* Consistent vertical rhythm and spacing */
* {
    box-sizing: border-box;
}

/* Spacing and alignment for the main content */
.main-content {
    margin: 15px;
    display: flex;
    flex-direction: column;
    align-items: flex-start;
}

.tables-container {
    width: 100%;
}

/* Align filters and buttons to the left */
.filters-container,
.download-buttons-container {
    display: flex;
    justify-content: flex-start;
    gap: 10px;
    margin-top: 15px;
    margin-bottom: 15px; /* Adjust this value to match the desired spacing */
}

@media screen and (max-width: 600px) {
    .filters-container,
    .download-buttons-container {
        flex-direction: column;
    }
}

.tables-container {
    display: flex;
    flex-direction: column;
    margin-bottom: 20px; /* Adjust this value to match the footer's top margin */
}

.responsive-table td, .responsive-table th {
    min-width: 120px; /* or any other appropriate value */
}

.tables-container {
    max-width: 100%;
    overflow-x: auto; /* for horizontal scrolling if necessary */
}

.responsive-table th:nth-child(1),
.responsive-table td:nth-child(1) {
    width: 10%; /* Adjust the percentage based on your design */
}
/* Repeat for each column as necessary */

/* Products Table Styling */
#products-table {
    max-height: 500px; /* Adjust the height as needed */
    overflow-y: auto; /* Enable vertical scrolling */
}

/* Style adjustments for table rows and cells */
#products-table table {
    width: 100%;
    border-collapse: collapse;
}

#products-table th,
#products-table td {
    border: 1px solid #ddd;
    padding: 8px;
    text-align: left;
}


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
        $perPage = 200;

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
## public/js/ProductsTable.js
```
// ProductsTable.js

console.log('ProductsTable.js loaded');

let selectedProductCode = null;
window.currentFilters = {};
window.showingAllRecords = false;

// Modify loadProducts function to fetch all products without pagination
window.loadProducts = async function(filters = window.currentFilters) {
    console.log(`Fetching products with filters`, filters);

    const query = new URLSearchParams(filters).toString();
    const url = `/products?${query}`;

    const headers = {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
    };

    try {
        const response = await fetch(url, { method: 'GET', headers: headers });
        if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
        const data = await response.json();
        populateProductsTable(data.data || []);
    } catch (error) {
        console.error("[ProductsTable] Failed to load products", error);
        populateProductsTable([]);
    }
};


// Populate the products table
window.populateProductsTable = function(products) {
    console.log("[ProductsTable] Populating products table with data:", products);

    let tableBody = document.getElementById('products-table-body');
    if (!tableBody) {
        console.error("Table body not found");
        return;
    }

    // Reset selected product and clear DataSeries view if products list changes
    if (selectedProductCode !== null) {
        selectedProductCode = null;
        clearDataSeriesView(); // Function to clear DataSeries view
    }

    // Clear the table before populating new data
    tableBody.innerHTML = '';

    // Only populate if there are products
    if (products.length > 0) {
        tableBody.innerHTML = products.map(product => {
            // Convert the frequency code to the corresponding word
            const freqWord = freqToWord[product.freq] || product.freq;

            return `
                <tr>
                   <td><input type="radio" name="productSelect" value="${product['Código_Produto']}" onchange="selectProduct('${product['Código_Produto']}')"></td>
                   <td>${product.Classificação}</td>
                   <td>${product.longo}</td>
                   <td>${freqWord}</td>
                   <td>${product.alterado}</td>
                </tr>
            `;
        }).join('');
        console.log("[ProductsTable] Products table populated with products.");
    } else {
        // Show a message or an empty state if there are no products
        tableBody.innerHTML = `<tr><td colspan="5">No products found.</td></tr>`;
        console.log("[ProductsTable] No products found message displayed.");
    }

    window.loadedProducts = products;
 };





window.selectProduct = function(productCode) {
    console.log("Selected product code: ", productCode);
    if (window.selectedProductCode !== productCode) {
        window.selectedProductCode = productCode;
        window.selectedProductCodeExport = productCode; // Set the export code
        window.loadDataSeries(productCode);
    } else {
        window.selectedProductCode = null;
        window.selectedProductCodeExport = null; // Clear the export code
        clearDataSeriesView();
    }
    updateSelectedProductName(); // Update the display of the selected product name
};



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
        if (window.selectedProductCode) {
            const selectedProduct = window.loadedProducts.find(product => product['Código_Produto'] === window.selectedProductCode);
            productNameDisplay.textContent = selectedProduct ? `DataSeries for: ${selectedProduct.longo}` : 'Product not found';
        } else {
            productNameDisplay.textContent = 'Por favor selecione um produto na tabela acima';
        }
    }
}


document.addEventListener('DOMContentLoaded', function () {
    console.log("[ProductsTable] Page loaded - Starting to load products.");
    loadProducts();
    updateSelectedProductName(); // Ensure placeholder is displayed initially
});


```
## resources/views/partials/products-table.blade.php
```
<div class="responsive-table animated" id="products-table">
    <table>
        <thead>
            <tr>
                <th> [Carregar Data Series] </th>
                <th>Produto  ('Classificação')  </th>
                <th>Nome ('longo')  </th>
                <th>Frequência</th>
                <th>Primeira Data</th>
            </tr>
        </thead>
        <tbody id="products-table-body">
            <!-- Data populated by ProductsTable.js -->
        </tbody>
    </table>
</div>
<div id="selected-product-name" class="selected-product-display"></div>

</div>

```
## resources/views/partials/download-buttons.blade.php
```
<div class="animated download-buttons-container">
    <button id="download-csv-btn" class="button">Download CSV</button>
    <button id="download-pdf-btn" class="button">Download PDF</button>
</div>
<script>console.log('[download-buttons.blade.php] Download buttons view loaded');</script>

```
