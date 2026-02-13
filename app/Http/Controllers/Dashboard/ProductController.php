<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\PackagingType;
use App\Models\PackagingSize;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with(['category', 'brand', 'packagingType', 'packagingSize', 'user'])
            ->latest()
            ->get();

        return view('dashboard.products.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::where('is_active', true)->orderBy('category_name')->get();
        $brands = Brand::where('is_active', true)->orderBy('brand_name')->get();
        $packagingTypes = PackagingType::orderBy('packaging_type')->get();
        $packagingSizes = PackagingSize::orderBy('size')->get();

        return view('dashboard.products.create', compact('categories', 'brands', 'packagingTypes', 'packagingSizes'));
    }

    public function edit($id)
    {
        $product = Product::findOrFail($id);
        $categories = Category::where('is_active', true)->orderBy('category_name')->get();
        $brands = Brand::where('is_active', true)->orderBy('brand_name')->get();
        $packagingTypes = PackagingType::orderBy('packaging_type')->get();
        $packagingSizes = PackagingSize::orderBy('size')->get();

        return view('dashboard.products.edit', compact(
            'product',
            'categories',
            'brands',
            'packagingTypes',
            'packagingSizes'
        ));
    }

    public function show($id)
    {
        $product = Product::with(['category', 'brand', 'packagingType', 'packagingSize', 'user'])
            ->findOrFail($id);

        return view('dashboard.products.show', compact('product'));
    }
}
