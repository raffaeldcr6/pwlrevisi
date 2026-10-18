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
        $sort     = $request->query('urut', 'terbaru');
        $minPrice = $request->query('harga_min');
        $maxPrice = $request->query('harga_max');

        $query = Equipment::with('category')
            ->when($request->filled('q'), fn ($q) => $q->where(function ($w) use ($request) {
                $keyword = '%' . $request->query('q') . '%';
                $w->where('name', 'like', $keyword)
                  ->orWhere('description', 'like', $keyword);
            }))
            ->when($request->filled('kategori'), fn ($q) => $q->where('category_id', $request->query('kategori')))
            ->when($request->filled('kondisi'), fn ($q) => $q->where('condition', $request->query('kondisi')))
            ->when($request->filled('ketersediaan'), function ($q) use ($request) {
                if ($request->query('ketersediaan') === 'available') {
                    $q->where('status', Equipment::STATUS_AVAILABLE)->where('stock', '>', 0);
                } else {
                    $q->where('status', Equipment::STATUS_MAINTENANCE);
                }
            })
            ->when($minPrice !== null && is_numeric($minPrice), fn ($q) => $q->where('price_per_day', '>=', (int) $minPrice))
            ->when($maxPrice !== null && is_numeric($maxPrice), fn ($q) => $q->where('price_per_day', '<=', (int) $maxPrice));

        match ($sort) {
            'termurah' => $query->orderBy('price_per_day'),
            'termahal' => $query->orderByDesc('price_per_day'),
            'nama'     => $query->orderBy('name'),
            default    => $query->latest(),
        };

        return view('catalog.index', [
            'equipment'   => $query->paginate(12)->withQueryString(),
            'categories'  => Category::withCount('equipment')->orderBy('name')->get(),
            'conditions'  => Equipment::CONDITIONS,
            'sort'        => $sort,
            'minPrice'    => $minPrice,
            'maxPrice'    => $maxPrice,
        ]);
    }

    public function show(Equipment $equipment): View
    {
        $equipment->load('category');

        $related = Equipment::with('category')
            ->where('category_id', $equipment->category_id)
            ->where('id', '!=', $equipment->id)
            ->where('status', Equipment::STATUS_AVAILABLE)
            ->take(3)
            ->get();

        return view('catalog.show', compact('equipment', 'related'));
    }
}
