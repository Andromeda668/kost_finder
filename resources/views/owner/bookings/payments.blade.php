@extends('layouts.app')

@section('title', 'Riwayat Pembayaran - KostFinder')

@section('content')
    @php
        $active = $status ?: 'all';
        $tabs = [
            'all' => ['label' => 'Semua', 'count' => $counts['all'] ?? 0],
            \App\Models\Booking::PAYMENT_PAID => ['label' => 'Sudah bayar', 'count' => $counts['paid'] ?? 0],
            \App\Models\Booking::PAYMENT_UNPAID => ['label' => 'Belum bayar', 'count' => $counts['unpaid'] ?? 0],
        ];
    @endphp

    <section class="page-heading" data-reveal>
        <p class="eyebrow">Riwayat Pembayaran</p>
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <h1 class="font-display text-4xl font-semibold sm:text-5xl">Riwayat pembayaran</h1>
                <p class="mt-3 max-w-3xl text-base leading-8 text-[var(--muted)]">Pantau bukti pembayaran dan status transfer yang masuk untuk kost Anda.</p>
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('owner.bookings.index') }}" class="ghost-button">Booking Pending</a>
                <a href="{{ route('owner.history') }}" class="ghost-button">Riwayat Booking</a>
            </div>
        </div>
    </section>

    <section class="mt-8 content-card" data-reveal>
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div class="flex flex-wrap gap-2">
                @foreach ($tabs as $key => $tab)
                    @php
                        $isActive = ($active === $key) || ($active === 'all' && $key === 'all');
                        $query = $key === 'all' ? [] : ['payment_status' => $key];
                    @endphp
                    <a
                        href="{{ route('owner.bookings.payments', $query) }}"
                        class="table-link {{ $isActive ? 'ghost-button-active' : '' }}"
                    >
                        {{ $tab['label'] }} <span class="ml-1 text-xs text-[var(--muted)]">({{ $tab['count'] }})</span>
                    </a>
                @endforeach
            </div>
            <div class="text-sm text-[var(--muted)]">
                Menampilkan {{ $bookings->count() }} dari {{ $bookings->total() }} booking.
            </div>
        </div>

        <div class="mt-6 grid gap-4">
            @forelse ($bookings as $booking)
                @php
                    $image = $booking->kost->display_image_url;
                    $latestLog = $booking->paymentLogs->last();
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
                                    <span class="{{ $booking->payment_status_badge_class }}">{{ $booking->payment_status_label }}</span>
                                </div>
                                <p class="mt-1 text-sm text-[var(--muted)]">
                                    {{ $booking->kost->nama_kost }} • {{ $booking->durasi_label }}
                                </p>
                                <div class="mt-2 grid gap-1 text-xs text-[var(--muted)] sm:grid-cols-2">
                                    <p>Masuk: <span class="font-semibold text-[var(--ink)]">{{ $booking->tanggal_masuk->translatedFormat('d M Y') }}</span></p>
                                    <p>Metode: <span class="font-semibold text-[var(--ink)]">{{ $booking->payment_method_label }}</span></p>
                                    <p>Lokasi: <span class="font-semibold text-[var(--ink)]">{{ $booking->kost->lokasi }}</span></p>
                                </div>

                                @if ($booking->payment_proof_url)
                                    <a href="{{ $booking->payment_proof_url }}" target="_blank" rel="noopener noreferrer" class="mt-3 inline-flex text-xs font-semibold text-[var(--primary-deep)]">Lihat bukti pembayaran</a>
                                @else
                                    <p class="mt-3 text-xs text-[var(--muted)]">Belum ada bukti pembayaran.</p>
                                @endif

                                @if ($latestLog)
                                    <div class="mt-4 rounded-[20px] border border-[var(--line)] bg-[var(--surface-muted)] p-4 text-sm">
                                        <p class="font-semibold text-[var(--ink)]">Log terakhir</p>
                                        <p class="mt-2 text-[var(--muted)]">{{ ucfirst(str_replace('_', ' ', $latestLog->type)) }} pada {{ $latestLog->created_at->translatedFormat('d M Y H:i') }}</p>
                                        @if (! empty($latestLog->data['payment_status']))
                                            <p class="mt-1">Status: <span class="font-semibold text-[var(--ink)]">{{ $latestLog->data['payment_status'] }}</span></p>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="flex flex-wrap items-center justify-end gap-3">
                            <a href="{{ route('kosts.show', $booking->kost) }}" class="table-link">Lihat Kost</a>
                            <a href="{{ route('owner.bookings.review', $booking) }}" class="table-link">Review Pembayaran</a>
                        </div>
                    </div>
                </article>
            @empty
                <div class="rounded-[28px] border border-[var(--line)] bg-[var(--surface-muted)] p-6 text-sm text-[var(--muted)]">
                    Belum ada riwayat pembayaran untuk filter ini.
                </div>
            @endforelse
        </div>

        <div class="mt-8">
            {{ $bookings->links() }}
        </div>
    </section>
@endsection
