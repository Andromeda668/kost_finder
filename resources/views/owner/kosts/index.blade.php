@extends('layouts.app')

@section('title', 'Dashboard Owner - KostFinder')

@section('content')
    <section class="page-heading" data-reveal>
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <p class="eyebrow">Dashboard Owner</p>
                <h1 class="font-display text-4xl font-semibold">Kelola kost dan booking masuk</h1>
                <p class="mt-3 max-w-3xl text-base leading-8 text-[var(--muted)]">Semua listing kost, ketersediaan kamar, dan permintaan booking tampil dalam satu dashboard bergaya clean seperti panel operator platform booking.</p>
            </div>

            <a href="{{ route('owner.kosts.create') }}" class="solid-button">Tambah Kost</a>
        </div>
    </section>

    <section class="dashboard-grid mt-8">
        <article class="dashboard-table-card" data-reveal>
            <div class="mb-5 flex items-center justify-between gap-4">
                <div>
                    <p class="eyebrow">Listing Kost</p>
                    <h2 class="font-display text-3xl font-semibold">Daftar Kost</h2>
                </div>
            </div>

            <div class="table-shell">
                <table class="dashboard-table">
                    <thead>
                        <tr>
                            <th>Kost</th>
                            <th>Lokasi</th>
                            <th>Harga</th>
                            <th>Kamar</th>
                            <th>Status</th>
                            <th class="text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($kosts as $kost)
                            <tr>
                                <td>
                                    <div class="table-kost-title">
                                        <span class="font-semibold text-[var(--ink)]">{{ $kost->nama_kost }}</span>
                                        <span class="text-xs text-[var(--muted)]">{{ \Illuminate\Support\Str::limit($kost->alamat, 50) }}</span>
                                    </div>
                                </td>
                                <td>{{ $kost->lokasi }}</td>
                                <td>Rp {{ number_format($kost->harga, 0, ',', '.') }}</td>
                                <td>{{ $kost->room?->kamar_tersedia ?? 0 }} / {{ $kost->room?->total_kamar ?? 0 }}</td>
                                <td><span class="{{ $kost->availability_badge_class }}">{{ $kost->availability_status }}</span></td>
                                <td>
                                    <div class="table-actions justify-end">
                                        <a href="{{ route('kosts.show', $kost) }}" class="table-link">Lihat</a>
                                        <a href="{{ route('owner.kosts.edit', $kost) }}" class="table-link">Edit</a>
                                        <form action="{{ route('owner.kosts.destroy', $kost) }}" method="POST" onsubmit="return confirm('Hapus kost ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="table-link table-link-danger">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-[var(--muted)]">Belum ada kost ditambahkan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-6">
                {{ $kosts->links() }}
            </div>
        </article>

        <article class="dashboard-table-card" data-reveal>
            <div class="mb-5">
                <p class="eyebrow">Booking Masuk</p>
                <h2 class="font-display text-3xl font-semibold">Permintaan Booking</h2>
            </div>

            <div class="table-shell">
                <table class="dashboard-table">
                    <thead>
                        <tr>
                            <th>Pemesan</th>
                            <th>Kost</th>
                            <th>Masuk</th>
                            <th>Durasi</th>
                            <th>Status</th>
                            <th class="text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($bookings as $booking)
                            <tr>
                                <td>
                                    <div class="table-kost-title">
                                        <span class="font-semibold text-[var(--ink)]">{{ $booking->user->name }}</span>
                                        <span class="text-xs text-[var(--muted)]">{{ $booking->user->email }}</span>
                                    </div>
                                </td>
                                <td>{{ $booking->kost->nama_kost }}</td>
                                <td>{{ $booking->tanggal_masuk->translatedFormat('d M Y') }}</td>
                                <td>{{ $booking->durasi_bulan }} bulan</td>
                                <td><span class="{{ $booking->status_badge_class }}">{{ ucfirst($booking->status) }}</span></td>
                                <td>
                                    @if ($booking->status === \App\Models\Booking::STATUS_PENDING)
                                        <div class="table-actions justify-end">
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
                                        </div>
                                    @else
                                        <div class="text-right text-sm text-[var(--muted)]">Sudah diproses</div>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-[var(--muted)]">Belum ada booking masuk.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-6">
                {{ $bookings->links() }}
            </div>
        </article>
    </section>
@endsection
