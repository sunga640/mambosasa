<?php

namespace Database\Seeders;

use App\Models\RestaurantMenuItem;
use Illuminate\Database\Seeder;

class RoomServiceMenuSeeder extends Seeder
{
    public function run(): void
    {
        if (RestaurantMenuItem::query()->exists()) {
            return;
        }

        $items = [
            ['name' => 'Continental breakfast', 'description' => 'Pastries, juice, coffee', 'price' => 18.00, 'preparation_minutes' => 20],
            ['name' => 'Club sandwich & fries', 'description' => null, 'price' => 14.50, 'preparation_minutes' => 25],
            ['name' => 'Chef salad', 'description' => 'Seasonal greens', 'price' => 12.00, 'preparation_minutes' => 15],
            ['name' => 'Grilled salmon', 'description' => 'Vegetables & rice', 'price' => 26.00, 'preparation_minutes' => 35],
            ['name' => 'Chocolate dessert', 'description' => null, 'price' => 8.00, 'preparation_minutes' => 10],
        ];

        foreach ($items as $i => $row) {
            RestaurantMenuItem::query()->create([
                'name' => $row['name'],
                'description' => $row['description'],
                'price' => $row['price'],
                'preparation_minutes' => $row['preparation_minutes'],
                'sort_order' => $i,
                'is_active' => true,
            ]);
        }
    }
}
