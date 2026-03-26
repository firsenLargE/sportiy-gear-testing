<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Province;

class ProvinceSeeder extends Seeder
{
    public function run(): void
    {
        $provinces = [
            'Koshi',
            'Madhesh',
            'Bagmati',
            'Gandaki',
            'Lumbini',
            'Karnali',
            'Sudurpashchim'
        ];

        foreach ($provinces as $province) {
            Province::create(['name' => $province]);
        }
    }
}
