<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SpinSegment;
use App\Http\Resources\SpinSegmentResource;

class SpinSegmentController extends Controller
{
    public function index()
    {
        $segments = SpinSegment::all();

        return SpinSegmentResource::collection($segments);
    }

    public function store(Request $request)
    {
        $validator = $request->validate([
            'game_id' => 'required|exists:games,id',
            'reward_id' => 'required|exists:rewards,id',
            'label' => 'required|string|max:255',
            'image_url' => 'sometimes|url|max:255',
            'weight' => 'required|integer|min:0',
            'is_active' => 'sometimes|boolean',
        ]);

        $segment = SpinSegment::create([
            'game_id' => $validator['game_id'],
            'reward_id' => $validator['reward_id'],
            'label' => $validator['label'],
            'image_url' => $validator['image_url'],
            'weight' => $validator['weight'],
            'is_active' => $validator['is_active'] ?? false,
        ]);

        return response()->json([
            'data' => new SpinSegmentResource($segment),
            'message' => 'Segmen berhasil ditambahkan.',
        ], 201);
    }

    public function show($id)
    {
        $segment = SpinSegment::findOrFail($id);

        return new SpinSegmentResource($segment);
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        $validator = $request->validate([
            'game_id' => 'sometimes|required|exists:games,id',
            'reward_id' => 'sometimes|required|exists:rewards,id',
            'label' => 'sometimes|required|string|max:255',
            'image_url' => 'sometimes|url|max:255',
            'weight' => 'sometimes|required|integer|min:0',
            'is_active' => 'sometimes|boolean',
        ]);

        $segment = SpinSegment::findOrFail($id);
        $segment->update($validator);

        return response()->json([
            'data' => new SpinSegmentResource($segment),
            'message' => 'Segmen berhasil diperbarui.',
        ], 200);
    }

    public function destroy($id)
    {
        $segment = SpinSegment::findOrFail($id);
        $segment->delete();

        return response()->json([
            'message' => 'Segmen berhasil dihapus.',
        ], 200);
    }
}
