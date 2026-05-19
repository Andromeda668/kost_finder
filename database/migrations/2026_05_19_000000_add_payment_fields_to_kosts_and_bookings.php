<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kosts', function (Blueprint $table) {
            $table->json('payment_methods')->nullable()->after('fasilitas');
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->string('payment_method', 40)->nullable()->after('durasi_bulan');
            $table->enum('payment_status', ['belum_bayar', 'sudah_bayar'])->default('belum_bayar')->after('payment_method');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['payment_method', 'payment_status']);
        });

        Schema::table('kosts', function (Blueprint $table) {
            $table->dropColumn('payment_methods');
        });
    }
};
