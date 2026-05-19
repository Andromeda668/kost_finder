@extends('layouts.app')

@section('title', 'Permintaan Booking - KostFinder')

@section('content')
    <section class="page-heading" data-reveal>
        <p class="eyebrow">Permintaan Booking</p>
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <h1 class="font-display text-4xl font-semibold sm:text-5xl">Permintaan booking</h1>
                <p class="mt-3 max-w-3xl text-base leading-8 text-[var(--muted)]">Tampilkan hanya booking pending yang perlu diproses.</p>
            </div>
            <a href="{{ route('owner.dashboard') }}" class="ghost-button">Kembali</a>
        </div>
    </section>

    <section class="mt-8 content-card" data-reveal>
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div class="text-sm text-[var(--muted)]">
                Menampilkan {{ $bookings->count() }} dari {{ $bookings->total() }} booking pending
            </div>
        </div>

        <div class="mt-6 grid gap-4">
            @forelse ($bookings as $booking)
                @php
                    $image = $booking->kost->display_image_url;
                @endphp

                <article class="rounded-[28px] border border-[var(--line)] bg-white p-5">
                    <div class="owner-card-row">
                        <div class="flex items-start gap-4">
                            @if ($image)
                                <img src="{{ $image }}" alt="{{ $booking->kost->nama_kost }}" class="owner-mini-cover">
                            @else
                                <div class="placeholder-cover owner-mini-cover">
                                    <span>Foto</span>
                                </div>
                            @endif

                            <div class="min-w-0">
                                <div class="flex flex-wrap items-center gap-2">
                                    <p class="text-sm font-semibold text-[var(--ink)]">{{ $booking->user->name }}</p>
                                    <span class="{{ $booking->status_badge_class }}">{{ ucfirst($booking->status) }}</span>
                                </div>
                                <p class="mt-1 text-sm text-[var(--muted)]">
                                    {{ $booking->kost->nama_kost }} • {{ $booking->durasi_label }}
                                </p>
                                <div class="mt-2 grid gap-1 text-xs text-[var(--muted)] sm:grid-cols-2">
                                    <p>Masuk: <span class="font-semibold text-[var(--ink)]">{{ $booking->tanggal_masuk->translatedFormat('d M Y') }}</span></p>
                                    <p>Lokasi: <span class="font-semibold text-[var(--ink)]">{{ $booking->kost->lokasi }}</span></p>
                                </div>
                            </div>
                        </div>

                        <div class="flex flex-wrap items-center justify-end gap-3">
                            <a href="{{ route('kosts.show', $booking->kost) }}" class="table-link">Lihat Kost</a>

                            @if ($booking->status === \App\Models\Booking::STATUS_PENDING)
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
                            @endif
                        </div>
                    </div>
                </article>
            @empty
                <div class="rounded-[28px] border border-[var(--line)] bg-[var(--surface-muted)] p-6 text-sm text-[var(--muted)]">
                    Belum ada booking masuk untuk filter ini.
                </div>
            @endforelse
        </div>

        <div class="mt-8">
            {{ $bookings->links() }}
        </div>
    </section>
@endsection

