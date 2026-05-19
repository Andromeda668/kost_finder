@extends('layouts.app')

@section('title', 'Dashboard Owner - KostFinder')

@section('content')
    @php
        $occupiedRooms = max($totalRooms - $availableRooms, 0);
        $occupancyRate = $totalRooms > 0 ? min(100, (int) round(($occupiedRooms / $totalRooms) * 100)) : 0;
    @endphp

    <div class="owner-dashboard-shell">
        <section class="owner-dashboard-showcase" data-reveal>
            <div class="owner-showcase-media">
                <img src="{{ asset('images/dashboard/owner-hero-room.jpeg') }}" alt="Interior kamar kost modern" class="owner-showcase-image">
                <div class="owner-showcase-overlay"></div>
                <div class="owner-showcase-copy">
                    <p class="eyebrow text-white/90">Dashboard Owner</p>
                    <h1 class="owner-showcase-title mt-3">Kelola kost lebih cepat, tanpa ribet.</h1>
                    <p class="owner-showcase-text mt-4">Mulai dari booking masuk, status kamar, sampai aksi pengelolaan kost — semuanya jelas dalam satu halaman.</p>

                    <div class="owner-showcase-actions mt-6">
                        <a href="{{ route('owner.kosts.create') }}" class="solid-button">Tambah Kost</a>
                        <a href="{{ route('owner.kosts.index') }}" class="owner-showcase-link">Kelola Kost</a>
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
                        <p class="owner-dashboard-stat-label">Butuh Diproses</p>
                        <p class="owner-dashboard-stat-value">{{ $pendingBookings }}</p>
                        <p class="owner-dashboard-stat-copy">Booking pending</p>
                    </article>
                </div>

                <div class="owner-panel-meter">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="owner-panel-meter-label">Okupansi kamar</p>
                            <p class="owner-panel-meter-copy">{{ $occupiedRooms }} kamar terisi dari {{ $totalRooms }} total kamar.</p>
                        </div>
                        <span class="owner-panel-meter-value">{{ $occupancyRate }}%</span>
                    </div>
                    <div class="owner-panel-meter-track" aria-hidden="true">
                        <span style="width: {{ $occupancyRate }}%"></span>
                    </div>
                </div>
            </div>
        </section>

        <section class="grid gap-5">
            <article class="dashboard-table-card dashboard-table-card-compact" data-reveal>
                <div class="dashboard-section-head">
                    <div>
                        <p class="eyebrow">Permintaan Booking</p>
                        <h2 class="dashboard-section-title mt-2">Perlu tindakan</h2>
                        <p class="dashboard-section-copy mt-1">Tinjau booking pending dan putuskan diterima atau ditolak.</p>
                    </div>
                </div>

                <div class="grid gap-3">
                    @forelse ($pendingBookingItems as $booking)
                        @php
                            $image = $booking->kost->display_image_url;
                        @endphp
                        <div class="rounded-[26px] border border-[var(--line)] bg-white p-5">
                            <div class="owner-card-row">
                                <div class="flex min-w-0 items-start gap-4">
                                    @if ($image)
                                        <img src="{{ $image }}" alt="{{ $booking->kost->nama_kost }}" class="owner-mini-cover" width="48" height="48" style="width:48px;height:48px;object-fit:cover;border-radius:16px;">
                                    @else
                                        <div class="placeholder-cover owner-mini-cover">
                                            <span>Foto</span>
                                        </div>
                                    @endif
                                    <div class="min-w-0">
                                        <p class="text-sm font-semibold text-[var(--ink)]">{{ $booking->user->name }}</p>
                                        <p class="mt-1 truncate text-sm text-[var(--muted)]">{{ $booking->kost->nama_kost }} • {{ $booking->durasi_label }}</p>
                                        <p class="mt-1 text-xs text-[var(--muted)]">Masuk: {{ $booking->tanggal_masuk->translatedFormat('d M Y') }} | {{ $booking->payment_method_label }} | {{ $booking->payment_status_label }}</p>
                                    </div>
                                </div>

                                <div class="flex flex-wrap items-center justify-end gap-3">
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
                            </div>
                        </div>
                    @empty
                        <div class="rounded-[26px] border border-[var(--line)] bg-[var(--surface-muted)] p-6 text-sm text-[var(--muted)]">
                            Belum ada booking pending. Jika ada, akan muncul di sini.
                        </div>
                    @endforelse
                </div>

                <div class="mt-5 flex justify-end">
                    <a href="{{ route('owner.bookings.index') }}" class="solid-button">Lihat Semua Booking</a>
                </div>
            </article>

            <article class="dashboard-table-card dashboard-table-card-compact" data-reveal>
                <div class="dashboard-section-head">
                    <div>
                        <p class="eyebrow">Listing</p>
                        <h2 class="dashboard-section-title mt-2">Daftar Kost</h2>
                        <p class="dashboard-section-copy mt-1">Ringkas kost Anda yang paling baru.</p>
                    </div>
                    <a href="{{ route('owner.kosts.index') }}" class="solid-button">Lihat Daftar Kost</a>
                </div>

                <div class="owner-kost-list">
                    @forelse ($kosts as $kost)
                        @php
                            $image = $kost->display_image_url;
                            $period = $kost->primary_rental_period;
                            $price = $kost->priceFor($period);
                            $availableRooms = $kost->room?->kamar_tersedia ?? 0;
                            $totalRooms = $kost->room?->total_kamar ?? 0;
                        @endphp
                        <article class="owner-kost-row">
                            <div class="owner-kost-image-wrapper">
                                <a href="{{ route('kosts.show', $kost) }}" class="owner-kost-image" aria-label="Lihat detail {{ $kost->nama_kost }}">
                                    @if ($image)
                                        <img src="{{ $image }}" alt="{{ $kost->nama_kost }}" loading="lazy">
                                    @else
                                        <div class="placeholder-cover h-full w-full flex items-center justify-center bg-[var(--surface-muted)]">
                                            <span class="text-xs text-[var(--muted)]">Foto</span>
                                        </div>
                                    @endif
                                </a>
                            </div>

                            <div class="owner-kost-info">
                                <div class="owner-kost-header">
                                    <h3><a href="{{ route('kosts.show', $kost) }}">{{ $kost->nama_kost }}</a></h3>
                                </div>
                                <div class="owner-kost-meta">
                                    <span class="owner-kost-meta-item"><span>Lokasi:</span><span>{{ $kost->lokasi }}</span></span>
                                    <span class="owner-kost-meta-item"><span>Kamar:</span><span>{{ $availableRooms }} / {{ $totalRooms }}</span></span>
                                    <span class="owner-kost-meta-item"><span>Harga:</span><span>{{ $kost->currency_symbol }} {{ $kost->formatMoney($price) }} / {{ $period === 'harian' ? 'hari' : 'bulan' }}</span></span>
                                </div>
                            </div>

                            <div class="owner-kost-actions">
                                <div class="owner-kost-price">
                                    <p>Harga</p>
                                    <p>{{ $kost->currency_symbol }} {{ $kost->formatMoney($price) }}</p>
                                </div>
                                <div class="owner-kost-buttons">
                                    <a href="{{ route('kosts.show', $kost) }}" class="ghost-button">Lihat</a>
                                    <a href="{{ route('owner.kosts.edit', $kost) }}" class="solid-button">Edit</a>
                                </div>
                            </div>
                        </article>
                    @empty
                        <div class="rounded-[26px] border border-[var(--line)] bg-[var(--surface-muted)] p-6 text-sm text-[var(--muted)]">
                            Belum ada kost. Tambahkan kost pertama untuk mulai menerima booking.
                        </div>
                    @endforelse
                </div>

            </article>
        </section>

        <section class="dashboard-table-card dashboard-table-card-compact" data-reveal id="dashboard-activity">
            <div class="dashboard-section-head">
                <div>
                    <p class="eyebrow">Aktivitas</p>
                    <h2 class="dashboard-section-title mt-2">Riwayat booking</h2>
                    <p class="dashboard-section-copy mt-1">Daftar histori booking tanpa aksi langsung. Gunakan halaman booking jika perlu kelola status.</p>
                </div>
            </div>

            <div class="table-shell">
                <table class="dashboard-table dashboard-table-compact">
                    <thead>
                        <tr>
                            <th>Pemesan</th>
                            <th>Kost</th>
                            <th>Masuk</th>
                            <th>Durasi</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($bookings as $booking)
                            <tr>
                                <td class="font-semibold text-[var(--ink)]">{{ $booking->user->name }}</td>
                                <td>{{ $booking->kost->nama_kost }}</td>
                                <td>{{ $booking->tanggal_masuk->translatedFormat('d M Y') }}</td>
                                <td>{{ $booking->durasi_label }}</td>
                                <td><span class="{{ $booking->status_badge_class }}">{{ ucfirst($booking->status) }}</span></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-[var(--muted)]">Belum ada booking masuk.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-5 flex justify-end">
                <a href="{{ route('owner.history') }}" class="solid-button">Lihat Riwayat Lengkap</a>
            </div>
        </section>
    </div>
@endsection
