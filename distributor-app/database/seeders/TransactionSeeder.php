<?php

namespace Database\Seeders;

use App\Models\Area;
use App\Models\Customer;
use App\Models\Plant;
use App\Models\Product;
use App\Models\ProductPrice;
use App\Models\ShippingRate;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Illuminate\Database\Seeder;

class TransactionSeeder extends Seeder
{
    public function run(): void
    {
        if (Transaction::count() > 0) {
            $this->command->info('Transaksi demo sudah ada, dilewati.');

            return;
        }

        $areaByCode = Area::pluck('id', 'code');
        $plant = Plant::first();
        $rates = ShippingRate::where('plant_id', $plant?->id)->pluck('rate', 'area_id');
        $products = Product::get(['id', 'code', 'price', 'harga_beli']);

        $prices = [];
        foreach (ProductPrice::get() as $pp) {
            $prices[$pp->area_id][$pp->product_id] = (float) $pp->price;
        }

        $customers = [
            ['name' => 'UD Sinar Jaya', 'area' => 'JKB', 'type' => 'toko'],
            ['name' => 'Toko Bangunan Amanah', 'area' => 'BGR', 'type' => 'toko'],
            ['name' => 'CV Mitra Karya', 'area' => 'DPK', 'type' => 'kontraktor'],
            ['name' => 'PT Karya Abadi', 'area' => 'JKS', 'type' => 'kontraktor'],
            ['name' => 'Kontraktor Jaya Makmur', 'area' => 'BKS', 'type' => 'kontraktor'],
            ['name' => 'Toko Bangunan Rejeki', 'area' => 'TGR', 'type' => 'toko'],
            ['name' => 'UD Berkah Jaya', 'area' => 'JKB', 'type' => 'toko'],
        ];

        $customerIds = [];
        foreach ($customers as $c) {
            $customerIds[$c['name']] = Customer::updateOrCreate(
                ['company_name' => $c['name']],
                [
                    'contact_person' => $c['name'],
                    'phone' => '08' . mt_rand(100000000, 999999999),
                    'area_id' => $areaByCode[$c['area']] ?? null,
                    'customer_type' => $c['type'],
                ]
            )->id;
        }

        $statuses = ['approved', 'approved', 'approved', 'approved', 'approved', 'pending'];
        $productCodes = $products->pluck('code')->all();

        for ($i = 0; $i < 90; $i++) {
            $customerId = $customerIds[array_rand($customerIds)];
            $customer = Customer::find($customerId);
            $areaId = $customer->area_id;

            $date = now()->subMonths(5)->addDays(mt_rand(0, 180))->setTime(mt_rand(8, 17), mt_rand(0, 59));
            if ($date->isFuture()) {
                $date = now();
            }

            $codes = $productCodes;
            shuffle($codes);
            $codes = array_slice($codes, 0, mt_rand(1, 2));

            $items = [];
            $totalQty = 0;
            $subtotal = 0;
            $hargaBeli = 0;

            foreach ($codes as $code) {
                $product = $products->firstWhere('code', $code);
                $qty = mt_rand(10, 150);
                $unitPrice = $prices[$areaId][$product->id] ?? (float) $product->price;

                $totalQty += $qty;
                $subtotal += $unitPrice * $qty;
                $hargaBeli += (float) $product->harga_beli * $qty;
                $items[] = [
                    'product_id' => $product->id,
                    'quantity' => $qty,
                    'unit_price' => $unitPrice,
                    'subtotal' => $unitPrice * $qty,
                ];
            }

            $ongkir = ($rates[$areaId] ?? 0) * $totalQty;
            $total = $subtotal + $ongkir;
            $margin = $subtotal - $hargaBeli;

            $status = $statuses[array_rand($statuses)];
            if ($status === 'pending' && $date->lt(now()->subDays(14))) {
                $status = 'approved';
            }

            $transaction = Transaction::create([
                'customer_id' => $customerId,
                'sales_id' => null,
                'plant_id' => $plant?->id,
                'transaction_date' => $date,
                'subtotal' => $subtotal,
                'ongkir' => $ongkir,
                'harga_beli' => $hargaBeli,
                'total' => $total,
                'margin_estimate' => $margin,
                'margin_status' => $margin > 0 ? 'positif' : ($margin < 0 ? 'negatif' : 'netral'),
                'status' => $status,
                'payment_method' => $status === 'approved' ? 'transfer' : null,
                'notes' => null,
            ]);

            foreach ($items as $item) {
                TransactionItem::create([...$item, 'transaction_id' => $transaction->id]);
            }
        }

        $this->command->info('Transaksi demo: ' . Transaction::count() . ' transaksi, ' . TransactionItem::count() . ' item.');
    }
}
