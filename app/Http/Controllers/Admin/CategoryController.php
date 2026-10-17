<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Throwable;

class CategoryController extends Controller
{
    public function index(): View
    {
        return view('admin.categories.index', [
            'categories' => Category::withCount('equipment')->orderBy('name')->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.categories.form', ['category' => new Category()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);

        try {
            Category::create($data);
        } catch (Throwable $e) {
            report($e);

            return back()->withInput()->with('error', 'Kategori gagal disimpan. Silakan coba lagi.');
        }

        return redirect()->route('admin.categories.index')->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function edit(Category $category): View
    {
        return view('admin.categories.form', compact('category'));
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        $data = $this->validated($request, $category);

        try {
            $category->update($data);
        } catch (Throwable $e) {
            report($e);

            return back()->withInput()->with('error', 'Kategori gagal diperbarui. Silakan coba lagi.');
        }

        return redirect()->route('admin.categories.index')->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroy(Category $category): RedirectResponse
    {
        if ($category->equipment()->exists()) {
            return back()->with('error', "Kategori \"{$category->name}\" masih punya alat. Pindahkan atau hapus alatnya terlebih dahulu.");
        }

        try {
            $category->delete();
        } catch (Throwable $e) {
            report($e);

            return back()->with('error', 'Kategori gagal dihapus. Silakan coba lagi.');
        }

        return redirect()->route('admin.categories.index')->with('success', 'Kategori berhasil dihapus.');
    }

    private function validated(Request $request, ?Category $category = null): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:60', Rule::unique('categories', 'name')->ignore($category?->id)],
            'icon' => ['required', Rule::in(array_keys(Category::ICONS))],
            'description' => ['nullable', 'string', 'max:255'],
        ]);

        $data['slug'] = Str::slug($data['name']);

        return $data;
    }
}
