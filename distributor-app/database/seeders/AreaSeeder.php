<?php

namespace Database\Seeders;

use App\Models\Area;
use Illuminate\Database\Seeder;

class AreaSeeder extends Seeder
{
    public function run(): void
    {
        $areas = [
            ['name' => 'Jakarta Selatan', 'code' => 'JKS', 'region' => 'DKI Jakarta'],
            ['name' => 'Jakarta Barat', 'code' => 'JKB', 'region' => 'DKI Jakarta'],
            ['name' => 'Bogor', 'code' => 'BGR', 'region' => 'Jawa Barat'],
            ['name' => 'Depok', 'code' => 'DPK', 'region' => 'Jawa Barat'],
            ['name' => 'Tangerang', 'code' => 'TGR', 'region' => 'Banten'],
            ['name' => 'Bekasi', 'code' => 'BKS', 'region' => 'Jawa Barat'],
        ];

        foreach ($areas as $area) {
            Area::updateOrCreate(['code' => $area['code']], $area);
        }
    }
}
