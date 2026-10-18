<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\AppNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function index(Request $request): View
    {
        $notifications = $request->user()
            ->appNotifications()
            ->with('rental')
            ->latest()
            ->paginate(15);

        // Tandai semua sebagai sudah dibaca saat halaman dibuka
        $request->user()->appNotifications()->whereNull('read_at')->update(['read_at' => now()]);

        return view('customer.notifications.index', compact('notifications'));
    }

    /** Endpoint AJAX: ambil jumlah notif belum dibaca. */
    public function count(Request $request): JsonResponse
    {
        return response()->json([
            'count' => $request->user()->appNotifications()->whereNull('read_at')->count(),
        ]);
    }

    public function markRead(Request $request, AppNotification $notification): RedirectResponse
    {
        abort_if($notification->user_id !== $request->user()->id, 403);

        $notification->update(['read_at' => now()]);

        if ($notification->rental_id) {
            return redirect()->route('customer.rentals.show', $notification->rental_id);
        }

        return redirect()->route('customer.notifications.index');
    }
}
