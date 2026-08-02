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
        $category = Category::updateOrCreate(
            ['slug' => 'semen'],
            ['name' => 'Semen']
        );

        $products = [
            ['code' => 'S50', 'name' => 'Semen Portland 50 kg', 'price' => 62000, 'harga_beli' => 51000, 'unit' => 'zak', 'min_stock' => 100],
            ['code' => 'S40', 'name' => 'Semen Portland 40 kg', 'price' => 52000, 'harga_beli' => 43000, 'unit' => 'zak', 'min_stock' => 100],
        ];

        foreach ($products as $p) {
            Product::updateOrCreate(
                ['code' => $p['code']],
                array_merge($p, ['description' => null, 'category_id' => $category->id])
            );
        }

        $hargaPerArea = [
            'JKS' => ['S50' => 63500, 'S40' => 53000],
            'JKB' => ['S50' => 62000, 'S40' => 52000],
            'BGR' => ['S50' => 61500, 'S40' => 51500],
            'DPK' => ['S50' => 62000, 'S40' => 52000],
            'TGR' => ['S50' => 62500, 'S40' => 52500],
            'BKS' => ['S50' => 62500, 'S40' => 52500],
        ];

        foreach ($hargaPerArea as $code => $prices) {
            $area = Area::where('code', $code)->first();
            if (! $area) {
                continue;
            }
            foreach ($prices as $productCode => $price) {
                ProductPrice::updateOrCreate(
                    ['product_id' => Product::where('code', $productCode)->first()?->id, 'area_id' => $area->id],
                    ['price' => $price]
                );
            }
        }

        $plant = Plant::updateOrCreate(
            ['name' => 'Pabrik Cibinong'],
            ['location' => 'Cibinong, Bogor', 'area_id' => Area::where('code', 'BGR')->first()?->id]
        );

        $ongkirPerArea = [
            'JKS' => 2500, 'JKB' => 2200, 'BGR' => 800,
            'DPK' => 1200, 'TGR' => 2000, 'BKS' => 1800,
        ];

        foreach ($ongkirPerArea as $code => $rate) {
            $area = Area::where('code', $code)->first();
            if (! $area) {
                continue;
            }
            ShippingRate::updateOrCreate(
                ['plant_id' => $plant->id, 'area_id' => $area->id],
                ['rate' => $rate]
            );
        }

        $customerUser = User::where('email', 'customer@demo.com')->first();
        Customer::updateOrCreate(
            ['user_id' => $customerUser?->id],
            [
                'company_name' => 'UD Sinar Jaya',
                'contact_person' => 'Budi Santoso',
                'phone' => '0812-3456-7890',
                'email' => 'customer@demo.com',
                'address' => 'Jl. Raya Kebon Jeruk No. 12',
                'area_id' => Area::where('code', 'JKB')->first()?->id,
                'customer_type' => 'toko',
            ]
        );

        $this->command->info('Business demo: kategori, produk, harga per area, plant + ongkir, customer.');
    }
}
