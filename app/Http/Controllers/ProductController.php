<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->string('q'));
        $allowedSorts = [
            'sku' => 'sku',
            'name' => 'name',
            'category' => 'category',
            'sell_price' => 'sell_price',
            'stock' => 'stock',
            'status' => 'is_active',
        ];
        $sort = $request->string('sort')->toString();
        $direction = strtolower($request->string('direction')->toString()) === 'asc' ? 'asc' : 'desc';

        if (! array_key_exists($sort, $allowedSorts)) {
            $sort = 'created_at';
            $direction = 'desc';
        }

        $products = Product::query()
            ->with('category')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($nestedQuery) use ($search) {
                    $nestedQuery->where('sku', 'like', "%{$search}%")
                        ->orWhere('name', 'like', "%{$search}%")
                        ->orWhereHas('category', function ($categoryQuery) use ($search) {
                            $categoryQuery->where('name', 'like', "%{$search}%");
                        });
                });
            })
            ->when($sort === 'category', function ($query) use ($direction) {
                $query->orderBy(
                    Category::query()
                        ->select('name')
                        ->whereColumn('categories.id', 'products.category_id')
                        ->limit(1),
                    $direction,
                );
            }, function ($query) use ($sort, $direction) {
                $query->orderBy($sort, $direction);
            })
            ->paginate(10)
            ->withQueryString();

        return $this->respond($request, 'products.index', compact('products', 'search', 'sort', 'direction'), $products);
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
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $validated['is_active'] = (bool) ($validated['is_active'] ?? false);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('products', 'public');
        } else {
            unset($validated['image']);
        }

        $product = Product::query()->create($validated);

        return $this->respondAfterMutation(
            $request,
            'products.index',
            'Produk berhasil ditambahkan.',
            $product->load('category'),
            201,
        );
    }

    public function show(Request $request, Product $product)
    {
        $product->load('category');

        return $this->respond($request, 'products.show', compact('product'), $product);
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
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $validated['is_active'] = (bool) ($validated['is_active'] ?? false);

        if ($request->hasFile('image')) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $validated['image'] = $request->file('image')->store('products', 'public');
        } elseif ($request->boolean('remove_image')) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $validated['image'] = null;
        } else {
            unset($validated['image']);
        }

        $product->update($validated);

        return $this->respondAfterMutation(
            $request,
            'products.index',
            'Produk berhasil diperbarui.',
            $product->fresh()->load('category'),
        );
    }

    public function destroy(Request $request, Product $product)
    {
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return $this->respondAfterMutation(
            $request,
            'products.index',
            'Produk berhasil dihapus.',
            ['message' => 'Product deleted'],
        );
    }
}
