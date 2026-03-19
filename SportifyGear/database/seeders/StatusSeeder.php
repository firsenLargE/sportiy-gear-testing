<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $statuses = [
            ['name' => 'Pending'],
            ['name' => 'Confirmed'],
            ['name' => 'Processing'],
            ['name' => 'Shipped'],
            ['name' => 'Delivered'],
            ['name' => 'Cancelled'],
            ['name' => 'Returned'],
        ];

        DB::table('statuses')->insert($statuses);
    }
}
