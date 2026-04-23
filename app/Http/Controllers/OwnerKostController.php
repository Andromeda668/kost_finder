<?php

namespace App\Http\Controllers;

use App\Models\Kost;
use App\Models\Booking;
use App\Models\Room;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class OwnerKostController extends Controller
{
    public function index(Request $request): View
    {
        $kosts = $request->user()
            ->kosts()
            ->with(['primaryImage', 'room'])
            ->latest()
            ->paginate(10);

        $bookings = Booking::query()
            ->with(['user', 'kost.primaryImage'])
            ->whereHas('kost', fn ($query) => $query->where('user_id', $request->user()->id))
            ->latest('created_at')
            ->paginate(8, ['*'], 'booking_page');

        return view('owner.kosts.index', [
            'kosts' => $kosts,
            'bookings' => $bookings,
        ]);
    }

    public function create(): View
    {
        return view('owner.kosts.create', [
            'kost' => (new Kost())->setRelation('room', new Room()),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateKost($request, true);

        $kost = $request->user()->kosts()->create($this->kostPayload($validated));

        $request->user()->ownerContact()->updateOrCreate(
            ['user_id' => $request->user()->id],
            [
                'phone' => $validated['phone'],
                'email' => $validated['contact_email'],
            ]
        );

        $kost->images()->create([
            'image_path' => $validated['image']->store('kosts', 'public'),
        ]);

        $kost->room()->create([
            'total_kamar' => (int) $validated['total_kamar'],
            'kamar_tersedia' => (int) $validated['kamar_tersedia'],
        ]);

        return redirect()
            ->route('owner.kosts.index')
            ->with('status', 'Data kost berhasil ditambahkan.');
    }

    public function edit(Request $request, Kost $kost): View
    {
        $this->authorizeOwner($request, $kost);
        $kost->load(['primaryImage', 'owner.ownerContact', 'room']);

        return view('owner.kosts.edit', [
            'kost' => $kost,
        ]);
    }

    public function update(Request $request, Kost $kost): RedirectResponse
    {
        $this->authorizeOwner($request, $kost);

        $validated = $this->validateKost($request, false);

        $kost->update($this->kostPayload($validated));

        $kost->room()->updateOrCreate(
            ['kost_id' => $kost->id],
            [
                'total_kamar' => (int) $validated['total_kamar'],
                'kamar_tersedia' => min((int) $validated['kamar_tersedia'], (int) $validated['total_kamar']),
            ]
        );

        $request->user()->ownerContact()->updateOrCreate(
            ['user_id' => $request->user()->id],
            [
                'phone' => $validated['phone'],
                'email' => $validated['contact_email'],
            ]
        );

        if (isset($validated['image'])) {
            $oldImage = $kost->primaryImage;

            if ($oldImage?->image_path) {
                Storage::disk('public')->delete($oldImage->image_path);
                $oldImage->delete();
            }

            $kost->images()->create([
                'image_path' => $validated['image']->store('kosts', 'public'),
            ]);
        }

        return redirect()
            ->route('owner.kosts.index')
            ->with('status', 'Data kost berhasil diperbarui.');
    }

    public function destroy(Request $request, Kost $kost): RedirectResponse
    {
        $this->authorizeOwner($request, $kost);

        foreach ($kost->images as $image) {
            if ($image->image_path) {
                Storage::disk('public')->delete($image->image_path);
            }
        }

        $kost->delete();

        return redirect()
            ->route('owner.kosts.index')
            ->with('status', 'Data kost berhasil dihapus.');
    }

    protected function validateKost(Request $request, bool $imageRequired): array
    {
        return $request->validate([
            'nama_kost' => ['required', 'string', 'max:255'],
            'alamat' => ['required', 'string'],
            'lokasi' => ['required', 'string', 'max:255'],
            'google_maps_link' => ['required', 'url', 'max:1000'],
            'harga' => ['required', 'numeric', 'min:0'],
            'deskripsi' => ['required', 'string'],
            'fasilitas' => ['required', 'string'],
            'total_kamar' => ['required', 'integer', 'min:1', 'max:500'],
            'kamar_tersedia' => ['required', 'integer', 'min:0', 'lte:total_kamar'],
            'phone' => ['required', 'string', 'max:30'],
            'contact_email' => ['required', 'email', 'max:255'],
            'image' => [$imageRequired ? 'required' : 'nullable', 'image', 'max:3072'],
        ], [
            'nama_kost.required' => 'Nama kost wajib diisi.',
            'alamat.required' => 'Alamat wajib diisi.',
            'lokasi.required' => 'Lokasi wajib diisi.',
            'google_maps_link.required' => 'Link Google Maps wajib diisi.',
            'google_maps_link.url' => 'Link Google Maps harus berupa URL yang valid.',
            'harga.required' => 'Harga sewa wajib diisi.',
            'harga.numeric' => 'Harga sewa harus berupa angka.',
            'deskripsi.required' => 'Deskripsi wajib diisi.',
            'fasilitas.required' => 'Fasilitas wajib diisi.',
            'total_kamar.required' => 'Total kamar wajib diisi.',
            'total_kamar.min' => 'Total kamar minimal 1.',
            'kamar_tersedia.required' => 'Kamar tersedia wajib diisi.',
            'kamar_tersedia.lte' => 'Kamar tersedia tidak boleh melebihi total kamar.',
            'phone.required' => 'Nomor HP owner wajib diisi.',
            'contact_email.required' => 'Email owner wajib diisi.',
            'contact_email.email' => 'Email owner tidak valid.',
            'image.required' => 'Foto kost wajib diunggah.',
            'image.image' => 'File foto harus berupa gambar.',
            'image.max' => 'Ukuran foto maksimal 3 MB.',
        ]);
    }

    protected function kostPayload(array $validated): array
    {
        return [
            'nama_kost' => $validated['nama_kost'],
            'alamat' => $validated['alamat'],
            'lokasi' => $validated['lokasi'],
            'google_maps_link' => $validated['google_maps_link'],
            'harga' => (int) $validated['harga'],
            'deskripsi' => $validated['deskripsi'],
            'fasilitas' => $validated['fasilitas'],
        ];
    }

    protected function authorizeOwner(Request $request, Kost $kost): void
    {
        abort_unless($kost->user_id === $request->user()->id, 403, 'Anda tidak berhak mengelola kost ini.');
    }
}
