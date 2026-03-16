<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Play;

class GameHistoryController extends Controller
{
    public function index()
    {
        $plays = Play::with(['game', 'responses', 'issuedRewards.reward'])->get();

        return view('dashboard.game-management.game-histories.index', compact('plays'));
    }

    public function show($id)
    {
        $play = Play::with(['game', 'responses', 'issuedRewards.reward'])->findOrFail($id);
        
        return view('dashboard.game-management.game-histories.show', compact('play'));
    }
}
