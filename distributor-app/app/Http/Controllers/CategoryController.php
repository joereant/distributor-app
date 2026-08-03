<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::orderBy('name')->get();

        return Inertia::render('Admin/Category/Index', [
            'categories' => $categories,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
            'is_active' => 'boolean',
        ]);

        Category::create($validated);

        return redirect()->back()->with('message', ['type' => 'success', 'text' => 'Kategori berhasil ditambahkan']);
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
            'is_active' => 'boolean',
        ]);

        $category->update($validated);

        return redirect()->back()->with('message', ['type' => 'success', 'text' => 'Kategori berhasil diupdate']);
    }

    public function destroy(Category $category)
    {
        // Check if category has products
        if ($category->products()->exists()) {
            return redirect()->back()->with('message', ['type' => 'error', 'text' => 'Kategori tidak bisa dihapus — sudah ada produk']);
        }

        $category->delete();

        return redirect()->back()->with('message', ['type' => 'success', 'text' => 'Kategori berhasil dihapus']);
    }
}
