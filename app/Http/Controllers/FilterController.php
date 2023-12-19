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
                return $item == 3 ? 'Sim' : 'Não'; // Ensure we return 'Sim'/'Não' instead of numeric values
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

    public function getUpdatedFilterOptions(Request $request)
    {
        Log::info('[FilterController] Fetching updated filter options with request: ', $request->all());
        try {
            // Initialize an array to hold the filter queries for each dropdown
            $filterQueries = [];

            // Loop through each filter to build its query
            foreach (['Classificação', 'subproduto', 'local', 'freq', 'proprietario'] as $filter) {
                // Start with the base query for the filter
                $filterQuery = ExtendedProductList::query();

                // Apply the other filters to this query
                foreach ($request->all() as $key => $value) {
                    if (!empty($value) && $key !== $filter) {
                        // Apply the filter if it's not the current one being processed
                        if ($key === 'proprietario') {
                            $filterQuery->where('fonte', $value === 'Sim' ? 3 : '<>', 3);
                        } else {
                            $filterQuery->where($key, $value);
                        }
                    }
                }

                // Store the query for this filter
                $filterQueries[$filter] = $filterQuery;
            }

            // Fetch the distinct values for each filter using the corresponding query
            $data = [
                'Classificação' => $filterQueries['Classificação']->distinct()->pluck('Classificação')->all(),
                'subproduto' => $filterQueries['subproduto']->distinct()->pluck('Subproduto')->all(),
                'local' => $filterQueries['local']->distinct()->pluck('Local')->all(),
                'freq' => $filterQueries['freq']->distinct()->pluck('freq')->all(),
                'proprietario' => $filterQueries['proprietario']->distinct()->pluck('fonte')->map(function ($item) {
                    return $item == 3 ? 'Sim' : 'Não';
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
