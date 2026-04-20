<?php

namespace Database\Seeders;

use App\Models\BookingMethod;
use Illuminate\Database\Seeder;

class BookingMethodSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            ['name' => 'Pay on arrive', 'slug' => 'pay_later', 'code' => 'cash', 'method_type' => 'offline', 'sort_order' => 10],
            ['name' => 'Pesapal', 'slug' => 'pesapal', 'code' => 'pesapal', 'method_type' => 'online', 'sort_order' => 20],
            ['name' => 'Lipa kwa simu (M-Pesa)', 'slug' => 'mpesa', 'code' => 'mpesa', 'method_type' => 'online', 'sort_order' => 30],
        ];

        foreach ($rows as $row) {
            BookingMethod::query()->updateOrCreate(
                ['slug' => $row['slug']],
                [
                    'name' => $row['name'],
                    'code' => $row['code'],
                    'method_type' => $row['method_type'],
                    'is_active' => true,
                    'show_on_booking_page' => true,
                    'sort_order' => $row['sort_order'],
                ]
            );
        }
    }
}
