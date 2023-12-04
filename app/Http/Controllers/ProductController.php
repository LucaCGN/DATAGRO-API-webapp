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

            // Dynamically apply filters if provided in the request, skip if null
            $filters = $request->only(['classificacao', 'subproduto', 'local', 'freq', 'bolsa']);

            // Check and replace 'classificacao' with 'Classificação'
            if (isset($filters['classificacao'])) {
                $filters['Classificação'] = $filters['classificacao'];
                unset($filters['classificacao']); // Remove the old key
            }

            foreach ($filters as $key => $value) {
                if (!is_null($value) && $value !== '') {
                    $query->where($key, $value);
                }
            }

            // Paginate the query result
            $products = $query->paginate($perPage, ['*'], 'page', $request->get('page', 1));

            Log::info('Products fetched successfully', ['count' => $products->count()]);
            return response()->json($products);
        } catch (\Exception $e) {
            Log::error('Error fetching products', ['message' => $e->getMessage()]);
            return response()->json(['error' => 'Error fetching products'], 500);
        }
    }
}
