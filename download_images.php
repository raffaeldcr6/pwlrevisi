<?php
/**
 * Script: download_images.php
 * Mengunduh gambar dari URL dan menyimpannya sebagai JPG ke storage/app/public/equipment/
 * Kemudian memperbarui kolom `image` di tabel equipment
 *
 * Jalankan: php artisan tinker < download_images.php
 */

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Equipment;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

// Mapping nama alat => URL gambar (dari Unsplash, Pexels, dll)
$imageMap = [
    'Tenda Dome Kapasitas 4' => 'https://images.unsplash.com/photo-1504280390367-361c6d9f38f4?w=800&q=80',
    'Tenda Dome Kapasitas 2 Ultralight' => 'https://images.unsplash.com/photo-1478131143081-80f7f84ca84d?w=800&q=80',
    'Flysheet / Tarp 3x4' => 'https://images.unsplash.com/photo-1521336575822-6da63fb45455?w=800&q=80',
    'Carrier 60L' => 'https://images.unsplash.com/photo-1553361371-9b22f78e8b1d?w=800&q=80',
    'Daypack 25L' => 'https://images.unsplash.com/photo-1581053369732-3e1e90d5bf01?w=800&q=80',
    'Sleeping Bag Polar' => 'https://images.unsplash.com/photo-1537225228614-56cc3556d7ed?w=800&q=80',
    'Matras Gulung' => 'https://images.unsplash.com/photo-1601758228041-f3b2795255f1?w=800&q=80',
    'Hammock + Strap' => 'https://images.unsplash.com/photo-1510672981848-a1c4f1cb5ccf?w=800&q=80',
    'Kompor Portable + Gas' => 'https://images.unsplash.com/photo-1499487759706-e93f8c0ac8be?w=800&q=80',
    'Nesting Cooking Set' => 'https://images.unsplash.com/photo-1508739773434-c26b3d09e071?w=800&q=80',
    'Headlamp 300 Lumen' => 'https://images.unsplash.com/photo-1603899122634-f086ca5f5ddd?w=800&q=80',
    'Lampu Tenda LED' => 'https://images.unsplash.com/photo-1540979388789-6cee28a1cdc9?w=800&q=80',
    'Kompas Lensatik' => 'https://images.unsplash.com/photo-1522542550221-31fd19575a2d?w=800&q=80',
    'Trekking Pole (sepasang)' => 'https://images.unsplash.com/photo-1551632436-cbf8dd35adfa?w=800&q=80',
    'Jas Hujan Ponco' => 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=800&q=80',
];

$equipments = Equipment::all();
$dir = storage_path('app/public/equipment');
if (!is_dir($dir)) mkdir($dir, 0775, true);

$ctx = stream_context_create(['http' => [
    'timeout' => 15,
    'user_agent' => 'Mozilla/5.0 (RentVentures image fetcher)',
    'follow_location' => true,
]]);

foreach ($equipments as $eq) {
    if (!isset($imageMap[$eq->name])) {
        echo "[SKIP] No URL for: {$eq->name}\n";
        continue;
    }

    $url = $imageMap[$eq->name];
    echo "[DOWNLOAD] {$eq->name} from {$url}\n";

    try {
        $raw = @file_get_contents($url, false, $ctx);
        if (!$raw) {
            echo "[FAIL] Could not download: {$eq->name}\n";
            continue;
        }

        // Buat GD image dari raw bytes
        $img = @imagecreatefromstring($raw);
        if (!$img) {
            echo "[FAIL] Could not parse image for: {$eq->name}\n";
            continue;
        }

        // Scale down jika perlu
        $origW = imagesx($img);
        $origH = imagesy($img);
        $maxW = 900;
        if ($origW > $maxW) {
            $newH = (int)($origH * $maxW / $origW);
            $resized = imagecreatetruecolor($maxW, $newH);
            imagecopyresampled($resized, $img, 0, 0, 0, 0, $maxW, $newH, $origW, $origH);
            imagedestroy($img);
            $img = $resized;
        }

        // Simpan sebagai JPG
        $filename = Str::uuid() . '.jpg';
        $path = $dir . '/' . $filename;
        imagejpeg($img, $path, 85);
        imagedestroy($img);

        // Update database
        $eq->update(['image' => 'equipment/' . $filename]);
        echo "[OK] Saved: equipment/{$filename}\n";

    } catch (\Throwable $e) {
        echo "[ERROR] {$eq->name}: " . $e->getMessage() . "\n";
    }
}

echo "\nDone! " . Equipment::whereNotNull('image')->count() . " equipment now have images.\n";
