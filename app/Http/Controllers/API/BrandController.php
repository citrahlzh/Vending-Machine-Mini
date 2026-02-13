<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Brand;
use App\Http\Resources\BrandResource;

class BrandController extends Controller
{
    public function index()
    {
        $brands = Brand::all();

        return BrandResource::collection($brands);
    }

    public function store(Request $request)
    {
        $validator = $request->validate([
            'brand_name' => 'required|string|max:255',
            'is_active' => 'required|boolean',
        ]);

        $brand = Brand::create([
            'user_id' => auth()->id(),
            'brand_name' => $validator['brand_name'],
            'is_active' => $validator['is_active'],
        ]);

        return response()->json([
            'data' => new BrandResource($brand),
            'message' => 'Brand created successfully.',
        ], 201);
    }

    public function show($id)
    {
        $brand = Brand::findOrFail($id);

        return new BrandResource($brand);
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        $validator = $request->validate([
            'brand_name' => 'sometimes|required|string|max:255',
            'is_active' => 'sometimes|required|boolean',
        ]);

        $brand = Brand::findOrFail($id);
        $brand->update($validator);

        return response()->json([
            'data' => new BrandResource($brand),
            'message' => 'Brand updated successfully.',
        ]);
    }

    public function destroy($id)
    {
        $brand = Brand::findOrFail($id);
        $brand->delete();

        return response()->json([
            'message' => 'Brand deleted successfully.'
        ]);
    }
}
