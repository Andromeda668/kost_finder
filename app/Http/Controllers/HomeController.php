<?php

namespace App\Http\Controllers;

use App\Models\Kost;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->string('search'));
        $maxPrice = $request->input('max_price');
        $sort = (string) $request->string('sort', 'latest');
        $quickLocation = trim((string) $request->string('quick_location'));

        $kostQuery = Kost::query()
            ->with(['owner.ownerContact', 'primaryImage', 'room'])
            ->search($search ?: $quickLocation)
            ->maxPrice($maxPrice)
            ->when($sort === 'price_asc', fn ($query) => $query->orderBy('harga'))
            ->when($sort === 'price_desc', fn ($query) => $query->orderByDesc('harga'))
            ->when($sort === 'availability', fn ($query) => $query->leftJoin('rooms', 'rooms.kost_id', '=', 'kosts.id')->orderByDesc('rooms.kamar_tersedia')->select('kosts.*'))
            ->when(! in_array($sort, ['price_asc', 'price_desc', 'availability'], true), fn ($query) => $query->latest())
            ->when($request->user() && ! $request->user()->isOwner(), fn ($query) => $query->withExists([
                'favorites as is_favorited' => fn ($favoriteQuery) => $favoriteQuery->where('user_id', $request->user()->id),
            ]));

        $kosts = $kostQuery
            ->paginate(9)
            ->withQueryString();

        $quickLocations = Kost::query()
            ->select('lokasi')
            ->distinct()
            ->orderBy('lokasi')
            ->limit(8)
            ->pluck('lokasi');

        return view('home', [
            'kosts' => $kosts,
            'search' => $search ?: $quickLocation,
            'maxPrice' => $maxPrice,
            'sort' => $sort,
            'quickLocations' => $quickLocations,
            'activeQuickLocation' => $quickLocation,
        ]);
    }
}
