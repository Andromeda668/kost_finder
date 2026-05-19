<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OwnerBookingController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $bookings = Booking::query()
            ->with(['user', 'kost.primaryImage'])
            ->whereHas('kost', fn ($query) => $query->where('user_id', $user->id))
            ->where('status', Booking::STATUS_PENDING)
            ->latest('created_at')
            ->paginate(10)
            ->withQueryString();

        return view('owner.bookings.index', [
            'bookings' => $bookings,
        ]);
    }

    public function history(Request $request): View
    {
        $user = $request->user();

        $status = $request->string('status')->toString();
        $status = in_array($status, [Booking::STATUS_ACCEPTED, Booking::STATUS_REJECTED], true)
            ? $status
            : '';

        $baseQuery = Booking::query()
            ->with(['user', 'kost.primaryImage'])
            ->whereHas('kost', fn ($query) => $query->where('user_id', $user->id))
            ->whereIn('status', [Booking::STATUS_ACCEPTED, Booking::STATUS_REJECTED])
            ->latest('created_at');

        $counts = [
            'accepted' => (clone $baseQuery)->where('status', Booking::STATUS_ACCEPTED)->count(),
            'rejected' => (clone $baseQuery)->where('status', Booking::STATUS_REJECTED)->count(),
            'all' => $baseQuery->count(),
        ];

        $bookings = (clone $baseQuery)
            ->when($status, fn ($query) => $query->where('status', $status))
            ->paginate(10)
            ->withQueryString();

        return view('owner.bookings.history', [
            'bookings' => $bookings,
            'status' => $status,
            'counts' => $counts,
        ]);
    }

    public function payments(Request $request): View
    {
        $user = $request->user();

        $status = $request->string('payment_status')->toString();
        $status = in_array($status, [Booking::PAYMENT_PAID, Booking::PAYMENT_UNPAID], true)
            ? $status
            : '';

        $baseQuery = Booking::query()
            ->with(['user', 'kost.primaryImage', 'paymentLogs'])
            ->whereHas('kost', fn ($query) => $query->where('user_id', $user->id));

        $counts = [
            'paid' => (clone $baseQuery)->where('payment_status', Booking::PAYMENT_PAID)->count(),
            'unpaid' => (clone $baseQuery)->where('payment_status', Booking::PAYMENT_UNPAID)->count(),
            'all' => $baseQuery->count(),
        ];

        $bookings = (clone $baseQuery)
            ->when($status, fn ($query) => $query->where('payment_status', $status))
            ->latest('created_at')
            ->paginate(10)
            ->withQueryString();

        return view('owner.bookings.payments', [
            'bookings' => $bookings,
            'status' => $status,
            'counts' => $counts,
        ]);
    }
    public function review(Request $request, Booking $booking): View
    {
        $this->authorizeOwner($request, $booking->kost);

        $booking->load(['user', 'kost.primaryImage', 'paymentLogs']);

        return view('owner.bookings.review', [
            'booking' => $booking,
        ]);
    }}
