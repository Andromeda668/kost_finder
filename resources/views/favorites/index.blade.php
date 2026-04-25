@extends('layouts.app')

@section('title', 'Wishlist - KostFinder')

@section('content')
    <section class="page-heading" data-reveal>
        <p class="eyebrow">Wishlist</p>
        <h1 class="font-display text-4xl font-semibold">Kost Favorit Anda</h1>
        <p class="mt-3 max-w-2xl text-base leading-8 text-[var(--muted)]">Simpan shortlist kost yang menarik, lalu bandingkan harga, status kamar, dan akses kontak owner sebelum booking.</p>
    </section>

    <section class="mt-8 space-y-5">
        @forelse ($favorites as $kost)
            @php
                $image = $kost->primaryImage?->image_url;
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
                    <p class="search-result-location">{{ $kost->lokasi }}</p>
                    <h2 class="font-display text-3xl font-semibold">{{ $kost->nama_kost }}</h2>
                    <div class="mt-4 flex flex-wrap items-center gap-3">
                        <span class="{{ $kost->availability_badge_class }}">{{ $kost->availability_status }}</span>
                        <span class="mini-meta">{{ $kost->room?->kamar_tersedia ?? 0 }} / {{ $kost->room?->total_kamar ?? 0 }} kamar tersedia</span>
                    </div>
                    <p class="mt-4 line-clamp-2 text-sm leading-7 text-[var(--muted)]">{{ $kost->deskripsi }}</p>
                </div>

                <div class="search-result-side">
                    <p class="search-result-price">Rp {{ number_format($kost->harga, 0, ',', '.') }}</p>
                    <div class="space-y-3">
                        <a href="{{ route('kosts.show', $kost) }}" class="solid-button w-full text-center">Lihat Detail</a>
                        <form action="{{ route('favorites.toggle', $kost) }}" method="POST">
                            @csrf
                            <button type="submit" class="ghost-button w-full">Hapus dari Wishlist</button>
                        </form>
                    </div>
                </div>
            </article>
        @empty
            <div class="empty-state">
                <h2 class="font-display text-2xl font-semibold">Wishlist masih kosong</h2>
                <p class="mt-3 text-[var(--muted)]">Tambahkan kost dari halaman pencarian agar Anda bisa membandingkannya nanti.</p>
            </div>
        @endforelse
    </section>

    <div class="mt-8">
        {{ $favorites->links() }}
    </div>
@endsection
