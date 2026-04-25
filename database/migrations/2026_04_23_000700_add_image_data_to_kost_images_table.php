<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kost_images', function (Blueprint $table) {
            $table->longText('image_data')->nullable()->after('image_path');
            $table->string('mime_type')->nullable()->after('image_data');
        });
    }

    public function down(): void
    {
        Schema::table('kost_images', function (Blueprint $table) {
            $table->dropColumn(['image_data', 'mime_type']);
        });
    }
};
