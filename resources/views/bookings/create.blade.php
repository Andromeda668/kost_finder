@extends('layouts.app')

@section('title', 'Booking Kost - KostFinder')

@section('content')
    @php
        $image = $kost->display_image_url;
        $dailyPrice = $kost->priceFor('harian');
        $monthlyPrice = $kost->priceFor('bulanan');
        $defaultType = request()->query('tipe_sewa', $kost->primary_rental_period);
        $defaultType = in_array($defaultType, ['harian', 'bulanan'], true) ? $defaultType : $kost->primary_rental_period;
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

                <div class="md:col-span-2">
                    <p class="text-sm font-semibold text-[var(--ink)]">Tipe sewa</p>
                    <p class="mt-1 text-sm text-[var(--muted)]">Pilih harian atau bulanan sesuai kebutuhan.</p>

                    <div class="mt-3 flex flex-wrap gap-3" data-rental-type>
                        @if ($dailyPrice)
                            <label class="inline-flex cursor-pointer items-center gap-2 rounded-[24px] border border-[var(--line)] bg-white px-5 py-3 text-sm font-semibold text-[var(--ink)]">
                                <input type="radio" name="tipe_sewa" value="harian" class="sr-only" {{ old('tipe_sewa', $defaultType) === 'harian' ? 'checked' : '' }} data-rental-option>
                                Harian
                            </label>
                        @endif
                        @if ($monthlyPrice)
                            <label class="inline-flex cursor-pointer items-center gap-2 rounded-[24px] border border-[var(--line)] bg-white px-5 py-3 text-sm font-semibold text-[var(--ink)]">
                                <input type="radio" name="tipe_sewa" value="bulanan" class="sr-only" {{ old('tipe_sewa', $defaultType) === 'bulanan' ? 'checked' : '' }} data-rental-option>
                                Bulanan
                            </label>
                        @endif

                        @if (! $dailyPrice && ! $monthlyPrice)
                            <input type="hidden" name="tipe_sewa" value="bulanan">
                            <span class="text-sm text-[var(--muted)]">Tipe sewa belum tersedia.</span>
                        @endif
                    </div>

                    @error('tipe_sewa')
                        <small class="field-error mt-2 block">{{ $message }}</small>
                    @enderror
                </div>

                <label class="field-group">
                    <span>Tanggal Masuk</span>
                    <input type="date" name="tanggal_masuk" value="{{ old('tanggal_masuk') }}" class="field-input" min="{{ now()->toDateString() }}" required>
                    @error('tanggal_masuk')
                        <small class="field-error">{{ $message }}</small>
                    @enderror
                </label>

                <label class="field-group">
                    <span>Durasi Sewa</span>
                    <input
                        type="number"
                        name="durasi"
                        value="{{ old('durasi', 1) }}"
                        min="1"
                        max="365"
                        class="field-input"
                        required
                        data-duration-input
                    >
                    <small class="text-xs text-[var(--muted)]" data-duration-hint>Bulanan maksimal 24 bulan.</small>
                    @error('durasi')
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
                        <span class="text-sm font-semibold text-[var(--ink)]">
                            @if ($monthlyPrice)
                                {{ $kost->currency_symbol }} {{ $kost->formatMoney($monthlyPrice) }} / bulan
                            @elseif ($dailyPrice)
                                {{ $kost->currency_symbol }} {{ $kost->formatMoney($dailyPrice) }} / hari
                            @else
                                -
                            @endif
                        </span>
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
