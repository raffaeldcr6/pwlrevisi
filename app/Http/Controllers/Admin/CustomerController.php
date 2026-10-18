<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Rental;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q'));

        $customers = User::where('role', User::ROLE_CUSTOMER)
            ->withCount('rentals')
            ->withSum('rentals', 'total_price')
            ->when($search !== '', fn ($q) => $q->where(function ($w) use ($search) {
                $w->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            }))
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('admin.customers.index', compact('customers', 'search'));
    }

    public function show(User $customer): View
    {
        abort_if($customer->isAdmin(), 404);

        $rentals = $customer->rentals()
            ->with('items')
            ->latest()
            ->paginate(8);

        $stats = [
            'total'    => $customer->rentals()->count(),
            'active'   => $customer->rentals()->whereIn('status', Rental::HOLDING_STATUSES)->count(),
            'returned' => $customer->rentals()->where('status', Rental::RETURNED)->count(),
            'spend'    => (int) $customer->rentals()
                ->whereIn('status', [Rental::APPROVED, Rental::RENTED, Rental::RETURNED])
                ->sum('total_price'),
        ];

        return view('admin.customers.show', compact('customer', 'rentals', 'stats'));
    }
}
