<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Kost;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $owner = User::query()->updateOrCreate(
            ['email' => 'owner@kostfinder.test'],
            [
                'name' => 'Nadia Owner',
                'password' => 'admin12345',
                'role' => User::ROLE_OWNER,
                'email_verified_at' => now(),
            ]
        );

        $owner->ownerContact()->updateOrCreate(
            ['user_id' => $owner->id],
            [
                'phone' => '081234567890',
                'email' => 'owner@kostfinder.test',
            ]
        );

        $user = User::query()->updateOrCreate(
            ['email' => 'user@kostfinder.test'],
            [
                'name' => 'Rizky Pencari Kost',
                'password' => 'user12345',
                'role' => User::ROLE_USER,
                'email_verified_at' => now(),
            ]
        );

        $samples = [
            [
                'nama_kost' => 'Kost Sakura Residence',
                'alamat' => 'Jl. Melati No. 18, Setiabudi, Jakarta Selatan',
                'lokasi' => 'Jakarta Selatan',
                'google_maps_link' => 'https://maps.google.com/?q=Setiabudi+Jakarta+Selatan',
                'currency' => 'IDR',
                'harga_bulanan' => 1800000,
                'harga_harian' => 100000, // Example daily price
                'deskripsi' => 'Kost putri dengan kamar rapi, area tenang, dan akses cepat ke halte TransJakarta.',
                'fasilitas' => "AC\nWiFi\nKamar mandi dalam\nLaundry mingguan",
                'payment_methods' => ['e-banking', 'e-wallet', 'qris'],
            ],
            [
                'nama_kost' => 'Kost Teras Senja',
                'alamat' => 'Jl. Cendana Raya No. 7, Depok',
                'lokasi' => 'Depok',
                'google_maps_link' => 'https://maps.google.com/?q=Depok',
                'currency' => 'IDR',
                'harga_bulanan' => 1250000,
                'harga_harian' => null, // No daily price for this one
                'deskripsi' => 'Pilihan nyaman untuk mahasiswa dan pekerja muda dengan suasana hangat seperti rumah.',
                'fasilitas' => "WiFi\nParkir motor\nDapur bersama\nKasur dan lemari",
                'payment_methods' => ['e-wallet', 'cash'],
            ],
            [
                'nama_kost' => 'Kost Botanica Living',
                'alamat' => 'Jl. Pandan Wangi No. 25, Sleman, Yogyakarta',
                'lokasi' => 'Sleman',
                'google_maps_link' => 'https://maps.google.com/?q=Sleman+Yogyakarta',
                'currency' => 'IDR',
                'harga_bulanan' => 950000,
                'harga_harian' => null,
                'deskripsi' => 'Kost modern minimalis dengan halaman hijau dan lingkungan yang adem.',
                'fasilitas' => "WiFi\nKursi kerja\nCCTV\nAir panas",
                'payment_methods' => ['qris', 'cash'],
            ],
        ];

        foreach ($samples as $sample) {
            $kost = Kost::query()->updateOrCreate(
                [
                    'user_id' => $owner->id,
                    'nama_kost' => $sample['nama_kost'],
                ],
                [
                    'alamat' => $sample['alamat'],
                    'lokasi' => $sample['lokasi'],
                    'google_maps_link' => $sample['google_maps_link'],
                    'currency' => $sample['currency'],
                    'harga_bulanan' => $sample['harga_bulanan'],
                    'harga_harian' => $sample['harga_harian'],
                    'harga' => $sample['harga_bulanan'] ?? $sample['harga_harian'] ?? 0, // Legacy field
                    'deskripsi' => $sample['deskripsi'],
                    'fasilitas' => $sample['fasilitas'],
                    'payment_methods' => $sample['payment_methods'],
                ]
            );

            $kost->room()->updateOrCreate(
                ['kost_id' => $kost->id],
                [
                    'total_kamar' => match ($sample['nama_kost']) {
                        'Kost Sakura Residence' => 12,
                        'Kost Teras Senja' => 8,
                        default => 6,
                    },
                    'kamar_tersedia' => match ($sample['nama_kost']) {
                        'Kost Sakura Residence' => 5,
                        'Kost Teras Senja' => 1,
                        default => 0,
                    },
                ]
            );
        }

        $sampleKost = Kost::query()->where('nama_kost', 'Kost Sakura Residence')->first();

        if ($sampleKost) {
            Booking::query()->updateOrCreate(
                [
                    'user_id' => $user->id,
                    'kost_id' => $sampleKost->id,
                    'tanggal_masuk' => now()->addWeek()->toDateString(),
                    'tipe_sewa' => 'bulanan', // Add tipe_sewa
                    'durasi' => 6, // Add durasi
                ],
                [
                    'durasi_bulan' => 6,
                    'payment_method' => 'e-banking', // Default payment method for seeded booking
                    'status' => Booking::STATUS_PENDING,
                    'created_at' => now(),
                ]
            );
        }
    }
}
