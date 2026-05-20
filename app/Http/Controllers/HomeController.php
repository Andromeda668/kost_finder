<?php

namespace App\Http\Controllers;

use App\Models\Kost;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(Request $request): View|RedirectResponse
    {
        if ($request->user()?->isOwner()) {
            return redirect()->route('owner.dashboard');
        }
        $allowedSorts = ['latest', 'price_asc', 'price_desc', 'availability'];
        $search = trim((string) $request->string('search'));
        $quickLocation = trim((string) $request->string('quick_location'));
        $activeSearch = $search ?: $quickLocation;
        $maxPrice = preg_replace('/\D+/', '', (string) $request->input('max_price'));
        $maxPrice = $maxPrice !== '' ? (int) $maxPrice : null;
        $sort = (string) $request->string('sort', 'latest');
        $sort = in_array($sort, $allowedSorts, true) ? $sort : 'latest';

        $kostQuery = Kost::query()
            ->with(['owner.ownerContact', 'primaryImage', 'room'])
            ->search($activeSearch)
            ->maxPrice($maxPrice)
            ->when($sort === 'price_asc', fn ($query) => $query->orderBy('harga'))
            ->when($sort === 'price_desc', fn ($query) => $query->orderByDesc('harga'))
            ->when($sort === 'availability', fn ($query) => $query
                ->leftJoin('rooms', 'rooms.kost_id', '=', 'kosts.id')
                ->orderByDesc('rooms.kamar_tersedia')
                ->orderByDesc('kosts.created_at')
                ->select('kosts.*'))
            ->when($sort === 'latest', fn ($query) => $query->latest())
            ->when($request->user() && ! $request->user()->isOwner(), fn ($query) => $query->withExists([
                'favorites as is_favorited' => fn ($favoriteQuery) => $favoriteQuery->where('user_id', $request->user()->id),
            ]));

        $kosts = $kostQuery
            ->paginate(9)
            ->withQueryString();

        $quickLocations = Kost::query()
            ->select('lokasi')
            ->whereNotNull('lokasi')
            ->where('lokasi', '!=', '')
            ->distinct()
            ->orderBy('lokasi')
            ->limit(8)
            ->pluck('lokasi');

        return view('home', [
            'kosts' => $kosts,
            'search' => $activeSearch,
            'maxPrice' => $maxPrice,
            'sort' => $sort,
            'quickLocations' => $quickLocations,
            'activeQuickLocation' => $quickLocation,
        ]);
    }
}
