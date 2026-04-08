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
        $config = $game->config_json ?? [];
        $count = (int) ($config['question_count'] ?? 5);
        if ($count <= 0) {
            $count = 5;
        }

        $gameQuestQuery = $game->quests()->where('is_active', true);
        if ($gameQuestQuery->exists()) {
            return $gameQuestQuery
                ->inRandomOrder()
                ->limit($count)
                ->get();
        }

        return Quest::where('game_type', $game->type)
            ->where('is_active', true)
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

            if ($quest->type === 'text') {
                $normalized = trim((string) $answer);
                $normalizedAnswer = trim((string) $correctAnswer);
                $correct = mb_strtolower($normalized) === mb_strtolower($normalizedAnswer);
            } else {
                $correct = (string) $answer === (string) $correctAnswer;
            }
        }

        return PlayResponse::updateOrCreate(
            [
                'play_id' => $play->id,
                'quest_id' => $quest->id,
            ],
            [
                'user_answer' => $answer,
                'is_correct' => $correct,
            ]
        );
    }


    /*
    |--------------------------------------------------------------------------
    | FINISH GAME
    |--------------------------------------------------------------------------
    */

    public function finish(Play $play)
    {
        $correctCount = PlayResponse::where('play_id', $play->id)
            ->where('is_correct', true)
            ->count();

        $play->update([
            'status' => 'finished',
            'score' => $correctCount,
            'finished_at' => now()
        ]);

        return $correctCount;
    }

    public function issueRewardForScore(Game $game, Play $play, int $score)
    {
        $config = $game->config_json ?? [];
        $distribution = $config['reward_distribution'] ?? [];

        if (!is_array($distribution) || empty($distribution)) {
            return null;
        }

        $candidates = collect($distribution)
            ->filter(function ($row) {
                return isset($row['score'], $row['reward_id']) && $row['reward_id'];
            })
            ->map(function ($row) {
                $row['score'] = (int) $row['score'];
                return $row;
            })
            ->sortByDesc('score')
            ->values();

        $selected = $candidates->first(function ($row) use ($score) {
            return $score >= (int) $row['score'];
        });

        if (!$selected) {
            return null;
        }

        $reward = \App\Models\Reward::where('id', $selected['reward_id'])
            ->where('is_active', true)
            ->first();

        if (!$reward) {
            return null;
        }

        return $this->rewardService->issueReward($play, $reward, false);
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
                ->issueReward($play, $segment->reward, false);

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
