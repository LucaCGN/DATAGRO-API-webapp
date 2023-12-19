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

            // Handle 'proprietario' filter conversion
            if ($request->filled('proprietario')) {
                $query->where('fonte', $request->input('proprietario') === 'Sim' ? 3 : '<>', 3);
                Log::info("Applying filter: fonte with value: " . $request->input('proprietario'));
            }

            // Handle other filters
            $filters = $request->only(['Classificação', 'subproduto', 'local', 'freq']);
            foreach ($filters as $key => $value) {
                if (!empty($value)) {
                    $query->where($key, $value);
                    Log::info("Applying filter: {$key} with value: {$value}");
                }
            }

            // Handle search across all columns
            if ($request->filled('search')) {
                $searchTerm = $request->input('search');
                $query->where(function ($subQuery) use ($searchTerm) {
                    $subQuery->where('longo', 'LIKE', "%{$searchTerm}%")
                              ->orWhere('Classificação', 'LIKE', "%{$searchTerm}%")
                              ->orWhere('subproduto', 'LIKE', "%{$searchTerm}%")
                              ->orWhere('local', 'LIKE', "%{$searchTerm}%")
                              ->orWhere('freq', 'LIKE', "%{$searchTerm}%")
                             // Add other columns as needed
                             ;
                });
                Log::info("Applying search filter with value: {$searchTerm}");
            }

            // Fetch the paginated products
            $products = $query->paginate($perPage);

            Log::info('Products fetched successfully with applied filters', ['count' => $products->count()]);
            return response()->json($products);
        } catch (\Exception $e) {
            Log::error('Error fetching products', ['message' => $e->getMessage()]);
            return response()->json(['error' => 'Error fetching products'], 500);
        }
    }
}
