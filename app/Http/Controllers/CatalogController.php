<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Equipment;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CatalogController extends Controller
{
    public function index(Request $request): View
    {
        $sort = $request->query('urut', 'terbaru');

        $query = Equipment::with('category')
            ->when($request->filled('q'), fn ($q) => $q->where('name', 'like', '%'.$request->query('q').'%'))
            ->when($request->filled('kategori'), fn ($q) => $q->where('category_id', $request->query('kategori')));

        match ($sort) {
            'termurah' => $query->orderBy('price_per_day'),
            'termahal' => $query->orderByDesc('price_per_day'),
            'nama' => $query->orderBy('name'),
            default => $query->latest(),
        };

        return view('catalog.index', [
            'equipment' => $query->paginate(9)->withQueryString(),
            'categories' => Category::withCount('equipment')->orderBy('name')->get(),
            'sort' => $sort,
        ]);
    }

    public function show(Equipment $equipment): View
    {
        $equipment->load('category');

        $related = Equipment::with('category')
            ->where('category_id', $equipment->category_id)
            ->where('id', '!=', $equipment->id)
            ->take(3)
            ->get();

        return view('catalog.show', compact('equipment', 'related'));
    }
}
