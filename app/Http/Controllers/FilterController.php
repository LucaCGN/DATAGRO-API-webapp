<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ExtendedProductList;
use Illuminate\Support\Facades\Log;

class FilterController extends Controller
{
    public function getInitialFilterData()
    {
        Log::info('[FilterController] Fetching initial filter data');
        try {
            $classificacao = ExtendedProductList::distinct()->pluck('Classificação');
            $subproduto = ExtendedProductList::distinct()->pluck('Subproduto');
            $local = ExtendedProductList::distinct()->pluck('Local');
            $freq = ExtendedProductList::distinct()->pluck('freq');
            $bolsa = ExtendedProductList::distinct()->pluck('bolsa');

            $proprietario = $bolsa->map(function ($item) {
                return $item == 2 ? 'Sim' : 'Não';
            })->unique()->values();

            Log::info('[FilterController] Filter data fetched successfully', [
                'classificacao' => $classificacao,
                'subproduto' => $subproduto,
                'local' => $local,
                'freq' => $freq,
                'proprietario' => $proprietario,
            ]);

            return response()->json([
                'classificacao' => $classificacao,
                'subproduto' => $subproduto,
                'local' => $local,
                'freq' => $freq,
                'proprietario' => $proprietario,
            ]);
        } catch (\Exception $e) {
            Log::error('[FilterController] Error fetching initial filter data: ' . $e’sgetMessage());
            return response()->json(['error' => 'Error fetching initial filter data'], 500);
        }
    }

    public function getDropdownData(Request $request)
    {
        Log::info('[FilterController] Fetching dropdown data');
        try {
            $query = ExtendedProductList::query();

            // Apply the filters if any are provided in the request
            foreach ($request->all() as $key => $value) {
                if (!empty($value)) {
                    $query->where($key, $value);
                }
            }

            // Fetch the data for dropdowns applying distinct to avoid duplicates
            $classificacao = $query->distinct()->pluck('Classificação');
            $subproduto = $query->distinct()->pluck('Subproduto');
            $local = $query->distinct()->pluck('Local');
            $freq = $query->distinct()->pluck('freq');
            $proprietario = $query->distinct()->pluck('bolsa');

            return response()->json([
                'classificacao' => $classificacao,
                'subproduto' => $subproduto,
                'local' => $local,
                'freq' => $freq,
                'proprietario' => $proprietario->mapWithKeys(function ($item) {
                    return [$item => $item == 2 ? 'sim' : 'nao'];
                }),
            ]);
        } catch (\Exception $e) {
            Log::error('[FilterController] Error fetching dropdown data: ' . $e->getMessage());
            return response()->json(['error' => 'Error fetching dropdown data'], 500);
        }
    }
    public function getUpdatedFilterOptions(Request $request)
    {
        Log::info('[FilterController] Fetching updated filter options based on current selections');
        try {
            // Initialize the base query
            $query = ExtendedProductList::query();

            // Dynamically build the query based on provided filters, except for the one being updated
            $filters = $request->all();
            foreach ($filters as $key => $value) {
                // Skip empty filters
                if (!empty($value)) {
                    $query->where($key, $value);
                }
            }

            // Fetch the distinct values for each filter, excluding the keys present in the request
            $data = [
                'classificacao' => $request->has('classificacao') ? [] : $query->distinct()->pluck('Classificação'),
                'subproduto' => $request->has('subproduto') ? [] : $query->distinct()->pluck('Subproduto'),
                'local' => $request->has('local') ? [] : $query->distinct()->pluck('Local'),
                'freq' => $request->has('freq') ? [] : $query->distinct()->pluck('freq'),
            ];

            // Special handling for 'proprietario' based on 'bolsa'
            if (!$request->has('proprietario')) {
                $bolsaValues = $query->distinct()->pluck('bolsa');
                $data['proprietario'] = $bolsaValues->mapWithKeys(function ($item) {
                    return [$item => $item == 2 ? 'Sim' : 'Não'];
                });
            } else {
                $data['proprietario'] = [];
            }

            return response()->json($data);
        } catch (\Exception $e) {
            Log::error('[FilterController] Error fetching updated filter options: ' . $e->getMessage());
            return response()->json(['error' => 'Error fetching updated filter options'], 500);
        }
    }

}

