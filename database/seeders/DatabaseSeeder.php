<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Equipment;
use App\Models\Rental;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ---- Akun ----
        User::create([
            'name' => 'Admin RentVentures',
            'email' => 'admin@rentventures.test',
            'password' => 'password',
            'role' => User::ROLE_ADMIN,
            'phone' => '081200000001',
        ]);

        $budi = User::create([
            'name' => 'Budi Santoso',
            'email' => 'budi@rentventures.test',
            'password' => 'password',
            'role' => User::ROLE_CUSTOMER,
            'phone' => '081200000002',
            'address' => 'Bandar Lampung',
        ]);

        $sari = User::create([
            'name' => 'Sari Wulandari',
            'email' => 'sari@rentventures.test',
            'password' => 'password',
            'role' => User::ROLE_CUSTOMER,
            'phone' => '081200000003',
            'address' => 'Metro, Lampung',
        ]);

        // ---- Kategori ----
        $cats = [];
        foreach ([
            ['Tenda', 'bi-triangle', 'Dome, ultralight, dan flysheet untuk berlindung di camp.'],
            ['Tas & Carrier', 'bi-backpack', 'Carrier dan daypack untuk membawa semua bawaan.'],
            ['Perlengkapan Tidur', 'bi-moon-stars', 'Sleeping bag, matras, dan hammock agar tidur tetap hangat.'],
            ['Alat Masak', 'bi-fire', 'Kompor lapangan dan nesting untuk masak di camp.'],
            ['Penerangan', 'bi-lightbulb', 'Headlamp dan lampu tenda untuk malam di gunung.'],
            ['Navigasi & Aksesoris', 'bi-compass', 'Kompas, trekking pole, dan jas hujan.'],
        ] as [$name, $icon, $desc]) {
            $cats[$name] = Category::create([
                'name' => $name, 'slug' => Str::slug($name), 'icon' => $icon, 'description' => $desc,
            ]);
        }

        // ---- Alat ----
        $equipment = [];
        foreach ([
            ['Tenda', 'Tenda Dome Kapasitas 4', 75000, 6, 'Tenda dome double layer untuk 3-4 orang. Tahan angin dan hujan ringan, lengkap dengan pasak dan frame.'],
            ['Tenda', 'Tenda Dome Kapasitas 2 Ultralight', 50000, 8, 'Tenda ringan untuk 2 orang, cocok untuk pendakian tektok maupun camping 2 hari.'],
            ['Tenda', 'Flysheet / Tarp 3x4', 20000, 10, 'Tarp serbaguna untuk dapur camp atau tambahan peneduh. Dilengkapi tali dan pasak.'],
            ['Tas & Carrier', 'Carrier 60L', 40000, 10, 'Carrier 60 liter dengan back system nyaman, rain cover, dan pinggang berbantalan.'],
            ['Tas & Carrier', 'Daypack 25L', 20000, 8, 'Tas harian 25 liter untuk summit attack atau camping santai.'],
            ['Perlengkapan Tidur', 'Sleeping Bag Polar', 25000, 15, 'Sleeping bag bahan polar, hangat untuk suhu sekitar 10 derajat. Sudah dicuci bersih tiap selesai disewa.'],
            ['Perlengkapan Tidur', 'Matras Gulung', 10000, 20, 'Matras gulung busa untuk alas tidur dan penahan dingin dari tanah.'],
            ['Perlengkapan Tidur', 'Hammock + Strap', 25000, 6, 'Hammock parasut dengan strap pohon. Sedang dalam pengecekan jahitan.', 'maintenance'],
            ['Alat Masak', 'Kompor Portable + Gas', 20000, 12, 'Kompor portable lengkap dengan satu kaleng gas. Praktis untuk masak mie dan air panas.'],
            ['Alat Masak', 'Nesting Cooking Set', 20000, 10, 'Satu set panci, wajan, dan sendok untuk 3-4 orang. Ringkas, bisa ditumpuk.'],
            ['Penerangan', 'Headlamp 300 Lumen', 15000, 14, 'Headlamp 300 lumen dengan tiga mode cahaya. Baterai disertakan.'],
            ['Penerangan', 'Lampu Tenda LED', 15000, 10, 'Lampu gantung LED untuk tenda, bisa diisi ulang lewat USB.'],
            ['Navigasi & Aksesoris', 'Kompas Lensatik', 10000, 5, 'Kompas lensatik untuk navigasi darat, akurat dan tahan benturan.'],
            ['Navigasi & Aksesoris', 'Trekking Pole (sepasang)', 20000, 9, 'Sepasang trekking pole aluminium yang bisa diatur tingginya. Mengurangi beban lutut saat turun.'],
            ['Navigasi & Aksesoris', 'Jas Hujan Ponco', 10000, 15, 'Ponco yang juga menutupi carrier. Bisa dipakai sebagai alas darurat.'],
        ] as $row) {
            [$cat, $name, $price, $stock, $desc] = $row;
            $equipment[$name] = Equipment::create([
                'category_id' => $cats[$cat]->id,
                'name' => $name,
                'price_per_day' => $price,
                'stock' => $stock,
                'description' => $desc,
                'status' => $row[5] ?? Equipment::STATUS_AVAILABLE,
            ]);
        }

        // ---- Contoh penyewaan (supaya dashboard tidak kosong) ----
        $samples = [
            [$budi, -40, 3, Rental::RETURNED, ['Tenda Dome Kapasitas 4' => 1, 'Carrier 60L' => 2, 'Sleeping Bag Polar' => 3]],
            [$sari, -25, 2, Rental::RETURNED, ['Tenda Dome Kapasitas 2 Ultralight' => 1, 'Headlamp 300 Lumen' => 2]],
            [$budi, -10, 3, Rental::RETURNED, ['Kompor Portable + Gas' => 1, 'Nesting Cooking Set' => 1, 'Matras Gulung' => 4]],
            [$sari, -1, 3, Rental::RENTED, ['Tenda Dome Kapasitas 4' => 1, 'Sleeping Bag Polar' => 4, 'Lampu Tenda LED' => 1]],
            [$budi, 5, 2, Rental::APPROVED, ['Carrier 60L' => 1, 'Trekking Pole (sepasang)' => 1]],
            [$sari, 8, 3, Rental::PENDING, ['Tenda Dome Kapasitas 2 Ultralight' => 2, 'Jas Hujan Ponco' => 2]],
        ];

        foreach ($samples as $i => [$user, $offset, $days, $status, $items]) {
            $start = now()->addDays($offset)->startOfDay();
            $end = $start->copy()->addDays($days - 1);
            $total = 0;
            $lines = [];

            foreach ($items as $name => $qty) {
                $eq = $equipment[$name];
                $subtotal = $eq->price_per_day * $qty * $days;
                $total += $subtotal;
                $lines[] = [
                    'equipment_id' => $eq->id, 'qty' => $qty,
                    'price_per_day' => $eq->price_per_day, 'subtotal' => $subtotal,
                ];
            }

            $rental = Rental::create([
                'user_id' => $user->id,
                'code' => 'RV-'.str_pad((string) (1001 + $i), 6, '0', STR_PAD_LEFT),
                'start_date' => $start,
                'end_date' => $end,
                'total_price' => $total,
                'status' => $status,
                'note' => $i === 3 ? 'Ambil sore hari ya, sekitar jam 4.' : null,
            ]);
            $rental->items()->createMany($lines);
        }
    }
}
