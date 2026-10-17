# RentVentures — Sistem Penyewaan Alat Camping & Outdoor

Website penyewaan perlengkapan camping (Laravel + MySQL + Bootstrap 5).

## Kebutuhan
- PHP 8.2 atau lebih baru (disarankan 8.3), ekstensi: mbstring, openssl, pdo_mysql, fileinfo, gd/ctype
- Composer
- MySQL / MariaDB (XAMPP atau Laragon)
- Koneksi internet saat membuka website (Bootstrap, ikon, dan font dimuat dari CDN)

## Cara menjalankan
1. Ekstrak ZIP, buka terminal di folder `rentventures`.
2. Buat database kosong bernama `rentventures` di phpMyAdmin.
3. Jalankan:

```bash
composer install
copy .env.example .env        # Mac/Linux: cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
php artisan serve
```

4. Buka http://localhost:8000

Jika username/password MySQL berbeda, ubah `DB_USERNAME` dan `DB_PASSWORD` di file `.env`.
Untuk mengulang dari awal (data kembali ke contoh): `php artisan migrate:fresh --seed`.

## Akun contoh (password semua: `password`)
| Role | Email |
|---|---|
| Admin | admin@rentventures.test |
| Customer | budi@rentventures.test |
| Customer | sari@rentventures.test |

## Fitur
**Publik**: beranda, katalog (cari, filter kategori, urutkan), detail alat, login, register.

**Admin** (`/admin`)
- CRUD **Alat** (nama, kategori, harga, stok, foto, status)
- CRUD **Kategori**
- Kelola **Penyewaan**: lihat, setujui/tolak, tandai diambil/dikembalikan, hapus riwayat
- Dashboard: statistik, grafik pendapatan 6 bulan, alat terlaris

**Customer**
- CRUD **Penyewaan**: ajukan sewa (banyak alat sekaligus, total otomatis), lihat riwayat, ubah (saat Menunggu), batalkan

## Aturan bisnis
- Stok dicek per rentang tanggal; penyewaan Menunggu/Disetujui/Sedang disewa menahan stok.
- Alat berstatus Perawatan tidak bisa disewa.
- Kategori yang masih punya alat dan alat yang pernah disewa tidak bisa dihapus (muncul pesan, bukan error).
- Alur status: Menunggu → Disetujui → Sedang disewa → Dikembalikan (atau Ditolak / Dibatalkan).

## Penanganan error
- Tidak ada tampilan error Laravel. Semua error (403, 404, 419, 429, 500, 503, dll.) memakai halaman custom di `resources/views/errors/`, termasuk saat `APP_DEBUG=true`.
- Pengecualian ditangkap di `bootstrap/app.php`; detail error tetap tercatat di `storage/logs/laravel.log`.
- Proses simpan memakai `try/catch` dan transaksi database, validasi memakai pesan bahasa Indonesia (`lang/id`).
- Role dijaga middleware `role:admin` / `role:customer`.

## Struktur penting
```
app/Http/Controllers/Admin/      CategoryController, EquipmentController, RentalController, DashboardController
app/Http/Controllers/Customer/   RentalController
app/Services/RentalService.php   hitung total, cek stok, simpan item
app/Http/Middleware/RoleMiddleware.php
bootstrap/app.php                alias middleware + render error custom
resources/views/                 layouts, admin, customer, catalog, errors
public/css/rentventures.css      tema tampilan
```
