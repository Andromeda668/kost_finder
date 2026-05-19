<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::table('kosts', function (Blueprint $table) {
            $table->longText('thumbnail_image_data')->nullable()->after('qris_mime_type');
            $table->string('thumbnail_image_mime_type')->nullable()->after('thumbnail_image_data');
        });
    }

    public function down(): void
    {
        Schema::table('kosts', function (Blueprint $table) {
            $table->dropColumn(['thumbnail_image_data', 'thumbnail_image_mime_type']);
        });
    }
};
