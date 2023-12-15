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
