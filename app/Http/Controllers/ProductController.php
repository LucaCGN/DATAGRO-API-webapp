<?php

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

            // Convert 'proprietario' filter from frontend to 'bolsa' for the database query
            if ($request->filled('proprietario')) {
                $bolsaValue = $request->input('proprietario') === 'Sim' ? 2 : ($request->input('proprietario') === 'Não' ? 1 : null);
                if (!is_null($bolsaValue)) {
                    $query->where('bolsa', $bolsaValue);
                    Log::info("Applying filter: bolsa with value: {$bolsaValue}");
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
