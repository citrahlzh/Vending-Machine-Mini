<?php

namespace App\Http\Controllers\Landing;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Game;
use App\Services\GamePlayService;

class GameController extends Controller
{
    public function index()
    {
        $games = Game::where('is_active', true)->get()->keyBy('type');

        return view('games.index', compact('games'));
    }

    public function quiz()
    {
        return view('games.quiz');
    }

    public function guessImage()
    {
        return view('games.guess-image');
    }

    public function spinWheel(Game $game)
    {
        $segments = $game->spinSegments()
            ->where('is_active', true)
            ->whereHas('reward', function ($query) {
                $query->where('is_active', true);
            })
            ->get();

        return view('games.spin-wheel', compact('game', 'segments'));
    }

    public function play(Request $request, Game $game, GamePlayService $gamePlayService)
    {

        if (!$game->is_active) {
            abort(404);
        }

        $play = $gamePlayService->start($game);
        $request->session()->put('play_id_' . $game->id, $play->id);

        switch ($game->type) {

            case 'quiz':
                return redirect()->route('games.quiz', $game->id);

            case 'guess_image':
                return redirect()->route('games.guess-image', $game->id);

            case 'spin':
                $request->session()->forget('spin_game_' . $game->id);
                return redirect()->route('games.spin-wheel', $game->id);

            default:
                abort(404);
        }

    }
}
