<?php

namespace App\Http\Controllers\Landing;

use App\Models\IssuedReward;
use App\Http\Controllers\Controller;
use App\Services\RewardService;
use Illuminate\Support\Facades\URL;
use Illuminate\Http\JsonResponse;

class GameResultController extends Controller
{

    public function success(IssuedReward $issuedReward)
    {
        $issuedReward->loadMissing(['reward.productDisplay.product', 'play.game']);
        $reward = $issuedReward->reward;
        $game = $issuedReward->play?->game;
        $maxSpin = (int) ($game?->config_json['max_spin_per_user'] ?? 0);
        $spinMeta = $game
            ? session()->get('spin_game_' . $game->id, ['count' => 0, 'last_spin_at' => null])
            : ['count' => 0, 'last_spin_at' => null];
        $spinUsed = (int) ($spinMeta['count'] ?? 0);
        $spinRemaining = $maxSpin > 0 ? max(0, $maxSpin - $spinUsed) : null;
        $dispenseUrl = URL::signedRoute('games.result.dispense', [
            'issuedReward' => $issuedReward->id,
        ]);

        return view('games.results.success', compact('reward', 'issuedReward', 'dispenseUrl', 'game', 'maxSpin', 'spinRemaining'));
    }

    public function fail()
    {
        return view('games.results.fail');
    }

    public function dispense(IssuedReward $issuedReward, RewardService $rewardService): JsonResponse
    {
        $issuedReward->loadMissing(['reward.productDisplay.cell']);

        if ($issuedReward->status === 'redeemed') {
            return response()->json([
                'status' => 'already',
            ]);
        }

        if ($issuedReward->reward?->type !== 'product') {
            return response()->json([
                'status' => 'skipped',
            ]);
        }

        $dispensed = $rewardService->dispenseIssuedReward($issuedReward);

        return response()->json([
            'status' => $dispensed ? 'dispensed' : 'skipped',
        ]);
    }

}
