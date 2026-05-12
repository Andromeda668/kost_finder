<?php

namespace App\Http\Controllers;

use App\Models\Kost;
use App\Models\Booking;
use App\Models\Room;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

        $totalKosts = $request->user()->kosts()->count();

        $roomSummary = Room::query()
            ->whereHas('kost', fn ($query) => $query->where('user_id', $request->user()->id))
            ->selectRaw('SUM(total_kamar) as total_kamar, SUM(kamar_tersedia) as kamar_tersedia')
            ->first();

        $pendingBookings = Booking::query()
            ->whereHas('kost', fn ($query) => $query->where('user_id', $request->user()->id))
            ->where('status', Booking::STATUS_PENDING)
            ->count();

        $bookings = Booking::query()
            ->with(['user', 'kost.primaryImage'])
            ->whereHas('kost', fn ($query) => $query->where('user_id', $request->user()->id))
            ->latest('created_at')
            ->paginate(8, ['*'], 'booking_page');

        return view('owner.kosts.index', [
            'kosts' => $kosts,
            'bookings' => $bookings,
            'totalKosts' => $totalKosts,
            'availableRooms' => $roomSummary->kamar_tersedia ?? 0,
            'totalRooms' => $roomSummary->total_kamar ?? 0,
            'pendingBookings' => $pendingBookings,
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

        DB::transaction(function () use ($request, $validated) {
            $kost = $request->user()->kosts()->create($this->kostPayload($validated));

            $request->user()->ownerContact()->updateOrCreate(
                ['user_id' => $request->user()->id],
                [
                    'phone' => $validated['phone'],
                    'email' => $validated['contact_email'],
                ]
            );

            $this->storeImages($kost, $validated['images']);

            $kost->room()->create([
                'total_kamar' => (int) $validated['total_kamar'],
                'kamar_tersedia' => (int) $validated['kamar_tersedia'],
            ]);
        });

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

        if (! empty($validated['images'])) {
            $this->deleteImages($kost);
            $this->storeImages($kost, $validated['images']);
        }

        return redirect()
            ->route('owner.kosts.index')
            ->with('status', 'Data kost berhasil diperbarui.');
    }

    public function destroy(Request $request, Kost $kost): RedirectResponse
    {
        $this->authorizeOwner($request, $kost);

        $this->deleteImages($kost);

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
            'harga' => ['required', 'string', 'regex:/^Rp [\d\.]+$/', 'min:3'],
            'deskripsi' => ['required', 'string'],
            'fasilitas' => ['required', 'string'],
            'total_kamar' => ['required', 'integer', 'min:1', 'max:500'],
            'kamar_tersedia' => ['required', 'integer', 'min:0', 'lte:total_kamar'],
            'phone' => ['required', 'string', 'max:30'],
            'contact_email' => ['required', 'email', 'max:255'],
            'images' => [$imageRequired ? 'required' : 'nullable', 'array', 'min:1', 'max:8'],
            'images.*' => ['image', 'max:2048'],
        ], [
            'nama_kost.required' => 'Nama kost wajib diisi.',
            'alamat.required' => 'Alamat wajib diisi.',
            'lokasi.required' => 'Lokasi wajib diisi.',
            'google_maps_link.required' => 'Link Google Maps wajib diisi.',
            'google_maps_link.url' => 'Link Google Maps harus berupa URL yang valid.',
            'harga.required' => 'Harga sewa wajib diisi.',
            'harga.regex' => 'Format harga tidak valid (contoh: Rp 1.000.000).',
            'harga.min' => 'Harga sewa minimal Rp 100.',
            'deskripsi.required' => 'Deskripsi wajib diisi.',
            'fasilitas.required' => 'Fasilitas wajib diisi.',
            'total_kamar.required' => 'Total kamar wajib diisi.',
            'total_kamar.min' => 'Total kamar minimal 1.',
            'kamar_tersedia.required' => 'Kamar tersedia wajib diisi.',
            'kamar_tersedia.lte' => 'Kamar tersedia tidak boleh melebihi total kamar.',
            'phone.required' => 'Nomor HP owner wajib diisi.',
            'contact_email.required' => 'Email owner wajib diisi.',
            'contact_email.email' => 'Email owner tidak valid.',
            'images.required' => 'Foto kost wajib diunggah.',
            'images.array' => 'Foto kost tidak valid.',
            'images.min' => 'Minimal unggah 1 foto kost.',
            'images.max' => 'Maksimal unggah 8 foto kost.',
            'images.*.image' => 'Semua file foto harus berupa gambar.',
            'images.*.max' => 'Ukuran tiap foto maksimal 2 MB.',
        ]);
    }

    protected function kostPayload(array $validated): array
    {
        return [
            'nama_kost' => $validated['nama_kost'],
            'alamat' => $validated['alamat'],
            'lokasi' => $validated['lokasi'],
            'google_maps_link' => $validated['google_maps_link'],
            'harga' => (int) str_replace(['Rp ', '.'], '', $validated['harga']),
            'deskripsi' => $validated['deskripsi'],
            'fasilitas' => $validated['fasilitas'],
        ];
    }

    protected function authorizeOwner(Request $request, Kost $kost): void
    {
        abort_unless($kost->user_id === $request->user()->id, 403, 'Anda tidak berhak mengelola kost ini.');
    }

    protected function storeImages(Kost $kost, array $images): void
    {
        foreach ($images as $image) {
            $kost->images()->create([
                'image_data' => base64_encode(file_get_contents($image->getRealPath())),
                'mime_type' => $image->getMimeType(),
                'image_path' => null,
            ]);
        }
    }

    protected function deleteImages(Kost $kost): void
    {
        $kost->loadMissing('images');

        foreach ($kost->images as $image) {
            if ($image->image_path) {
                Storage::disk('public')->delete($image->image_path);
            }

            $image->delete();
        }
    }
}
