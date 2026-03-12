<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Quest;
use App\Http\Resources\QuestResource;

class QuestController extends Controller
{
    public function index()
    {
        $quests = Quest::all();

        return QuestResource::collection($quests);
    }

    public function store(Request $request)
    {
        $validator = $request->validate([
            'game_id' => 'required|exists:games,id',
            'type' => 'required|string|max:255',
            'game_type' => 'required|string',
            'prompt' => 'required|json',
            'option' => 'sometimes|json',
            'answer' => 'sometimes|json',
            'image_url' => 'sometimes|url|max:255',
            'is_active' => 'sometimes|boolean',
        ]);

        $quest = Quest::create([
            'game_id' => $validator['game_id'],
            'type' => $validator['type'],
            'game_type' => $validator['game_type'],
            'prompt' => $validator['prompt'],
            'option' => $validator['option'],
            'answer' => $validator['answer'],
            'image_url' => $validator['image_url'] ?? null,
            'is_active' => $validator['is_active'] ?? false,
        ]);

        return response()->json([
            'data' => new QuestResource($quest),
            'message' => 'Quest berhasil ditambahkan.',
        ], 201);
    }

    public function show($id)
    {
        $quest = Quest::findOrFail($id);

        return new QuestResource($quest);
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        $validator = $request->validate([
            'game_id' => 'sometimes|required|exists:games,id',
            'type' => 'sometimes|required|string|max:255',
            'game_type' => 'sometimes|required|string',
            'prompt' => 'sometimes|required|json',
            'option' => 'sometimes|required|json',
            'answer' => 'sometimes|required|json',
            'image_url' => 'sometimes|url|max:255',
            'is_active' => 'sometimes|boolean',
        ]);

        $quest = Quest::findOrFail($id);
        $quest->update($validator);

        return response()->json([
            'data' => new QuestResource($quest),
            'message' => 'Quest berhasil diperbarui.',
        ]);
    }

    public function destroy($id)
    {
        $quest = Quest::findOrFail($id);
        $quest->delete();

        return response()->json([
            'message' => 'Quest berhasil dihapus.',
        ]);
    }
}
