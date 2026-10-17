<?php

namespace App\Http\Controllers\Customer;

use App\Exceptions\RentalException;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Equipment;
use App\Models\Rental;
use App\Services\RentalService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Throwable;

class RentalController extends Controller
{
    public function __construct(private RentalService $service)
    {
    }

    /** READ: daftar penyewaan milik customer. */
    public function index(Request $request): View
    {
        $status = $request->query('status');

        $rentals = $request->user()->rentals()
            ->with('items.equipment')
            ->when($status && isset(Rental::STATUSES[$status]), fn ($q) => $q->where('status', $status))
            ->latest()
            ->paginate(6)
            ->withQueryString();

        return view('customer.rentals.index', compact('rentals', 'status'));
    }

    /** CREATE: form penyewaan baru. */
    public function create(Request $request): View
    {
        // Prefill dari katalog (?alat=ID) atau dari widget beranda (?pilih[]=ID)
        $picked = collect($request->query('pilih', []))
            ->when($request->filled('alat'), fn ($c) => $c->push($request->query('alat')))
            ->filter(fn ($id) => is_numeric($id))
            ->mapWithKeys(fn ($id) => [(int) $id => 1])
            ->all();

        return view('customer.rentals.form', [
            'rental' => new Rental(),
            'groups' => $this->equipmentGroups(),
            'quantities' => old('items', $picked),
            'startDate' => old('start_date', now()->addDay()->toDateString()),
            'endDate' => old('end_date', now()->addDays(2)->toDateString()),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request, true);

        try {
            $rental = $this->service->create($request->user(), $data);
        } catch (RentalException $e) {
            return back()->withInput()->withErrors(['items' => $e->getMessage()]);
        } catch (Throwable $e) {
            report($e);

            return back()->withInput()->with('error', 'Penyewaan gagal disimpan karena kendala sistem. Silakan coba lagi.');
        }

        return redirect()->route('customer.rentals.show', $rental)
            ->with('success', "Pengajuan {$rental->code} terkirim. Admin akan segera memprosesnya.");
    }

    /** READ: detail satu penyewaan. */
    public function show(Request $request, int $rental): View
    {
        $rental = $this->findOwn($request, $rental)->load('items.equipment.category');

        return view('customer.rentals.show', compact('rental'));
    }

    /** UPDATE: form ubah penyewaan (hanya saat menunggu). */
    public function edit(Request $request, int $rental): View|RedirectResponse
    {
        $rental = $this->findOwn($request, $rental)->load('items');

        if (! $rental->isPending()) {
            return redirect()->route('customer.rentals.show', $rental)
                ->with('error', 'Penyewaan ini sudah diproses admin sehingga tidak bisa diubah.');
        }

        return view('customer.rentals.form', [
            'rental' => $rental,
            'groups' => $this->equipmentGroups(),
            'quantities' => old('items', $rental->items->pluck('qty', 'equipment_id')->all()),
            'startDate' => old('start_date', $rental->start_date->toDateString()),
            'endDate' => old('end_date', $rental->end_date->toDateString()),
        ]);
    }

    public function update(Request $request, int $rental): RedirectResponse
    {
        $rental = $this->findOwn($request, $rental);
        $data = $this->validated($request, false);

        try {
            $this->service->update($rental, $data);
        } catch (RentalException $e) {
            return back()->withInput()->withErrors(['items' => $e->getMessage()]);
        } catch (Throwable $e) {
            report($e);

            return back()->withInput()->with('error', 'Perubahan gagal disimpan karena kendala sistem. Silakan coba lagi.');
        }

        return redirect()->route('customer.rentals.show', $rental)
            ->with('success', 'Penyewaan berhasil diperbarui.');
    }

    /** DELETE: batalkan pengajuan (hanya saat menunggu). */
    public function destroy(Request $request, int $rental): RedirectResponse
    {
        $rental = $this->findOwn($request, $rental);

        if (! $rental->isPending()) {
            return back()->with('error', 'Hanya pengajuan berstatus Menunggu yang bisa dibatalkan.');
        }

        try {
            $code = $rental->code;
            $rental->delete();
        } catch (Throwable $e) {
            report($e);

            return back()->with('error', 'Pengajuan gagal dibatalkan. Silakan coba lagi.');
        }

        return redirect()->route('customer.rentals.index')
            ->with('success', "Pengajuan {$code} dibatalkan.");
    }

    // ------------------------------------------------------------------

    private function findOwn(Request $request, int $id): Rental
    {
        // Customer hanya bisa membuka penyewaannya sendiri; selain itu 404.
        return $request->user()->rentals()->findOrFail($id);
    }

    private function validated(Request $request, bool $isNew): array
    {
        return $request->validate([
            'start_date' => ['required', 'date', $isNew ? 'after_or_equal:today' : 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'note' => ['nullable', 'string', 'max:500'],
            'items' => ['required', 'array'],
            'items.*' => ['nullable', 'integer', 'min:0', 'max:50'],
        ], [
            'items.required' => 'Pilih minimal satu alat.',
            'items.*.integer' => 'Jumlah alat harus berupa angka bulat.',
            'items.*.min' => 'Jumlah alat tidak boleh negatif.',
            'items.*.max' => 'Jumlah alat maksimal 50 per jenis.',
        ]);
    }

    private function equipmentGroups()
    {
        return Category::with(['equipment' => fn ($q) => $q->where('status', Equipment::STATUS_AVAILABLE)->where('stock', '>', 0)->orderBy('name')])
            ->orderBy('name')
            ->get()
            ->filter(fn ($c) => $c->equipment->isNotEmpty());
    }
}
