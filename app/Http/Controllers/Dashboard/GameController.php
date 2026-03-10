<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Game;
use APP\Models\Quest;
use App\Models\Reward;
use App\Models\SpinSegment;

class GameController extends Controller
{
    public function index()
    {
        $games = Game::with('quests', 'rewards', 'spinSegments')->get();
        return view('dashboard.game-management.index', compact('games'));
    }

    public function create()
    {
        $rewards = Reward::all();
        return view('dashboard.game-management.create', compact('rewards'));
    }

    public function edit($id)
    {
        $game = Game::with('quests', 'rewards', 'spinSegments')->findOrFail($id);
        $rewards = Reward::all();
        return view('dashboard.game-management.edit', compact('game', 'rewards'));
    }

    public function show($id)
    {
        $game = Game::with('quests', 'rewards', 'spinSegments')->findOrFail($id);
        return view('dashboard.game-management.show', compact('game'));
    }
}
