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
}
