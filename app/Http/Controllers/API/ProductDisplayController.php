<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProductDisplay;
use App\Http\Resources\ProductDisplayResource;

class ProductDisplayController extends Controller
{
    public function index()
    {
        $productDisplays = ProductDisplay::all();

        return ProductDisplayResource::collection($productDisplays);
    }

    public function store(Request $request)
    {
        $validator = $request->validate([
            'product_id' => 'required|exists:products,id',
            'price_id' => 'required|exists:prices,id',
            'cell_id' => 'required|exists:cells,id',
            'is_empty' => 'sometimes|boolean',
        ]);

        $productDisplay = ProductDisplay::create([
            'user_id' => 1,
            'product_id' => $validator['product_id'],
            'price_id' => $validator['price_id'],
            'cell_id' => $validator['cell_id'],
            'is_empty' => $validator['is_empty'] ?? false,
        ]);

        return response()->json([
            'data' => new ProductDisplayResource($productDisplay),
            'message' => 'Product display created successfully.',
        ], 201);
    }

    public function show($id)
    {
        $productDisplay = ProductDisplay::findOrFail($id);

        return new ProductDisplayResource($productDisplay);
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        $validator = $request->validate([
            'product_id' => 'sometimes|required|exists:products,id',
            'price_id' => 'sometimes|required|exists:prices,id',
            'cell_id' => 'sometimes|required|exists:cells,id',
            'is_empty' => 'sometimes|boolean',
        ]);

        $productDisplay = ProductDisplay::findOrFail($id);
        $productDisplay->update($validator);

        return response()->json([
            'data' => new ProductDisplayResource($productDisplay),
            'message' => 'Product display updated successfully.',
        ]);
    }

    public function destroy($id)
    {
        $productDisplay = ProductDisplay::findOrFail($id);
        $productDisplay->delete();

        return response()->json([
            'message' => 'Product display deleted successfully.',
        ]);
    }
}
