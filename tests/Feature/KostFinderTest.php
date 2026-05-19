<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Kost;
use App\Models\OwnerContact;
use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KostFinderTest extends TestCase
{
    use RefreshDatabase;

    protected function createKostWithRoom(User $owner, array $kostAttributes = [], array $roomAttributes = []): Kost
    {
        $kost = Kost::query()->create(array_merge([
            'user_id' => $owner->id,
            'nama_kost' => 'Kost Melati',
            'alamat' => 'Jl. Melati No. 10',
            'lokasi' => 'Bandung',
            'google_maps_link' => 'https://maps.google.com/?q=Bandung',
            'currency' => 'IDR', // Added currency
            'harga_bulanan' => 1200000, // Changed from 'harga'
            'harga_harian' => 50000, // Added daily price
            'deskripsi' => 'Kost nyaman dekat kampus.',
            'fasilitas' => "WiFi\nAC",
            'payment_methods' => ['cash', 'ewallet_dana'],
        ], $kostAttributes));

        Room::query()->create(array_merge([
            'kost_id' => $kost->id,
            'total_kamar' => 10,
            'kamar_tersedia' => 4,
        ], $roomAttributes));

        return $kost;
    }

    public function test_homepage_shows_kost_list(): void
    {
        $owner = User::factory()->create(['role' => User::ROLE_OWNER]);
        OwnerContact::query()->create([
            'user_id' => $owner->id,
            'phone' => '081234567890',
            'email' => $owner->email,
        ]);

        $this->createKostWithRoom($owner);

        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('Kost Melati');
        $response->assertSee('Tersedia');
    }

    public function test_search_filters_kost_by_location(): void
    {
        $owner = User::factory()->create(['role' => User::ROLE_OWNER]);

        $this->createKostWithRoom($owner, [
            'nama_kost' => 'Kost Mawar',
            'lokasi' => 'Yogyakarta',
            'google_maps_link' => 'https://maps.google.com/?q=Yogyakarta',
        ]);

        $this->createKostWithRoom($owner, [
            'nama_kost' => 'Kost Kenanga',
            'lokasi' => 'Malang',
            'google_maps_link' => 'https://maps.google.com/?q=Malang',
        ]);

        $response = $this->get('/?search=Yogyakarta');

        $response->assertOk();
        $response->assertSee('Kost Mawar');
        $response->assertDontSee('Kost Kenanga');
    }

    public function test_owner_can_access_dashboard(): void
    {
        $owner = User::factory()->create(['role' => User::ROLE_OWNER]);

        $response = $this->actingAs($owner)->get(route('owner.dashboard'));

        $response->assertOk();
        $response->assertSee('Dashboard Owner');
        $response->assertSee('Permintaan Booking');
    }

    public function test_regular_user_cannot_access_owner_dashboard(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);

        $response = $this->actingAs($user)->get(route('owner.dashboard'));

        $response->assertForbidden();
    }

    public function test_user_can_create_booking(): void
    {
        $owner = User::factory()->create(['role' => User::ROLE_OWNER]);
        $user = User::factory()->create(['role' => User::ROLE_USER]);
        $kost = $this->createKostWithRoom($owner);

        $response = $this->actingAs($user)->post(route('bookings.store', $kost), [
            'tanggal_masuk' => now()->addDays(3)->toDateString(),
            'tipe_sewa' => 'bulanan',
            'durasi' => 3,
            'payment_method' => 'cash',
        ]);

        $response->assertRedirect(route('kosts.show', $kost));
        $this->assertDatabaseHas('bookings', [
            'user_id' => $user->id,
            'kost_id' => $kost->id,
            'payment_method' => 'cash',
            'status' => Booking::STATUS_PENDING,
        ]);
    }

    public function test_user_cannot_create_booking_with_unavailable_payment_method(): void
    {
        $owner = User::factory()->create(['role' => User::ROLE_OWNER]);
        $user = User::factory()->create(['role' => User::ROLE_USER]);
        // Create a kost that only accepts 'cash'
        $kost = $this->createKostWithRoom($owner, ['payment_methods' => ['cash']]);

        $response = $this->actingAs($user)->post(route('bookings.store', $kost), [
            'tanggal_masuk' => now()->addDays(3)->toDateString(),
            'tipe_sewa' => 'bulanan',
            'durasi' => 3,
            'payment_method' => 'e-banking', // Try to book with an unavailable method
        ]);

        $response->assertSessionHasErrors('payment_method');
        $response->assertRedirect(); // Should redirect back with errors
        $this->assertDatabaseMissing('bookings', [
            'user_id' => $user->id,
            'kost_id' => $kost->id,
            'status' => Booking::STATUS_PENDING,
            'payment_method' => 'cash',
            'payment_status' => Booking::PAYMENT_UNPAID,
        ]);
    }

    public function test_user_can_view_booking_history_page(): void
    {
        $owner = User::factory()->create(['role' => User::ROLE_OWNER]);
        $user = User::factory()->create(['role' => User::ROLE_USER]);
        $kost = $this->createKostWithRoom($owner, ['nama_kost' => 'Kost Flamboyan']);

        Booking::query()->create([
            'user_id' => $user->id,
            'kost_id' => $kost->id,
            'tanggal_masuk' => now()->addDays(4)->toDateString(),
            'durasi_bulan' => 2,
            'status' => Booking::STATUS_PENDING,
            'created_at' => now(),
        ]);

        $response = $this->actingAs($user)->get(route('bookings.index'));

        $response->assertOk();
        $response->assertSee('Booking Saya');
        $response->assertSee('Kost Flamboyan');
    }

    public function test_user_can_toggle_favorite_kost(): void
    {
        $owner = User::factory()->create(['role' => User::ROLE_OWNER]);
        $user = User::factory()->create(['role' => User::ROLE_USER]);
        $kost = $this->createKostWithRoom($owner, ['nama_kost' => 'Kost Dahlia']);

        $response = $this->actingAs($user)->post(route('favorites.toggle', $kost));

        $response->assertRedirect();
        $this->assertDatabaseHas('favorites', [
            'user_id' => $user->id,
            'kost_id' => $kost->id,
        ]);
    }

    public function test_owner_can_accept_booking_and_reduce_availability(): void
    {
        $owner = User::factory()->create(['role' => User::ROLE_OWNER]);
        $user = User::factory()->create(['role' => User::ROLE_USER]);
        $kost = $this->createKostWithRoom($owner, [], ['total_kamar' => 5, 'kamar_tersedia' => 2]);

        $booking = Booking::query()->create([
            'user_id' => $user->id,
            'kost_id' => $kost->id,
            'tanggal_masuk' => now()->addDays(5)->toDateString(),
            'durasi_bulan' => 6,
            'status' => Booking::STATUS_PENDING,
            'created_at' => now(),
        ]);

        $response = $this->actingAs($owner)->patch(route('owner.bookings.status', $booking), [
            'status' => Booking::STATUS_ACCEPTED,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id,
            'status' => Booking::STATUS_ACCEPTED,
        ]);
        $this->assertDatabaseHas('rooms', [
            'kost_id' => $kost->id,
            'kamar_tersedia' => 1,
        ]);
    }
}
