<?php

namespace App\Http\Controllers;

use App\Models\Plant;
use App\Models\Area;
use Illuminate\Http\Request;

class PlantController extends Controller
{
    public function index()
    {
        $plants = Plant::with('area')->orderBy('name')->paginate(20);
        $areas = Area::orderBy('name')->get();
        return inertia('Admin/Plant/Index', [
            'plants' => $plants,
            'areas' => $areas,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
            'area_id' => 'nullable|exists:areas,id',
        ]);

        Plant::create($validated);

        return redirect('/admin/plants')->with('success', 'Pabrik berhasil ditambahkan.');
    }

    public function update(Request $request, Plant $plant)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
            'area_id' => 'nullable|exists:areas,id',
        ]);

        $plant->update($validated);

        return redirect('/admin/plants')->with('success', 'Pabrik berhasil diperbarui.');
    }

    public function destroy(Plant $plant)
    {
        $plant->delete();

        return redirect('/admin/plants')->with('success', 'Pabrik berhasil dihapus.');
    }
}
