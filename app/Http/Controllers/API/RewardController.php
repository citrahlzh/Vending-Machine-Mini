<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Reward;
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
            'description' => 'required|string',
            'type' => 'required|string|max:255',
            'product_display_id' => 'sometimes|integer',
            'stock' => 'sometimes|integer',
            'is_active' => 'sometimes|boolean',
        ]);

        $reward = Reward::create([
            'code' => $validator['code'],
            'name' => $validator['name'],
            'description' => $validator['description'],
            'type' => $validator['type'],
            'product_display_id' => $validator['product_display_id'],
            'stock' => $validator['stock'],
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
            'description' => 'sometimes|required|string',
            'type' => 'sometimes|required|string|max:255',
            'product_display_id' => 'sometimes|required|integer|min:0',
            'stock' => 'sometimes|required|integer|min:0',
            'is_active' => 'sometimes|boolean',
        ]);

        $reward = Reward::findOrFail($id);
        $reward->update($validator);

        return response()->json([
            'data' => new RewardResource($reward),
            'message' => 'Reward berhasil diperbarui.',
        ], 200);
    }
}
