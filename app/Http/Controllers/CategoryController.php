<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->string('q'));
        $allowedSorts = [
            'id' => 'id',
            'name' => 'name',
            'description' => 'description',
        ];
        $sort = $request->string('sort')->toString();
        $direction = strtolower($request->string('direction')->toString()) === 'asc' ? 'asc' : 'desc';

        if (! array_key_exists($sort, $allowedSorts)) {
            $sort = 'created_at';
            $direction = 'desc';
        }

        $categories = Category::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($nestedQuery) use ($search) {
                    $nestedQuery->where('name', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->orderBy($sort, $direction)
            ->paginate(10)
            ->withQueryString();

        return $this->respond($request, 'categories.index', compact('categories', 'search', 'sort', 'direction'), $categories);
    }

    public function create()
    {
        return view('categories.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:categories,name'],
            'description' => ['nullable', 'string'],
        ]);

        $category = Category::query()->create($validated);

        return $this->respondAfterMutation(
            $request,
            'categories.index',
            'Kategori berhasil ditambahkan.',
            $category,
            201,
        );
    }

    public function show(Request $request, Category $category)
    {
        return $this->respond($request, 'categories.show', compact('category'), $category);
    }

    public function edit(Category $category)
    {
        return view('categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:categories,name,' . $category->id],
            'description' => ['nullable', 'string'],
        ]);

        $category->update($validated);

        return $this->respondAfterMutation(
            $request,
            'categories.index',
            'Kategori berhasil diperbarui.',
            $category->fresh(),
        );
    }

    public function destroy(Request $request, Category $category)
    {
        $category->delete();

        return $this->respondAfterMutation(
            $request,
            'categories.index',
            'Kategori berhasil dihapus.',
            ['message' => 'Category deleted'],
        );
    }
}
