<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PackagingSize;
use App\Http\Resources\PackagingSizeResource;

class PackagingSizeController extends Controller
{
    public function index()
    {
        $packagingSizes = PackagingSize::all();

        return PackagingSizeResource::collection($packagingSizes);
    }

    public function store(Request $request)
    {
        $validator = $request->validate([
            'size' => 'required|string|max:255',
        ]);

        $packagingSize = PackagingSize::create([
            'user_id' => $request->user()->id,
            'size' => $validator['size'],
        ]);

        return response()->json([
            'data' => new PackagingSizeResource($packagingSize),
            'message' => 'Ukuran kemasan berhasil ditambahkan.',
        ], 201);
    }

    public function show($id)
    {
        $packagingSize = PackagingSize::findOrFail($id);

        return new PackagingSizeResource($packagingSize);
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        $validator = $request->validate([
            'size' => 'sometimes|required|string|max:255',
        ]);

        $packagingSize = PackagingSize::findOrFail($id);
        $packagingSize->update($validator);

        return response()->json([
            'data' => new PackagingSizeResource($packagingSize),
            'message' => 'Ukuran kemasan berhasil diperbarui.',
        ]);
    }

    public function destroy($id)
    {
        $packagingSize = PackagingSize::findOrFail($id);
        $packagingSize->delete();

        return response()->json([
            'message' => 'Ukuran kemasan berhasil dihapus.'
        ]);
    }
}

