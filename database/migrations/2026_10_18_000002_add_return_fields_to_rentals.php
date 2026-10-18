<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rentals', function (Blueprint $table) {
           
            $table->text('condition_out')->nullable()->after('admin_note');
           
            $table->text('condition_in')->nullable()->after('condition_out');
           
            $table->unsignedBigInteger('late_fee')->default(0)->after('condition_in');
       
            $table->date('returned_at')->nullable()->after('late_fee');
        });
    }

    public function down(): void
    {
        Schema::table('rentals', function (Blueprint $table) {
            $table->dropColumn(['condition_out', 'condition_in', 'late_fee', 'returned_at']);
        });
    }
};
