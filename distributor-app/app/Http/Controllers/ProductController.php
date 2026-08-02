<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductPrice;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::with('category')
            ->when($request->search, fn ($q) => $q->where('name', 'like', "%{$request->search}%")
                ->orWhere('code', 'like', "%{$request->search}%"))
            ->when($request->category, fn ($q) => $q->where('category_id', $request->category))
            ->when($request->has('active'), fn ($q) => $q->where('is_active', $request->boolean('active')))
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        $categories = Category::where('is_active', true)->orderBy('name')->get(['id', 'name']);

        return inertia('Admin/Product/Index', [
            'products' => $products,
            'categories' => $categories,
            'filters' => $request->only(['search', 'category', 'active']),
        ]);
    }

    public function create()
    {
        $categories = Category::where('is_active', true)->orderBy('name')->get(['id', 'name']);

        return inertia('Admin/Product/Create', [
            'categories' => $categories,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['nullable', 'string', 'max:50', 'unique:products,code'],
            'description' => ['nullable', 'string'],
            'unit' => ['required', 'string', 'max:20'],
            'packaging_type' => ['required', Rule::in(['zak', 'ton_bag', 'bulk'])],
            'price' => ['required', 'numeric', 'min:0'],
            'harga_beli' => ['required', 'numeric', 'min:0'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'min_stock' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['is_active'] = $data['is_active'] ?? true;
        $data['min_stock'] = $data['min_stock'] ?? 0;

        $product = Product::create($data);

        return redirect("/admin/products/{$product->id}/edit")
            ->with('message', "Produk '{$product->name}' berhasil ditambahkan.");
    }

    public function edit(Product $product)
    {
        $categories = Category::where('is_active', true)->orderBy('name')->get(['id', 'name']);
        $allCategories = Category::orderBy('name')->get(['id', 'name']);

        return inertia('Admin/Product/Edit', [
            'product' => $product,
            'categories' => $categories,
            'allCategories' => $allCategories,
        ]);
    }

    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['nullable', 'string', 'max:50', Rule::unique('products', 'code')->ignore($product->id)],
            'description' => ['nullable', 'string'],
            'unit' => ['required', 'string', 'max:20'],
            'packaging_type' => ['required', Rule::in(['zak', 'ton_bag', 'bulk'])],
            'price' => ['required', 'numeric', 'min:0'],
            'harga_beli' => ['required', 'numeric', 'min:0'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'min_stock' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['is_active'] = $data['is_active'] ?? false;
        $data['min_stock'] = $data['min_stock'] ?? 0;

        $product->update($data);

        return back()->with('message', 'Perubahan disimpan.');
    }

    public function destroy(Product $product)
    {
        if ($product->transactionItems()->exists()) {
            return back()->withErrors(['delete' => "Produk '{$product->name}' tidak bisa dihapus karena sudah punya riwayat transaksi."]);
        }

        $product->prices()->delete();
        $product->delete();

        return redirect('/admin/products')->with('message', "Produk '{$product->name}' berhasil dihapus.");
    }
}
