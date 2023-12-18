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
            $fonteOptions = ExtendedProductList::distinct()->pluck('fonte');

            // Map 'fonte' to 'proprietario' for frontend representation
            $proprietarioOptions = $fonteOptions->map(function ($item) {
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
                    // Convert 'proprietario' filter from frontend to 'fonte' for the database query
                    if ($key === 'proprietario') {
                        if ($value === 'Sim') {
                            $query->where('fonte', 2);
                        } elseif ($value === 'Não') { // Corrected typo here
                            $query->where('fonte', '<>', 2);
                        }
                        Log::info("Applied filter for 'fonte' with value: {$value}");
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
                // Fetch 'fonte' options and map to 'proprietario' for frontend representation
                'proprietario'  => $request->filled('proprietario') ? [] : $query->distinct()->pluck('fonte')->map(function ($item) {
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
