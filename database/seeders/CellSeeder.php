<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Cell;

class CellSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cells = [
            ['row' => 1, 'column' => 1, 'code' => 'A', 'qty_current' => 0, 'capacity' =>10],
            ['row' => 1, 'column' => 2, 'code' => 'B', 'qty_current' => 0, 'capacity' =>10],
            ['row' => 2, 'column' => 1, 'code' => 'C', 'qty_current' => 0, 'capacity' =>10],
            ['row' => 2, 'column' => 2, 'code' => 'D', 'qty_current' => 0, 'capacity' =>10],
            ['row' => 3, 'column' => 1, 'code' => 'E', 'qty_current' => 0, 'capacity' =>10],
            ['row' => 3, 'column' => 2, 'code' => 'F', 'qty_current' => 0, 'capacity' =>10],
        ];

        foreach ($cells as $cell) {
            Cell::create($cell);
        }
    }
}
