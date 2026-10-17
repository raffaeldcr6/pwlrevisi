<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Equipment;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $categories = Category::withCount('equipment')->orderBy('name')->get();

        $popular = Equipment::with('category')
            ->where('status', Equipment::STATUS_AVAILABLE)
            ->withSum('rentalItems', 'qty')
            ->orderByDesc('rental_items_sum_qty')
            ->orderBy('name')
            ->take(6)
            ->get();

        // Untuk widget "daftar bawaan" di hero
        $packing = Equipment::whereIn('name', [
            'Tenda Dome Kapasitas 4',
            'Carrier 60L',
            'Sleeping Bag Polar',
            'Matras Gulung',
            'Kompor Portable + Gas',
            'Headlamp 300 Lumen',
        ])->where('status', Equipment::STATUS_AVAILABLE)->get();

        if ($packing->count() < 3) {
            $packing = $popular->take(5);
        }

        return view('home', [
            'categories' => $categories,
            'popular' => $popular,
            'packing' => $packing,
            'totalEquipment' => Equipment::count(),
        ]);
    }
}
