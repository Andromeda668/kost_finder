@extends('layouts.app')

@section('title', 'Dashboard Owner - KostFinder')

@section('content')
    <div class="owner-dashboard-shell">
        <section class="owner-dashboard-showcase" data-reveal>
            <div class="owner-showcase-media">
                <img src="{{ asset('images/dashboard/owner-hero-room.jpeg') }}" alt="Interior kamar kost modern" class="owner-showcase-image">
                <div class="owner-showcase-overlay"></div>
                <div class="owner-showcase-copy">
                    <p class="eyebrow text-white/90">Dashboard Owner</p>
                    <h1 class="owner-showcase-title mt-3">Kelola kost dengan tampilan yang lebih estetik, fokus, dan profesional.</h1>
                    <p class="owner-showcase-text mt-4">Semua ringkasan penting, aktivitas booking, dan performa listing tampil dalam satu workspace yang lebih rapi dan nyaman dipantau setiap hari.</p>

                    <div class="owner-showcase-actions mt-6">
                        <a href="{{ route('owner.kosts.create') }}" class="solid-button">Tambah Kost Baru</a>
                        <a href="#owner-listings" class="owner-showcase-link">Lihat Listing</a>
                    </div>
                </div>
            </div>

            <div class="owner-showcase-panel">
                <div class="owner-dashboard-stats owner-dashboard-stats-showcase">
                    <article class="owner-dashboard-stat-card owner-dashboard-stat-card-light">
                        <p class="owner-dashboard-stat-label">Total Kost</p>
                        <p class="owner-dashboard-stat-value">{{ $totalKosts }}</p>
                        <p class="owner-dashboard-stat-copy">Listing aktif</p>
                    </article>
                    <article class="owner-dashboard-stat-card owner-dashboard-stat-card-light">
                        <p class="owner-dashboard-stat-label">Kamar Tersedia</p>
                        <p class="owner-dashboard-stat-value">{{ $availableRooms }}</p>
                        <p class="owner-dashboard-stat-copy">Dari {{ $totalRooms }} kamar</p>
                    </article>
                    <article class="owner-dashboard-stat-card owner-dashboard-stat-card-warm">
                        <p class="owner-dashboard-stat-label">Booking Pending</p>
                        <p class="owner-dashboard-stat-value">{{ $pendingBookings }}</p>
                        <p class="owner-dashboard-stat-copy">Menunggu keputusan</p>
                    </article>
                </div>

                <div class="owner-showcase-note">
                    <p class="owner-showcase-note-label">Aksi cepat</p>
                    <div class="owner-showcase-note-list">
                        <div class="owner-showcase-note-item">
                            <span class="owner-showcase-note-icon">01</span>
                            <div>
                                <p class="font-semibold text-[var(--ink)]">Tambah listing baru</p>
                                <p class="mt-1 text-sm leading-6 text-[var(--muted)]">Masukkan kost baru dengan detail yang lengkap dan siap tampil.</p>
                            </div>
                        </div>
                        <div class="owner-showcase-note-item">
                            <span class="owner-showcase-note-icon">02</span>
                            <div>
                                <p class="font-semibold text-[var(--ink)]">Tinjau booking masuk</p>
                                <p class="mt-1 text-sm leading-6 text-[var(--muted)]">Prioritaskan booking pending agar calon penyewa mendapat respons cepat.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="dashboard-grid">
            <article class="dashboard-table-card dashboard-table-card-compact" data-reveal id="owner-listings">
                <div class="dashboard-section-head">
                    <div>
                        <p class="eyebrow">Listing Kost</p>
                        <h2 class="dashboard-section-title mt-2">Daftar kost Anda</h2>
                        <p class="dashboard-section-copy mt-1">Tampilan listing dibuat lebih bersih supaya status dan aksi utama lebih cepat dipindai.</p>
                    </div>
                    <a href="{{ route('owner.kosts.create') }}" class="ghost-button">Tambah Listing</a>
                </div>

                <div class="table-shell">
                    <table class="dashboard-table dashboard-table-compact">
                        <thead>
                            <tr>
                                <th>Kost</th>
                                <th>Harga</th>
                                <th>Kamar</th>
                                <th>Status</th>
                                <th class="text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($kosts as $kost)
                                <tr>
                                    <td>
                                        <div class="table-kost-title">
                                            <span class="font-semibold text-[var(--ink)]">{{ $kost->nama_kost }}</span>
                                            <span class="text-xs text-[var(--muted)]">{{ $kost->lokasi }}</span>
                                        </div>
                                    </td>
                                    <td>Rp {{ number_format($kost->harga, 0, ',', '.') }}</td>
                                    <td>{{ $kost->room?->kamar_tersedia ?? 0 }} / {{ $kost->room?->total_kamar ?? 0 }}</td>
                                    <td><span class="{{ $kost->availability_badge_class }}">{{ $kost->availability_status }}</span></td>
                                    <td>
                                        <div class="table-actions justify-end">
                                            <a href="{{ route('kosts.show', $kost) }}" class="table-link">Lihat</a>
                                            <a href="{{ route('owner.kosts.edit', $kost) }}" class="table-link">Edit</a>
                                            <form action="{{ route('owner.kosts.destroy', $kost) }}" method="POST" onsubmit="return confirm('Hapus kost ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="table-link table-link-danger">Hapus</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-[var(--muted)]">Belum ada kost ditambahkan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-5">
                    {{ $kosts->links() }}
                </div>
            </article>

            <article class="dashboard-table-card dashboard-table-card-compact" data-reveal>
                <div class="dashboard-section-head">
                    <div>
                        <p class="eyebrow">Booking Masuk</p>
                        <h2 class="dashboard-section-title mt-2">Booking terbaru</h2>
                        <p class="dashboard-section-copy mt-1">Booking yang perlu segera diproses ditempatkan di area yang tetap tenang dan mudah dibaca.</p>
                    </div>
                </div>

                <div class="table-shell">
                    <table class="dashboard-table dashboard-table-compact">
                        <thead>
                            <tr>
                                <th>Pemesan</th>
                                <th>Kost</th>
                                <th>Masuk</th>
                                <th>Status</th>
                                <th class="text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($bookings as $booking)
                                <tr>
                                    <td>
                                        <div class="table-kost-title">
                                            <span class="font-semibold text-[var(--ink)]">{{ $booking->user->name }}</span>
                                            <span class="text-xs text-[var(--muted)]">{{ $booking->durasi_bulan }} bulan</span>
                                        </div>
                                    </td>
                                    <td>{{ $booking->kost->nama_kost }}</td>
                                    <td>{{ $booking->tanggal_masuk->translatedFormat('d M Y') }}</td>
                                    <td><span class="{{ $booking->status_badge_class }}">{{ ucfirst($booking->status) }}</span></td>
                                    <td>
                                        @if ($booking->status === \App\Models\Booking::STATUS_PENDING)
                                            <div class="table-actions justify-end">
                                                <form action="{{ route('owner.bookings.status', $booking) }}" method="POST">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="status" value="diterima">
                                                    <button type="submit" class="table-link table-link-success">Terima</button>
                                                </form>
                                                <form action="{{ route('owner.bookings.status', $booking) }}" method="POST">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="status" value="ditolak">
                                                    <button type="submit" class="table-link table-link-danger">Tolak</button>
                                                </form>
                                            </div>
                                        @else
                                            <div class="text-right text-sm text-[var(--muted)]">Sudah diproses</div>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-[var(--muted)]">Belum ada booking masuk.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-5">
                    {{ $bookings->links() }}
                </div>
            </article>
        </section>
    </div>
@endsection
