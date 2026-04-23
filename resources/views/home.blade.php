@extends('layouts.app')

@section('title', 'KostFinder - Cari kost terbaik untuk hidup nyaman')

@section('content')
    <section class="travel-hero" data-reveal>
        <div class="grid gap-10 lg:grid-cols-[1.15fr_0.85fr] lg:items-center">
            <div>
                <p class="eyebrow">Platform Booking Kost</p>
                <h1 class="font-display text-4xl font-semibold leading-tight text-[var(--ink)] sm:text-5xl lg:text-6xl">
                    Cari kost terbaik untuk hidup nyaman
                </h1>
                <p class="mt-5 max-w-2xl text-base leading-8 text-[var(--muted)] sm:text-lg">
                    Temukan kost dengan informasi kamar yang jelas, alur booking cepat, dan kontak owner langsung dalam satu pengalaman yang rapi seperti platform travel modern.
                </p>

                <div class="mt-7 flex flex-wrap gap-3">
                    <span class="mini-pill">Booking mudah</span>
                    <span class="mini-pill">Status kamar real-time</span>
                    <span class="mini-pill">Chat owner langsung</span>
                </div>
            </div>

            <div class="hero-stat-grid">
                <article class="hero-stat-card">
                    <p class="hero-stat-label">Total hasil</p>
                    <p class="hero-stat-value">{{ $kosts->total() }}</p>
                    <p class="hero-stat-copy">Pilihan kost tampil sesuai pencarian Anda.</p>
                </article>
                <article class="hero-stat-card hero-stat-card-accent">
                    <p class="hero-stat-label">Flow booking</p>
                    <p class="hero-stat-value">Search → Detail → Booking</p>
                    <p class="hero-stat-copy">Dibuat ringkas supaya user cepat mengambil keputusan.</p>
                </article>
            </div>
        </div>

        <form action="{{ route('home') }}" method="GET" class="search-surface" data-search-form>
            <div class="search-field search-field-icon">
                <span class="search-icon">⌖</span>
                <label class="field-group">
                    <span>Lokasi</span>
                    <select
                        name="search"
                        class="field-input field-input-plain"
                        data-location-select
                        data-placeholder="Semua kota/kabupaten"
                        data-selected="{{ $search }}"
                    >
                        <option value="">{{ $search ?: 'Memuat daftar lokasi...' }}</option>
                    </select>
                </label>
            </div>

            <div class="search-field">
                <label class="field-group">
                    <span>Harga maksimal</span>
                    <input type="number" name="max_price" value="{{ $maxPrice }}" min="0" placeholder="Contoh 1500000" class="field-input field-input-plain">
                </label>
            </div>

            <div class="search-field">
                <label class="field-group">
                    <span>Urutkan</span>
                    <select name="sort" class="field-input field-input-plain">
                        <option value="latest" @selected($sort === 'latest')>Terbaru</option>
                        <option value="price_asc" @selected($sort === 'price_asc')>Harga termurah</option>
                        <option value="price_desc" @selected($sort === 'price_desc')>Harga tertinggi</option>
                        <option value="availability" @selected($sort === 'availability')>Kamar tersedia terbanyak</option>
                    </select>
                </label>
            </div>

            <button type="submit" class="search-submit">
                Cari
            </button>
        </form>
    </section>

    @if ($quickLocations->isNotEmpty())
        <section class="mt-8" data-reveal>
            <div class="flex flex-wrap items-center gap-3">
                <span class="text-sm font-semibold text-[var(--muted)]">Filter lokasi cepat:</span>
                @foreach ($quickLocations as $location)
                    <a
                        href="{{ route('home', ['quick_location' => $location, 'sort' => $sort]) }}"
                        class="quick-filter-chip {{ $activeQuickLocation === $location ? 'quick-filter-chip-active' : '' }}"
                    >
                        {{ $location }}
                    </a>
                @endforeach
                @if ($activeQuickLocation)
                    <a href="{{ route('home', ['sort' => $sort]) }}" class="quick-filter-reset">Reset</a>
                @endif
            </div>
        </section>
    @endif

    <section class="mt-10" data-reveal>
        <div class="mb-6 flex flex-wrap items-end justify-between gap-4">
            <div>
                <p class="eyebrow">Hasil Pencarian</p>
                <h2 class="font-display text-3xl font-semibold">Pilihan Kost Tersedia</h2>
            </div>

            @if ($search || $maxPrice || $activeQuickLocation || $sort !== 'latest')
                <a href="{{ route('home') }}" class="text-sm font-semibold text-[var(--terracotta-deep)]">Reset semua filter</a>
            @endif
        </div>

        @if ($kosts->count())
            <div class="space-y-5" data-loading-region>
                @foreach ($kosts as $kost)
                    @php
                        $image = $kost->primaryImage?->image_path ? asset('storage/'.$kost->primaryImage->image_path) : null;
                        $facilities = collect(preg_split('/\r\n|\r|\n/', $kost->fasilitas) ?: [])->filter()->take(3);
                        $isFavorited = (bool) ($kost->is_favorited ?? false);
                    @endphp

                    <article class="search-result-card" data-reveal>
                        <div class="search-result-media">
                            @if ($image)
                                <img src="{{ $image }}" alt="{{ $kost->nama_kost }}" class="h-full w-full object-cover">
                            @else
                                <div class="placeholder-cover h-full min-h-[220px] w-full">
                                    <span>Foto Kost</span>
                                </div>
                            @endif
                        </div>

                        <div class="search-result-main">
                            <div class="flex flex-wrap items-start justify-between gap-4">
                                <div>
                                    <p class="search-result-location">{{ $kost->lokasi }}</p>
                                    <h3 class="font-display text-3xl font-semibold">{{ $kost->nama_kost }}</h3>
                                    <p class="mt-3 text-sm leading-7 text-[var(--muted)]">{{ \Illuminate\Support\Str::limit($kost->alamat, 92) }}</p>
                                </div>

                                @auth
                                    @if (! auth()->user()->isOwner())
                                        <form action="{{ route('favorites.toggle', $kost) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="wishlist-button {{ $isFavorited ? 'wishlist-button-active' : '' }}">
                                                {{ $isFavorited ? 'Tersimpan' : 'Wishlist' }}
                                            </button>
                                        </form>
                                    @endif
                                @endauth
                            </div>

                            <div class="mt-5 flex flex-wrap items-center gap-3">
                                <span class="{{ $kost->availability_badge_class }}">{{ $kost->availability_status }}</span>
                                <span class="mini-meta">{{ $kost->room?->kamar_tersedia ?? 0 }} / {{ $kost->room?->total_kamar ?? 0 }} kamar tersedia</span>
                            </div>

                            <div class="mt-5 flex flex-wrap gap-3">
                                @foreach ($facilities as $facility)
                                    <span class="feature-chip">{{ trim($facility) }}</span>
                                @endforeach
                            </div>

                            <p class="mt-5 line-clamp-2 text-sm leading-7 text-[var(--muted)]">{{ $kost->deskripsi }}</p>
                        </div>

                        <div class="search-result-side">
                            <div>
                                <p class="text-sm font-medium text-[var(--muted)]">Mulai dari</p>
                                <p class="search-result-price">Rp {{ number_format($kost->harga, 0, ',', '.') }}</p>
                                <p class="text-sm text-[var(--muted)]">per bulan</p>
                            </div>

                            <div class="space-y-3">
                                <a href="{{ route('kosts.show', $kost) }}" class="solid-button w-full text-center">
                                    Lihat Detail
                                </a>

                                @auth
                                    @if (! auth()->user()->isOwner())
                                        <a href="{{ route('bookings.create', $kost) }}" class="ghost-button w-full text-center">
                                            Booking Cepat
                                        </a>
                                    @endif
                                @endauth
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>

            <div class="mt-8">
                {{ $kosts->links() }}
            </div>
        @else
            <div class="empty-state">
                <h3 class="font-display text-2xl font-semibold">Belum ada hasil yang cocok</h3>
                <p class="mt-3 text-[var(--muted)]">Ubah lokasi, batas harga, atau urutan hasil untuk menemukan kost yang lebih sesuai.</p>
            </div>
        @endif
    </section>
@endsection
