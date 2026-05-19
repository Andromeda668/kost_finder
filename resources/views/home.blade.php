@extends('layouts.app')

@section('title', 'KostFinder - Cari kost nyaman dan siap booking')

@section('content')
    @php
        $priceOptions = [
            '500000' => '< Rp 500.000',
            '1000000' => '< Rp 1.000.000',
            '1500000' => '< Rp 1.500.000',
            '2500000' => '< Rp 2.500.000',
            '5000000' => '< Rp 5.000.000',
        ];
        $selectedMaxPrice = $maxPrice ? (string) $maxPrice : '';
        $hasFilters = $search || $maxPrice || $activeQuickLocation || $sort !== 'latest';
    @endphp

    <section class="home-hero" data-reveal>
        <div class="home-hero-copy">
            <p class="eyebrow">KostFinder</p>
            <h1 class="home-hero-title">Temukan kost nyaman lebih cepat.</h1>
            <p class="home-hero-text">
                Pilih lokasi, bandingkan harga, cek fasilitas, lalu booking kost yang paling cocok untuk kamu.
            </p>

            <div class="home-hero-actions">
                <a href="#kost-list" class="solid-button">Cari Kost</a>
                @guest
                    <a href="{{ route('register') }}" class="ghost-button">Daftar</a>
                @endguest
            </div>
        </div>

        <div class="home-hero-visual" aria-hidden="true">
            <img src="{{ asset('images/dashboard/owner-hero-room.jpeg') }}" alt="" class="home-hero-image">
        </div>
    </section>

    <form action="{{ route('home') }}" method="GET" class="home-search-panel" data-search-form data-reveal>
        <div class="contents" data-location-group>
            <label class="field-group">
                <span>Provinsi</span>
                <select
                    class="field-input"
                    data-location-province-select
                    data-placeholder="Semua provinsi"
                >
                    <option value="">Memuat provinsi...</option>
                </select>
            </label>

            <label class="field-group">
                <span>Kabupaten / Kota</span>
                <select
                    name="search"
                    class="field-input"
                    data-location-select
                    data-placeholder="Pilih kabupaten / kota"
                    data-selected="{{ $search }}"
                >
                    <option value="">{{ $search ?: 'Pilih kabupaten / kota' }}</option>
                </select>
            </label>
        </div>

        <label class="field-group">
            <span>Budget maksimal</span>
            <select name="max_price" class="field-input">
                <option value="">Semua harga</option>
                @foreach ($priceOptions as $value => $label)
                    <option value="{{ $value }}" @selected($selectedMaxPrice === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </label>

        <label class="field-group">
            <span>Urutkan</span>
            <select name="sort" class="field-input">
                <option value="latest" @selected($sort === 'latest')>Terbaru</option>
                <option value="price_asc" @selected($sort === 'price_asc')>Harga terendah</option>
                <option value="price_desc" @selected($sort === 'price_desc')>Harga tertinggi</option>
                <option value="availability" @selected($sort === 'availability')>Kamar tersedia</option>
            </select>
        </label>

        <button type="submit" class="solid-button home-search-button">Cari</button>
    </form>

    <section id="kost-list" class="home-results" data-search-results data-reveal>
        <div class="home-section-head">
            <div>
                <p class="eyebrow">Pilihan Kost</p>
                <h2>{{ $search ? 'Kost di '.$search : 'Rekomendasi untuk kamu' }}</h2>
                <p>{{ $kosts->total() }} kost ditemukan</p>
            </div>

            @if ($hasFilters)
                <a href="{{ route('home') }}" class="ghost-button">Reset</a>
            @endif
        </div>

        @if ($quickLocations->isNotEmpty())
            <div class="home-quick-locations" aria-label="Filter lokasi cepat">
                @foreach ($quickLocations as $location)
                    <a
                        href="{{ route('home', array_filter(['quick_location' => $location, 'max_price' => $maxPrice, 'sort' => $sort !== 'latest' ? $sort : null])) }}"
                        class="home-location-chip {{ $activeQuickLocation === $location || $search === $location ? 'home-location-chip-active' : '' }}"
                    >
                        {{ $location }}
                    </a>
                @endforeach
            </div>
        @endif

        @if ($kosts->count())
            <div class="home-kost-grid" data-loading-region>
                @foreach ($kosts as $kost)
                    @php
                        $image = $kost->display_image_url;
                        $facilities = collect(preg_split('/\r\n|\r|\n/', (string) $kost->fasilitas) ?: [])
                            ->map(fn ($facility) => trim($facility))
                            ->filter()
                            ->take(3);
                        $isFavorited = (bool) ($kost->is_favorited ?? false);
                        $availableRooms = $kost->room?->kamar_tersedia ?? 0;
                        $totalRooms = $kost->room?->total_kamar ?? 0;
                    @endphp

                    <article class="home-kost-card" data-reveal>
                        <a href="{{ route('kosts.show', $kost) }}" class="home-kost-media" aria-label="Lihat detail {{ $kost->nama_kost }}">
                            @if ($image)
                                <img src="{{ $image }}" alt="{{ $kost->nama_kost }}">
                            @else
                                <div class="placeholder-cover h-full min-h-[230px] w-full">
                                    <span>Foto belum tersedia</span>
                                </div>
                            @endif
                            <span class="{{ $kost->availability_badge_class }}">{{ $kost->availability_status }}</span>
                        </a>

                        <div class="home-kost-body">
                            <div class="home-kost-topline">
                                <span>{{ $kost->lokasi }}</span>
                                <span>{{ $availableRooms }} / {{ $totalRooms }} kamar</span>
                            </div>

                            <h3>
                                <a href="{{ route('kosts.show', $kost) }}">{{ $kost->nama_kost }}</a>
                            </h3>
                            <p class="home-kost-address">{{ \Illuminate\Support\Str::limit($kost->alamat, 92) }}</p>

                            @if ($facilities->isNotEmpty())
                                <div class="home-facility-row">
                                    @foreach ($facilities as $facility)
                                        <span>{{ $facility }}</span>
                                    @endforeach
                                </div>
                            @endif

                            <div class="home-kost-footer">
                                <div>
                                    @php
                                        $period = $kost->primary_rental_period;
                                        $price = $kost->priceFor($period);
                                    @endphp
                                    <p class="home-price">{{ $kost->currency_symbol }} {{ $kost->formatMoney($price) }} <span class="text-xs text-[var(--muted)]">/ {{ $period === 'harian' ? 'hari' : 'bulan' }}</span></p>
                                    <span>per bulan</span>
                                </div>

                                <div class="home-card-actions">
                                    @auth
                                        @if (! auth()->user()->isOwner())
                                            <form action="{{ route('favorites.toggle', $kost) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="home-icon-button {{ $isFavorited ? 'home-icon-button-active' : '' }}" aria-label="{{ $isFavorited ? 'Hapus dari wishlist' : 'Tambah ke wishlist' }}">
                                                    {{ $isFavorited ? 'Simpan' : 'Wishlist' }}
                                                </button>
                                            </form>
                                        @endif
                                    @endauth

                                    <a href="{{ route('kosts.show', $kost) }}" class="solid-button">Detail</a>
                                </div>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>

            <div class="mt-10">
                {{ $kosts->links() }}
            </div>
        @else
            <div class="empty-state">
                <h3 class="font-display text-2xl font-semibold">Belum ada kost yang cocok</h3>
                <p class="mx-auto mt-3 max-w-md text-[var(--muted)]">Coba lokasi lain atau naikkan batas budget.</p>
                <a href="{{ route('home') }}" class="solid-button mt-6 inline-flex">Lihat Semua Kost</a>
            </div>
        @endif
    </section>
@endsection
