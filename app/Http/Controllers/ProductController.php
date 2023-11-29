<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ExtendedProductList;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    public function index()
    {
        Log::info('ProductController: index method called');
        $products = ExtendedProductList::paginate(10); // Default to 10 per page
        Log::info('Products Retrieved: ' . $products->count());
        return response()->json($products);
    }

    public function paginate($page, $perPage)
    {
        Log::info("ProductController: paginate method called with page {$page} and perPage {$perPage}");
        $products = ExtendedProductList::paginate($perPage, ['*'], 'page', $page);
        Log::info('Paginated Products Retrieved');
        return response()->json($products);
    }

    // Adjusted to handle POST requests for filtering
    public function filter(Request $request)
    {
        // Ensure your method is equipped to handle the request appropriately
        $query = ExtendedProductList::query();

        if ($request->filled('produto')) {
            $query->where('Código_Produto', 'like', '%' . $request->produto . '%');
        }
        if ($request->filled('subproduto')) {
            $query->where('Subproduto', 'like', '%' . $request->subproduto . '%');
        }
        // Add other filters similarly...

        $products = $query->paginate(10); // Or the perPage value sent from the front-end
        return response()->json($products);
    }
}
