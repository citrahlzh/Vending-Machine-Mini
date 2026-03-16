<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Reward;
use App\Models\Game;
use App\Models\ProductDisplay;

class RewardController extends Controller
{
    public function index()
    {
        $rewards = Reward::all();

        return view('dashboard.game-management.rewards.index', compact('rewards'));
    }

    public function create()
    {
        $games = Game::all();
        $products = ProductDisplay::where('status', 'active')->get();

        return view('dashboard.game-management.rewards.create', compact('games', 'products'));
    }

    public function edit($id)
    {
        $reward = Reward::findOrFail($id);

        $products = ProductDisplay::where('status', 'active')->get();

        return view(
            'dashboard.game-management.rewards.edit',
            compact('reward', 'products')
        );
    }

    public function show($id)
    {
        $reward = Reward::with('productDisplay')->findOrFail($id);

        return view(
            'dashboard.game-management.rewards.show',
            compact('reward')
        );
    }
}
