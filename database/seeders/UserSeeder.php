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
                'bank' => '',
                'no_rekening' => '',
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
                'bank' => '',
                'no_rekening' => '',
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
                'bank' => '',
                'no_rekening' => '',
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
                'bank' => '',
                'no_rekening' => '',
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
                'bank' => '',
                'no_rekening' => '',
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
                'bank' => '',
                'no_rekening' => '',
                'status' => 'aktif',
            ],
            [
                'name' => 'Nopal Sopir',
                'email' => 'sopir@example.com',
                'email_verified_at' => now(),
                'password' => bcrypt('12345678'),
                'telepon' => '081234567896',
                'alamat' => 'Jl. Sopir No. 7',
                'role' => 'operasional_sopir',
                'bank' => 'BCA',
                'no_rekening' => '765432345',
                'status' => 'aktif',
            ],
            [
                'name' => 'Raka Sopir 1',
                'email' => 'sopir1@example.com',
                'email_verified_at' => now(),
                'password' => bcrypt('12345678'),
                'telepon' => '081234567897',
                'alamat' => 'Jl. Sopir No. 8',
                'role' => 'operasional_sopir',
                'bank' => 'BNI',
                'no_rekening' => '32567845378',
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
                'bank' => '',
                'no_rekening' => '',
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
                'bank' => '',
                'no_rekening' => '',
                'status' => 'aktif',
            ],
            [
                'name' => 'PT Sumatera Trans Logistic',
                'email' => 'stl@example.com',
                'email_verified_at' => now(),
                'password' => bcrypt('12345678'),
                'telepon' => '081234567900',
                'alamat' => 'Jl. Customer No. 11',
                'role' => 'customer',
                'bank' => 'BCA',
                'no_rekening' => '1243512377',
                'status' => 'aktif',
            ],
            [
                'name' => 'Asitcom Group',
                'email' => 'ag@example.com',
                'email_verified_at' => now(),
                'password' => bcrypt('12345678'),
                'telepon' => '081234567901',
                'alamat' => 'Jl. Customer No. 12',
                'role' => 'customer',
                'bank' => '',
                'no_rekening' => '',
                'status' => 'aktif',
            ],
        ];

        foreach ($users as $user) {
            DB::table('users')->insert($user);
        }
    }
}
