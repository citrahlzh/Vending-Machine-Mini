<?php

namespace App\Http\Controllers\Landing;

use App\Models\IssuedReward;
use App\Http\Controllers\Controller;

class GameResultController extends Controller
{

    public function success(IssuedReward $issuedReward)
    {
        $issuedReward->loadMissing(['reward.productDisplay.product']);
        $reward = $issuedReward->reward;

        return view('games.results.success', compact('reward', 'issuedReward'));
    }

    public function fail()
    {
        return view('games.results.fail');
    }

}
