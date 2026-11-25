<?php

namespace App\Http\Controllers;

use App\Models\Kost;
use App\Models\Room;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
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

        return view('owner.kosts.index', [
            'kosts' => $kosts,
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

            if (! empty($validated['images'])) {
                $this->storeImages($kost, $validated['images']);
            }

            $this->storeQrisImage($request, $kost);
            $this->storeThumbnailImage($request, $kost);

            $kost->room()->create([
                'total_kamar' => (int) $validated['total_kamar'],
                'kamar_tersedia' => (int) $validated['kamar_tersedia'],
            ]);

            $this->syncNearbyPlaces($kost, $validated['nearby_places'] ?? []);
        });

        return redirect()
            ->route('owner.kosts.index')
            ->with('status', 'Data kost berhasil ditambahkan.');
    }

    public function edit(Request $request, Kost $kost): View
    {
        $this->authorizeOwner($request, $kost);
        $kost->load(['primaryImage', 'owner.ownerContact', 'room', 'nearbyPlaces']);

        return view('owner.kosts.edit', [
            'kost' => $kost,
        ]);
    }

    public function update(Request $request, Kost $kost): RedirectResponse
    {
        $this->authorizeOwner($request, $kost);

        // Ensure array inputs fall back to existing values when not present in the request
        if (! $request->has('payment_methods')) {
            $request->merge(['payment_methods' => $kost->payment_methods ?? []]);
        }

        if (! $request->has('payment_details')) {
            $request->merge(['payment_details' => $kost->payment_details ?? []]);
        }

        if (! $request->has('fasilitas_items')) {
            $request->merge(['fasilitas_items' => preg_split('/\r\n|\r|\n/', (string) $kost->fasilitas) ?: []]);
        }

        $validated = $this->validateKost($request, false);

        DB::transaction(function () use ($request, $validated, $kost) {
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

            $this->storeQrisImage($request, $kost);
            $this->storeThumbnailImage($request, $kost);
            $this->syncNearbyPlaces($kost, $validated['nearby_places'] ?? []);
        });

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
        $imageRules = ['nullable', 'array', 'max:8'];
        if ($imageRequired) {
            $imageRules = ['required', 'array', 'min:1', 'max:8'];
        }

        return $request->validate([
            'nama_kost' => ['required', 'string', 'max:255'],
            'alamat' => ['required', 'string'],
            'lokasi' => ['required', 'string', 'max:255'],
            'google_maps_link' => ['nullable', 'url', 'max:1000'],
            'currency' => ['required', 'string', 'size:3', 'in:IDR,USD,EUR,SGD,MYR'],
            'harga_bulanan' => ['nullable', 'string', 'max:30', 'required_without:harga_harian'],
            'harga_harian' => ['nullable', 'string', 'max:30', 'required_without:harga_bulanan'],
            'deskripsi' => ['required', 'string'],
            'fasilitas' => ['nullable', 'string'],
            'fasilitas_items' => ['nullable', 'array', 'max:40'],
            'fasilitas_items.*' => ['nullable', 'string', 'max:80'],
            'payment_methods' => ['required', 'array', 'min:1'],
            'payment_methods.*' => ['string', 'in:'.implode(',', array_keys(Kost::paymentMethodOptions()))],
            'payment_details' => ['nullable', 'array'],
            'payment_details.*.account_name' => ['nullable', 'string', 'max:120'],
            'payment_details.*.account_number' => ['nullable', 'string', 'max:120'],
            'payment_details.*.instructions' => ['nullable', 'string', 'max:500'],
            'qris_image' => ['nullable', 'image', 'max:2048'],
            'thumbnail_image' => ['nullable', 'image', 'max:2048'],
            'total_kamar' => ['required', 'integer', 'min:1', 'max:500'],
            'kamar_tersedia' => ['required', 'integer', 'min:0', 'lte:total_kamar'],
            'phone' => ['required', 'string', 'max:30'],
            'contact_email' => ['required', 'email', 'max:255'],
            'nearby_places' => ['nullable', 'array', 'max:8'],
            'nearby_places.*.label' => ['required_with:nearby_places', 'string', 'max:255'],
            'nearby_places.*.category' => ['nullable', 'string', 'max:30'],
            'nearby_places.*.google_maps_link' => ['nullable', 'url', 'max:2048'],
            'nearby_places.*.distance_km' => ['required_with:nearby_places', 'numeric', 'min:0.1', 'max:999.99'],
            'images' => $imageRules,
            'images.*' => ['image', 'max:2048'],
        ], [
            'payment_methods' => ['required', 'array', 'min:1'],
            'payment_methods.*' => ['string', Rule::in(Kost::PAYMENT_METHODS)],
            'nama_kost.required' => 'Nama kost wajib diisi.',
            'alamat.required' => 'Alamat wajib diisi.',
            'lokasi.required' => 'Lokasi wajib diisi.',
            'google_maps_link.url' => 'Link Google Maps harus berupa URL yang valid.',
            'currency.required' => 'Mata uang wajib dipilih.',
            'harga_bulanan.required_without' => 'Isi harga bulanan atau harga harian.',
            'harga_harian.required_without' => 'Isi harga harian atau harga bulanan.',
            'deskripsi.required' => 'Deskripsi wajib diisi.',
            'payment_methods.required' => 'Pilih minimal 1 metode pembayaran.',
            'payment_methods.min' => 'Pilih minimal 1 metode pembayaran.',
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
            'qris_image.image' => 'File QRIS harus berupa gambar.',
            'qris_image.max' => 'Ukuran QRIS maksimal 2 MB.',
            'thumbnail_image.image' => 'File thumbnail harus berupa gambar.',
            'thumbnail_image.max' => 'Ukuran thumbnail maksimal 2 MB.',
            'payment_methods.required' => 'Metode pembayaran wajib dipilih.',
            'payment_methods.array' => 'Metode pembayaran tidak valid.',
            'payment_methods.min' => 'Minimal pilih 1 metode pembayaran.',
            'payment_methods.*.in' => 'Metode pembayaran tidak valid.',
        ]);
    }

    protected function kostPayload(array $validated): array
    {
        $monthly = $this->sanitizeMoney($validated['harga_bulanan'] ?? null);
        $daily = $this->sanitizeMoney($validated['harga_harian'] ?? null);

        $facilitiesItems = collect($validated['fasilitas_items'] ?? [])
            ->map(fn ($item) => trim((string) $item))
            ->filter()
            ->values();

        $facilitiesText = trim((string) ($validated['fasilitas'] ?? ''));
        if ($facilitiesItems->isNotEmpty()) {
            $facilitiesText = $facilitiesItems->implode("\n");
        }

        if ($facilitiesText === '') {
            abort(422, 'Minimal tambahkan 1 fasilitas.');
        }

        $mapsLink = trim((string) ($validated['google_maps_link'] ?? ''));
        if ($mapsLink === '') {
            $mapsLink = 'https://www.google.com/maps?q='.rawurlencode(trim($validated['alamat'].' '.$validated['lokasi']));
        }

        $selectedPaymentMethods = collect($validated['payment_methods'] ?? ['cash'])
            ->filter(fn ($method) => isset(Kost::paymentMethodOptions()[$method]))
            ->values()
            ->all();

        $paymentDetails = [];
        foreach ($selectedPaymentMethods as $method) {
            $detail = (array) (($validated['payment_details'] ?? [])[$method] ?? []);
            $paymentDetails[$method] = [
                'account_name' => trim((string) ($detail['account_name'] ?? '')),
                'account_number' => trim((string) ($detail['account_number'] ?? '')),
                'instructions' => trim((string) ($detail['instructions'] ?? '')),
            ];
        }

        return [
            'nama_kost' => $validated['nama_kost'],
            'alamat' => $validated['alamat'],
            'lokasi' => $validated['lokasi'],
            'google_maps_link' => $mapsLink,
            'currency' => strtoupper($validated['currency']),
            'harga_harian' => $daily ?: null,
            'harga_bulanan' => $monthly ?: null,
            // Backward-compat: `harga` tetap dipakai oleh beberapa query lama.
            'harga' => (int) ($monthly ?: $daily ?: 0),
            'deskripsi' => $validated['deskripsi'],
            'fasilitas' => $facilitiesText,
            'payment_methods' => $selectedPaymentMethods,
            'payment_details' => $paymentDetails,
        ];
    }

    protected function sanitizeMoney(?string $input): ?int
    {
        if (! $input) {
            return null;
        }

        $digits = preg_replace('/\D+/', '', $input);

        if (! $digits) {
            return null;
        }

        return max((int) $digits, 0);
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

    protected function storeQrisImage(Request $request, Kost $kost): void
    {
        $image = $request->file('qris_image');

        if (! $image) {
            return;
        }

        $kost->forceFill([
            'qris_image_data' => base64_encode(file_get_contents($image->getRealPath())),
            'qris_mime_type' => $image->getMimeType(),
        ])->save();
    }

    protected function storeThumbnailImage(Request $request, Kost $kost): void
    {
        $image = $request->file('thumbnail_image');

        if (! $image) {
            return;
        }

        $kost->forceFill([
            'thumbnail_image_data' => base64_encode(file_get_contents($image->getRealPath())),
            'thumbnail_image_mime_type' => $image->getMimeType(),
        ])->save();
    }

    protected function syncNearbyPlaces(Kost $kost, array $nearbyPlaces): void
    {
        $kost->nearbyPlaces()->delete();

        collect($nearbyPlaces)
            ->filter(fn ($place) => ! empty($place['label']) && ! empty($place['distance_km']))
            ->each(fn ($place) => $kost->nearbyPlaces()->create([
                'kost_id' => $kost->id,
                'label' => (string) $place['label'],
                'category' => (string) ($place['category'] ?? 'lainnya'),
                'google_maps_link' => ! empty($place['google_maps_link']) ? (string) $place['google_maps_link'] : null,
                'distance_km' => (float) $place['distance_km'],
            ]));
    }
}
