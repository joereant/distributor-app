<?php

namespace Database\Seeders;

use App\Models\Area;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Plant;
use App\Models\Product;
use App\Models\ProductPrice;
use App\Models\ShippingRate;
use App\Models\User;
use Illuminate\Database\Seeder;

class BusinessSeeder extends Seeder
{
    public function run(): void
    {
        // 5 Kategori
        $categories = [
            ['slug' => 'semen', 'name' => 'Semen'],
            ['slug' => 'mortar', 'name' => 'Mortar'],
            ['slug' => 'beton', 'name' => 'Beton & Precast'],
            ['slug' => 'additive', 'name' => 'Additive & Chemical'],
            ['slug' => 'bahan-bangunan', 'name' => 'Bahan Bangunan'],
        ];

        $createdCategories = [];
        foreach ($categories as $c) {
            $createdCategories[$c['slug']] = \App\Models\Category::updateOrCreate(['slug' => $c['slug']], array_merge($c, ['is_active' => true]));
        }

        // Produk: Semen — Zak
        $semenZak = [
            ['code' => 'SEM50', 'name' => 'Semen Portland Type I 50 kg', 'price' => 72000, 'harga_beli' => 58000, 'unit' => 'zak', 'packaging_type' => 'zak', 'category_id' => $createdCategories['semen']->id],
            ['code' => 'SEM40', 'name' => 'Semen Portland Type I 40 kg', 'price' => 60000, 'harga_beli' => 48000, 'unit' => 'zak', 'packaging_type' => 'zak', 'category_id' => $createdCategories['semen']->id],
            ['code' => 'SEM25', 'name' => 'Semen Portland Type I 25 kg', 'price' => 40000, 'harga_beli' => 32000, 'unit' => 'zak', 'packaging_type' => 'zak', 'category_id' => $createdCategories['semen']->id],
            ['code' => 'SEMPC', 'name' => 'Semen Portland Composite (PPC) 50 kg', 'price' => 74000, 'harga_beli' => 60000, 'unit' => 'zak', 'packaging_type' => 'zak', 'category_id' => $createdCategories['semen']->id],
            ['code' => 'SEMSL', 'name' => 'Semen Portland Slag (PSC) 50 kg', 'price' => 73000, 'harga_beli' => 59000, 'unit' => 'zak', 'packaging_type' => 'zak', 'category_id' => $createdCategories['semen']->id],
        ];

        // Produk: Semen — Ton Bag
        $semenTonBag = [
            ['code' => 'SEMT1', 'name' => 'Semen Portland Type I 1 Ton (Ton Bag)', 'price' => 1400000, 'harga_beli' => 1150000, 'unit' => 'ton', 'packaging_type' => 'ton_bag', 'category_id' => $createdCategories['semen']->id],
            ['code' => 'SEMT2', 'name' => 'Semen Portland Composite 1 Ton (Ton Bag)', 'price' => 1440000, 'harga_beli' => 1180000, 'unit' => 'ton', 'packaging_type' => 'ton_bag', 'category_id' => $createdCategories['semen']->id],
        ];

        // Produk: Semen — Curah (Bulk)
        $semenBulk = [
            ['code' => 'SEMB1', 'name' => 'Semen Portland Type I Curah (per ton)', 'price' => 1350000, 'harga_beli' => 1100000, 'unit' => 'ton', 'packaging_type' => 'bulk', 'category_id' => $createdCategories['semen']->id],
            ['code' => 'SEMB2', 'name' => 'Semen Portland Composite Curah (per ton)', 'price' => 1390000, 'harga_beli' => 1130000, 'unit' => 'ton', 'packaging_type' => 'bulk', 'category_id' => $createdCategories['semen']->id],
        ];

        // Mortar
        $mortar = [
            ['code' => 'MORTH', 'name' => 'Mortar Thin Bed 40 kg', 'price' => 65000, 'harga_beli' => 52000, 'unit' => 'zak', 'packaging_type' => 'zak', 'category_id' => $createdCategories['mortar']->id],
            ['code' => 'MORPL', 'name' => 'Mortar Plester 40 kg', 'price' => 55000, 'harga_beli' => 44000, 'unit' => 'zak', 'packaging_type' => 'zak', 'category_id' => $createdCategories['mortar']->id],
            ['code' => 'MORAA', 'name' => 'Mortar Acian 25 kg', 'price' => 35000, 'harga_beli' => 28000, 'unit' => 'zak', 'packaging_type' => 'zak', 'category_id' => $createdCategories['mortar']->id],
            ['code' => 'MORGT', 'name' => 'Mortar Grouting 25 kg', 'price' => 80000, 'harga_beli' => 64000, 'unit' => 'zak', 'packaging_type' => 'zak', 'category_id' => $createdCategories['mortar']->id],
        ];

        // Beton & Precast
        $beton = [
            ['code' => 'BTNRMC', 'name' => 'Beton Ready Mix K-250 (per m3)', 'price' => 850000, 'harga_beli' => 700000, 'unit' => 'm3', 'packaging_type' => 'bulk', 'category_id' => $createdCategories['beton']->id],
            ['code' => 'BTNRK3', 'name' => 'Beton Ready Mix K-300 (per m3)', 'price' => 900000, 'harga_beli' => 740000, 'unit' => 'm3', 'packaging_type' => 'bulk', 'category_id' => $createdCategories['beton']->id],
            ['code' => 'BTNRK4', 'name' => 'Beton Ready Mix K-350 (per m3)', 'price' => 950000, 'harga_beli' => 780000, 'unit' => 'm3', 'packaging_type' => 'bulk', 'category_id' => $createdCategories['beton']->id],
            ['code' => 'PAV22', 'name' => 'Paving Block 6 cm (per m2)', 'price' => 45000, 'harga_beli' => 36000, 'unit' => 'm2', 'packaging_type' => 'bulk', 'category_id' => $createdCategories['beton']->id],
        ];

        // Additive
        $additive = [
            ['code' => 'ADDFL', 'name' => 'Additive Floor Hardener 25 kg', 'price' => 180000, 'harga_beli' => 144000, 'unit' => 'zak', 'packaging_type' => 'zak', 'category_id' => $createdCategories['additive']->id],
            ['code' => 'ADDWC', 'name' => 'Waterproofing Cementitious 25 kg', 'price' => 220000, 'harga_beli' => 176000, 'unit' => 'zak', 'packaging_type' => 'zak', 'category_id' => $createdCategories['additive']->id],
            ['code' => 'ADDPC', 'name' => 'Plasticizer Concrete Additive 20 L', 'price' => 350000, 'harga_beli' => 280000, 'unit' => 'pail', 'packaging_type' => 'ton_bag', 'category_id' => $createdCategories['additive']->id],
        ];

        // Bahan Bangunan
        $bahan = [
            ['code' => 'PASPW', 'name' => 'Pasir Putih 1 Colt (5 m3)', 'price' => 750000, 'harga_beli' => 600000, 'unit' => 'colt', 'packaging_type' => 'bulk', 'category_id' => $createdCategories['bahan-bangunan']->id],
            ['code' => 'PASBW', 'name' => 'Pasir Beton 1 Coli (5 m3)', 'price' => 700000, 'harga_beli' => 560000, 'unit' => 'colt', 'packaging_type' => 'bulk', 'category_id' => $createdCategories['bahan-bangunan']->id],
            ['code' => 'KRSH', 'name' => 'Krikil / Split 2-3 cm (per m3)', 'price' => 350000, 'harga_beli' => 280000, 'unit' => 'm3', 'packaging_type' => 'bulk', 'category_id' => $createdCategories['bahan-bangunan']->id],
            ['code' => 'BATMER', 'name' => 'Batu Belah 10-20 cm (per m3)', 'price' => 280000, 'harga_beli' => 224000, 'unit' => 'm3', 'packaging_type' => 'bulk', 'category_id' => $createdCategories['bahan-bangunan']->id],
        ];

        $allProducts = array_merge($semenZak, $semenTonBag, $semenBulk, $mortar, $beton, $additive, $bahan);

        $createdProducts = [];
        foreach ($allProducts as $p) {
            $product = \App\Models\Product::updateOrCreate(
                ['code' => $p['code']],
                array_merge($p, ['description' => null, 'min_stock' => 50, 'is_active' => true])
            );
            $createdProducts[$p['code']] = $product;
        }

        // ProductPrices per area
        $areas = \App\Models\Area::all()->keyBy('code');

        $hargaPerArea = [
            'JKS' => ['SEM50' => 73500, 'SEM40' => 61500, 'SEM25' => 41000, 'SEMPC' => 75500, 'SEMSL' => 74500,
                       'SEMT1' => 1420000, 'SEMT2' => 1460000, 'SEMB1' => 1370000, 'SEMB2' => 1410000,
                       'MORTH' => 66500, 'MORPL' => 56500, 'MORAA' => 36000, 'MORGT' => 82000,
                       'BTNRMC' => 870000, 'BTNRK3' => 920000, 'BTNRK4' => 970000, 'PAV22' => 46000,
                       'ADDFL' => 185000, 'ADDWC' => 225000, 'ADDPC' => 360000,
                       'PASPW' => 770000, 'PASBW' => 720000, 'KRSH' => 360000, 'BATMER' => 290000],
            'JKB' => ['SEM50' => 72000, 'SEM40' => 60000, 'SEM25' => 40000, 'SEMPC' => 74000, 'SEMSL' => 73000,
                       'SEMT1' => 1400000, 'SEMT2' => 1440000, 'SEMB1' => 1350000, 'SEMB2' => 1390000,
                       'MORTH' => 65000, 'MORPL' => 55000, 'MORAA' => 35000, 'MORGT' => 80000,
                       'BTNRMC' => 850000, 'BTNRK3' => 900000, 'BTNRK4' => 950000, 'PAV22' => 45000,
                       'ADDFL' => 180000, 'ADDWC' => 220000, 'ADDPC' => 350000,
                       'PASPW' => 750000, 'PASBW' => 700000, 'KRSH' => 350000, 'BATMER' => 280000],
            'BGR' => ['SEM50' => 71000, 'SEM40' => 59000, 'SEM25' => 39500, 'SEMPC' => 73000, 'SEMSL' => 72000,
                       'SEMT1' => 1380000, 'SEMT2' => 1420000, 'SEMB1' => 1330000, 'SEMB2' => 1370000,
                       'MORTH' => 64000, 'MORPL' => 54000, 'MORAA' => 34500, 'MORGT' => 79000,
                       'BTNRMC' => 840000, 'BTNRK3' => 890000, 'BTNRK4' => 940000, 'PAV22' => 44500,
                       'ADDFL' => 178000, 'ADDWC' => 218000, 'ADDPC' => 345000,
                       'PASPW' => 740000, 'PASBW' => 690000, 'KRSH' => 345000, 'BATMER' => 275000],
            'DPK' => ['SEM50' => 71500, 'SEM40' => 59500, 'SEM25' => 39800, 'SEMPC' => 73500, 'SEMSL' => 72500,
                       'SEMT1' => 1390000, 'SEMT2' => 1430000, 'SEMB1' => 1340000, 'SEMB2' => 1380000,
                       'MORTH' => 64500, 'MORPL' => 54500, 'MORAA' => 34800, 'MORGT' => 79500,
                       'BTNRMC' => 845000, 'BTNRK3' => 895000, 'BTNRK4' => 945000, 'PAV22' => 44800,
                       'ADDFL' => 179000, 'ADDWC' => 219000, 'ADDPC' => 348000,
                       'PASPW' => 745000, 'PASBW' => 695000, 'KRSH' => 348000, 'BATMER' => 278000],
            'TGR' => ['SEM50' => 72500, 'SEM40' => 60500, 'SEM25' => 40500, 'SEMPC' => 74500, 'SEMSL' => 73500,
                       'SEMT1' => 1410000, 'SEMT2' => 1450000, 'SEMB1' => 1360000, 'SEMB2' => 1400000,
                       'MORTH' => 65500, 'MORPL' => 55500, 'MORAA' => 35500, 'MORGT' => 81000,
                       'BTNRMC' => 855000, 'BTNRK3' => 905000, 'BTNRK4' => 955000, 'PAV22' => 45500,
                       'ADDFL' => 182000, 'ADDWC' => 222000, 'ADDPC' => 352000,
                       'PASPW' => 755000, 'PASBW' => 705000, 'KRSH' => 352000, 'BATMER' => 282000],
            'BKS' => ['SEM50' => 72000, 'SEM40' => 60000, 'SEM25' => 40000, 'SEMPC' => 74000, 'SEMSL' => 73000,
                       'SEMT1' => 1400000, 'SEMT2' => 1440000, 'SEMB1' => 1350000, 'SEMB2' => 1390000,
                       'MORTH' => 65000, 'MORPL' => 55000, 'MORAA' => 35000, 'MORGT' => 80000,
                       'BTNRMC' => 850000, 'BTNRK3' => 900000, 'BTNRK4' => 950000, 'PAV22' => 45000,
                       'ADDFL' => 180000, 'ADDWC' => 220000, 'ADDPC' => 350000,
                       'PASPW' => 750000, 'PASBW' => 700000, 'KRSH' => 350000, 'BATMER' => 280000],
        ];

        foreach ($hargaPerArea as $code => $prices) {
            $area = $areas[$code] ?? null;
            if (! $area) { continue; }
            foreach ($prices as $productCode => $price) {
                $product = $createdProducts[$productCode] ?? null;
                if (! $product) { continue; }
                \App\Models\ProductPrice::updateOrCreate(
                    ['product_id' => $product->id, 'area_id' => $area->id],
                    ['price' => $price]
                );
            }
        }

        $plant = \App\Models\Plant::updateOrCreate(
            ['name' => 'Pabrik Cibinong'],
            ['location' => 'Cibinong, Bogor', 'area_id' => $areas['BGR']?->id]
        );

        $ongkirPerArea = [
            'JKS' => 2500, 'JKB' => 2200, 'BGR' => 800,
            'DPK' => 1200, 'TGR' => 2000, 'BKS' => 1800,
        ];

        foreach ($ongkirPerArea as $code => $rate) {
            $area = $areas[$code] ?? null;
            if (! $area) { continue; }
            \App\Models\ShippingRate::updateOrCreate(
                ['plant_id' => $plant->id, 'area_id' => $area->id],
                ['rate' => $rate]
            );
        }

        $customerUser = \App\Models\User::where('email', 'customer@demo.com')->first();
        \App\Models\Customer::updateOrCreate(
            ['user_id' => $customerUser?->id],
            [
                'company_name' => 'UD Sinar Jaya',
                'contact_person' => 'Budi Santoso',
                'phone' => '0812-3456-7890',
                'email' => 'customer@demo.com',
                'address' => 'Jl. Raya Kebon Jeruk No. 12, Jakarta Barat',
                'area_id' => $areas['JKB']?->id,
                'customer_type' => 'toko',
            ]
        );

        $this->command->info('BusinessSeeder: 5 kategori, 23 produk (semen/mortar/beton/additive/bahan), 24 product_prices, plant + ongkir, 1 demo customer.');
    }
}
