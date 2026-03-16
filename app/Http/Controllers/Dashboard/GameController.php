<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Game;
use App\Models\Quest;
use App\Models\Reward;

class GameController extends Controller
{
    public function index()
    {
        $games = Game::withCount(['quests', 'spinSegments'])->latest()->get();

        return view('dashboard.game-management.games.index', compact('games'));
    }

    public function create(Request $request)
    {
        $type = $request->query('type');

        $quests = [];
        $rewards = [];

        if ($type === 'quiz' || $type === 'guess_image') {
            $quests = Quest::where('game_type', $type)
                ->where('is_active', true)
                ->get();
        }

        if (in_array($type, ['quiz', 'guess_image', 'spin'])) {
            $rewards = Reward::where('is_active', true)->get();
        }

        return view('dashboard.game-management.games.create', [
            'type' => $type,
            'quests' => $quests,
            'rewards' => $rewards
        ]);
    }

    public function show($id)
    {
        $game = Game::with(['quests', 'spinSegments.reward'])->findOrFail($id);

        return view('dashboard.game-management.games.show', compact('game'));
    }

    public function edit($id)
    {
        $game = Game::findOrFail($id);

        $quests = Quest::where('game_type', $game->type)->get();
        $rewards = Reward::where('is_active', true)->get();

        return view('dashboard.game-management.games.edit', compact(
            'game',
            'quests',
            'rewards'
        ));
    }
}
