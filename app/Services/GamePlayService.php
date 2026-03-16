<?php

namespace App\Services;

use App\Models\Game;
use App\Models\Play;
use App\Models\Quest;
use App\Models\PlayResponse;
use Illuminate\Support\Str;
use App\Services\RewardService;
use App\Models\SpinSegment;

class GamePlayService
{
    protected $rewardService;

    public function __construct(RewardService $rewardService)
    {
        $this->rewardService = $rewardService;
    }

    /*
    |--------------------------------------------------------------------------
    | START PLAY
    |--------------------------------------------------------------------------
    */

    public function start(Game $game)
    {
        return Play::create([
            'idempotency_key' => (string) Str::uuid(),
            'game_id' => $game->id,
            'status' => 'started',
            'started_at' => now()
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | GET QUESTIONS
    |--------------------------------------------------------------------------
    */

    public function getQuestions(Game $game)
    {
        $count = $game->config['question_count'] ?? 5;

        return Quest::where('game_type', $game->type)
            ->inRandomOrder()
            ->limit($count)
            ->get();
    }


    /*
    |--------------------------------------------------------------------------
    | SUBMIT ANSWER
    |--------------------------------------------------------------------------
    */

    public function submitAnswer(Play $play, Quest $quest, $answer)
    {
        $correct = false;

        if ($quest->answer) {

            $correctAnswer = $quest->answer['correct_answer'] ?? null;

            $correct = $answer == $correctAnswer;

        }

        return PlayResponse::create([
            'play_id' => $play->id,
            'quest_id' => $quest->id,
            'answer' => $answer,
            'is_correct' => $correct
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | FINISH GAME
    |--------------------------------------------------------------------------
    */

    public function finish(Play $play)
    {
        $play->update([
            'status' => 'finished',
            'finished_at' => now()
        ]);

        return PlayResponse::where('play_id', $play->id)
            ->where('is_correct', true)
            ->count();
    }

    public function spin(Game $game, Play $play)
    {
        $segments = SpinSegment::where('game_id', $game->id)
            ->where('is_active', true)
            ->whereHas('reward', function ($query) {
                $query->where('is_active', true);
            })
            ->get();

        if ($segments->isEmpty()) {
            return [
                'segment' => null,
                'reward' => null
            ];
        }

        $segment = $this->pickWeightedSegment($segments);

        $reward = null;

        if ($segment && $segment->reward && $segment->reward->type !== 'none') {

            $reward = $this->rewardService
                ->issueReward($play, $segment->reward);

        }

        return [
            'segment' => $segment,
            'reward' => $reward
        ];
    }

    private function pickWeightedSegment($segments)
    {
        $totalWeight = $segments->sum('weight');

        if ($totalWeight <= 0) {
            return $segments->first();
        }

        $rand = random_int(1, $totalWeight);

        $current = 0;

        foreach ($segments as $segment) {

            $current += $segment->weight;

            if ($rand <= $current) {
                return $segment;
            }

        }

        return $segments->first();
    }

}
