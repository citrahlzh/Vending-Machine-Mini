<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Game;
use App\Http\Resources\GameResource;

class GameController extends Controller
{
    public function index()
    {
        $games = Game::all();

        return GameResource::collection($games);
    }

    public function store(Request $request)
    {
        $validator = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'config_json' => 'required|json',
            'is_active' => 'sometimes|boolean',
            'start_date' => 'sometimes|date',
            'end_date' => 'sometimes|date|after_or_equal:start_date',
        ]);

        $game = Game::create([
            'name' => $validator['name'],
            'type' => $validator['type'],
            'config_json' => $validator['config_json'],
            'is_active' => $validator['is_active'] ?? false,
            'start_date' => $validator['start_date'] ?? null,
            'end_date' => $validator['end_date'] ?? null,
        ]);

        return response()->json([
            'data' => new GameResource($game),
            'message' => 'Game berhasil ditambahkan.',
        ], 201);
    }

    public function show($id)
    {
        $game = Game::findOrFail($id);

        return new GameResource($game);
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        $validator = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'type' => 'sometimes|required|string|max:255',
            'config_json' => 'sometimes|required|json',
            'is_active' => 'sometimes|boolean',
            'start_date' => 'sometimes|date',
            'end_date' => 'sometimes|date|after_or_equal:start_date',
        ]);

        $game = Game::findOrFail($id);
        $game->update($validator);

        return response()->json([
            'data' => new GameResource($game),
            'message' => 'Game berhasil diperbarui.',
        ], 200);
    }

    public function destroy($id)
    {
        $game = Game::findOrFail($id);
        $game->delete();

        return response()->json([
            'message' => 'Game berhasil dihapus.',
        ], 200);
    }
}
