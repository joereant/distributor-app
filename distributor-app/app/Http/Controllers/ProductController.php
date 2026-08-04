<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\Category;
use App\Models\Plant;
use App\Models\Product;
use App\Models\ProductPrice;
use Illuminate\Http\Request;
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

    public function prices(Request $request)
    {
        $plantId = $request->get('plant');

        // Get all active plants
        $plants = Plant::orderBy('name')->get(['id', 'name', 'location']);

        // Default plant
        $selectedPlant = $plantId
            ? Plant::find($plantId)
            : Plant::first();

        // All areas
        $areas = Area::orderBy('name')->get(['id', 'name', 'code']);

        // All active products grouped by category
        $products = Product::with('category')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $groupedProducts = $products->groupBy(fn ($p) => $p->category?->name ?? 'Tanpa Kategori')
            ->sortBy(fn ($_, $k) => $k);

        // Price matrix: product_id × area_id → price
        $prices = ProductPrice::get()
            ->keyBy(fn ($p) => "{$p->product_id}_{$p->area_id}");

        return inertia('Admin/Product/Price', [
            'plants' => $plants,
            'selected_plant' => $selectedPlant,
            'areas' => $areas,
            'grouped_products' => $groupedProducts,
            'prices' => $prices,
            'filters' => ['plant' => $selectedPlant?->id],
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

    public function savePrices(Request $request)
    {
        $data = $request->validate([
            'plant_id' => ['required', 'exists:plants,id'],
            'prices' => ['required', 'array'],
            'prices.*' => ['nullable', 'numeric', 'min:0'],
        ]);

        $plantId = $data['plant_id'];
        $prices = $data['prices'];

        foreach ($prices as $productId => $areaPrices) {
            foreach ($areaPrices as $areaId => $price) {
                if ($price === null || $price === '') continue;

                ProductPrice::updateOrCreate(
                    ['product_id' => $productId, 'area_id' => $areaId],
                    ['price' => $price]
                );
            }
        }

        return back()->with('message', 'Daftar harga berhasil disimpan.');
    }
}
