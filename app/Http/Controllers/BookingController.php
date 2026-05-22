<?php

namespace App\Http\Controllers;

use App\Jobs\SendBookingPaymentNotification;
use App\Models\Booking;
use App\Models\Kost;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class BookingController extends Controller
{
    public function index(Request $request): View
    {
        $bookings = $request->user()
            ->bookings()
            ->with(['kost.primaryImage', 'kost.room', 'kost.owner.ownerContact'])
            ->latest('created_at')
            ->paginate(10);

        return view('bookings.index', [
            'bookings' => $bookings,
        ]);
    }

    public function create(Kost $kost): View
    {
        $kost->load(['owner.ownerContact', 'room', 'primaryImage']);

        abort_if(auth()->user()?->isOwner() && auth()->id() === $kost->user_id, 403, 'Owner tidak bisa membooking kost miliknya sendiri.');

        return view('bookings.create', [
            'kost' => $kost,
        ]);
    }

    public function store(Request $request, Kost $kost): RedirectResponse
    {
        abort_if($request->user()->isOwner() && $request->user()->id === $kost->user_id, 403, 'Owner tidak bisa membooking kost miliknya sendiri.');

        $validated = $request->validate([
            'tanggal_masuk' => ['required', 'date', 'after_or_equal:today'],
            'tipe_sewa' => ['required', 'in:harian,bulanan'],
            'durasi' => ['required', 'integer', 'min:1', 'max:365'],
            'payment_method' => ['required', 'string', 'in:'.implode(',', $kost->available_payment_methods)],
            'payment_proof' => ['nullable', 'image', 'max:2048'],
        ], [
            'tanggal_masuk.required' => 'Tanggal masuk wajib diisi.',
            'tanggal_masuk.after_or_equal' => 'Tanggal masuk tidak boleh sebelum hari ini.',
            'tipe_sewa.required' => 'Pilih tipe sewa.',
            'durasi.required' => 'Durasi sewa wajib diisi.',
            'durasi.min' => 'Durasi minimal 1.',
            'durasi.max' => 'Durasi terlalu besar.',
            'payment_method.required' => 'Pilih metode pembayaran.',
            'payment_method.in' => 'Metode pembayaran tidak tersedia untuk kost ini.',
            'payment_proof.image' => 'Bukti pembayaran harus berupa gambar.',
            'payment_proof.max' => 'Ukuran bukti pembayaran maksimal 2 MB.',
        ]);

        $paymentProof = $request->file('payment_proof');

        if ($validated['tipe_sewa'] === 'bulanan' && (int) $validated['durasi'] > 24) {
            return back()->withInput()->withErrors([
                'durasi' => 'Durasi bulanan maksimal 24 bulan.',
            ]);
        }

        if ($validated['tipe_sewa'] === 'harian' && ! $kost->priceFor('harian')) {
            return back()->withInput()->withErrors([
                'tipe_sewa' => 'Kost ini belum menyediakan harga harian.',
            ]);
        }

        if ($validated['tipe_sewa'] === 'bulanan' && ! $kost->priceFor('bulanan')) {
            return back()->withInput()->withErrors([
                'tipe_sewa' => 'Kost ini belum menyediakan harga bulanan.',
            ]);
        }

        if (($kost->room?->kamar_tersedia ?? 0) < 1) {
            return back()->withInput()->with('status', 'Maaf, kost ini sedang penuh dan belum bisa dibooking.');
        }

        $booking = Booking::create([
            'user_id' => $request->user()->id,
            'kost_id' => $kost->id,
            'tanggal_masuk' => $validated['tanggal_masuk'],
            'tipe_sewa' => $validated['tipe_sewa'],
            'durasi' => (int) $validated['durasi'],

            'durasi_bulan' => $validated['tipe_sewa'] === 'bulanan' ? (int) $validated['durasi'] : 0,
            'payment_method' => $validated['payment_method'],
            'payment_status' => Booking::PAYMENT_UNPAID,
            'payment_proof_data' => $paymentProof ? base64_encode(file_get_contents($paymentProof->getRealPath())) : null,
            'payment_proof_mime_type' => $paymentProof?->getMimeType(),
            'status' => Booking::STATUS_PENDING,
            'payment_method' => $validated['payment_method'],
            'created_at' => now(),
        ]);

        if ($paymentProof) {
            $booking->paymentLogs()->create([
                'user_id' => $request->user()->id,
                'type' => 'proof_uploaded',
                'data' => [
                    'mime' => $paymentProof->getMimeType(),
                    'size' => $paymentProof->getSize(),
                ],
                'created_at' => now(),
            ]);

            SendBookingPaymentNotification::dispatch($booking, 'proof_uploaded', $request->user()->id, [
                'mime' => $paymentProof->getMimeType(),
                'size' => $paymentProof->getSize(),
            ]);
        }

        return redirect()
            ->route('kosts.show', $kost)
            ->with('status', 'Booking berhasil dikirim. Owner akan meninjau permintaan Anda.');
    }

    public function updateStatus(Request $request, Booking $booking): RedirectResponse
    {
        abort_unless($booking->kost->user_id === $request->user()->id, 403, 'Anda tidak berhak mengelola booking ini.');

        $validated = $request->validate([
            'status' => ['required', 'in:diterima,ditolak'],
        ]);

        if ($booking->status !== Booking::STATUS_PENDING) {
            return back()->with('status', 'Booking ini sudah diproses sebelumnya.');
        }

        DB::transaction(function () use ($booking, $validated): void {
            $booking->loadMissing('kost.room');
            $room = $booking->kost->room;

            if ($validated['status'] === Booking::STATUS_ACCEPTED) {
                abort_if(! $room || $room->kamar_tersedia < 1, 422, 'Kamar tidak tersedia untuk booking ini.');
                $room->decrement('kamar_tersedia');
            }

            $booking->update([
                'status' => $validated['status'],
            ]);
        });

        return back()->with('status', 'Status booking berhasil diperbarui.');
    }

    public function updatePaymentStatus(Request $request, Booking $booking): RedirectResponse
    {
        abort_unless($booking->kost->user_id === $request->user()->id, 403, 'Anda tidak berhak mengelola booking ini.');

        $validated = $request->validate([
            'payment_status' => ['required', 'in:belum_bayar,sudah_bayar'],
        ]);

        $booking->update([
            'payment_status' => $validated['payment_status'],
        ]);

        $booking->paymentLogs()->create([
            'user_id' => $request->user()->id,
            'type' => 'status_changed',
            'data' => [
                'payment_status' => $validated['payment_status'],
            ],
            'created_at' => now(),
        ]);

        SendBookingPaymentNotification::dispatch($booking, 'status_changed', $request->user()->id, [
            'payment_status' => $validated['payment_status'],
        ]);

        return back()->with('status', 'Status pembayaran berhasil diperbarui.');
    }

    public function destroy(Request $request, Booking $booking): RedirectResponse
    {
        // Only the booking owner can cancel their booking
        abort_unless($booking->user_id === $request->user()->id, 403, 'Anda tidak berhak membatalkan booking ini.');

        if ($booking->status === Booking::STATUS_REJECTED) {
            return back()->with('status', 'Booking ini sudah dibatalkan.');
        }

        if ($booking->payment_status === Booking::PAYMENT_PAID) {
            return back()->with('status', 'Tidak dapat membatalkan booking yang sudah dibayar. Silakan hubungi owner.');
        }

        DB::transaction(function () use ($booking) {
            $booking->loadMissing('kost.room');

            // If booking was already accepted, free up the room
            if ($booking->status === Booking::STATUS_ACCEPTED) {
                $room = $booking->kost->room;
                if ($room) {
                    $room->increment('kamar_tersedia');
                }
            }

            $booking->update([
                'status' => Booking::STATUS_REJECTED,
            ]);

            $booking->paymentLogs()->create([
                'user_id' => $booking->user_id,
                'type' => 'cancelled_by_user',
                'data' => null,
                'created_at' => now(),
            ]);
        });

        return back()->with('status', 'Booking berhasil dibatalkan.');
    }
}
