<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;

class ImageService
{
    /**
     * Konversi gambar yang diunggah ke JPG, simpan ke storage/app/public/{directory},
     * dan kembalikan path relatifnya (misal "equipment/abc123.jpg").
     *
     * @param  UploadedFile  $file       File yang diunggah
     * @param  string        $directory  Sub-direktori di dalam storage/app/public
     * @param  int           $maxWidth   Lebar maksimal (px); tinggi menyesuaikan
     * @param  int           $quality    Kualitas JPG (0-100)
     */
    public function storeAsJpg(
        UploadedFile $file,
        string $directory = 'equipment',
        int $maxWidth = 1200,
        int $quality = 85
    ): string {
        // Nama file unik dengan ekstensi .jpg
        $filename = Str::uuid() . '.jpg';
        $relativePath = $directory . '/' . $filename;

        // Baca gambar, resize jika lebih lebar dari maxWidth, encode ke JPEG
        $image = Image::read($file->getRealPath());

        // Scale down jika perlu, pertahankan aspect ratio
        if ($image->width() > $maxWidth) {
            $image->scaleDown(width: $maxWidth);
        }

        // Encode ke JPEG (transparansi PNG otomatis di-flatten dengan background putih)
        $encoded = $image->toJpeg($quality);

        // Simpan ke storage/app/public/{relativePath}
        Storage::disk('public')->put($relativePath, (string) $encoded);

        return $relativePath;
    }

    /**
     * Hapus gambar dari storage jika path-nya ada.
     */
    public function delete(?string $path): void
    {
        if ($path) {
            Storage::disk('public')->delete($path);
        }
    }
}
