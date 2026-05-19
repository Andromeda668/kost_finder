@extends('layouts.app')

@section('title', 'Review Pembayaran - KostFinder')

@section('content')
    <section class="page-heading" data-reveal>
        <p class="eyebrow">Review Pembayaran</p>
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <h1 class="font-display text-4xl font-semibold sm:text-5xl">Review pembayaran booking</h1>
                <p class="mt-3 max-w-3xl text-base leading-8 text-[var(--muted)]">Lihat bukti pembayaran, log audit, dan tandai status pembayaran sesuai transfer yang diterima.</p>
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('owner.bookings.payments') }}" class="ghost-button">Kembali ke Pembayaran</a>
                <a href="{{ route('owner.bookings.index') }}" class="ghost-button">Booking Pending</a>
            </div>
        </div>
    </section>

    <section class="mt-8 content-card" data-reveal>
        <div class="grid gap-6 lg:grid-cols-[1.5fr_1fr]">
            <div class="space-y-6">
                <article class="rounded-[28px] border border-[var(--line)] bg-white p-6">
                    <div class="flex flex-wrap items-center justify-between gap-4">
                        <div>
                            <p class="text-sm font-semibold text-[var(--ink)]">{{ $booking->kost->nama_kost }}</p>
                            <p class="mt-1 text-sm text-[var(--muted)]">Booking oleh {{ $booking->user->name }} | Masuk {{ $booking->tanggal_masuk->translatedFormat('d M Y') }}</p>
                        </div>
                        <span class="{{ $booking->payment_status_badge_class }}">{{ $booking->payment_status_label }}</span>
                    </div>

                    <div class="mt-5 grid gap-3 text-sm text-[var(--muted)]">
                        <p>Metode: <span class="font-semibold text-[var(--ink)]">{{ $booking->payment_method_label }}</span></p>
                        <p>Durasi: <span class="font-semibold text-[var(--ink)]">{{ $booking->durasi_label }}</span></p>
                        <p>Alamat kost: <span class="font-semibold text-[var(--ink)]">{{ $booking->kost->alamat }}, {{ $booking->kost->lokasi }}</span></p>
                    </div>

                    @if ($booking->payment_proof_url)
                        <div class="mt-6">
                            <p class="text-sm font-semibold text-[var(--ink)]">Bukti Pembayaran</p>
                            <img src="{{ $booking->payment_proof_url }}" alt="Bukti pembayaran" class="mt-4 w-full rounded-[22px] object-contain border border-[var(--line)]">
                        </div>
                    @else
                        <div class="mt-6 rounded-[22px] border border-[var(--line)] bg-[var(--surface-muted)] p-5 text-sm text-[var(--muted)]">
                            Belum ada bukti pembayaran diunggah oleh penyewa.
                        </div>
                    @endif
                </article>

                <article class="rounded-[28px] border border-[var(--line)] bg-white p-6">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <p class="text-sm font-semibold text-[var(--ink)]">Log Audit Pembayaran</p>
                        <span class="text-xs text-[var(--muted)]">{{ $booking->paymentLogs->count() }} entri</span>
                    </div>

                    <div class="mt-4 space-y-4">
                        @forelse ($booking->paymentLogs as $log)
                            <div class="rounded-[22px] border border-[var(--line)] bg-[var(--surface-muted)] p-4 text-sm">
                                <p class="font-semibold text-[var(--ink)]">{{ ucfirst(str_replace('_', ' ', $log->type)) }}</p>
                                <p class="mt-1 text-[var(--muted)]">{{ $log->created_at->translatedFormat('d M Y H:i') }}</p>
                                @if (! empty($log->data))
                                    <pre class="mt-3 overflow-x-auto rounded-lg bg-black/5 p-3 text-xs leading-5">{{ json_encode($log->data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                @endif
                            </div>
                        @empty
                            <p class="text-sm text-[var(--muted)]">Belum ada riwayat pembayaran dicatat.</p>
                        @endforelse
                    </div>
                </article>
            </div>

            <aside class="space-y-6">
                <article class="rounded-[28px] border border-[var(--line)] bg-white p-6">
                    <p class="text-sm font-semibold text-[var(--ink)]">Tindakan</p>
                    <div class="mt-4 grid gap-3">
                        <form action="{{ route('owner.bookings.payment-status', $booking) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="payment_status" value="{{ $booking->payment_status === \App\Models\Booking::PAYMENT_PAID ? \App\Models\Booking::PAYMENT_UNPAID : \App\Models\Booking::PAYMENT_PAID }}">
                            <button type="submit" class="solid-button w-full">
                                {{ $booking->payment_status === \App\Models\Booking::PAYMENT_PAID ? 'Tandai Belum Bayar' : 'Tandai Sudah Bayar' }}
                            </button>
                        </form>
                        <a href="{{ route('kosts.show', $booking->kost) }}" class="ghost-button w-full text-center">Lihat Kost</a>
                    </div>
                </article>
            </aside>
        </div>
    </section>
@endsection
