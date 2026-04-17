<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\SiteSetting;

class SiteSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'key' => 'site_name',
                'label' => 'Nama Website',
                'value' => 'Vending Machine',
                'type' => 'text',
                'group' => 'Umum'
            ],
            [
                'key' => 'site_description',
                'label' => 'Deskripsi Website',
                'value' => 'Website untuk mengelola vending machine',
                'type' => 'text',
                'group' => 'Umum'
            ],
            [
                'key' => 'company_name',
                'label' => 'Nama Perusahaan',
                'value' => 'PT Manusia Solusi Terbaik',
                'type' => 'text',
                'group' => 'Umum'
            ],
            [
                'key' => 'logo_url',
                'label' => 'Logo',
                'value' => 'https://example.com/logo.png',
                'type' => 'file',
                'group' => 'Umum'
            ],
            [
                'key' => 'call_center_number',
                'label' => 'Nomor Call Center',
                'value' => '0800123456',
                'type' => 'tel',
                'group' => 'Kontak'
            ],
            [
                'key' => 'whatsapp_number',
                'label' => 'Nomor WhatsApp',
                'value' => '62800123456',
                'type' => 'tel',
                'group' => 'Kontak'
            ],
            [
                'key' => 'email_address',
                'label' => 'Alamat Email',
                'value' => 'info@vendingmachine.com',
                'type' => 'email',
                'group' => 'Kontak'
            ],
            [
                'key' => 'machine_name',
                'label' => 'Nama Mesin',
                'value' => 'NEXSELL Machine 01',
                'type' => 'text',
                'group' => 'Identitas Mesin'
            ],
            [
                'key' => 'machine_code',
                'label' => 'Kode Mesin',
                'value' => 'VM-001',
                'type' => 'text',
                'group' => 'Identitas Mesin'
            ],
            [
                'key' => 'machine_serial_number',
                'label' => 'Nomor Seri',
                'value' => 'SN-VM-001',
                'type' => 'text',
                'group' => 'Identitas Mesin'
            ],
            [
                'key' => 'machine_location',
                'label' => 'Lokasi Mesin',
                'value' => 'Lobby Utama',
                'type' => 'text',
                'group' => 'Identitas Mesin'
            ],
            [
                'key' => 'machine_operator_name',
                'label' => 'PIC / Operator',
                'value' => 'Tim Operasional',
                'type' => 'text',
                'group' => 'Identitas Mesin'
            ]
        ];

        foreach ($data as $item) {
            SiteSetting::updateOrCreate(
                ['key' => $item['key']],
                $item
            );
        }
    }
}
