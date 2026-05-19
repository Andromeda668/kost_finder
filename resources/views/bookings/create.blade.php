@extends('layouts.app')

@section('title', 'Booking Kost - KostFinder')

@section('content')
    @php
        $image = $kost->display_image_url;
        $dailyPrice = $kost->priceFor('harian');
        $monthlyPrice = $kost->priceFor('bulanan');
        $defaultType = request()->query('tipe_sewa', $kost->primary_rental_period);
        $defaultType = in_array($defaultType, ['harian', 'bulanan'], true) ? $defaultType : $kost->primary_rental_period;
        if ($defaultType === 'harian' && ! $dailyPrice) {
            $defaultType = 'bulanan';
        }
        if ($defaultType === 'bulanan' && ! $monthlyPrice) {
            $defaultType = 'harian';
        }
        $paymentMethods = $kost->available_payment_methods;
        $defaultPaymentMethod = old('payment_method', $paymentMethods[0] ?? 'cash');
    @endphp

    <section class="page-heading" data-reveal>
        <p class="eyebrow">Booking</p>
        <h1 class="font-display text-4xl font-semibold">Konfirmasi booking untuk {{ $kost->nama_kost }}</h1>
        <p class="mt-3 max-w-2xl text-base leading-8 text-[var(--muted)]">Pilih tanggal masuk dan durasi sewa. Owner akan meninjau permintaan ini sebelum mengonfirmasi ketersediaan kamar.</p>
    </section>

    <div class="detail-layout mt-8">
        <section class="content-card" data-reveal>
            <form action="{{ route('bookings.store', $kost) }}" method="POST" enctype="multipart/form-data" class="grid gap-5 md:grid-cols-2">
                @csrf

                <div class="md:col-span-2">
                    <p class="text-sm font-semibold text-[var(--ink)]">Tipe sewa</p>
                    <p class="mt-1 text-sm text-[var(--muted)]">Pilih harian atau bulanan sesuai kebutuhan.</p>

                    <div class="mt-3 grid gap-3 sm:grid-cols-2" data-rental-type>
                        @if ($dailyPrice)
                            <label class="payment-choice">
                                <input type="radio" name="tipe_sewa" value="harian" class="h-4 w-4 accent-[var(--primary)]" {{ old('tipe_sewa', $defaultType) === 'harian' ? 'checked' : '' }} data-rental-option>
                                <span>
                                    <span class="block font-semibold">Harian</span>
                                    <span class="block text-xs text-[var(--muted)]">{{ $kost->currency_symbol }} {{ $kost->formatMoney($dailyPrice) }} / hari</span>
                                </span>
                            </label>
                        @endif
                        @if ($monthlyPrice)
                            <label class="payment-choice">
                                <input type="radio" name="tipe_sewa" value="bulanan" class="h-4 w-4 accent-[var(--primary)]" {{ old('tipe_sewa', $defaultType) === 'bulanan' ? 'checked' : '' }} data-rental-option>
                                <span>
                                    <span class="block font-semibold">Bulanan</span>
                                    <span class="block text-xs text-[var(--muted)]">{{ $kost->currency_symbol }} {{ $kost->formatMoney($monthlyPrice) }} / bulan</span>
                                </span>
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

                <div class="md:col-span-2">
                    <p class="text-sm font-semibold text-[var(--ink)]">Metode pembayaran</p>
                    <p class="mt-1 text-sm text-[var(--muted)]">Pilih salah satu metode yang disediakan owner.</p>

                    <div class="mt-3 grid gap-3 sm:grid-cols-2">
                        @foreach ($paymentMethods as $method)
                            <label class="payment-choice">
                                <input
                                    type="radio"
                                    name="payment_method"
                                    value="{{ $method }}"
                                    class="h-4 w-4 accent-[var(--primary)]"
                                    {{ $defaultPaymentMethod === $method ? 'checked' : '' }}
                                    required
                                    data-payment-option
                                >
                                <span>{{ \App\Models\Kost::paymentMethodLabel($method) }}</span>
                            </label>
                        @endforeach
                    </div>

                    @error('payment_method')
                        <small class="field-error mt-2 block">{{ $message }}</small>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <p class="text-sm font-semibold text-[var(--ink)]">Detail pembayaran</p>
                    <div class="mt-3 grid gap-3">
                        @foreach ($paymentMethods as $method)
                            @php
                                $detail = $kost->paymentDetailFor($method);
                                $isQris = $method === 'qris';
                                $isCash = $method === 'cash';
                            @endphp
                            <div class="payment-detail-panel {{ $defaultPaymentMethod === $method ? '' : 'hidden' }}" data-payment-detail="{{ $method }}">
                                <div class="flex flex-wrap items-center justify-between gap-3">
                                    <p class="text-sm font-semibold text-[var(--ink)]">{{ \App\Models\Kost::paymentMethodLabel($method) }}</p>
                                    @if (! $isCash && ! $isQris && ! empty($detail['account_number']))
                                        <button type="button" class="table-link" data-copy-text="{{ $detail['account_number'] }}">Salin nomor</button>
                                    @endif
                                </div>

                                @if (! $isCash && ! $isQris)
                                    <div class="mt-3 grid gap-2 text-sm text-[var(--muted)] sm:grid-cols-2">
                                        <p>Nama penerima: <span class="font-semibold text-[var(--ink)]">{{ $detail['account_name'] ?: '-' }}</span></p>
                                        <p>Nomor tujuan: <span class="font-semibold text-[var(--ink)]">{{ $detail['account_number'] ?: '-' }}</span></p>
                                    </div>
                                @endif

                                @if ($isQris)
                                    @if ($kost->qris_image_url)
                                        <img src="{{ $kost->qris_image_url }}" alt="QRIS {{ $kost->nama_kost }}" class="mt-3 h-72 w-full rounded-[22px] object-contain bg-white">
                                    @else
                                        <p class="mt-3 text-sm text-[var(--muted)]">Owner belum mengunggah gambar QRIS.</p>
                                    @endif
                                @endif

                                @if (! empty($detail['instructions']))
                                    <p class="mt-3 text-sm leading-6 text-[var(--muted)]">{{ $detail['instructions'] }}</p>
                                @elseif ($isCash)
                                    <p class="mt-3 text-sm leading-6 text-[var(--muted)]">Bayar langsung ke owner sesuai kesepakatan saat check-in.</p>
                                @else
                                    <p class="mt-3 text-sm leading-6 text-[var(--muted)]">Lakukan pembayaran ke detail di atas, lalu upload bukti pembayaran jika sudah tersedia.</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>

                <label class="field-group md:col-span-2">
                    <span>Bukti pembayaran (opsional)</span>
                    <input type="file" name="payment_proof" accept="image/*" class="field-input">
                    <small class="text-xs text-[var(--muted)]">Upload screenshot transfer/QRIS agar owner bisa verifikasi lebih cepat.</small>
                    @error('payment_proof')
                        <small class="field-error">{{ $message }}</small>
                    @enderror
                </label>

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
                    <div class="flex items-start justify-between gap-3">
                        <span class="text-sm text-[var(--muted)]">Pembayaran</span>
                        <span class="text-right text-sm font-semibold text-[var(--ink)]">{{ implode(', ', $kost->payment_method_labels) }}</span>
                    </div>
                </div>
            </div>
        </aside>
    </div>

    <script>
        (function () {
            const options = Array.from(document.querySelectorAll('[data-payment-option]'));
            const panels = Array.from(document.querySelectorAll('[data-payment-detail]'));

            const sync = () => {
                const selected = options.find((option) => option.checked)?.value;
                panels.forEach((panel) => panel.classList.toggle('hidden', panel.dataset.paymentDetail !== selected));
            };

            options.forEach((option) => option.addEventListener('change', sync));
            sync();

            document.querySelectorAll('[data-copy-text]').forEach((button) => {
                button.addEventListener('click', async () => {
                    try {
                        await navigator.clipboard.writeText(button.dataset.copyText || '');
                        button.textContent = 'Tersalin';
                        window.setTimeout(() => button.textContent = 'Salin nomor', 1200);
                    } catch {
                        button.textContent = 'Gagal salin';
                    }
                });
            });
        })();
    </script>
@endsection
