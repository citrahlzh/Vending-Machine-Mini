<?php

namespace App\Services;

use App\Models\Play;
use App\Models\Reward;
use App\Models\IssuedReward;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RewardService
{

    protected $dispenseService;

    public function __construct(VendingDispenseService $dispenseService)
    {
        $this->dispenseService = $dispenseService;
    }

    public function issueReward(Play $play, Reward $reward): ?IssuedReward
    {

        $issued = DB::transaction(function () use ($play, $reward) {

            $reward = Reward::lockForUpdate()->find($reward->id);

            if ($reward->stock !== null && $reward->stock <= 0) {
                if ($reward->is_active) {
                    $reward->update(['is_active' => false]);
                }
                return null;
            }

            $issued = IssuedReward::create([
                'play_id' => $play->id,
                'reward_id' => $reward->id,
                'code' => strtoupper(Str::random(10)),
                'status' => 'issued',
                'issued_at' => now(),
            ]);

            if ($reward->stock !== null) {
                $reward->decrement('stock');
                $reward->refresh();
                if ($reward->stock <= 0 && $reward->is_active) {
                    $reward->update(['is_active' => false]);
                }
            }

            if ($reward->type === 'product' && $reward->productDisplay && $reward->productDisplay->cell) {
                $cell = $reward->productDisplay->cell()->lockForUpdate()->first();

                if ($cell && (int) $cell->qty_current > 0) {
                    $cell->decrement('qty_current');
                    $cell->refresh();
                }

                if ($cell && (int) $cell->qty_current <= 0 && !$reward->productDisplay->is_empty) {
                    $reward->productDisplay->update(['is_empty' => true]);
                }
            }

            return $issued;

        });

        if (!$issued) {
            return null;
        }

        if ($reward->type === 'product' && $reward->productDisplay) {

            $cellCode = $reward->productDisplay->cell?->code;

            if (!$cellCode) {
                return $issued;
            }

            $this->dispenseService->dispense(
                $issued->code,
                $cellCode
            );

            $issued->update([
                'status' => 'redeemed',
                'redeemed_at' => now(),
            ]);

        }

        return $issued;
    }

}
