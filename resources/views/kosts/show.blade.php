@extends('layouts.app')

@section('title', $kost->nama_kost.' - KostFinder')

@section('content')
    @php
        $images = $kost->images->count() ? $kost->images : collect([$kost->primaryImage])->filter();
        $gallery = $images->map(fn ($item) => $item?->image_url)->filter()->values();
        $heroImage = $gallery->first() ?: $kost->google_street_view_image_url;
        $contact = $kost->owner?->ownerContact;
        $ownerEmail = $contact?->email ?? $kost->owner?->email;
        $facilities = collect(preg_split('/\r\n|\r|\n/', $kost->fasilitas) ?: [])->filter()->values();
        $isFavorited = (bool) ($kost->is_favorited ?? false);
        $nearbyPlaces = $kost->nearbyPlaces?->sortBy('distance_km')->values() ?? collect();
        $monthlyPrice = $kost->priceFor('bulanan');
        $dailyPrice = $kost->priceFor('harian');
        $primaryPeriod = $kost->primary_rental_period;
        $primaryPrice = $kost->priceFor($primaryPeriod);
    @endphp

    <section class="page-heading" data-reveal>
        <p class="eyebrow">Detail Kost</p>
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <h1 class="font-display text-4xl font-semibold sm:text-5xl">{{ $kost->nama_kost }}</h1>
                <p class="mt-3 max-w-3xl text-base leading-8 text-[var(--muted)]">{{ $kost->alamat }}</p>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <span class="{{ $kost->availability_badge_class }}">{{ $kost->availability_status }}</span>
                <span class="mini-meta">{{ $kost->room?->kamar_tersedia ?? 0 }} / {{ $kost->room?->total_kamar ?? 0 }} kamar tersedia</span>
            </div>
        </div>
    </section>

    <div class="detail-layout mt-8">
        <section class="space-y-6">
            <article class="gallery-shell" data-reveal>
                <div class="gallery-main-wrap">
                    @if ($heroImage)
                        <img src="{{ $heroImage }}" alt="{{ $kost->nama_kost }}" class="gallery-main-image" data-gallery-main>
                    @else
                        <div class="placeholder-cover gallery-main-image">
                            <span>Belum ada foto</span>
                        </div>
                    @endif

                    @if ($gallery->count() > 1)
                        <button type="button" class="gallery-nav-button gallery-nav-prev" data-gallery-prev aria-label="Foto sebelumnya">&lsaquo;</button>
                        <button type="button" class="gallery-nav-button gallery-nav-next" data-gallery-next aria-label="Foto berikutnya">&rsaquo;</button>
                    @endif
                </div>

                @if ($gallery->count() > 1)
                    <div class="gallery-thumbs">
                        @foreach ($gallery as $image)
                            <button type="button" class="gallery-thumb {{ $loop->first ? 'gallery-thumb-active' : '' }}" data-gallery-thumb data-image="{{ $image }}">
                                <img src="{{ $image }}" alt="{{ $kost->nama_kost }} {{ $loop->iteration }}" class="h-full w-full object-cover">
                            </button>
                        @endforeach
                    </div>
                @endif
            </article>

            <article class="content-card" data-reveal>
                <h2 class="section-title">Tentang Kost</h2>
                <p class="mt-4 text-sm leading-8 text-[var(--muted)]">{{ $kost->deskripsi }}</p>
            </article>

            <article class="content-card" data-reveal>
                <h2 class="section-title">Fasilitas</h2>
                <div class="facility-grid">
                    @foreach ($facilities as $facility)
                        <div class="facility-tile">
                            <span class="facility-icon" aria-hidden="true">✓</span>
                            <span>{{ trim($facility) }}</span>
                        </div>
                    @endforeach
                </div>
            </article>

            @if ($nearbyPlaces->count())
                <article class="content-card" data-reveal>
                    <h2 class="section-title">Dekat dengan</h2>
                    <div class="mt-5 grid gap-3 sm:grid-cols-2">
                        @foreach ($nearbyPlaces as $place)
                            <div class="facility-tile">
                                <span class="facility-icon" aria-hidden="true">✓</span>
                                <div class="grid gap-0.5">
                                    <span class="font-medium text-[var(--ink)]">{{ $place->label }}</span>
                                    <span class="text-xs text-[var(--muted)]">{{ number_format((float) $place->distance_km, 1, ',', '.') }} km • {{ ucfirst($place->category) }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </article>
            @endif

            <article class="content-card" data-reveal>
                <div class="mb-4 flex items-center justify-between gap-4">
                    <h2 class="section-title">Lokasi di Peta</h2>
                    <a href="{{ $kost->google_maps_link }}" target="_blank" rel="noopener noreferrer" class="text-sm font-semibold text-[var(--terracotta-deep)]">Buka Google Maps</a>
                </div>
                <div class="map-embed-shell">
                    <iframe
                        src="{{ $kost->google_maps_embed_link }}"
                        loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade"
                        class="map-embed-frame"
                        title="Peta lokasi {{ $kost->nama_kost }}"
                    ></iframe>
                </div>
            </article>
        </section>

        <aside class="sticky-panel">
            <div class="booking-sticky-card" data-reveal>
                <p class="text-sm font-medium text-[var(--muted)]">Harga sewa</p>
                <div class="price-stack">
                    <p class="price-value">{{ $kost->currency_symbol }} {{ $kost->formatMoney($primaryPrice) }}</p>
                    <span class="price-period">/ {{ $primaryPeriod === 'harian' ? 'hari' : 'bulan' }}</span>
                </div>

                @if ($monthlyPrice || $dailyPrice)
                    <div class="mt-3 grid gap-2 text-sm text-[var(--muted)]">
                        @if ($dailyPrice)
                            <div class="flex items-center justify-between gap-3">
                                <span>Harian</span>
                                <span class="font-semibold text-[var(--ink)]">{{ $kost->currency_symbol }} {{ $kost->formatMoney($dailyPrice) }}</span>
                            </div>
                        @endif
                        @if ($monthlyPrice)
                            <div class="flex items-center justify-between gap-3">
                                <span>Bulanan</span>
                                <span class="font-semibold text-[var(--ink)]">{{ $kost->currency_symbol }} {{ $kost->formatMoney($monthlyPrice) }}</span>
                            </div>
                        @endif
                    </div>
                @endif

                <div class="mt-6 space-y-4 rounded-[28px] bg-[var(--surface-muted)] p-5">
                    <div class="flex items-center justify-between gap-3">
                        <span class="text-sm text-[var(--muted)]">Status kamar</span>
                        <span class="{{ $kost->availability_badge_class }}">{{ $kost->availability_status }}</span>
                    </div>
                    <div class="flex items-center justify-between gap-3">
                        <span class="text-sm text-[var(--muted)]">Ketersediaan</span>
                        <span class="text-sm font-semibold text-[var(--ink)]">{{ $kost->room?->kamar_tersedia ?? 0 }} dari {{ $kost->room?->total_kamar ?? 0 }}</span>
                    </div>
                    <div class="space-y-1">
                        <p class="text-sm text-[var(--muted)]">Owner</p>
                        <p class="font-semibold text-[var(--ink)]">{{ $kost->owner?->name }}</p>
                        <p class="text-sm text-[var(--muted)]">{{ $contact?->phone ?? '-' }}</p>
                        <p class="text-sm text-[var(--muted)]">{{ $ownerEmail }}</p>
                    </div>
                </div>

                <div class="mt-6 space-y-3">
                    @auth
                        @if (! auth()->user()->isOwner() || auth()->id() !== $kost->user_id)
                            <a href="{{ route('bookings.create', $kost) }}?tipe_sewa={{ $primaryPeriod }}" class="solid-button flex w-full items-center justify-center text-center">
                                Booking Sekarang
                            </a>
                        @endif

                        @if (! auth()->user()->isOwner())
                            <form action="{{ route('favorites.toggle', $kost) }}" method="POST">
                                @csrf
                                <button type="submit" class="ghost-button w-full {{ $isFavorited ? 'ghost-button-active' : '' }}">
                                    {{ $isFavorited ? 'Hapus dari Wishlist' : 'Simpan ke Wishlist' }}
                                </button>
                            </form>
                        @endif
                    @else
                        <a href="{{ route('login') }}" class="solid-button flex w-full items-center justify-center text-center">
                            Login untuk Booking
                        </a>
                    @endauth

                    <a href="{{ $kost->whatsapp_link }}" target="_blank" rel="noopener noreferrer" class="contact-action contact-action-wa">Chat WhatsApp</a>
                    <a href="mailto:{{ $ownerEmail }}" class="contact-action contact-action-email">Kirim Email</a>
                    <a href="{{ $kost->google_maps_link }}" target="_blank" rel="noopener noreferrer" class="contact-action contact-action-map">Lihat Maps</a>
                </div>
            </div>

            @if (auth()->check() && auth()->user()->isOwner() && auth()->id() === $kost->user_id)
                <div class="content-card mt-5" data-reveal>
                    <h2 class="section-title">Kelola Listing</h2>
                    <p class="mt-3 text-sm leading-7 text-[var(--muted)]">Anda sedang melihat detail kost milik sendiri. Perbarui listing kapan saja dari dashboard owner.</p>
                    <a href="{{ route('owner.kosts.edit', $kost) }}" class="ghost-button mt-5 block w-full text-center">Edit Kost</a>
                </div>
            @endif
        </aside>
    </div>
@endsection
