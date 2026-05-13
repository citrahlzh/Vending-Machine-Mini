<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('site_settings')
            ->whereIn('key', [
                'machine_name',
                'machine_code',
                'machine_serial_number',
                'machine_location',
                'machine_operator_name',
            ])
            ->delete();
    }

    public function down(): void
    {
        $machine = DB::table('machines')->latest('id')->first();
        $now = now();

        $settings = [
            [
                'key' => 'machine_name',
                'label' => 'Nama Mesin',
                'value' => $machine?->name ?? 'Vending Machine',
                'type' => 'text',
                'group' => 'Identitas Mesin',
            ],
            [
                'key' => 'machine_code',
                'label' => 'Kode Mesin',
                'value' => $machine?->code ?? 'AAA',
                'type' => 'text',
                'group' => 'Identitas Mesin',
            ],
            [
                'key' => 'machine_serial_number',
                'label' => 'Nomor Seri',
                'value' => $machine?->serial_number,
                'type' => 'text',
                'group' => 'Identitas Mesin',
            ],
            [
                'key' => 'machine_location',
                'label' => 'Lokasi Mesin',
                'value' => $machine?->location,
                'type' => 'text',
                'group' => 'Identitas Mesin',
            ],
            [
                'key' => 'machine_operator_name',
                'label' => 'PIC / Operator',
                'value' => $machine?->operator_name,
                'type' => 'text',
                'group' => 'Identitas Mesin',
            ],
        ];

        foreach ($settings as $setting) {
            DB::table('site_settings')->updateOrInsert(
                ['key' => $setting['key']],
                [
                    'label' => $setting['label'],
                    'value' => $setting['value'],
                    'type' => $setting['type'],
                    'group' => $setting['group'],
                    'updated_at' => $now,
                    'created_at' => $now,
                ]
            );
        }
    }
};
