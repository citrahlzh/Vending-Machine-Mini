<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RewardSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        DB::table('rewards')->insert([

            // PRODUCT REWARDS
            [
                'code' => 'RWD-PROD-001',
                'name' => 'Snack Gratis',
                'description' => 'Selamat! Kamu mendapatkan snack gratis.',
                'type' => 'product',
                'product_display_id' => 43,
                'stock' => 20,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'code' => 'RWD-PROD-002',
                'name' => 'Minuman Gratis',
                'description' => 'Selamat! Kamu mendapatkan minuman gratis.',
                'type' => 'product',
                'product_display_id' => 44,
                'stock' => 20,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'code' => 'RWD-PROD-003',
                'name' => 'Coklat Premium',
                'description' => 'Selamat! Kamu mendapatkan coklat premium.',
                'type' => 'product',
                'product_display_id' => 45,
                'stock' => 15,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'code' => 'RWD-PROD-004',
                'name' => 'Keripik Kentang',
                'description' => 'Keripik kentang renyah sebagai hadiah.',
                'type' => 'product',
                'product_display_id' => 46,
                'stock' => 25,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'code' => 'RWD-PROD-005',
                'name' => 'Permen Mix',
                'description' => 'Permen campuran warna-warni.',
                'type' => 'product',
                'product_display_id' => 47,
                'stock' => 30,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'code' => 'RWD-PROD-006',
                'name' => 'Minuman Energi',
                'description' => 'Minuman energi sebagai hadiah.',
                'type' => 'product',
                'product_display_id' => 48,
                'stock' => 10,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],

            // VARIASI PRODUCT
            [
                'code' => 'RWD-PROD-007',
                'name' => 'Snack Jagung',
                'description' => 'Snack jagung gurih.',
                'type' => 'product',
                'product_display_id' => 43,
                'stock' => 18,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'code' => 'RWD-PROD-008',
                'name' => 'Minuman Soda',
                'description' => 'Minuman soda segar.',
                'type' => 'product',
                'product_display_id' => 44,
                'stock' => 22,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'code' => 'RWD-PROD-009',
                'name' => 'Biskuit Coklat',
                'description' => 'Biskuit dengan lapisan coklat.',
                'type' => 'product',
                'product_display_id' => 45,
                'stock' => 16,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'code' => 'RWD-PROD-010',
                'name' => 'Snack Pedas',
                'description' => 'Snack dengan rasa pedas.',
                'type' => 'product',
                'product_display_id' => 46,
                'stock' => 14,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],

            // PRODUCT LANGKA
            [
                'code' => 'RWD-PROD-011',
                'name' => 'Snack Langka',
                'description' => 'Hadiah snack langka.',
                'type' => 'product',
                'product_display_id' => 47,
                'stock' => 5,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'code' => 'RWD-PROD-012',
                'name' => 'Minuman Premium',
                'description' => 'Minuman premium spesial.',
                'type' => 'product',
                'product_display_id' => 48,
                'stock' => 5,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],

            // ZONK / NONE
            [
                'code' => 'RWD-NONE-001',
                'name' => 'Zonk',
                'description' => 'Sayang sekali, belum beruntung.',
                'type' => 'none',
                'product_display_id' => null,
                'stock' => null,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'code' => 'RWD-NONE-002',
                'name' => 'Coba Lagi',
                'description' => 'Belum dapat hadiah, coba lagi!',
                'type' => 'none',
                'product_display_id' => null,
                'stock' => null,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'code' => 'RWD-NONE-003',
                'name' => 'Hampir',
                'description' => 'Hampir saja! Coba lagi.',
                'type' => 'none',
                'product_display_id' => null,
                'stock' => null,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'code' => 'RWD-NONE-004',
                'name' => 'Belum Beruntung',
                'description' => 'Hari ini belum beruntung.',
                'type' => 'none',
                'product_display_id' => null,
                'stock' => null,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],

            // TAMBAHAN ZONK UNTUK PROBABILITY
            [
                'code' => 'RWD-NONE-005',
                'name' => 'Zonk Lagi',
                'description' => 'Coba lagi lain waktu.',
                'type' => 'none',
                'product_display_id' => null,
                'stock' => null,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'code' => 'RWD-NONE-006',
                'name' => 'Kesempatan Berikutnya',
                'description' => 'Belum dapat hadiah.',
                'type' => 'none',
                'product_display_id' => null,
                'stock' => null,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'code' => 'RWD-NONE-007',
                'name' => 'Coba Sekali Lagi',
                'description' => 'Masih ada kesempatan berikutnya.',
                'type' => 'none',
                'product_display_id' => null,
                'stock' => null,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }
}
