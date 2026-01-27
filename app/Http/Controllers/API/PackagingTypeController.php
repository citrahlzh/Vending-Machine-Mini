<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PackagingType;
use App\Http\Resources\PackagingTypeResource;

class PackagingTypeController extends Controller
{
    public function index()
    {
        $packagingTypes = PackagingType::all();

        return PackagingTypeResource::collection($packagingTypes);
    }

    public function store(Request $request)
    {
        $validator = $request->validate([
            'packaging_type' => 'required|string|max:255',
        ]);

        $packagingType = PackagingType::create([
            'user_id' => 1,
            'packaging_type' => $validator['packaging_type'],
        ]);

        return response()->json([
            'data' => new PackagingTypeResource($packagingType),
            'message' => 'Packaging type created successfully.',
        ], 201);
    }

    public function show($id)
    {
        $packagingType = PackagingType::findOrFail($id);

        return new PackagingTypeResource($packagingType);
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        $validator = $request->validate([
            'packaging_type' => 'sometimes|required|string|max:255',
        ]);

        $packagingType = PackagingType::findOrFail($id);
        $packagingType->update($validator);

        return response()->json([
            'data' => new PackagingTypeResource($packagingType),
            'message' => 'Packaging type updated successfully.',
        ]);
    }

    public function destroy($id)
    {
        $packagingType = PackagingType::findOrFail($id);
        $packagingType->delete();

        return response()->json([
            'message' => 'Packaging type deleted successfully.',
        ]);
    }
}
