<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KendaraanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $kendaraans = [
            [
                'no_polisi' => 'KT 1234 BT',
                'merk' => 'Mitsubishi',
                'type' => 'Fuso FM517 HL',
                'jenis' => 'Dump Truck',
                'tahun' => 2018,
                'warna' => 'Oranye',
                'no_rangka' => 'MMBJAAF50JK000001',
                'no_mesin' => '6D16T1234567',
                'status' => 'beroperasi',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'no_polisi' => 'KT 5678 BT',
                'merk' => 'Hino',
                'type' => 'Ranger FM',
                'jenis' => 'Dump Truck',
                'tahun' => 2019,
                'warna' => 'Kuning',
                'no_rangka' => 'JH4KA2650MC000002',
                'no_mesin' => 'N04CT6947',
                'status' => 'beroperasi',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'no_polisi' => 'KT 9101 BT',
                'merk' => 'Isuzu',
                'type' => 'Giga CXZ',
                'jenis' => 'Dump Truck',
                'tahun' => 2020,
                'warna' => 'Oranye',
                'no_rangka' => 'JALC4W164L7000003',
                'no_mesin' => '6UZ1-TC12345',
                'status' => 'beroperasi',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'no_polisi' => 'KT 1122 BT',
                'merk' => 'Volvo',
                'type' => 'FMX 460',
                'jenis' => 'Dump Truck',
                'tahun' => 2021,
                'warna' => 'Hitam',
                'no_rangka' => 'YV2T4T9H8M0000004',
                'no_mesin' => 'D13C-460',
                'status' => 'perbaikan',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'no_polisi' => 'KT 3344 BT',
                'merk' => 'Scania',
                'type' => 'P360',
                'jenis' => 'Dump Truck',
                'tahun' => 2017,
                'warna' => 'Kuning',
                'no_rangka' => 'YS2P4X20005300005',
                'no_mesin' => 'DC13 1132',
                'status' => 'dijadwalkan',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'no_polisi' => 'KT 5566 BT',
                'merk' => 'Mercedes-Benz',
                'type' => 'Arocs 4145',
                'jenis' => 'Dump Truck',
                'tahun' => 2018,
                'warna' => 'Oranye',
                'no_rangka' => 'WDB9303601P100006',
                'no_mesin' => 'OM 460 LA 460 HP',
                'status' => 'siap',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'no_polisi' => 'KT 7788 BT',
                'merk' => 'Kenworth',
                'type' => 'T880',
                'jenis' => 'Dump Truck',
                'tahun' => 2019,
                'warna' => 'Kuning',
                'no_rangka' => '1XKDP8EX7LJ000007',
                'no_mesin' => 'PACCAR PX-9',
                'status' => 'beroperasi',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'no_polisi' => 'KT 9900 BT',
                'merk' => 'Caterpillar',
                'type' => '797F',
                'jenis' => 'Dump Truck',
                'tahun' => 2016,
                'warna' => 'Kuning',
                'no_rangka' => 'CAT0797F00000008',
                'no_mesin' => 'CAT C175-20',
                'status' => 'beroperasi',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('kendaraans')->insert($kendaraans);
    }
}

