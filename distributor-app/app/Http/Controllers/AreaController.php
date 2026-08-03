<?php

namespace App\Http\Controllers;

use App\Models\Area;
use Illuminate\Http\Request;

class AreaController extends Controller
{
    public function index()
    {
        $areas = Area::withCount('productPrices')->orderBy('name')->paginate(20);
        return inertia('Admin/Area/Index', [
            'areas' => $areas,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:20|unique:areas,code',
            'region' => 'nullable|string|max:255',
        ]);

        Area::create($validated);

        return redirect('/admin/areas')->with('message', 'Area berhasil ditambahkan.');
    }

    public function update(Request $request, Area $area)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:20|unique:areas,code,' . $area->id,
            'region' => 'nullable|string|max:255',
        ]);

        $area->update($validated);

        return redirect('/admin/areas')->with('message', 'Area berhasil diperbarui.');
    }

    public function destroy(Area $area)
    {
        $area->delete();

        return redirect('/admin/areas')->with('message', 'Area berhasil dihapus.');
    }
}
