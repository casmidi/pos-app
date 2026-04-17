<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::query()->with('category')->latest()->paginate(10);

        return view('products.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::query()->orderBy('name')->get();

        return view('products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'sku' => ['required', 'string', 'max:50', 'unique:products,sku'],
            'name' => ['required', 'string', 'max:150'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'cost_price' => ['required', 'numeric', 'min:0'],
            'sell_price' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
            'description' => ['nullable', 'string'],
        ]);

        $validated['is_active'] = (bool) ($validated['is_active'] ?? false);

        Product::query()->create($validated);

        return redirect()->route('products.index')->with('success', 'Produk berhasil ditambahkan.');
    }

    public function show(Product $product)
    {
        $product->load('category');

        return view('products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $categories = Category::query()->orderBy('name')->get();

        return view('products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'sku' => ['required', 'string', 'max:50', 'unique:products,sku,' . $product->id],
            'name' => ['required', 'string', 'max:150'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'cost_price' => ['required', 'numeric', 'min:0'],
            'sell_price' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
            'description' => ['nullable', 'string'],
        ]);

        $validated['is_active'] = (bool) ($validated['is_active'] ?? false);

        $product->update($validated);

        return redirect()->route('products.index')->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()->route('products.index')->with('success', 'Produk berhasil dihapus.');
    }
}
