<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Reward;
use App\Models\Game;

class RewardController extends Controller
{
    public function index()
    {
        $rewards = Reward::with('game')->get();
        return view('dashboard.game-management.rewards.index', compact('rewards'));
    }

    public function create()
    {
        $games = Game::all();
        return view('dashboard.game-management.rewards.create', compact('games'));
    }

    public function edit($id)
    {
        $reward = Reward::findOrFail($id);
        $games = Game::all();
        return view('dashboard.game-management.rewards.edit', compact('reward', 'games'));
    }

    public function show($id)
    {
        $reward = Reward::with('game')->findOrFail($id);
        return view('dashboard.game-management.rewards.show', compact('reward'));
    }
}
