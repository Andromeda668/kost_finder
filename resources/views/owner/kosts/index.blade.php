@extends('layouts.app')

@section('title', 'Kelola Kost - KostFinder')

@section('content')
    <div class="owner-dashboard-shell owner-dashboard-compact">
        <section class="dashboard-table-card dashboard-table-card-compact" data-reveal id="owner-listings">
            <div class="dashboard-section-head">
                <div>
                    <p class="eyebrow">Kelola Kost</p>
                    <h1 class="dashboard-section-title mt-2">Daftar kost Anda</h1>
                    <p class="dashboard-section-copy mt-1">Halaman ini fokus untuk mengelola listing. Ringkasan & booking ada di dashboard.</p>
                </div>
                <div class="flex flex-wrap items-center gap-3">
                    <a href="{{ route('owner.dashboard') }}" class="ghost-button">Buka Dashboard</a>
                    <a href="{{ route('owner.kosts.create') }}" class="solid-button">Tambah Kost</a>
                </div>
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

                    <article class="owner-kost-row" data-reveal>
                        <!-- Gambar Kiri -->
                        <div class="owner-kost-image-wrapper">
                            <a href="{{ route('kosts.show', $kost) }}" class="owner-kost-image" aria-label="Lihat detail {{ $kost->nama_kost }}">
                                @if ($image)
                                    <img src="{{ $image }}" alt="{{ $kost->nama_kost }}" loading="lazy">
                                @else
                                    <div class="placeholder-cover h-full w-full flex items-center justify-center bg-[var(--surface-muted)]">
                                        <span class="text-xs text-[var(--muted)]">Foto</span>
                                    </div>
                                @endif
                                <span class="{{ $kost->availability_badge_class }}">{{ $kost->availability_status }}</span>
                            </a>
                        </div>

                        <!-- Info Tengah -->
                        <div class="owner-kost-info">
                            <div class="owner-kost-header">
                                <h3>
                                    <a href="{{ route('kosts.show', $kost) }}" class="text-[var(--ink)] hover:text-[var(--primary)]">{{ $kost->nama_kost }}</a>
                                </h3>
                            </div>

                            <div class="owner-kost-meta">
                                <span class="owner-kost-meta-item">
                                    <span class="text-xs text-[var(--muted)]">Lokasi:</span>
                                    <span class="font-medium">{{ $kost->lokasi }}</span>
                                </span>
                                <span class="owner-kost-meta-item">
                                    <span class="text-xs text-[var(--muted)]">Kamar:</span>
                                    <span class="font-medium">{{ $availableRooms }} / {{ $totalRooms }}</span>
                                </span>
                            </div>
                        </div>

                        <!-- Harga & Aksi Kanan -->
                        <div class="owner-kost-actions">
                            <div class="owner-kost-price">
                                <p class="text-sm text-[var(--muted)]">Harga {{ $period === 'harian' ? 'harian' : 'bulanan' }}</p>
                                <p class="text-lg font-bold text-[var(--primary)]">{{ $kost->currency_symbol }} {{ $kost->formatMoney($price) }}</p>
                            </div>

                            <div class="owner-kost-buttons">
                                <a href="{{ route('owner.kosts.edit', $kost) }}" class="solid-button">Edit</a>
                                <a href="{{ route('kosts.show', $kost) }}" class="ghost-button">Lihat</a>
                                <form action="{{ route('owner.kosts.destroy', $kost) }}" method="POST" onsubmit="return confirm('Hapus kost ini?')" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="ghost-button text-[var(--soft-red)]">Hapus</button>
                                </form>
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="rounded-[26px] border border-[var(--line)] bg-[var(--surface-muted)] p-6 text-sm text-[var(--muted)]">
                        Belum ada kost ditambahkan.
                    </div>
                @endforelse
            </div>

            <div class="mt-5">
                {{ $kosts->links() }}
            </div>
        </section>
    </div>
@endsection
