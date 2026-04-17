<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::query()->with('category')->latest()->paginate(10);

        return response()->json($products);
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

        $product = Product::query()->create($validated);

        return response()->json($product->load('category'), 201);
    }

    public function show(Product $product)
    {
        return response()->json($product->load('category'));
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

        return response()->json($product->load('category'));
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return response()->json(['message' => 'Product deleted']);
    }
}
