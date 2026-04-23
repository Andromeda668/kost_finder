<?php

namespace App\Http\Controllers;

use App\Models\Kost;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FavoriteController extends Controller
{
    public function index(Request $request): View
    {
        abort_if($request->user()->isOwner(), 403, 'Owner tidak memiliki halaman wishlist.');

        $favorites = $request->user()
            ->favoriteKosts()
            ->with(['primaryImage', 'room', 'owner.ownerContact'])
            ->latest('kosts.created_at')
            ->paginate(10);

        return view('favorites.index', [
            'favorites' => $favorites,
        ]);
    }

    public function toggle(Request $request, Kost $kost): RedirectResponse
    {
        abort_if($request->user()->isOwner(), 403, 'Owner tidak dapat menambahkan wishlist.');

        $favorites = $request->user()->favoriteKosts();
        $exists = $favorites->where('kost_id', $kost->id)->exists();

        if ($exists) {
            $favorites->detach($kost->id);
            return back()->with('status', 'Kost dihapus dari wishlist.');
        }

        $favorites->attach($kost->id);

        return back()->with('status', 'Kost ditambahkan ke wishlist.');
    }
}
