<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Price;
use App\Models\Product;

class PriceController extends Controller
{
    public function index()
    {
        $prices = Price::with(['product', 'user'])->latest()->get();

        return view('dashboard.prices.index', compact('prices'));
    }

    public function create()
    {
        $products = Product::orderBy('product_name')->get();

        return view('dashboard.prices.create', compact('products'));
    }

    public function edit($id)
    {
        $price = Price::findOrFail($id);
        $products = Product::orderBy('product_name')->get();

        return view('dashboard.prices.edit', compact('price', 'products'));
    }

    public function show($id)
    {
        $price = Price::with(['product', 'user'])->findOrFail($id);

        return view('dashboard.prices.show', compact('price'));
    }
}
