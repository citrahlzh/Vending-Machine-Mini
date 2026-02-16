<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Price;
use App\Http\Resources\PriceResource;

class PriceController extends Controller
{
    public function index()
    {
        $prices = Price::all();

        return PriceResource::collection($prices);
    }

    public function store(Request $request)
    {
        $validator = $request->validate([
            'product_id' => 'required|exists:products,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'price' => 'required|numeric',
            'is_active' => 'sometimes|boolean',
        ]);

        $price = Price::create([
            'user_id' => $request->user()->id,
            'product_id' => $validator['product_id'],
            'start_date' => $validator['start_date'],
            'end_date' => $validator['end_date'],
            'price' => $validator['price'],
            'is_active' => $validator['is_active'] ?? true,
        ]);

        return response()->json([
            'data' => new PriceResource($price),
            'message' => 'Harga berhasil ditambahkan.',
        ], 201);
    }

    public function show($id)
    {
        $price = Price::findOrFail($id);

        return new PriceResource($price);
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        $validator = $request->validate([
            'product_id' => 'sometimes|required|exists:products,id',
            'start_date' => 'sometimes|date',
            'end_date' => 'sometimes|date|after_or_equal:start_date',
            'price' => 'sometimes|required|numeric',
            'is_active' => 'sometimes|required|boolean'
        ]);

        $price = Price::findOrFail($id);
        $price->update($validator);

        return response()->json([
            'data' => new PriceResource($price),
            'message' => 'Harga berhasil diperbarui.',
        ]);
    }

    public function destroy($id)
    {
        $price = Price::findOrFail($id);
        $price->delete();

        return response()->json([
            'message' => 'Harga berhasil dihapus.',
        ]);
    }
}

