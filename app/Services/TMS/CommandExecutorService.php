<?php

namespace App\Services\TMS;

use App\Models\Ad;
use App\Models\Cell;
use App\Models\Game;
use App\Models\Machine;
use App\Models\Price;
use App\Models\Product;
use App\Models\ProductDisplay;
use App\Models\Quest;
use App\Models\Reward;
use App\Models\SiteSetting;
use App\Models\SpinSegment;
use App\Models\TmsCommandQueue;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CommandExecutorService
{
    public function processNext(): void
    {
        $cmd = TmsCommandQueue::pending()->first();
        if (!$cmd) {
            return;
        }

        $cmd->update(['status' => 'executing']);

        try {
            match ($cmd->type) {
                'sync_prices'    => $this->syncPrices($cmd->payload),
                'sync_planogram' => $this->syncPlanogram($cmd->payload),
                'sync_ads'       => $this->syncAds($cmd->payload),
                'reboot_app'     => $this->rebootApp($cmd->payload),
                'update_config'  => $this->updateConfig($cmd->payload),
                'sync_games'     => $this->syncGames($cmd->payload),
                default          => throw new \Exception("Tipe command tidak dikenal: {$cmd->type}"),
            };

            $cmd->update(['status' => 'done', 'executed_at' => now()]);
            $this->sendAck($cmd->tms_command_id, 'success');
            Log::info("[TMS] Command {$cmd->type} berhasil dieksekusi.");
        } catch (\Throwable $e) {
            $cmd->update([
                'status'        => 'failed',
                'error_message' => $e->getMessage(),
                'executed_at'   => now(),
            ]);
            $this->sendAck($cmd->tms_command_id, 'failed', $e->getMessage());
            Log::error("[TMS] Command {$cmd->type} gagal: " . $e->getMessage());
        }
    }

    private function sendAck(int $commandId, string $result, ?string $errorMessage = null): void
    {
        try {
            Http::timeout(10)
                ->withHeaders(['X-Machine-Key' => config('tms.api_key')])
                ->post(config('tms.base_url') . "/api/vm/commands/{$commandId}/ack", [
                    'machine_code'  => config('tms.machine_code'),
                    'result'        => $result,
                    'error_message' => $errorMessage,
                ]);
        } catch (\Throwable $e) {
            Log::error("[TMS] Gagal kirim ack untuk command {$commandId}: " . $e->getMessage());
        }
    }

    private function syncPrices(array $payload): void
    {
        $items = $this->extractItems($payload, ['prices', 'items', 'data']);
        $userId = $this->resolveDefaultUserId($payload);
        $defaultStart = now();
        $defaultEnd = now()->addYear();

        DB::transaction(function () use ($items, $userId, $defaultStart, $defaultEnd) {
            foreach ($items as $item) {
                $productId = $this->resolveProductId($item);

                if (!$productId) {
                    Log::warning('[TMS] sync_prices dilewati karena product_id tidak ditemukan.', [
                        'payload' => $item,
                    ]);
                    continue;
                }

                $priceValue = data_get($item, 'price', data_get($item, 'amount'));
                if ($priceValue === null || $priceValue === '') {
                    Log::warning('[TMS] sync_prices dilewati karena nilai price kosong.', [
                        'product_id' => $productId,
                    ]);
                    continue;
                }

                $startDate = $this->parseDate(
                    data_get($item, 'start_date', data_get($item, 'effective_at')),
                    $defaultStart
                );
                $endDate = $this->parseDate(
                    data_get($item, 'end_date', data_get($item, 'expired_at')),
                    $defaultEnd
                );
                $isActive = $this->toBoolean(data_get($item, 'is_active', true), true);

                $price = null;
                if (data_get($item, 'price_id')) {
                    $price = Price::query()->find(data_get($item, 'price_id'));
                }

                if (!$price) {
                    $price = Price::updateOrCreate(
                        [
                            'product_id' => $productId,
                            'start_date' => $startDate,
                            'end_date'   => $endDate,
                        ],
                        [
                            'user_id'    => data_get($item, 'user_id', $userId),
                            'price'      => $priceValue,
                            'is_active'  => $isActive,
                        ]
                    );
                } else {
                    $price->fill([
                        'product_id' => $productId,
                        'user_id'    => data_get($item, 'user_id', $userId),
                        'start_date' => $startDate,
                        'end_date'   => $endDate,
                        'price'      => $priceValue,
                        'is_active'  => $isActive,
                    ]);
                    $price->save();
                }

                if ($isActive) {
                    Price::query()
                        ->where('product_id', $productId)
                        ->where('id', '!=', $price->id)
                        ->update(['is_active' => false]);
                }
            }

            Price::deactivateOutsideRange();
        });
    }

    private function syncPlanogram(array $payload): void
    {
        $items = $this->extractItems($payload, ['planogram', 'slots', 'cells', 'product_displays']);
        $userId = $this->resolveDefaultUserId($payload);

        DB::transaction(function () use ($items, $userId) {
            foreach ($items as $item) {
                $cell = $this->upsertCell($item);

                if (!$cell) {
                    Log::warning('[TMS] sync_planogram dilewati karena data cell tidak lengkap.', [
                        'payload' => $item,
                    ]);
                    continue;
                }

                $productDisplayData = [
                    'user_id'  => data_get($item, 'user_id', $userId),
                    'cell_id'  => $cell->id,
                    'is_empty' => $this->toBoolean(data_get($item, 'is_empty'), false),
                    'status'   => $this->normalizeStatus(data_get($item, 'status'), 'active'),
                ];

                $productId = $this->resolveProductId($item);
                $priceId = $this->resolvePriceId($item, $productId);

                if (!$productId || !$priceId) {
                    if (data_get($item, 'product_display_id')) {
                        $productDisplay = ProductDisplay::query()->find(data_get($item, 'product_display_id'));

                        if ($productDisplay) {
                            $productDisplay->fill([
                                'user_id'  => $productDisplayData['user_id'],
                                'cell_id'  => $cell->id,
                                'is_empty' => $productDisplayData['is_empty'],
                                'status'   => $productDisplayData['status'],
                            ]);
                            $productDisplay->save();
                        }
                    }

                    Log::warning('[TMS] sync_planogram dilewati karena product_id atau price_id tidak ditemukan.', [
                        'payload' => $item,
                    ]);
                    continue;
                }

                $productDisplayData['product_id'] = $productId;
                $productDisplayData['price_id'] = $priceId;

                $productDisplay = null;
                if (data_get($item, 'product_display_id')) {
                    $productDisplay = ProductDisplay::query()->find(data_get($item, 'product_display_id'));
                }

                if (!$productDisplay) {
                    $productDisplay = ProductDisplay::query()->firstOrNew([
                        'cell_id' => $cell->id,
                    ]);
                }

                $productDisplay->fill($productDisplayData);
                $productDisplay->save();
            }
        });
    }

    private function syncAds(array $payload): void
    {
        $items = $this->extractItems($payload, ['ads', 'items', 'data']);

        DB::transaction(function () use ($items) {
            foreach ($items as $item) {
                $ad = null;

                if (data_get($item, 'id')) {
                    $ad = Ad::query()->find(data_get($item, 'id'));
                }

                if (!$ad) {
                    $ad = Ad::query()->firstOrNew([
                        'title' => (string) data_get($item, 'title', ''),
                    ]);
                }

                $ad->fill([
                    'title'    => (string) data_get($item, 'title', $ad->title ?? ''),
                    'image_url'=> (string) data_get($item, 'image_url', $ad->image_url ?? ''),
                    'status'   => $this->normalizeStatus(data_get($item, 'status'), 'inactive'),
                ]);
                $ad->save();
            }
        });
    }

    private function rebootApp(array $payload = []): void
    {
        Artisan::call('queue:restart');
        Artisan::call('optimize:clear');

        Log::info('[TMS] Reboot app diproses sebagai soft restart.', [
            'payload' => $payload,
        ]);
    }

    private function updateConfig(array $payload): void
    {
        DB::transaction(function () use ($payload) {
            $machineData = data_get($payload, 'machine', []);
            if (!$this->isAssociativeArray($machineData)) {
                $machineData = [];
            }

            $siteSettings = data_get($payload, 'site_settings', data_get($payload, 'settings', []));
            if ($this->isAssociativeArray($siteSettings)) {
                $siteSettings = [$siteSettings];
            }

            $machine = Machine::query()->latest('id')->first();
            if ($machine) {
                $machine->fill(array_filter([
                    'name'            => data_get($machineData, 'name', data_get($payload, 'machine_name')),
                    'code'            => data_get($machineData, 'code', data_get($payload, 'machine_code')),
                    'serial_number'   => data_get($machineData, 'serial_number', data_get($payload, 'machine_serial_number')),
                    'location'        => data_get($machineData, 'location', data_get($payload, 'machine_location')),
                    'operator_name'   => data_get($machineData, 'operator_name', data_get($payload, 'machine_operator_name')),
                    'category'        => data_get($machineData, 'category'),
                    'size'            => data_get($machineData, 'size'),
                    'photo_url'       => data_get($machineData, 'photo_url'),
                    'is_android'      => data_get($machineData, 'is_android'),
                    'status'          => data_get($machineData, 'status'),
                    'condition_status'=> data_get($machineData, 'condition_status'),
                ], static fn ($value) => $value !== null));
                $machine->save();
            }

            foreach ($siteSettings as $setting) {
                if (!$this->isAssociativeArray($setting)) {
                    continue;
                }

                $key = (string) data_get($setting, 'key');
                if ($key === '') {
                    continue;
                }

                $label = (string) data_get($setting, 'label', ucfirst(str_replace('_', '', $key)));
                $value = data_get($setting, 'value');
                $type = (string) data_get($setting, 'type', 'text');
                $group = data_get($setting, 'group');

                SiteSetting::query()->updateOrCreate(
                    ['key' => $key],
                    [
                        'label' => $label,
                        'value' => is_array($value) ? json_encode($value) : $value,
                        'type'  => $type,
                        'group' => $group,
                    ]
                );
            }
        });

        cache()->forget('site_settings');
        cache()->forget('current_machine');
    }

    private function syncGames(array $payload): void
    {
        $items = $this->extractItems($payload, ['games', 'items', 'data']);

        DB::transaction(function () use ($items) {
            foreach ($items as $item) {
                $game = $this->upsertGame($item);

                if (!$game) {
                    Log::warning('[TMS] sync_games dilewati karena data game tidak valid.', [
                        'payload' => $item,
                    ]);
                    continue;
                }

                if (in_array($game->type, ['quiz', 'guess_image'], true)) {
                    $this->syncGameQuests($game, $item);
                }

                if ($game->type === 'spin') {
                    $this->syncSpinSegments($game, $item);
                }
            }
        });
    }

    private function syncGameQuests(Game $game, array $payload): void
    {
        $items = $this->extractItems($payload, ['quests', 'questions']);
        if ($items === []) {
            return;
        }

        $syncData = [];

        foreach ($items as $order => $item) {
            $quest = null;

            if (data_get($item, 'id')) {
                $quest = Quest::query()->find(data_get($item, 'id'));
            }

            if (!$quest) {
                $quest = Quest::query()->firstOrNew([
                    'prompt'    => (string) data_get($item, 'prompt', ''),
                    'game_type' => $game->type,
                ]);
            }

            $quest->fill([
                'type'            => data_get($item, 'type', $quest->type ?? 'multiple_choice'),
                'game_type'       => $game->type,
                'prompt'          => (string) data_get($item, 'prompt', $quest->prompt ?? ''),
                'option'          => $this->normalizeJsonField(data_get($item, 'option', data_get($item, 'options'))),
                'answer'          => $this->normalizeJsonField(data_get($item, 'answer')),
                'image_url'       => data_get($item, 'image_url'),
                'answer_image_url'=> data_get($item, 'answer_image_url'),
                'is_active'       => $this->toBoolean(data_get($item, 'is_active', true), true),
            ]);
            $quest->save();

            $syncData[$quest->id] = ['order' => data_get($item, 'order', $order + 1)];
        }

        $game->quests()->sync($syncData);
    }

    private function syncSpinSegments(Game $game, array $payload): void
    {
        $items = $this->extractItems($payload, ['spin_segments', 'segments']);
        if ($items === []) {
            return;
        }

        foreach ($items as $item) {
            $rewardId = $this->resolveRewardId($item);
            if (!$rewardId) {
                Log::warning('[TMS] sync_games spin dilewati karena reward_id tidak ditemukan.', [
                    'payload' => $item,
                ]);
                continue;
            }

            $segment = null;
            if (data_get($item, 'id')) {
                $segment = SpinSegment::query()->find(data_get($item, 'id'));
            }

            if (!$segment) {
                $segment = SpinSegment::query()->firstOrNew([
                    'game_id' => $game->id,
                    'label'   => (string) data_get($item, 'label', ''),
                ]);
            }

            $segment->fill([
                'game_id'   => $game->id,
                'reward_id' => $rewardId,
                'label'     => (string) data_get($item, 'label', $segment->label ?? ''),
                'image_url' => data_get($item, 'image_url'),
                'weight'    => (int) data_get($item, 'weight', 1),
                'is_active' => $this->toBoolean(data_get($item, 'is_active', true), true),
            ]);
            $segment->save();
        }
    }

    private function upsertGame(array $item): ?Game
    {
        $name = (string) data_get($item, 'name', '');
        $type = (string) data_get($item, 'type', '');

        if ($name === '' || $type === '') {
            return null;
        }

        $game = null;
        if (data_get($item, 'id')) {
            $game = Game::query()->find(data_get($item, 'id'));
        }

        if (!$game) {
            $game = Game::query()->firstOrNew([
                'name' => $name,
                'type' => $type,
            ]);
        }

        $game->fill([
            'name'        => $name,
            'type'        => $type,
            'config_json' => $this->normalizeJsonField(data_get($item, 'config_json', data_get($item, 'config', []))),
            'is_active'   => $this->toBoolean(data_get($item, 'is_active', true), true),
            'start_date'   => $this->parseDate(data_get($item, 'start_date'), null),
            'end_date'     => $this->parseDate(data_get($item, 'end_date'), null),
        ]);
        $game->save();

        return $game;
    }

    private function upsertCell(array $item): ?Cell
    {
        $cellCode = (string) data_get($item, 'code', data_get($item, 'cell_code', ''));
        $row = data_get($item, 'row');
        $column = data_get($item, 'column');

        if ($cellCode === '' && ($row === null || $column === null)) {
            return null;
        }

        $cell = null;
        if (data_get($item, 'cell_id')) {
            $cell = Cell::query()->find(data_get($item, 'cell_id'));
        }

        if (!$cell && $cellCode !== '') {
            $cell = Cell::query()->firstOrNew([
                'code' => $cellCode,
            ]);
        }

        if (!$cell) {
            $cell = Cell::query()->firstOrNew([
                'row'    => (int) $row,
                'column' => (int) $column,
            ]);
        }

        $cell->fill([
            'row'         => data_get($item, 'row', $cell->row ?? 0),
            'column'      => data_get($item, 'column', $cell->column ?? 0),
            'code'        => $cellCode !== '' ? $cellCode : ($cell->code ?? ''),
            'qty_current' => (int) data_get($item, 'qty_current', data_get($item, 'quantity', $cell->qty_current ?? 0)),
            'capacity'    => (int) data_get($item, 'capacity', $cell->capacity ?? 0),
        ]);
        $cell->save();

        return $cell;
    }

    private function resolveProductId(array $item): ?int
    {
        $productId = data_get($item, 'product_id');
        if ($productId) {
            return (int) $productId;
        }

        $productDisplayId = data_get($item, 'product_display_id');
        if ($productDisplayId) {
            $displayProductId = ProductDisplay::query()->whereKey($productDisplayId)->value('product_id');
            if ($displayProductId) {
                return (int) $displayProductId;
            }
        }

        $productName = data_get($item, 'product_name');
        if ($productName) {
            $foundProductId = Product::query()
                ->where('product_name', $productName)
                ->value('id');

            if ($foundProductId) {
                return (int) $foundProductId;
            }
        }

        return null;
    }

    private function resolveRewardId(array $item): ?int
    {
        $rewardId = data_get($item, 'reward_id');
        if ($rewardId) {
            return (int) $rewardId;
        }

        $rewardCode = data_get($item, 'reward_code');
        if ($rewardCode) {
            $foundRewardId = Reward::query()
                ->where('code', $rewardCode)
                ->value('id');

            if ($foundRewardId) {
                return (int) $foundRewardId;
            }
        }

        $rewardName = data_get($item, 'reward_name');
        if ($rewardName) {
            $foundRewardId = Reward::query()
                ->where('name', $rewardName)
                ->value('id');

            if ($foundRewardId) {
                return (int) $foundRewardId;
            }
        }

        return null;
    }

    private function resolvePriceId(array $item, ?int $productId = null): ?int
    {
        $priceId = data_get($item, 'price_id');
        if ($priceId) {
            return (int) $priceId;
        }

        $productId ??= $this->resolveProductId($item);

        if (!$productId) {
            return null;
        }

        $activePriceId = Price::query()
            ->where('product_id', $productId)
            ->where('is_active', true)
            ->orderByDesc('start_date')
            ->value('id');

        if ($activePriceId) {
            return (int) $activePriceId;
        }

        $latestPriceId = Price::query()
            ->where('product_id', $productId)
            ->latest('id')
            ->value('id');

        return $latestPriceId ? (int) $latestPriceId : null;
    }

    private function resolveDefaultUserId(array $payload): int
    {
        $userId = data_get($payload, 'user_id');
        if ($userId) {
            return (int) $userId;
        }

        return (int) (User::query()->latest('id')->value('id') ?? User::query()->value('id') ?? 1);
    }

    private function extractItems(array $payload, array $keys = []): array
    {
        foreach ($keys as $key) {
            $value = data_get($payload, $key);
            if (is_array($value)) {
                return $this->isAssociativeArray($value) ? [$value] : array_values($value);
            }
        }

        if ($this->isAssociativeArray($payload)) {
            return [$payload];
        }

        return array_values($payload);
    }

    private function normalizeJsonField(mixed $value): array|null
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_array($value)) {
            return $value;
        }

        if (is_object($value)) {
            return (array) $value;
        }

        if (is_string($value)) {
            $decoded = json_decode($value, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                return $decoded;
            }
        }

        return [$value];
    }

    private function normalizeStatus(mixed $value, string $default): string
    {
        $status = strtolower(trim((string) $value));

        if ($status === '') {
            return $default;
        }

        if (in_array($status, ['active', 'inactive'], true)) {
            return $status;
        }

        if (in_array($status, ['1', 'true', 'yes', 'on'], true)) {
            return 'active';
        }

        if (in_array($status, ['0', 'false', 'no', 'off'], true)) {
            return 'inactive';
        }

        return $default;
    }

    private function toBoolean(mixed $value, bool $default = false): bool
    {
        $filtered = filter_var($value, FILTER_VALIDATE_BOOL, FILTER_NULL_ON_FAILURE);

        return $filtered ?? $default;
    }

    private function parseDate(mixed $value, ?Carbon $fallback = null): ?Carbon
    {
        if ($value instanceof Carbon) {
            return $value;
        }

        if ($value instanceof \DateTimeInterface) {
            return Carbon::instance($value);
        }

        if ($value === null || $value === '') {
            return $fallback;
        }

        try {
            return Carbon::parse($value);
        } catch (\Throwable $throwable) {
            return $fallback;
        }
    }

    private function isAssociativeArray(mixed $value): bool
    {
        if (!is_array($value) || $value === []) {
            return false;
        }

        return array_keys($value) !== range(0, count($value) - 1);
    }
}
