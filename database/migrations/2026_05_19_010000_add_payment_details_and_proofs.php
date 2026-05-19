<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kosts', function (Blueprint $table) {
            $table->json('payment_details')->nullable()->after('payment_methods');
            $table->longText('qris_image_data')->nullable()->after('payment_details');
            $table->string('qris_mime_type')->nullable()->after('qris_image_data');
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->longText('payment_proof_data')->nullable()->after('payment_status');
            $table->string('payment_proof_mime_type')->nullable()->after('payment_proof_data');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['payment_proof_data', 'payment_proof_mime_type']);
        });

        Schema::table('kosts', function (Blueprint $table) {
            $table->dropColumn(['payment_details', 'qris_image_data', 'qris_mime_type']);
        });
    }
};
