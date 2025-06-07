<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'HR Admin',
                'email' => 'hr@example.com',
                'email_verified_at' => now(),
                'password' => bcrypt('12345678'),
                'telepon' => '081234567890',
                'alamat' => 'Jl. HR No. 1',
                'role' => 'admin_hr',
                'status' => 'aktif',
            ],
            [
                'name' => 'Direksi Admin',
                'email' => 'direksi@example.com',
                'email_verified_at' => now(),
                'password' => bcrypt('12345678'),
                'telepon' => '081234567891',
                'alamat' => 'Jl. Direksi No. 2',
                'role' => 'admin_direksi',
                'status' => 'aktif',
            ],
            [
                'name' => 'Transportasi Operasional',
                'email' => 'st@example.com',
                'email_verified_at' => now(),
                'password' => bcrypt('12345678'),
                'telepon' => '081234567892',
                'alamat' => 'Jl. Transportasi No. 3',
                'role' => 'operasional_transportasi',
                'status' => 'aktif',
            ],
            [
                'name' => 'Pengiriman Operasional',
                'email' => 'kp@example.com',
                'email_verified_at' => now(),
                'password' => bcrypt('12345678'),
                'telepon' => '081234567893',
                'alamat' => 'Jl. Pengiriman No. 4',
                'role' => 'operasional_pengiriman',
                'status' => 'aktif',
            ],
            [
                'name' => 'Bengkel Operasional',
                'email' => 'sb@example.com',
                'email_verified_at' => now(),
                'password' => bcrypt('12345678'),
                'telepon' => '081234567894',
                'alamat' => 'Jl. Bengkel No. 5',
                'role' => 'operasional_bengkel',
                'status' => 'aktif',
            ],
            [
                'name' => 'Teknisi Operasional',
                'email' => 'teknisi@example.com',
                'email_verified_at' => now(),
                'password' => bcrypt('12345678'),
                'telepon' => '081234567895',
                'alamat' => 'Jl. Teknisi No. 6',
                'role' => 'operasional_teknisi',
                'status' => 'aktif',
            ],
            [
                'name' => 'Sopir Operasional 1',
                'email' => 'sopir@example.com',
                'email_verified_at' => now(),
                'password' => bcrypt('12345678'),
                'telepon' => '081234567896',
                'alamat' => 'Jl. Sopir No. 7',
                'role' => 'operasional_sopir',
                'status' => 'aktif',
            ],
            [
                'name' => 'Sopir Operasional 2',
                'email' => 'sopir1@example.com',
                'email_verified_at' => now(),
                'password' => bcrypt('12345678'),
                'telepon' => '081234567897',
                'alamat' => 'Jl. Sopir No. 8',
                'role' => 'operasional_sopir',
                'status' => 'aktif',
            ],
            [
                'name' => 'Akuntan',
                'email' => 'akuntan@example.com',
                'email_verified_at' => now(),
                'password' => bcrypt('12345678'),
                'telepon' => '081234567898',
                'alamat' => 'Jl. Akuntan No. 9',
                'role' => 'akuntan',
                'status' => 'aktif',
            ],
            [
                'name' => 'Customer Service Pemasaran',
                'email' => 'cs@example.com',
                'email_verified_at' => now(),
                'password' => bcrypt('12345678'),
                'telepon' => '081234567899',
                'alamat' => 'Jl. CS No. 10',
                'role' => 'pemasaran_cs',
                'status' => 'aktif',
            ],
            [
                'name' => 'Customer 1',
                'email' => 'stl@example.com',
                'email_verified_at' => now(),
                'password' => bcrypt('12345678'),
                'telepon' => '081234567900',
                'alamat' => 'Jl. Customer No. 11',
                'role' => 'customer',
                'status' => 'aktif',
            ],
            [
                'name' => 'Customer 2',
                'email' => 'ag@example.com',
                'email_verified_at' => now(),
                'password' => bcrypt('12345678'),
                'telepon' => '081234567901',
                'alamat' => 'Jl. Customer No. 12',
                'role' => 'customer',
                'status' => 'aktif',
            ],
        ];

        foreach ($users as $user) {
            DB::table('users')->insert($user);
        }
    }
}
