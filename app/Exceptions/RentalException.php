<?php

namespace App\Exceptions;

use Exception;

/**
 * Kesalahan bisnis pada proses penyewaan (stok kurang, tanggal salah, dll).
 * Pesannya aman ditampilkan langsung ke pengguna.
 */
class RentalException extends Exception
{
}
