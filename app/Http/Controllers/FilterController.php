<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ExtendedProductList;
use Illuminate\Support\Facades\Log;

class FilterController extends Controller
{
    public function getDropdownData()
    {
        Log::info('[FilterController] Fetching dropdown data');
        try {
            // Fetching 'Classificação' instead of 'Produto'
            $classificacao = ExtendedProductList::distinct('Classificação')->pluck('Classificação', 'id');
            Log::info('[FilterController] Classificação data: ' . json_encode($classificacao));

            $subproduto = ExtendedProductList::distinct('Subproduto')->pluck('Subproduto', 'id');
            Log::info('[FilterController] Subproduto data: ' . json_encode($subproduto));

            $local = ExtendedProductList::distinct('Local')->pluck('Local', 'id');
            Log::info('[FilterController] Local data: ' . json_encode($local));

            $freq = ExtendedProductList::distinct('freq')->pluck('freq', 'id');
            Log::info('[FilterController] freq data: ' . json_encode($freq));

            // Fetching 'bolsa' and converting to 'Proprietário' data
            $proprietario = ExtendedProductList::pluck('bolsa', 'id')
                ->mapWithKeys(function ($item, $key) {
                    return [$key => $item == 2 ? 'sim' : 'nao'];
                });
            Log::info('[FilterController] Proprietário data: ' . json_encode($proprietario));

            return response()->json([
                'classificacao' => $classificacao,
                'subproduto' => $subproduto,
                'local' => $local,
                'freq' => $freq,
                'proprietario' => $proprietario,
                // Add any other fields if necessary
            ]);
        } catch (\Exception $e) {
            Log::error('[FilterController] Error fetching dropdown data: ' . $e->getMessage());
            return response()->json(['error' => 'Error fetching dropdown data'], 500);
        }
    }
}
