<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ExtendedProductList;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        Log::info('ProductController: index method called');
        $page = $request->get('page', 1); // Default to page 1 if not provided
        Log::info('Requested page: ' . $page);
        $perPage = 10; // Default to 10 per page
        $products = ExtendedProductList::paginate($perPage, ['*'], 'page', $page);
        Log::info('Products Retrieved: ' . $products->count());
        Log::info('Products Retrieved: ' . $products);
        return response()->json($products);
    }

}
