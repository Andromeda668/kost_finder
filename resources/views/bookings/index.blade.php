@extends('layouts.app')

@section('title', 'Booking Saya - KostFinder')

@section('content')
    <section class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <p class="eyebrow">Riwayat Booking</p>
            <h1 class="font-display text-4xl font-semibold">Booking Saya</h1>
            <p class="mt-3 max-w-2xl text-base leading-8 text-[var(--muted)]">Pantau semua permintaan booking yang sudah Anda kirim, termasuk status pending, diterima, atau ditolak.</p>
        </div>
    </section>

    <section class="mt-8 grid gap-4">
        @forelse ($bookings as $booking)
            @php
                $image = $booking->kost->primaryImage?->image_path ? asset('storage/'.$booking->kost->primaryImage->image_path) : null;
                $ownerContact = $booking->kost->owner?->ownerContact;
                $ownerEmail = $ownerContact?->email ?? $booking->kost->owner?->email;
            @endphp

            <article class="owner-card">
                <div class="grid gap-5 lg:grid-cols-[180px_1fr_auto] lg:items-center">
                    @if ($image)
                        <img src="{{ $image }}" alt="{{ $booking->kost->nama_kost }}" class="h-40 w-full rounded-[24px] object-cover lg:h-32">
                    @else
                        <div class="placeholder-cover h-40 w-full rounded-[24px] lg:h-32">
                            <span>Belum ada foto</span>
                        </div>
                    @endif

                    <div>
                        <div class="flex flex-wrap items-center gap-3">
                            <h2 class="font-display text-2xl font-semibold">{{ $booking->kost->nama_kost }}</h2>
                            <span class="{{ $booking->status_badge_class }}">{{ ucfirst($booking->status) }}</span>
                            <span class="{{ $booking->kost->availability_badge_class }}">{{ $booking->kost->availability_status }}</span>
                        </div>
                        <p class="mt-3 text-sm text-[var(--muted)]">{{ $booking->kost->lokasi }} | {{ $booking->kost->alamat }}</p>
                        <p class="mt-2 text-sm text-[var(--muted)]">Tanggal masuk: {{ $booking->tanggal_masuk->translatedFormat('d F Y') }} | Durasi: {{ $booking->durasi_bulan }} bulan</p>
                        <p class="mt-2 text-sm text-[var(--olive)]">Kamar tersedia saat ini: {{ $booking->kost->room?->kamar_tersedia ?? 0 }} / {{ $booking->kost->room?->total_kamar ?? 0 }}</p>
                    </div>

                    <div class="flex flex-col gap-3">
                        <a href="{{ route('kosts.show', $booking->kost) }}" class="rounded-2xl border border-[var(--line)] px-5 py-3 text-center text-sm font-semibold text-[var(--ink)] transition hover:border-[var(--olive)] hover:bg-[var(--sage)]">
                            Lihat Kost
                        </a>
                        <a href="{{ $booking->kost->whatsapp_link }}" target="_blank" rel="noopener noreferrer" class="rounded-2xl bg-[#e6f6ea] px-5 py-3 text-center text-sm font-semibold text-[#237a43] transition hover:opacity-90">
                            Chat WA
                        </a>
                        <a href="mailto:{{ $ownerEmail }}" class="rounded-2xl bg-[#fff1e7] px-5 py-3 text-center text-sm font-semibold text-[var(--terracotta-deep)] transition hover:opacity-90">
                            Kirim Email
                        </a>
                    </div>
                </div>
            </article>
        @empty
            <div class="empty-state">
                <h2 class="font-display text-2xl font-semibold">Belum ada booking</h2>
                <p class="mt-3 text-[var(--muted)]">Saat Anda mengirim booking ke kost yang diinginkan, riwayatnya akan muncul di halaman ini.</p>
            </div>
        @endforelse
    </section>

    <div class="mt-8">
        {{ $bookings->links() }}
    </div>
@endsection
