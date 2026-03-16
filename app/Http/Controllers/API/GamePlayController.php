<?php

namespace App\Http\Controllers\Api;

use App\Models\Game;
use App\Models\Play;
use App\Models\Quest;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use App\Services\GamePlayService;
use App\Http\Controllers\Controller;

class GamePlayController extends Controller
{

    protected $service;

    public function __construct(GamePlayService $service)
    {
        $this->service = $service;
    }


    public function start($gameId)
    {
        $game = Game::findOrFail($gameId);

        $play = $this->service->start($game);

        $questions = $this->service->getQuestions($game);

        return response()->json([
            'play' => $play,
            'questions' => $questions
        ]);
    }


    public function answer(Request $request)
    {
        $play = Play::findOrFail($request->play_id);

        $quest = Quest::findOrFail($request->quest_id);

        $response = $this->service->submitAnswer(
            $play,
            $quest,
            $request->answer
        );

        return response()->json($response);
    }


    public function finish(Request $request)
    {
        $play = Play::findOrFail($request->play_id);

        $correct = $this->service->finish($play);

        $reward = $this->service->issueReward($play);

        return response()->json([
            'correct_answer' => $correct,
            'reward' => $reward
        ]);
    }

    public function spin(Request $request, Game $game)
    {
        $config = $game->config_json ?? [];
        $maxSpinPerUser = (int) ($config['max_spin_per_user'] ?? 0);
        $cooldownMinutes = (int) ($config['cooldown_minutes'] ?? 0);
        $sessionKey = 'spin_game_' . $game->id;

        $spinMeta = $request->session()->get($sessionKey, [
            'count' => 0,
            'last_spin_at' => null,
        ]);

        if ($maxSpinPerUser > 0 && $spinMeta['count'] >= $maxSpinPerUser) {
            return response()->json([
                'message' => 'Kesempatan spin kamu sudah habis.',
                'code' => 'max_spin_reached',
            ], 429);
        }

        if ($cooldownMinutes > 0 && $spinMeta['last_spin_at']) {
            $lastSpin = Carbon::createFromTimestamp($spinMeta['last_spin_at']);
            $nextAllowed = $lastSpin->copy()->addMinutes($cooldownMinutes);

            if (now()->lt($nextAllowed)) {
                $remainingSeconds = now()->diffInSeconds($nextAllowed);
                $remainingMinutes = max(1, (int) ceil($remainingSeconds / 60));

                return response()->json([
                    'message' => 'Silakan tunggu ' . $remainingMinutes . ' menit sebelum spin lagi.',
                    'code' => 'cooldown_active',
                    'retry_after_seconds' => $remainingSeconds,
                ], 429);
            }
        }

        $playId = $request->session()->get('play_id_' . $game->id);
        $play = $playId ? Play::find($playId) : null;
        if (!$play) {
            $play = $this->service->start($game);
            $request->session()->put('play_id_' . $game->id, $play->id);
        }

        $result = $this->service->spin($game, $play);

        if (!$result['segment']) {
            return response()->json([
                'message' => 'Segmen belum tersedia.'
            ], 422);
        }

        $play->update([
            'status' => 'finished',
            'finished_at' => now()
        ]);

        $request->session()->put($sessionKey, [
            'count' => $spinMeta['count'] + 1,
            'last_spin_at' => now()->timestamp,
        ]);

        $successUrl = null;
        if ($result['reward']) {
            $successUrl = URL::signedRoute('games.result.success', [
                'issuedReward' => $result['reward']->id,
            ]);
        }

        return response()->json([
            'segment_id' => $result['segment']->id,
            'label' => $result['segment']->label,
            'reward' => $result['reward'],
            'success_url' => $successUrl,
        ]);

    }
}
