<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kosts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('nama_kost');
            $table->text('alamat');
            $table->string('lokasi')->index();
            $table->string('google_maps_link', 1000);
            $table->unsignedBigInteger('harga');
            $table->longText('deskripsi');
            $table->text('fasilitas');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kosts');
    }
};
