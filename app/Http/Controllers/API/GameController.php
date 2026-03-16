<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Game;
use App\Models\SpinSegment;
use App\Models\GameQuest;
use Illuminate\Support\Facades\DB;

class GameController extends Controller
{
    public function destroy($id)
    {
        DB::beginTransaction();

        try {
            $game = Game::findOrFail($id);

            SpinSegment::where('game_id', $game->id)->delete();
            GameQuest::where('game_id', $game->id)->delete();

            $game->delete();

            DB::commit();

            return response()->json([
                'message' => 'Game berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Gagal menghapus game',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:quiz,guess_image,spin',
            'is_active' => 'boolean',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',

            'config' => 'nullable|array',
            'quests' => 'nullable|array',

            'segments' => 'nullable|array'
        ]);

        DB::beginTransaction();

        try {
            $isActive = (bool) ($request->is_active ?? false);
            $type = $request->type;

            $game = Game::create([
                'name' => $request->name,
                'type' => $request->type,
                'is_active' => $isActive,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'config_json' => $request->config ?? []
            ]);

            if ($isActive) {
                Game::where('type', $type)
                    ->where('id', '!=', $game->id)
                    ->update(['is_active' => false]);
            }
            /*
            =========================
            QUIZ / GUESS IMAGE
            =========================
            */

            if (in_array($game->type, ['quiz', 'guess_image'])) {

                if (!empty($validated['quests'])) {

                    foreach ($validated['quests'] as $index => $questId) {

                        GameQuest::create([
                            'game_id' => $game->id,
                            'quest_id' => $questId,
                            'order' => $index + 1
                        ]);

                    }

                }

            }

            /*
            =========================
            SPIN
            =========================
            */

            if ($game->type === 'spin') {

                if (!empty($validated['segments'])) {

                    foreach ($validated['segments'] as $index => $segment) {

                        $imagePath = null;

                        $image = $request->file("segments.$index.image");

                        if ($image) {
                            $imagePath = $image->store('spin_segments', 'public');
                        }

                        SpinSegment::create([
                            'game_id' => $game->id,
                            'reward_id' => $segment['reward_id'],
                            'label' => $segment['label'],
                            'image_url' => $imagePath,
                            'weight' => $segment['weight'],
                            'is_active' => true
                        ]);

                    }

                }

            }

            DB::commit();

            return response()->json([
                'message' => 'Game berhasil dibuat',
                'data' => $game
            ], 201);

        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                'message' => 'Gagal membuat game',
                'error' => $e->getMessage()
            ], 500);

        }
    }

    public function update(Request $request, Game $game)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:quiz,guess_image,spin',
            'is_active' => 'boolean',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'config' => 'nullable|array',
            'quests' => 'nullable|array',
            'segments' => 'nullable|array'
        ]);

        DB::beginTransaction();

        try {
            $game->update([
                'name' => $request->name,
                'type' => $request->type,
                'is_active' => $request->is_active,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'config_json' => $request->config ?? []
            ]);

            if ($game->is_active) {
                Game::where('type', $game->type)
                    ->where('id', '!=', $game->id)
                    ->update(['is_active' => false]);
            }

            if (in_array($game->type, ['quiz', 'guess_image'])) {
                GameQuest::where('game_id', $game->id)->delete();

                if (!empty($validated['quests'])) {
                    foreach ($validated['quests'] as $index => $questId) {
                        GameQuest::create([
                            'game_id' => $game->id,
                            'quest_id' => $questId,
                            'order' => $index + 1
                        ]);
                    }
                }
            }

            if ($game->type === 'spin') {
                SpinSegment::where('game_id', $game->id)->delete();

                if (!empty($validated['segments'])) {
                    foreach ($validated['segments'] as $segment) {
                        SpinSegment::create([
                            'game_id' => $game->id,
                            'label' => $segment['label'],
                            'reward_id' => $segment['reward_id'],
                            'weight' => $segment['weight'],
                            'is_active' => true
                        ]);
                    }
                }
            }

            DB::commit();

            return response()->json([
                'message' => 'Game berhasil diperbarui'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Gagal memperbarui game',
                'error' => $e->getMessage()
            ], 500);
        }

    }
}
