<?php

namespace App\Http\Controllers;

use App\Models\Kost;
use Illuminate\View\View;

class KostController extends Controller
{
    public function show(Kost $kost): View
    {
        $kost->load(['owner.ownerContact', 'images', 'primaryImage', 'room']);

        if (auth()->check() && ! auth()->user()->isOwner()) {
            $kost->loadExists([
                'favorites as is_favorited' => fn ($query) => $query->where('user_id', auth()->id()),
            ]);
        }

        return view('kosts.show', [
            'kost' => $kost,
        ]);
    }
}
