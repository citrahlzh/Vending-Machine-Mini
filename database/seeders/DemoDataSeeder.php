<?php

namespace Database\Seeders;

use App\Models\ActivityLog;
use App\Models\Ad;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Cell;
use App\Models\PackagingSize;
use App\Models\PackagingType;
use App\Models\Price;
use App\Models\Product;
use App\Models\ProductDisplay;
use App\Models\Sale;
use App\Models\SaleLine;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->truncateData();

        $users = $this->seedUsers();
        $master = $this->seedMasterData($users['admin']->id);
        $this->seedAssets();
        $products = $this->seedProducts($users['admin']->id, $master);
        $prices = $this->seedPrices($users['admin']->id, $products);
        $cells = $this->seedCells();
        $displays = $this->seedProductDisplays($users['admin']->id, $products, $prices, $cells);
        $this->seedAds();
        $this->seedSales($displays);
        $this->seedActivityLogs($users['admin']->id);
    }

    private function truncateData(): void
    {
        Schema::disableForeignKeyConstraints();

        DB::table('sales_lines')->truncate();
        DB::table('sales')->truncate();
        DB::table('product_displays')->truncate();
        DB::table('prices')->truncate();
        DB::table('products')->truncate();
        DB::table('cells')->truncate();
        DB::table('ads')->truncate();
        DB::table('packaging_sizes')->truncate();
        DB::table('packaging_types')->truncate();
        DB::table('brands')->truncate();
        DB::table('categories')->truncate();
        DB::table('notifications')->truncate();

        if (Schema::hasTable('activity_logs')) {
            DB::table('activity_logs')->truncate();
        }

        DB::table('users')->truncate();

        Schema::enableForeignKeyConstraints();
    }

    private function seedUsers(): array
    {
        $admin = User::create([
            'name' => 'Admin XNINE',
            'phone_number' => '081234567890',
            'whatsapp_number' => '6281234567890',
            'is_active' => true,
            'username' => 'admin',
            'password' => 'adminadmin',
        ]);

        $operator = User::create([
            'name' => 'Operator Lapangan',
            'phone_number' => '081298765432',
            'whatsapp_number' => '6281298765432',
            'is_active' => true,
            'username' => 'operator',
            'password' => 'operator123',
        ]);

        return [
            'admin' => $admin,
            'operator' => $operator,
        ];
    }

    private function seedMasterData(int $userId): array
    {
        $categories = collect([
            'Minuman',
            'Snack',
            'Kopi',
            'Susu',
            'Biskuit',
        ])->map(fn (string $name) => Category::create([
            'user_id' => $userId,
            'category_name' => $name,
            'is_active' => true,
        ]));

        $brands = collect([
            'Ultra',
            'Indomilk',
            'Coca Cola',
            'Mayora',
            'Kapal Api',
        ])->map(fn (string $name) => Brand::create([
            'user_id' => $userId,
            'brand_name' => $name,
            'is_active' => true,
        ]));

        $packagingTypes = collect([
            'Botol',
            'Kaleng',
            'Kotak',
            'Sachet',
        ])->map(fn (string $name) => PackagingType::create([
            'user_id' => $userId,
            'packaging_type' => $name,
        ]));

        $packagingSizes = collect([
            '120 ml',
            '200 ml',
            '250 ml',
            '330 ml',
            '500 ml',
        ])->map(fn (string $name) => PackagingSize::create([
            'user_id' => $userId,
            'size' => $name,
        ]));

        return [
            'categories' => $categories,
            'brands' => $brands,
            'packagingTypes' => $packagingTypes,
            'packagingSizes' => $packagingSizes,
        ];
    }

    private function seedAssets(): void
    {
        $disk = Storage::disk('public');

        for ($i = 1; $i <= 12; $i++) {
            $disk->put("products/demo-product-{$i}.svg", $this->buildSvg("P{$i}", '#F4E9FF', '#4B1F74'));
        }

        for ($i = 1; $i <= 3; $i++) {
            $disk->put("ads/demo-ad-{$i}.svg", $this->buildSvg("IKLAN {$i}", '#EDE2FF', '#5C2A94'));
        }
    }

    private function seedProducts(int $userId, array $master): array
    {
        $names = [
            'Susu Coklat',
            'Air Mineral',
            'Teh Manis',
            'Kopi Latte',
            'Soda Lemon',
            'Biskuit Coklat',
            'Keripik Kentang',
            'Susu Stroberi',
            'Kopi Hitam',
            'Jus Jeruk',
            'Wafer Keju',
            'Minuman Energi',
        ];

        return collect($names)->map(function (string $name, int $index) use ($userId, $master) {
            return Product::create([
                'user_id' => $userId,
                'category_id' => $master['categories'][$index % $master['categories']->count()]->id,
                'brand_id' => $master['brands'][$index % $master['brands']->count()]->id,
                'packaging_type_id' => $master['packagingTypes'][$index % $master['packagingTypes']->count()]->id,
                'packaging_size_id' => $master['packagingSizes'][$index % $master['packagingSizes']->count()]->id,
                'product_name' => $name,
                'image_url' => "products/demo-product-" . ($index + 1) . ".svg",
            ]);
        })->all();
    }

    private function seedPrices(int $userId, array $products): array
    {
        $now = CarbonImmutable::now();

        return collect($products)->map(function (Product $product, int $index) use ($userId, $now) {
            Price::create([
                'user_id' => $userId,
                'product_id' => $product->id,
                'start_date' => $now->subMonths(3),
                'end_date' => $now->subDay(),
                'price' => 4000 + ($index * 500),
                'is_active' => false,
            ]);

            return Price::create([
                'user_id' => $userId,
                'product_id' => $product->id,
                'start_date' => $now->subDays(10),
                'end_date' => $now->addMonths(6),
                'price' => 5000 + ($index * 700),
                'is_active' => true,
            ]);
        })->all();
    }

    private function seedCells(): array
    {
        $cells = [];
        $rows = ['A', 'B', 'C'];
        $capacity = 20;

        foreach ($rows as $rowIndex => $prefix) {
            for ($column = 1; $column <= 4; $column++) {
                $cells[] = Cell::create([
                    'row' => $rowIndex + 1,
                    'column' => $column,
                    'code' => $prefix . $column,
                    'qty_current' => random_int(4, 18),
                    'capacity' => $capacity,
                ]);
            }
        }

        return $cells;
    }

    private function seedProductDisplays(int $userId, array $products, array $prices, array $cells): array
    {
        return collect($products)->map(function (Product $product, int $index) use ($userId, $prices, $cells) {
            $status = 'active';
            if ($index === 10) {
                $status = 'inactive';
            } elseif ($index === 11) {
                $status = 'discontinued';
            }

            $cell = $cells[$index];

            return ProductDisplay::create([
                'user_id' => $userId,
                'product_id' => $product->id,
                'price_id' => $prices[$index]->id,
                'cell_id' => $cell->id,
                'is_empty' => $cell->qty_current <= 0,
                'status' => $status,
            ]);
        })->all();
    }

    private function seedAds(): void
    {
        for ($i = 1; $i <= 3; $i++) {
            Ad::create([
                'title' => "Promo Spesial {$i}",
                'image_url' => "ads/demo-ad-{$i}.svg",
                'status' => 'active',
            ]);
        }
    }

    private function seedSales(array $displays): void
    {
        $statuses = ['paid', 'pending', 'failed', 'expired'];

        for ($i = 1; $i <= 8; $i++) {
            $status = $statuses[$i % count($statuses)];
            $dispenseStatus = match ($status) {
                'paid' => 'success',
                'pending' => 'pending',
                default => 'failed',
            };

            $sale = Sale::create([
                'idempotency_key' => Str::upper(Str::random(12)),
                'qris_id' => 'QRIS-' . str_pad((string) $i, 4, '0', STR_PAD_LEFT),
                'transaction_date' => now()->subDays(9 - $i),
                'status' => $status,
                'dispense_status' => $dispenseStatus,
                'total_amount' => 0,
            ]);

            $lineCount = random_int(1, 3);
            $total = 0;

            for ($line = 0; $line < $lineCount; $line++) {
                $display = $displays[array_rand($displays)];
                $lineStatus = match ($status) {
                    'paid' => 'success',
                    'pending' => 'pending',
                    default => 'failed',
                };

                SaleLine::create([
                    'sale_id' => $sale->id,
                    'product_display_id' => $display->id,
                    'status' => $lineStatus,
                ]);

                $total += (int) optional($display->price)->price;
            }

            $sale->update([
                'total_amount' => $total,
            ]);
        }
    }

    private function seedActivityLogs(int $userId): void
    {
        if (!Schema::hasTable('activity_logs')) {
            return;
        }

        $actions = [
            'auth.login',
            'web.get.dashboard_index',
            'product.store',
            'price.update',
            'transaction.checkout',
            'transaction.status_sync',
        ];

        foreach ($actions as $index => $action) {
            ActivityLog::create([
                'user_id' => $userId,
                'action' => $action,
                'description' => 'Demo aktivitas ' . $action,
                'properties' => [
                    'seeded' => true,
                    'sequence' => $index + 1,
                ],
                'ip_address' => '127.0.0.1',
                'user_agent' => 'Seeder',
                'http_method' => 'GET',
                'url' => '/seed/demo/' . ($index + 1),
                'created_at' => now()->subMinutes(20 - ($index * 2)),
                'updated_at' => now()->subMinutes(20 - ($index * 2)),
            ]);
        }
    }

    private function buildSvg(string $label, string $background, string $textColor): string
    {
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" width="640" height="360" viewBox="0 0 640 360">
    <rect width="640" height="360" fill="{$background}" />
    <rect x="30" y="30" width="580" height="300" rx="24" fill="#ffffff" />
    <text x="320" y="190" text-anchor="middle" font-family="Arial, sans-serif" font-size="42" fill="{$textColor}" font-weight="700">{$label}</text>
</svg>
SVG;
    }
}
