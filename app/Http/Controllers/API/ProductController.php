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
            'image_url' => 'required|file|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $image_url = null;
        if ($request->hasFile('image_url')) {
            $image_url = $request->file('image_url')->store('products', 'public');
        }

        $product = Product::create([
            'user_id' => auth()->id(),
            'category_id' => $validator['category_id'],
            'brand_id' => $validator['brand_id'],
            'packaging_type_id' => $validator['packaging_type_id'],
            'packaging_size_id' => $validator['packaging_size_id'],
            'product_name' => $validator['product_name'],
            'image_url' => $image_url,
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
        $validated = $request->validate([
            'category_id' => 'sometimes|required|exists:categories,id',
            'brand_id' => 'sometimes|required|exists:brands,id',
            'packaging_type_id' => 'sometimes|required|exists:packaging_types,id',
            'packaging_size_id' => 'sometimes|required|exists:packaging_sizes,id',
            'product_name' => 'sometimes|required|string|max:255',
            'image_url' => 'sometimes|file|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $product = Product::findOrFail($id);

        if ($request->hasFile('image_url')) {
            $image_url = $request->file('image_url')->store('products', 'public');
            $validated['image_url'] = $image_url;
        } else {
            unset($validated['image_url']);
        }

        $product->fill($validated);
        $product->save();

        return response()->json([
            'data' => new ProductResource($product),
            'message' => 'Product updated successfully.',
        ]);
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();

        return response()->json([
            'message' => 'Product deleted successfully.',
        ]);
    }
}
