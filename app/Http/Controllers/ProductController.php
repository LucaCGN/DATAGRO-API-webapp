<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ExtendedProductList;

class ProductController extends Controller
{
    public function index()
    {
        $products = ExtendedProductList::all();
        return view('partials.products-table')->with('products', $products);
    }

    public function paginate($page, $perPage)
    {
        $products = ExtendedProductList::paginate($perPage);
        return view('partials.products-table')->with('products', $products);
    }

    public function filter(Request $request)
    {
        $products = ExtendedProductList::where('Código_Produto', $request->Código_Produto)
                    ->orWhere('descr', $request->descr)
                    ->get();
        return view('partials.products-table')->with('products', $products);
    }
}
