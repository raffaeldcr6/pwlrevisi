<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Equipment;
use App\Models\Rental;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $earning = [Rental::APPROVED, Rental::RENTED, Rental::RETURNED];

        $stats = [
            'equipment' => Equipment::count(),
            'categories' => Category::count(),
            'customers' => User::where('role', User::ROLE_CUSTOMER)->count(),
            'pending' => Rental::where('status', Rental::PENDING)->count(),
            'active' => Rental::whereIn('status', [Rental::APPROVED, Rental::RENTED])->count(),
            'revenue' => (int) Rental::whereIn('status', $earning)->sum('total_price'),
        ];

        // Pendapatan 6 bulan terakhir (dikelompokkan di PHP agar kompatibel MySQL/SQLite)
        $months = collect(range(5, 0))->map(fn ($i) => now()->startOfMonth()->subMonths($i));
        $byMonth = Rental::whereIn('status', $earning)
            ->where('start_date', '>=', $months->first()->toDateString())
            ->get(['start_date', 'total_price'])
            ->groupBy(fn ($r) => $r->start_date->format('Y-m'))
            ->map(fn ($g) => (int) $g->sum('total_price'));

        $chart = $months->map(fn (Carbon $m) => [
            'label' => $m->translatedFormat('M'),
            'value' => $byMonth->get($m->format('Y-m'), 0),
        ]);

        return view('admin.dashboard', [
            'stats' => $stats,
            'chart' => $chart,
            'chartMax' => max(1, $chart->max('value')),
            'recent' => Rental::with('user')->latest()->take(6)->get(),
            'top' => Equipment::with('category')
                ->withSum('rentalItems', 'qty')
                ->orderByDesc('rental_items_sum_qty')
                ->take(5)
                ->get(),
        ]);
    }
}
