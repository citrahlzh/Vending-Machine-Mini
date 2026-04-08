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
        $activeTypes = Game::activeNow()
            ->pluck('type')
            ->unique()
            ->values();

        return view('games.index', compact('activeTypes'));
    }

    public function quiz(Request $request, Game $game, GamePlayService $gamePlayService)
    {
        if (!$game->is_active || $game->type !== 'quiz') {
            abort(404);
        }

        if (!Game::activeNow()->where('id', $game->id)->exists()) {
            abort(404);
        }

        $playId = $request->session()->get('play_id_' . $game->id);
        $play = $playId ? $game->plays()->find($playId) : null;
        if (!$play || $play->status === 'finished') {
            $play = $gamePlayService->start($game);
            $request->session()->put('play_id_' . $game->id, $play->id);
        }

        $questions = $gamePlayService->getQuestions($game);
        $config = $game->config_json ?? [];

        return view('games.quiz', compact('game', 'questions', 'play', 'config'));
    }

    public function guessImage(Request $request, Game $game, GamePlayService $gamePlayService)
    {
        if (!$game->is_active || $game->type !== 'guess_image') {
            abort(404);
        }

        if (!Game::activeNow()->where('id', $game->id)->exists()) {
            abort(404);
        }

        $playId = $request->session()->get('play_id_' . $game->id);
        $play = $playId ? $game->plays()->find($playId) : null;
        if (!$play || $play->status === 'finished') {
            $play = $gamePlayService->start($game);
            $request->session()->put('play_id_' . $game->id, $play->id);
        }

        $questions = $gamePlayService->getQuestions($game);
        $config = $game->config_json ?? [];

        return view('games.guess-image', compact('game', 'questions', 'play', 'config'));
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

        if (!Game::activeNow()->where('id', $game->id)->exists()) {
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

    public function playByType(Request $request, string $type, GamePlayService $gamePlayService)
    {
        if (!in_array($type, ['quiz', 'guess_image', 'spin'], true)) {
            abort(404);
        }

        $game = Game::activeNow()
            ->where('type', $type)
            ->inRandomOrder()
            ->first();

        if (!$game) {
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
