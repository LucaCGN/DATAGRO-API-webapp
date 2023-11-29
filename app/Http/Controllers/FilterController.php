<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ExtendedProductList;
use Illuminate\Support\Facades\Log;

class FilterController extends Controller
{
    public function getDropdownData()
    {
        // Assuming these are the fields for your dropdowns
        $produto = ExtendedProductList::distinct('Código_Produto')->pluck('Código_Produto', 'id');
        $subproduto = ExtendedProductList::distinct('Subproduto')->pluck('Subproduto', 'id');
        $local = ExtendedProductList::distinct('Local')->pluck('Local', 'id');
        $freq = ExtendedProductList::distinct('Freq')->pluck('Freq', 'id');
        $proprietario = ExtendedProductList::distinct('Proprietario')->pluck('Proprietario', 'id');

        return response()->json([
            'produto' => $produto,
            'subproduto' => $subproduto,
            'local' => $local,
            'freq' => $freq,
            'proprietario' => $proprietario
        ]);
    }
}
