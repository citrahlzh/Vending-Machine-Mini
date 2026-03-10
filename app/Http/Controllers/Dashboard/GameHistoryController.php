<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Play;
use App\Models\Game;
use App\Models\PlayResponse;
use App\Models\IssuedReward;

class GameHistoryController extends Controller
{
    public function index()
    {
        $plays = Play::with('game', 'playResponse', 'issuedReward')->get();
        return view('dashboard.game-management.history.index', compact('plays'));
    }

    public function show($id)
    {
        $play = Play::with('game', 'playResponse', 'issuedReward')->findOrFail($id);
        return view('dashboard.game-management.history.show', compact('play'));
    }
}
