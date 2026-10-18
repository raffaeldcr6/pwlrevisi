<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Equipment;
use App\Services\ImageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Throwable;

class EquipmentController extends Controller
{
    public function __construct(private ImageService $imageService) {}

    public function index(Request $request): View
    {
        $items = Equipment::with('category')
            ->when($request->filled('q'), fn ($q) => $q->where('name', 'like', '%' . $request->query('q') . '%'))
            ->when($request->filled('kategori'), fn ($q) => $q->where('category_id', $request->query('kategori')))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->query('status')))
            ->orderBy('name')
            ->paginate(12)
            ->withQueryString();

        return view('admin.equipment.index', [
            'items'      => $items,
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.equipment.form', [
            'equipment'  => new Equipment(['status' => Equipment::STATUS_AVAILABLE, 'stock' => 1, 'condition' => Equipment::CONDITION_GOOD]),
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);

        try {
            if ($request->hasFile('image')) {
                $data['image'] = $this->imageService->storeAsJpg($request->file('image'));
            }

            Equipment::create($data);
        } catch (Throwable $e) {
            report($e);
            return back()->withInput()->with('error', 'Alat gagal disimpan. Silakan coba lagi.');
        }

        return redirect()->route('admin.equipment.index')->with('success', 'Alat berhasil ditambahkan.');
    }

    public function edit(Equipment $equipment): View
    {
        return view('admin.equipment.form', [
            'equipment'  => $equipment,
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Equipment $equipment): RedirectResponse
    {
        $data = $this->validated($request);

        try {
            if ($request->boolean('remove_image') && $equipment->image) {
                $this->imageService->delete($equipment->image);
                $data['image'] = null;
            }

            if ($request->hasFile('image')) {
                $this->imageService->delete($equipment->image);
                $data['image'] = $this->imageService->storeAsJpg($request->file('image'));
            }

            $equipment->update($data);
        } catch (Throwable $e) {
            report($e);
            return back()->withInput()->with('error', 'Alat gagal diperbarui. Silakan coba lagi.');
        }

        return redirect()->route('admin.equipment.index')->with('success', 'Data alat berhasil diperbarui.');
    }

    public function destroy(Equipment $equipment): RedirectResponse
    {
        if ($equipment->rentalItems()->exists()) {
            return back()->with('error', "\"{$equipment->name}\" pernah disewa sehingga tidak bisa dihapus. Ubah statusnya menjadi Perawatan agar tidak tampil untuk disewa.");
        }

        try {
            $this->imageService->delete($equipment->image);
            $equipment->delete();
        } catch (Throwable $e) {
            report($e);
            return back()->with('error', 'Alat gagal dihapus. Silakan coba lagi.');
        }

        return redirect()->route('admin.equipment.index')->with('success', 'Alat berhasil dihapus.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'category_id'    => ['required', 'exists:categories,id'],
            'name'           => ['required', 'string', 'max:120'],
            'description'    => ['nullable', 'string', 'max:2000'],
            'specifications' => ['nullable', 'string', 'max:1000'],
            'price_per_day'  => ['required', 'integer', 'min:1000', 'max:10000000'],
            'stock'          => ['required', 'integer', 'min:0', 'max:1000'],
            'status'         => ['required', Rule::in(array_keys(Equipment::STATUSES))],
            'condition'      => ['required', Rule::in(array_keys(Equipment::CONDITIONS))],
            // Hanya terima JPG/JPEG/PNG/WebP — semua akan dikonversi ke JPG di server
            'image'          => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ], [
            'price_per_day.min' => 'Harga sewa minimal Rp 1.000 per hari.',
        ]);
    }
}
