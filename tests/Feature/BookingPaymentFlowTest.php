<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Kost;
use App\Models\OwnerContact;
use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BookingPaymentFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_upload_proof_and_owner_can_mark_paid(): void
    {
        Storage::fake('public');

        $owner = User::factory()->create(['role' => User::ROLE_OWNER]);
        OwnerContact::query()->create([
            'user_id' => $owner->id,
            'phone' => '081234567890',
            'email' => $owner->email,
        ]);

        $user = User::factory()->create(['role' => User::ROLE_USER]);

        $kost = Kost::query()->create([
            'user_id' => $owner->id,
            'nama_kost' => 'Kost Test',
            'alamat' => 'Jl Test 1',
            'lokasi' => 'Jakarta',
            'google_maps_link' => 'https://maps.google.com/?q=Jakarta',
            'harga' => 500000,
            'deskripsi' => 'Desc',
            'fasilitas' => "WiFi\nAC",
            'payment_methods' => ['ewallet_dana'],
        ]);

        Room::query()->create([
            'kost_id' => $kost->id,
            'total_kamar' => 5,
            'kamar_tersedia' => 3,
        ]);

        $response = $this->actingAs($user)->post(route('bookings.store', $kost), [
            'tanggal_masuk' => now()->addDays(2)->toDateString(),
            'tipe_sewa' => 'bulanan',
            'durasi' => 1,
            'payment_method' => 'ewallet_dana',
            'payment_proof' => UploadedFile::fake()->create('proof.jpg', 100, 'image/jpeg'),
        ]);

        $response->assertRedirect();

        $this->assertDatabaseCount('bookings', 1);

        $booking = Booking::first();

        $this->assertNotNull($booking->payment_proof_data);

        // Owner marks as paid
        $response = $this->actingAs($owner)->patch(route('owner.bookings.payment-status', $booking), [
            'payment_status' => Booking::PAYMENT_PAID,
        ]);

        $response->assertRedirect();

        $booking->refresh();

        $this->assertEquals(Booking::PAYMENT_PAID, $booking->payment_status);

        // Check payment logs
        $this->assertDatabaseHas('payment_logs', [
            'booking_id' => $booking->id,
            'type' => 'proof_uploaded',
        ]);

        $this->assertDatabaseHas('payment_logs', [
            'booking_id' => $booking->id,
            'type' => 'status_changed',
        ]);
    }
}
