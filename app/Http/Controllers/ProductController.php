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
        $products = ExtendedProductList::all();
        Log::info('Products Retrieved: ' . $products->count());
        return view('partials.products-table')->with('products', $products);
    }

    public function paginate($page, $perPage)
    {
        Log::info("ProductController: paginate method called with page {$page} and perPage {$perPage}");
        $products = ExtendedProductList::paginate($perPage);
        Log::info('Paginated Products Retrieved');
        return view('partials.products-table')->with('products', $products);
    }

    public function filter(Request $request)
    {
        Log::info("ProductController: filter method called with request: ", $request->all());
        $products = ExtendedProductList::where('Código_Produto', $request->Código_Produto)
                    ->orWhere('descr', $request->descr)
                    ->get();
        Log::info('Filtered Products Retrieved: ' . $products->count());
        return view('partials.products-table')->with('products', $products);
    }
}
