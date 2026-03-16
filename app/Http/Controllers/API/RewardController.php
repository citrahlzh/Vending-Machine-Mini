<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Reward;
use App\Models\ProductDisplay;
use App\Http\Resources\RewardResource;

class RewardController extends Controller
{
    public function index()
    {
        $rewards = Reward::all();

        return RewardResource::collection($rewards);
    }

    public function store(Request $request)
    {
        $validator = $request->validate([
            'code' => 'required|string|max:255|unique:rewards,code',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|string|max:255',
            'product_display_id' => 'nullable|integer',
            'stock' => 'nullable|integer',
            'is_active' => 'sometimes|boolean',
        ]);

        if (($validator['type'] ?? null) === 'product') {
            $displayId = $validator['product_display_id'] ?? null;
            $display = $displayId ? ProductDisplay::with('cell')->find($displayId) : null;

            if (!$display || !$display->cell) {
                return response()->json([
                    'message' => 'Product display tidak valid.',
                ], 422);
            }

            if (array_key_exists('stock', $validator) && $validator['stock'] !== null) {
                $available = (int) $display->cell->qty_current;
                if ((int) $validator['stock'] > $available) {
                    return response()->json([
                        'message' => 'Stok reward tidak boleh melebihi stok display produk.',
                    ], 422);
                }
            }
        }

        $reward = Reward::create([
            'code' => $validator['code'],
            'name' => $validator['name'],
            'description' => $validator['description'],
            'type' => $validator['type'],
            'product_display_id' => $validator['product_display_id'] ?? null,
            'stock' => $validator['stock'] ?? null,
            'is_active' => $validator['is_active'] ?? false,
        ]);

        return response()->json([
            'data' => new RewardResource($reward),
            'message' => 'Reward berhasil ditambahkan.',
        ], 201);
    }

    public function show($id)
    {
        $reward = Reward::findOrFail($id);

        return new RewardResource($reward);
    }

    public function edit($id){
        $reward = Reward::findOrFail($id);

        return new RewardResource($reward);
    }

    public function update(Request $request, $id)
    {
        $validator = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'sometimes|required|string|max:255',
            'product_display_id' => 'sometimes|required|integer|min:0',
            'stock' => 'sometimes|required|integer|min:0',
            'is_active' => 'sometimes|boolean',
        ]);

        $reward = Reward::findOrFail($id);

        $type = $validator['type'] ?? $reward->type;
        if ($type === 'product') {
            $displayId = $validator['product_display_id'] ?? $reward->product_display_id;
            $display = $displayId ? ProductDisplay::with('cell')->find($displayId) : null;

            if (!$display || !$display->cell) {
                return response()->json([
                    'message' => 'Product display tidak valid.',
                ], 422);
            }

            if (array_key_exists('stock', $validator) && $validator['stock'] !== null) {
                $available = (int) $display->cell->qty_current;
                if ((int) $validator['stock'] > $available) {
                    return response()->json([
                        'message' => 'Stok reward tidak boleh melebihi stok display produk.',
                    ], 422);
                }
            }
        }

        $reward->update($validator);

        return response()->json([
            'data' => new RewardResource($reward),
            'message' => 'Reward berhasil diperbarui.',
        ], 200);
    }

    public function destroy($id) {
        $reward = Reward::findOrFail($id);
        $reward->delete();

        return response()->json([
            'message' => 'Hadiah berhasil dihapus.',
        ]);
    }
}
