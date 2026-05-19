<?php

use App\Models\Kost;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kosts', function (Blueprint $table) {
            $table->string('currency', 3)->default('IDR')->after('google_maps_link');
            $table->unsignedBigInteger('harga_harian')->nullable()->after('harga');
            $table->unsignedBigInteger('harga_bulanan')->nullable()->after('harga_harian');
        });

        Kost::query()
            ->whereNull('harga_bulanan')
            ->update([
                'harga_bulanan' => \DB::raw('harga'),
            ]);

        Schema::create('nearby_places', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kost_id')->constrained()->cascadeOnDelete();
            $table->string('label');
            $table->string('category', 30)->default('lainnya');
            $table->decimal('distance_km', 6, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nearby_places');

        Schema::table('kosts', function (Blueprint $table) {
            $table->dropColumn(['currency', 'harga_harian', 'harga_bulanan']);
        });
    }
};

