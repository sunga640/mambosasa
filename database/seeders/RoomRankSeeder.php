<?php

namespace Database\Seeders;

use App\Models\RoomRank;
use Illuminate\Database\Seeder;

class RoomRankSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            ['name' => 'Standard', 'slug' => 'standard', 'sort_order' => 10],
            ['name' => 'VIP', 'slug' => 'vip', 'sort_order' => 20],
            ['name' => 'VVIP', 'slug' => 'vvip', 'sort_order' => 30],
            ['name' => 'MVP', 'slug' => 'mvp', 'sort_order' => 40],
        ];
        foreach ($rows as $row) {
            RoomRank::query()->updateOrCreate(
                ['slug' => $row['slug']],
                ['name' => $row['name'], 'sort_order' => $row['sort_order'], 'is_active' => true]
            );
        }
    }
}
