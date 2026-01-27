<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Http\Resources\ProductResource;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::all();

        return ProductResource::collection($products);
    }

    public function store(Request $request)
    {
        $validator = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'required|exists:brands,id',
            'packaging_type_id' => 'required|exists:packaging_types,id',
            'packaging_size_id' => 'required|exists:packaging_sizes,id',
            'product_name' => 'required|string|max:255',
            'image_path' => 'nullable|string|max:255',
        ]);

        $product = Product::create([
            // 'user_id' => auth()->id(),
            'user_id' => 1, // Temporary hardcoded user ID
            'category_id' => $validator['category_id'],
            'brand_id' => $validator['brand_id'],
            'packaging_type_id' => $validator['packaging_type_id'],
            'packaging_size_id' => $validator['packaging_size_id'],
            'product_name' => $validator['product_name'],
            'image_path' => $validator['image_path'],
        ]);

        return response()->json([
            'data' => new ProductResource($product),
            'message' => 'Product created successfully.',
        ], 201);
    }

    public function show($id)
    {
        $product = Product::findOrFail($id);

        return new ProductResource($product);
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        $validator = $request->validate([
            'category_id' => 'sometimes|required|exists:categories,id',
            'brand_id' => 'sometimes|required|exists:brands,id',
            'packaging_type_id' => 'sometimes|required|exists:packaging_types,id',
            'packaging_size_id' => 'sometimes|required|exists:packaging_sizes,id',
            'product_name' => 'sometimes|required|string|max:255',
            'image_path' => 'sometimes|nullable|string|max:255',
        ]);

        $product = Product::findOrFail($id);
        $product->update($validator);

        return response()->json([
            'data' => new ProductResource($product),
            'message' => 'Product updated successfully.',
        ]);
    }
}
