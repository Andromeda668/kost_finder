<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('nearby_places', function (Blueprint $table) {
            $table->string('google_maps_link')->nullable()->after('category');
            $table->decimal('latitude', 10, 8)->nullable()->after('google_maps_link');
            $table->decimal('longitude', 11, 8)->nullable()->after('latitude');
        });
    }

    public function down(): void
    {
        Schema::table('nearby_places', function (Blueprint $table) {
            $table->dropColumn(['google_maps_link', 'latitude', 'longitude']);
        });
    }
};
