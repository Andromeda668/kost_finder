<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->enum('tipe_sewa', ['harian', 'bulanan'])->default('bulanan')->after('tanggal_masuk');
            $table->unsignedInteger('durasi')->default(1)->after('tipe_sewa');
        });

        \DB::table('bookings')->update([
            'tipe_sewa' => 'bulanan',
            'durasi' => \DB::raw('durasi_bulan'),
        ]);
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['tipe_sewa', 'durasi']);
        });
    }
};

