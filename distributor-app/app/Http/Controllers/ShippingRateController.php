<?php

namespace App\Http\Controllers;

use App\Models\ShippingRate;
use App\Models\Plant;
use App\Models\Area;
use Illuminate\Http\Request;

class ShippingRateController extends Controller
{
    public function index()
    {
        $rates = ShippingRate::with(['plant', 'area'])
            ->orderBy('plant_id')
            ->orderBy('area_id')
            ->paginate(30);
        $plants = Plant::with('area')->orderBy('name')->get();
        $areas = Area::orderBy('name')->get();

        return inertia('Admin/ShippingRate/Index', [
            'rates' => $rates,
            'plants' => $plants,
            'areas' => $areas,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'plant_id' => 'required|exists:plants,id',
            'area_id' => 'required|exists:areas,id',
            'rate' => 'required|numeric|min:0',
        ]);

        $exists = ShippingRate::where('plant_id', $validated['plant_id'])
            ->where('area_id', $validated['area_id'])
            ->exists();

        if ($exists) {
            return back()->withErrors(['area_id' => 'Tarif untuk kombinasi ini sudah ada.']);
        }

        ShippingRate::create($validated);

        return redirect('/admin/shipping-rates')->with('success', 'Tarif ongkir berhasil ditambahkan.');
    }

    public function update(Request $request, ShippingRate $shippingRate)
    {
        $validated = $request->validate([
            'rate' => 'required|numeric|min:0',
        ]);

        $shippingRate->update($validated);

        return redirect('/admin/shipping-rates')->with('success', 'Tarif ongkir berhasil diperbarui.');
    }

    public function destroy(ShippingRate $shippingRate)
    {
        $shippingRate->delete();

        return redirect('/admin/shipping-rates')->with('success', 'Tarif ongkir berhasil dihapus.');
    }
}
