<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ExtendedProductList;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Builder;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        Log::info('ProductController: index method called', $request->all());
        $perPage = 10;

        try {
            $products = ExtendedProductList::query()
                ->when($request->filled('classificacao'), function (Builder $query) use ($request) {
                    $query->where('Classificação', $request->classificacao);
                })
                ->when($request->filled('subproduto'), function (Builder $query) use ($request) {
                    $query->where('Subproduto', $request->subproduto);
                })
                ->when($request->filled('local'), function (Builder $query) use ($request) {
                    $query->where('Local', $request->local);
                })
                ->when($request->filled('freq'), function (Builder $query) use ($request) {
                    $query->where('freq', $request->freq);
                })
                ->when($request->filled('bolsa'), function (Builder $query) use ($request) {
                    $query->where('bolsa', $request->bolsa);
                })
                ->paginate($perPage, ['*'], 'page', $request->get('page', 1));

            Log::info('Products fetched successfully', ['count' => $products->count()]);
            return response()->json($products);
        } catch (\Exception $e) {
            Log::error('Error fetching products', ['message' => $e->getMessage()]);
            return response()->json(['error' => 'Error fetching products'], 500);
        }
    }
}
