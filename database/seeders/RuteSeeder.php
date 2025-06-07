<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RuteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rutes = [
            [
                'customer_id' => 11,
                'nama_rute' => 'Rute A - Tambang 1',
                'jarak_km' => 50.5,
                'harga' => 1000000.00,
                'uang_jalan' => 200000.00,
                'bonus' => 50000.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'customer_id' => 11,
                'nama_rute' => 'Rute B - Tambang 2',
                'jarak_km' => 75.0,
                'harga' => 1500000.00,
                'uang_jalan' => 300000.00,
                'bonus' => 75000.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'customer_id' => 12,
                'nama_rute' => 'Rute C - Tambang 3',
                'jarak_km' => 60.0,
                'harga' => 1200000.00,
                'uang_jalan' => 250000.00,
                'bonus' => 60000.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'customer_id' => 12,
                'nama_rute' => 'Rute D - Tambang 4',
                'jarak_km' => 80.0,
                'harga' => 1600000.00,
                'uang_jalan' => 350000.00,
                'bonus' => 80000.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'customer_id' => 11,
                'nama_rute' => 'Rute E - Tambang 5',
                'jarak_km' => 90.0,
                'harga' => 1800000.00,
                'uang_jalan' => 400000.00,
                'bonus' => 90000.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'customer_id' => 12,
                'nama_rute' => 'Rute F - Tambang 6',
                'jarak_km' => 70.0,
                'harga' => 1400000.00,
                'uang_jalan' => 300000.00,
                'bonus' => 70000.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('rutes')->insert($rutes);
    }
}
