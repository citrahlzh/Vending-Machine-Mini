<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\QuestService;
use App\Models\Quest;
use App\Http\Resources\QuestResource;

class QuestController extends Controller
{
    protected $questService;

    public function __construct(QuestService $questService)
    {
        $this->questService = $questService;
    }

    public function index()
    {
        $quests = Quest::latest()->get();

        return QuestResource::collection($quests);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([

            'type' => 'required|in:multiple_choice,text',

            'game_type' => 'required|in:quiz,guess_image',

            'prompt' => 'required|string',

            'option' => 'nullable|array',

            'correct_answer' => 'required|string',

            'image_url' => 'nullable|image|max:2048'

        ]);

        $this->questService->create($request->all());

        return response()->json([
            'message' => 'Soal berhasil ditambahkan.'
        ], 201);
    }

    public function show($id)
    {
        $quest = Quest::findOrFail($id);

        return new QuestResource($quest);
    }

    public function update(Request $request, $id)
    {
        $quest = Quest::findOrFail($id);

        $validated = $request->validate([
            'type' => 'sometimes|in:multiple_choice,text',
            'game_type' => 'sometimes|in:quiz,guess_image',
            'prompt' => 'sometimes|string',
            'option' => 'nullable|array',
            'correct_answer' => 'sometimes|string',
            'image_url' => 'nullable|image|max:2048',
            'is_active' => 'sometimes|boolean'
        ]);

        $this->questService->update($quest, $request->all());

        return response()->json([
            'message' => 'Soal berhasil diperbarui'
        ]);
    }

    public function destroy($id)
    {
        $quest = Quest::findOrFail($id);

        $quest->delete();

        return response()->json([
            'message' => 'Soal berhasil dihapus'
        ]);
    }
}
