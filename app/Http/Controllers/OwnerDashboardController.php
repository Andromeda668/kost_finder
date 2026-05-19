<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OwnerDashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $totalKosts = $user->kosts()->count();

        $roomSummary = Room::query()
            ->whereHas('kost', fn ($query) => $query->where('user_id', $user->id))
            ->selectRaw('SUM(total_kamar) as total_kamar, SUM(kamar_tersedia) as kamar_tersedia')
            ->first();

        $pendingBookings = Booking::query()
            ->whereHas('kost', fn ($query) => $query->where('user_id', $user->id))
            ->where('status', Booking::STATUS_PENDING)
            ->count();

        $pendingBookingItems = Booking::query()
            ->with(['user', 'kost.primaryImage', 'kost.room'])
            ->whereHas('kost', fn ($query) => $query->where('user_id', $user->id))
            ->where('status', Booking::STATUS_PENDING)
            ->latest('created_at')
            ->take(5)
            ->get();

        $recentBookingItems = Booking::query()
            ->with(['user', 'kost.primaryImage', 'kost.room'])
            ->whereHas('kost', fn ($query) => $query->where('user_id', $user->id))
            ->whereIn('status', [Booking::STATUS_ACCEPTED, Booking::STATUS_REJECTED])
            ->latest('created_at')
            ->take(5)
            ->get();

        $kosts = $user->kosts()
            ->with(['primaryImage', 'room'])
            ->latest()
            ->take(5)
            ->get();

        return view('owner.dashboard.index', [
            'kosts' => $kosts,
            'pendingBookingItems' => $pendingBookingItems,
            'bookings' => $recentBookingItems,
            'totalKosts' => $totalKosts,
            'availableRooms' => $roomSummary->kamar_tersedia ?? 0,
            'totalRooms' => $roomSummary->total_kamar ?? 0,
            'pendingBookings' => $pendingBookings,
        ]);
    }
}
