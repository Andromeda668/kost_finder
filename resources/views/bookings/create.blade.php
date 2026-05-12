@extends('layouts.app')

@section('title', 'Booking Kost - KostFinder')

@section('content')
    @php
        $image = $kost->display_image_url;
    @endphp

    <section class="page-heading" data-reveal>
        <p class="eyebrow">Booking</p>
        <h1 class="font-display text-4xl font-semibold">Konfirmasi booking untuk {{ $kost->nama_kost }}</h1>
        <p class="mt-3 max-w-2xl text-base leading-8 text-[var(--muted)]">Pilih tanggal masuk dan durasi sewa. Owner akan meninjau permintaan ini sebelum mengonfirmasi ketersediaan kamar.</p>
    </section>

    <div class="detail-layout mt-8">
        <section class="content-card" data-reveal>
            <form action="{{ route('bookings.store', $kost) }}" method="POST" class="grid gap-5 md:grid-cols-2">
                @csrf

                <label class="field-group">
                    <span>Tanggal Masuk</span>
                    <input type="date" name="tanggal_masuk" value="{{ old('tanggal_masuk') }}" class="field-input" min="{{ now()->toDateString() }}" required>
                    @error('tanggal_masuk')
                        <small class="field-error">{{ $message }}</small>
                    @enderror
                </label>

                <label class="field-group">
                    <span>Durasi Sewa (bulan)</span>
                    <input type="number" name="durasi_bulan" value="{{ old('durasi_bulan', 1) }}" min="1" max="24" class="field-input" required>
                    @error('durasi_bulan')
                        <small class="field-error">{{ $message }}</small>
                    @enderror
                </label>

                <div class="md:col-span-2 flex flex-wrap gap-3 pt-2">
                    <button type="submit" class="solid-button">
                        Konfirmasi Booking
                    </button>
                    <a href="{{ route('kosts.show', $kost) }}" class="ghost-button">
                        Kembali ke Detail
                    </a>
                </div>
            </form>
        </section>

        <aside class="sticky-panel">
            <div class="booking-sticky-card" data-reveal>
                @if ($image)
                    <img src="{{ $image }}" alt="{{ $kost->nama_kost }}" class="h-60 w-full rounded-[26px] object-cover">
                @else
                    <div class="placeholder-cover h-60 w-full rounded-[26px]">
                        <span>Belum ada foto</span>
                    </div>
                @endif

                <div class="mt-5">
                    <h2 class="font-display text-3xl font-semibold">{{ $kost->nama_kost }}</h2>
                    <p class="mt-2 text-sm text-[var(--muted)]">{{ $kost->alamat }}</p>
                </div>

                <div class="mt-5 grid gap-3 rounded-[24px] bg-[var(--surface-muted)] p-5">
                    <div class="flex items-center justify-between gap-3">
                        <span class="text-sm text-[var(--muted)]">Harga</span>
                        <span class="text-sm font-semibold text-[var(--ink)]">Rp {{ number_format($kost->harga, 0, ',', '.') }} / bulan</span>
                    </div>
                    <div class="flex items-center justify-between gap-3">
                        <span class="text-sm text-[var(--muted)]">Status kamar</span>
                        <span class="{{ $kost->availability_badge_class }}">{{ $kost->availability_status }}</span>
                    </div>
                    <div class="flex items-center justify-between gap-3">
                        <span class="text-sm text-[var(--muted)]">Ketersediaan</span>
                        <span class="text-sm font-semibold text-[var(--ink)]">{{ $kost->room?->kamar_tersedia ?? 0 }} / {{ $kost->room?->total_kamar ?? 0 }}</span>
                    </div>
                </div>
            </div>
        </aside>
    </div>
@endsection
