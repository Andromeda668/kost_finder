@extends('layouts.app')

@section('title', 'KostFinder - Cari kost terbaik untuk hidup nyaman')

@section('content')
    @php
        $priceOptions = [
            '100000' => '< Rp 100.000',
            '500000' => '< Rp 500.000',
            '1000000' => '< Rp 1.000.000',
        ];
        $selectedMaxPrice = $maxPrice ? (string) preg_replace('/\D+/', '', (string) $maxPrice) : '';
    @endphp

    <section class="travel-hero" data-reveal>
        <div class="mb-10 grid gap-12 lg:grid-cols-[1.2fr_0.8fr] lg:items-start">
            <div>
                <p class="eyebrow">PLATFORM BOOKING KOST</p>
                <h1 class="font-display text-4xl font-semibold leading-tight text-[var(--ink)] sm:text-5xl lg:text-6xl">
                    Temukan kost impian, booking dalam hitungan menit
                </h1>
                <p class="mt-6 text-lg leading-8 text-[var(--muted)]">
                    Jelajahi ribuan pilihan kost dengan informasi lengkap, cek ketersediaan kamar secara real-time,
                    dan hubungi owner langsung dalam satu platform yang nyaman dipakai.
                </p>

                <div class="mt-8 grid gap-4 sm:grid-cols-3">
                    <div class="rounded-2xl border border-[var(--line)] bg-white p-4">
                        <p class="text-xs uppercase tracking-[0.2em] text-[var(--terracotta-deep)]">Proses Cepat</p>
                        <p class="mt-2 text-sm font-medium text-[var(--ink)]">Cari, lihat, lalu booking</p>
                        <p class="mt-1 text-xs text-[var(--muted)]">Hanya 3 langkah mudah</p>
                    </div>
                    <div class="rounded-2xl border border-[var(--line)] bg-white p-4">
                        <p class="text-xs uppercase tracking-[0.2em] text-[var(--accent)]">Data Lengkap</p>
                        <p class="mt-2 text-sm font-medium text-[var(--ink)]">Foto, harga, fasilitas</p>
                        <p class="mt-1 text-xs text-[var(--muted)]">Semua informasi tersedia</p>
                    </div>
                    <div class="rounded-2xl border border-[var(--line)] bg-white p-4">
                        <p class="text-xs uppercase tracking-[0.2em] text-[var(--ink)]">{{ $kosts->total() }} Kost</p>
                        <p class="mt-2 text-sm font-medium text-[var(--terracotta-deep)]">Tersedia</p>
                        <p class="mt-1 text-xs text-[var(--muted)]">Di berbagai kota</p>
                    </div>
                </div>
            </div>

            <div class="rounded-[32px] border border-[var(--line)] bg-gradient-to-br from-white via-white to-[var(--surface-muted)] p-8 shadow-[0_8px_30px_rgba(61,51,43,0.05)]">
                <p class="text-xs uppercase tracking-[0.2em] text-[var(--muted)]">Bagaimana cara kerjanya?</p>
                <div class="mt-6 space-y-6">
                    <div class="flex gap-4">
                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-[var(--terracotta)] text-sm font-bold text-white">1</div>
                        <div>
                            <p class="font-semibold text-[var(--ink)]">Cari Kost</p>
                            <p class="mt-1 text-sm text-[var(--muted)]">Pilih lokasi dan budget yang paling pas</p>
                        </div>
                    </div>
                    <div class="flex gap-4">
                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-[var(--accent)] text-sm font-bold text-white">2</div>
                        <div>
                            <p class="font-semibold text-[var(--ink)]">Lihat Detail</p>
                            <p class="mt-1 text-sm text-[var(--muted)]">Bandingkan foto, harga, dan fasilitas</p>
                        </div>
                    </div>
                    <div class="flex gap-4">
                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-[var(--terracotta)] text-sm font-bold text-white">3</div>
                        <div>
                            <p class="font-semibold text-[var(--ink)]">Booking Langsung</p>
                            <p class="mt-1 text-sm text-[var(--muted)]">Ajukan permintaan ke owner tanpa ribet</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <form action="{{ route('home') }}" method="GET" class="rounded-[32px] border border-[var(--line)] bg-white p-6 shadow-[0_8px_30px_rgba(61,51,43,0.05)] lg:p-8" data-search-form>
            <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-[0.2em] text-[var(--muted)]">Cari Kost Sekarang</p>
                    <p class="mt-2 text-sm text-[var(--muted)]">Filter lokasi, budget, dan urutan hasil dalam satu panel.</p>
                </div>
                <div class="hidden rounded-full bg-[var(--surface-muted)] px-4 py-2 text-sm font-semibold text-[var(--terracotta-deep)] md:inline-flex">
                    {{ $kosts->total() }} hasil aktif
                </div>
            </div>

            <div class="grid gap-4 lg:grid-cols-[2fr_1.5fr_1.5fr_auto]">
                <div>
                    <label class="field-group">
                        <span>Pilih Lokasi</span>
                        <select
                            name="search"
                            class="field-input field-input-plain"
                            data-location-select
                            data-placeholder="Semua kota/kabupaten"
                            data-selected="{{ $search }}"
                        >
                            <option value="">{{ $search ?: 'Semua Lokasi' }}</option>
                        </select>
                    </label>
                </div>

                <div>
                    <label class="field-group">
                        <span>Harga Maksimal (Rp)</span>
                        <select name="max_price" class="field-input field-input-plain">
                            <option value="">Semua Harga</option>
                            @foreach ($priceOptions as $value => $label)
                                <option value="{{ $value }}" @selected($selectedMaxPrice === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </label>
                </div>

                <div>
                    <label class="field-group">
                        <span>Urutkan Hasil</span>
                        <select name="sort" class="field-input field-input-plain">
                            <option value="latest" @selected($sort === 'latest')>Terbaru</option>
                            <option value="price_asc" @selected($sort === 'price_asc')>Harga Terendah</option>
                            <option value="price_desc" @selected($sort === 'price_desc')>Harga Tertinggi</option>
                            <option value="availability" @selected($sort === 'availability')>Kamar Tersedia</option>
                        </select>
                    </label>
                </div>

                <div class="flex items-end">
                    <button type="submit" class="solid-button w-full lg:w-auto">Cari Sekarang</button>
                </div>
            </div>
        </form>
    </section>

    <section class="mt-12" data-reveal>
        <div class="mb-8 flex flex-wrap items-center justify-between gap-4">
            <div>
                <p class="text-sm uppercase tracking-[0.2em] text-[var(--terracotta-deep)]">HASIL PENCARIAN</p>
                <h2 class="mt-2 font-display text-3xl font-semibold">{{ $search ? 'Kost di '.$search : 'Semua Pilihan Kost' }}</h2>
                <p class="mt-2 text-sm text-[var(--muted)]">{{ $kosts->total() }} kost ditemukan</p>
            </div>

            @if ($search || $maxPrice || $activeQuickLocation || $sort !== 'latest')
                <a href="{{ route('home') }}" class="inline-flex items-center gap-2 rounded-full border border-[var(--line)] px-4 py-2 text-sm font-semibold transition hover:bg-[var(--surface-muted)]">
                    Reset Filter
                </a>
            @endif
        </div>

        @if ($quickLocations->isNotEmpty())
            <div class="mb-6 flex flex-wrap gap-2">
                <span class="text-xs uppercase tracking-[0.2em] text-[var(--muted)]">Filter cepat:</span>
                @foreach ($quickLocations as $location)
                    <a
                        href="{{ route('home', ['quick_location' => $location, 'sort' => $sort]) }}"
                        class="inline-flex items-center rounded-full border px-3 py-1 text-sm font-medium transition {{ $activeQuickLocation === $location ? 'border-[var(--terracotta)] bg-[var(--terracotta)] text-white' : 'border-[var(--line)] hover:border-[var(--terracotta)]' }}"
                    >
                        {{ $location }}
                    </a>
                @endforeach
            </div>
        @endif

        @if ($kosts->count())
            <div class="space-y-5" data-loading-region>
                @foreach ($kosts as $kost)
                    @php
                        $image = $kost->primaryImage?->image_url;
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
                                    <p class="search-result-location">Lokasi: {{ $kost->lokasi }}</p>
                                    <h3 class="mt-2 font-display text-2xl font-semibold">{{ $kost->nama_kost }}</h3>
                                    <p class="mt-2 text-sm leading-6 text-[var(--muted)]">{{ \Illuminate\Support\Str::limit($kost->alamat, 100) }}</p>
                                </div>

                                @auth
                                    @if (! auth()->user()->isOwner())
                                        <form action="{{ route('favorites.toggle', $kost) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="inline-flex items-center gap-2 rounded-full border px-3 py-2 text-sm font-medium transition {{ $isFavorited ? 'border-[var(--terracotta)] bg-[var(--terracotta)] text-white' : 'border-[var(--line)] text-[var(--ink)] hover:border-[var(--terracotta)]' }}">
                                                {{ $isFavorited ? 'Tersimpan' : 'Tambah ke Wishlist' }}
                                            </button>
                                        </form>
                                    @endif
                                @endauth
                            </div>

                            <div class="mt-4 flex flex-wrap items-center gap-3">
                                <span class="{{ $kost->availability_badge_class }}">{{ $kost->availability_status }}</span>
                                <span class="inline-flex items-center gap-1 text-sm text-[var(--muted)]">
                                    {{ $kost->room?->kamar_tersedia ?? 0 }} / {{ $kost->room?->total_kamar ?? 0 }} kamar
                                </span>
                            </div>

                            <div class="mt-4 flex flex-wrap gap-2">
                                @foreach ($facilities as $facility)
                                    <span class="inline-flex items-center rounded-full bg-[var(--surface-muted)] px-3 py-1 text-sm text-[var(--muted)]">{{ trim($facility) }}</span>
                                @endforeach
                            </div>

                            <p class="mt-4 line-clamp-2 text-sm leading-6 text-[var(--muted)]">{{ $kost->deskripsi }}</p>
                        </div>

                        <div class="search-result-side">
                            <div>
                                <p class="text-xs uppercase tracking-[0.15em] text-[var(--muted)]">Harga</p>
                                <p class="mt-2 text-2xl font-semibold text-[var(--terracotta-deep)]">Rp {{ number_format($kost->harga, 0, ',', '.') }}</p>
                                <p class="text-xs text-[var(--muted)]">per bulan</p>
                            </div>

                            <div class="space-y-2">
                                <a href="{{ route('kosts.show', $kost) }}" class="solid-button block w-full text-center">
                                    Lihat Detail
                                </a>

                                @auth
                                    @if (! auth()->user()->isOwner())
                                        <a href="{{ route('bookings.create', $kost) }}" class="ghost-button block w-full text-center">
                                            Booking Sekarang
                                        </a>
                                    @endif
                                @endauth
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
                <div class="mb-4 text-6xl">...</div>
                <h3 class="font-display text-2xl font-semibold">Belum ada kost yang cocok</h3>
                <p class="mx-auto mt-3 max-w-md text-[var(--muted)]">Coba ubah lokasi, perluas budget, atau lihat semua kost untuk menemukan pilihan yang tepat.</p>
                <a href="{{ route('home') }}" class="solid-button mt-6 inline-flex">Lihat Semua Kost</a>
            </div>
        @endif
    </section>
@endsection
