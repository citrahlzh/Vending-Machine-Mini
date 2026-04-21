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

    public function __construct(
        VendingDispenseService $dispenseService,
        protected AuditLogService $auditLogService
    ) {
        $this->dispenseService = $dispenseService;
    }

    public function issueReward(Play $play, Reward $reward, bool $dispenseNow = true): ?IssuedReward
    {
        $result = DB::transaction(function () use ($play, $reward) {

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

            return [
                'issued' => $issued,
                'reward' => $reward->load('productDisplay.cell'),
            ];
        });

        if (!$result) {
            return null;
        }

        $issued = $result['issued'];
        $reward = $result['reward'];

        \Log::info('RewardService::issueReward check', [
            'dispenseNow' => $dispenseNow,
            'reward_type' => $reward->type,
            'reward_id' => $reward->id,
            'productDisplay' => $reward->productDisplay?->id,
            'cell' => $reward->productDisplay?->cell?->code,
        ]);

        if ($dispenseNow && $reward->type === 'product' && $reward->productDisplay) {

            $cellCode = $reward->productDisplay->cell?->code;

            if (!$cellCode) {
                $this->auditLogService->logBusinessEvent(
                    'dispense.skipped',
                    "Dispense hadiah {$issued->code} dilewati karena cell tidak tersedia.",
                    [
                        'source' => 'reward',
                        'issued_reward_id' => $issued->id,
                        'reward_id' => $reward->id,
                    ],
                    null,
                    $issued
                );

                return $issued;
            }

            $this->dispenseService->dispense(
                $issued->code,
                $cellCode,
                [
                    'source' => 'reward',
                    'issued_reward_id' => $issued->id,
                    'reward_id' => $reward->id,
                    'play_id' => $play->id,
                ]
            );

            $issued->update([
                'status' => 'redeemed',
                'redeemed_at' => now(),
            ]);
        }

        return $issued;
    }

    public function dispenseIssuedReward(IssuedReward $issuedReward): bool
    {
        $issued = DB::transaction(function () use ($issuedReward) {
            $lockedIssued = IssuedReward::lockForUpdate()->find($issuedReward->id);

            if (!$lockedIssued || $lockedIssued->status === 'redeemed') {
                return $lockedIssued;
            }

            $lockedIssued->loadMissing(['reward.productDisplay.cell']);

            $reward = $lockedIssued->reward;
            if (!$reward || $reward->type !== 'product') {
                return $lockedIssued;
            }

            $cellCode = $reward->productDisplay?->cell?->code;
            if (!$cellCode) {
                $this->auditLogService->logBusinessEvent(
                    'dispense.skipped',
                    "Dispense hadiah {$lockedIssued->code} dilewati karena cell tidak tersedia.",
                    [
                        'source' => 'reward',
                        'issued_reward_id' => $lockedIssued->id,
                        'reward_id' => $reward?->id,
                    ],
                    null,
                    $lockedIssued
                );

                return $lockedIssued;
            }

            $this->dispenseService->dispense(
                $lockedIssued->code,
                $cellCode,
                [
                    'source' => 'reward',
                    'issued_reward_id' => $lockedIssued->id,
                    'reward_id' => $reward->id,
                    'play_id' => $lockedIssued->play_id,
                ]
            );

            $lockedIssued->update([
                'status' => 'redeemed',
                'redeemed_at' => now(),
            ]);

            return $lockedIssued->refresh();
        });

        return (bool) ($issued && $issued->status === 'redeemed');
    }
}
