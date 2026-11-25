<?php

namespace Database\Seeders;

use App\Models\Kost;
use App\Models\KostImage;
use App\Models\Room;
use App\Models\User;
use Illuminate\Database\Seeder;

class SampleKostSeeder extends Seeder
{
    public function run(): void
    {
        $owner = User::query()->where('email', 'mami@gmail.com')->first();

        if (! $owner) {
            $this->command?->error('Akun mami@gmail.com tidak ditemukan.');

            return;
        }

        $samples = [
            [
                'nama_kost' => 'Kost Melati Asri',
                'alamat' => 'Jl. Melati No. 12, Laweyan, Surakarta',
                'lokasi' => 'Laweyan, Surakarta',
                'maps' => 'Kost+Laweyan+Surakarta',
                'harga' => 850000,
                'deskripsi' => 'Kost nyaman dan bersih di lingkungan tenang, cocok untuk mahasiswa maupun pekerja.',
                'fasilitas' => "WiFi\nKamar mandi dalam\nKasur dan lemari\nParkir motor",
                'total' => 10,
                'tersedia' => 4,
                'foto' => 'melati-asri.jpg',
            ],
            [
                'nama_kost' => 'Kost Griya Manahan',
                'alamat' => 'Jl. Adi Sucipto No. 28, Manahan, Surakarta',
                'lokasi' => 'Manahan, Surakarta',
                'maps' => 'Kost+Manahan+Surakarta',
                'harga' => 1200000,
                'deskripsi' => 'Hunian praktis dengan akses mudah ke pusat kota, area olahraga, dan transportasi umum.',
                'fasilitas' => "WiFi\nAC\nKamar mandi dalam\nParkir motor dan mobil",
                'total' => 12,
                'tersedia' => 5,
                'foto' => 'griya-manahan.jpg',
            ],
            [
                'nama_kost' => 'Kost Puri UNS',
                'alamat' => 'Jl. Ki Hajar Dewantara No. 6, Jebres, Surakarta',
                'lokasi' => 'Jebres, Surakarta',
                'maps' => 'Kost+Jebres+Surakarta',
                'harga' => 700000,
                'deskripsi' => 'Kost sederhana yang dekat kampus dan tempat makan, dengan suasana yang nyaman untuk belajar.',
                'fasilitas' => "WiFi\nMeja belajar\nLemari\nDapur bersama",
                'total' => 8,
                'tersedia' => 3,
                'foto' => 'puri-uns.jpg',
            ],
            [
                'nama_kost' => 'Kost Taman Kartini',
                'alamat' => 'Jl. Kartini No. 19, Banjarsari, Surakarta',
                'lokasi' => 'Banjarsari, Surakarta',
                'maps' => 'Kost+Banjarsari+Surakarta',
                'harga' => 950000,
                'deskripsi' => 'Kost dengan halaman asri dan kamar berjendela, berada di lingkungan yang nyaman.',
                'fasilitas' => "WiFi\nKulkas bersama\nDapur bersama\nParkir motor",
                'total' => 9,
                'tersedia' => 2,
                'foto' => 'taman-kartini.jpg',
            ],
            [
                'nama_kost' => 'Kost Harmoni Pasar Kliwon',
                'alamat' => 'Jl. Kapten Mulyadi No. 45, Pasar Kliwon, Surakarta',
                'lokasi' => 'Pasar Kliwon, Surakarta',
                'maps' => 'Kost+Pasar+Kliwon+Surakarta',
                'harga' => 650000,
                'deskripsi' => 'Pilihan kost terjangkau dengan akses ke pasar, pusat kuliner, dan berbagai kebutuhan harian.',
                'fasilitas' => "Kipas angin\nLemari\nKamar mandi luar\nParkir motor",
                'total' => 7,
                'tersedia' => 3,
                'foto' => 'harmoni-pasar-kliwon.jpg',
            ],
            [
                'nama_kost' => 'Kost Nusa Indah',
                'alamat' => 'Jl. Samanhudi No. 8, Sondakan, Surakarta',
                'lokasi' => 'Sondakan, Surakarta',
                'maps' => 'Kost+Sondakan+Surakarta',
                'harga' => 800000,
                'deskripsi' => 'Kost rapi dengan suasana kekeluargaan dan akses yang mudah ke fasilitas umum.',
                'fasilitas' => "WiFi\nKulkas\nKompor bersama\nKamar mandi luar",
                'total' => 10,
                'tersedia' => 6,
                'foto' => 'nusa-indah.jpg',
            ],
            [
                'nama_kost' => 'Kost Cendana Solo Baru',
                'alamat' => 'Jl. Ir. Soekarno No. 17, Grogol, Sukoharjo',
                'lokasi' => 'Grogol, Sukoharjo',
                'maps' => 'Kost+Grogol+Sukoharjo',
                'harga' => 1500000,
                'deskripsi' => 'Kost modern untuk pekerja dengan akses praktis ke area perkantoran dan pusat belanja.',
                'fasilitas' => "AC\nWiFi\nKamar mandi dalam\nParkir mobil\nDapur bersama",
                'total' => 14,
                'tersedia' => 5,
                'foto' => 'cendana-solo-baru.jpg',
            ],
            [
                'nama_kost' => 'Kost Mawar Residence',
                'alamat' => 'Jl. Rajawali No. 21, Colomadu, Karanganyar',
                'lokasi' => 'Colomadu, Karanganyar',
                'maps' => 'Kost+Colomadu+Karanganyar',
                'harga' => 1100000,
                'deskripsi' => 'Kost nyaman dengan kamar luas dan area bersama yang bersih untuk penghuni.',
                'fasilitas' => "WiFi\nKasur dan lemari\nKulkas\nParkir motor",
                'total' => 11,
                'tersedia' => 4,
                'foto' => 'mawar-residence.jpg',
            ],
            [
                'nama_kost' => 'Kost Anggrek Jebres',
                'alamat' => 'Jl. Ir. Sutami No. 33, Jebres, Surakarta',
                'lokasi' => 'Jebres, Surakarta',
                'maps' => 'Kost+Jebres+Surakarta',
                'harga' => 900000,
                'deskripsi' => 'Kost tenang dekat kampus dan rumah sakit, dilengkapi fasilitas dasar untuk tinggal nyaman.',
                'fasilitas' => "WiFi\nMeja belajar\nKamar mandi dalam\nKompor bersama",
                'total' => 9,
                'tersedia' => 3,
                'foto' => 'anggrek-jebres.jpg',
            ],
        ];

        foreach ($samples as $sample) {
            $kost = Kost::query()->updateOrCreate(
                ['user_id' => $owner->id, 'nama_kost' => $sample['nama_kost']],
                [
                    'alamat' => $sample['alamat'],
                    'lokasi' => $sample['lokasi'],
                    'google_maps_link' => 'https://maps.google.com/?q='.$sample['maps'],
                    'currency' => 'IDR',
                    'harga' => $sample['harga'],
                    'harga_bulanan' => $sample['harga'],
                    'harga_harian' => null,
                    'deskripsi' => $sample['deskripsi'],
                    'fasilitas' => $sample['fasilitas'],
                    'payment_methods' => ['qris', 'cash'],
                ]
            );

            Room::query()->updateOrCreate(
                ['kost_id' => $kost->id],
                [
                    'total_kamar' => $sample['total'],
                    'kamar_tersedia' => $sample['tersedia'],
                ]
            );

            KostImage::query()->updateOrCreate(
                ['kost_id' => $kost->id],
                ['image_path' => 'kost/'.$sample['foto']]
            );
        }
    }
}
