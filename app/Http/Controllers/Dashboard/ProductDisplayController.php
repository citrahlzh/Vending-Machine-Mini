<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProductDisplay;
use App\Models\Product;
use App\Models\Price;
use App\Models\Cell;

class ProductDisplayController extends Controller
{
    public function index()
    {
        $productDisplays = ProductDisplay::with(['product', 'price', 'cell', 'user'])
            ->latest()
            ->get();

        $products = Product::orderBy('product_name')->get();
        $prices = Price::with('product')->orderByDesc('start_date')->get();
        $cells = Cell::orderBy('code')->get();

        return view('dashboard.product-displays.index', compact(
            'productDisplays',
            'products',
            'prices',
            'cells'
        ));
    }

    public function create()
    {
        return redirect()->route('dashboard.product-displays.index');
    }

    public function edit($id)
    {
        $productDisplay = ProductDisplay::findOrFail($id);
        $products = Product::orderBy('product_name')->get();
        $prices = Price::with('product')->orderByDesc('start_date')->get();
        $cells = Cell::orderBy('code')->get();

        return view('dashboard.product-displays.edit', compact(
            'productDisplay',
            'products',
            'prices',
            'cells'
        ));
    }

    public function show($id)
    {
        $productDisplay = ProductDisplay::with(['product', 'price', 'cell', 'user'])->findOrFail($id);

        return view('dashboard.product-displays.show', compact('productDisplay'));
    }
}
