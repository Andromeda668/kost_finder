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
                'harga' => 1800000,
                'deskripsi' => 'Kost putri dengan kamar rapi, area tenang, dan akses cepat ke halte TransJakarta.',
                'fasilitas' => "AC\nWiFi\nKamar mandi dalam\nLaundry mingguan",
            ],
            [
                'nama_kost' => 'Kost Teras Senja',
                'alamat' => 'Jl. Cendana Raya No. 7, Depok',
                'lokasi' => 'Depok',
                'google_maps_link' => 'https://maps.google.com/?q=Depok',
                'harga' => 1250000,
                'deskripsi' => 'Pilihan nyaman untuk mahasiswa dan pekerja muda dengan suasana hangat seperti rumah.',
                'fasilitas' => "WiFi\nParkir motor\nDapur bersama\nKasur dan lemari",
            ],
            [
                'nama_kost' => 'Kost Botanica Living',
                'alamat' => 'Jl. Pandan Wangi No. 25, Sleman, Yogyakarta',
                'lokasi' => 'Sleman',
                'google_maps_link' => 'https://maps.google.com/?q=Sleman+Yogyakarta',
                'harga' => 950000,
                'deskripsi' => 'Kost modern minimalis dengan halaman hijau dan lingkungan yang adem.',
                'fasilitas' => "WiFi\nKursi kerja\nCCTV\nAir panas",
            ],
        ];

        foreach ($samples as $sample) {
            $kost = Kost::query()->updateOrCreate(
                [
                    'user_id' => $owner->id,
                    'nama_kost' => $sample['nama_kost'],
                ],
                $sample
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
                ],
                [
                    'durasi_bulan' => 6,
                    'status' => Booking::STATUS_PENDING,
                    'created_at' => now(),
                ]
            );
        }
    }
}
