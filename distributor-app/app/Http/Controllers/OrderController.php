<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\Customer;
use App\Models\Plant;
use App\Models\Product;
use App\Models\ProductPrice;
use App\Models\ShippingRate;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Illuminate\Http\Request;
use Inertia\Inertia;

class OrderController extends Controller
{
    public function index()
    {
        $transactions = Transaction::with(['customer', 'items.product'])
            ->orderByDesc('transaction_date')
            ->limit(50)
            ->get();

        return Inertia::render('Admin/Order', ['transactions' => $transactions]);
    }

    public function approve(Request $request, Transaction $transaction)
    {
        if ($transaction->status !== 'pending') {
            $request->session()->flash('message', "Order #{$transaction->id} sudah diproses.");

            return redirect()->back();
        }

        $transaction->update(['status' => 'approved']);
        $request->session()->flash('message', "Order #{$transaction->id} disetujui.");

        return redirect()->back();
    }

    public function create()
    {
        $products = Product::orderBy('name')->get();
        $areas = Area::orderBy('name')->get();
        $plant = Plant::first();
        $shippingRates = [];

        if ($plant) {
            $shippingRates = ShippingRate::where('plant_id', $plant->id)
                ->get()
                ->map(fn (ShippingRate $rate) => ['area_id' => $rate->area_id, 'rate' => $rate->rate])
                ->values();
        }

        $productPrices = ProductPrice::get()
            ->groupBy('area_id')
            ->map(fn ($group) => $group->mapWithKeys(fn ($price) => [$price->product_id => (float) $price->price]));

        return Inertia::render('Customer/Order', [
            'products' => $products,
            'areas' => $areas,
            'plant' => $plant,
            'shipping_rates' => $shippingRates,
            'product_prices' => $productPrices,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'area_id' => ['required', 'exists:areas,id'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
        ]);

        $user = $request->user();
        $customer = Customer::firstOrCreate(
            ['user_id' => $user->id],
            [
                'company_name' => $user->name,
                'contact_person' => $user->name,
                'phone' => $user->phone,
                'email' => $user->email,
                'area_id' => $data['area_id'],
            ]
        );

        $productPrices = ProductPrice::where('area_id', $data['area_id'])
            ->get()
            ->keyBy('product_id');

        $totalQty = 0;
        $subtotal = 0;
        $hargaBeli = 0;

        $items = collect($data['items'])->map(function ($item) use ($productPrices, &$totalQty, &$subtotal, &$hargaBeli) {
            $product = Product::findOrFail($item['product_id']);
            $unitPrice = $productPrices->get($product->id)?->price ?? $product->price;

            $totalQty += $item['quantity'];
            $subtotal += $unitPrice * $item['quantity'];
            $hargaBeli += $product->harga_beli * $item['quantity'];

            return [
                'product_id' => $product->id,
                'quantity' => $item['quantity'],
                'unit_price' => $unitPrice,
                'subtotal' => $unitPrice * $item['quantity'],
            ];
        });

        $plant = Plant::first();
        $rate = ShippingRate::where('plant_id', $plant?->id)->where('area_id', $data['area_id'])->first();
        $ongkir = $rate ? $rate->rate * $totalQty : 0;

        $total = $subtotal + $ongkir;
        $marginEstimate = $subtotal - $hargaBeli;

        $transaction = Transaction::create([
            'customer_id' => $customer->id,
            'sales_id' => null,
            'plant_id' => $plant?->id,
            'transaction_date' => now(),
            'subtotal' => $subtotal,
            'ongkir' => $ongkir,
            'harga_beli' => $hargaBeli,
            'total' => $total,
            'margin_estimate' => $marginEstimate,
            'margin_status' => $marginEstimate > 0 ? 'positif' : ($marginEstimate < 0 ? 'negatif' : 'netral'),
            'status' => 'pending',
            'payment_method' => null,
            'notes' => null,
        ]);

        foreach ($items as $item) {
            TransactionItem::create([...$item, 'transaction_id' => $transaction->id]);
        }

        $request->session()->flash('message', 'Pesanan berhasil dibuat & menunggu persetujuan admin.');

        return redirect()->back();
    }
}
