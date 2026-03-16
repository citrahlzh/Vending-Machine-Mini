<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Quest;
use App\Models\Game;

class QuestController extends Controller
{
    public function index()
    {
        $quests = Quest::all();

        return view('dashboard.game-management.quests.index', compact(['quests']));
    }

    public function create()
    {
        return view('dashboard.game-management.quests.create');
    }

    public function edit($id)
    {
        $quest = Quest::findOrFail($id);

        return view('dashboard.game-management.quests.edit', compact('quest'));
    }

    public function show($id)
    {
        $quest = Quest::findOrFail($id);

        return view('dashboard.game-management.quests.show', compact('quest'));
    }
}
