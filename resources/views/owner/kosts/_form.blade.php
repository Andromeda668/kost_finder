@php
    $contact = $kost->owner?->ownerContact;
    $isEdit = $kost->exists;
    $nearbyPlaces = old('nearby_places', $kost->nearbyPlaces?->map(fn ($place) => [
        'label' => $place->label,
        'category' => $place->category,
        'distance_km' => $place->distance_km,
        'google_maps_link' => $place->google_maps_link,
    ])->toArray() ?? []);
    $facilityItems = old('fasilitas_items', collect(preg_split('/\r\n|\r|\n/', (string) $kost->fasilitas) ?: [])
        ->map(fn ($item) => trim($item))
        ->filter()
        ->values()
        ->toArray());
@endphp

<div data-kost-wizard>
    <div class="wizard-shell">
        <div class="wizard-progress">
            <div class="wizard-progress-bar" data-wizard-progress aria-hidden="true"></div>
        </div>
        <div class="wizard-steps" aria-label="Langkah pengisian">
            <button type="button" class="wizard-step wizard-step-active" data-wizard-step-indicator="1">Identitas</button>
            <button type="button" class="wizard-step" data-wizard-step-indicator="2">Lokasi</button>
            <button type="button" class="wizard-step" data-wizard-step-indicator="3">Informasi</button>
            <button type="button" class="wizard-step" data-wizard-step-indicator="4">Harga</button>
        </div>
    </div>

    <section class="wizard-panel" data-wizard-step="1">
        <div class="grid gap-5 md:grid-cols-2">
            <label class="field-group md:col-span-2">
                <span>Nama Kost</span>
                <input type="text" name="nama_kost" value="{{ old('nama_kost', $kost->nama_kost) }}" class="field-input" required>
                @error('nama_kost')
                    <small class="field-error">{{ $message }}</small>
                @enderror
            </label>

            <label class="field-group">
                <span>Nomor HP Owner</span>
                <input type="text" name="phone" value="{{ old('phone', $contact?->phone) }}" class="field-input" inputmode="tel" autocomplete="tel" required>
                @error('phone')
                    <small class="field-error">{{ $message }}</small>
                @enderror
            </label>

            <label class="field-group">
                <span>Email Owner</span>
                <input type="email" name="contact_email" value="{{ old('contact_email', $contact?->email ?? auth()->user()->email) }}" class="field-input" autocomplete="email" required>
                @error('contact_email')
                    <small class="field-error">{{ $message }}</small>
                @enderror
            </label>
        </div>
    </section>

    <section class="wizard-panel hidden" data-wizard-step="2">
        <div class="grid gap-5 md:grid-cols-2">
            <div class="grid gap-5 md:col-span-2 md:grid-cols-2" data-location-group>
                <label class="field-group">
                    <span>Provinsi</span>
                    <select
                        class="field-input"
                        data-location-province-select
                        data-placeholder="Pilih provinsi"
                        required
                    >
                        <option value="">Memuat provinsi...</option>
                    </select>
                </label>

                <label class="field-group">
                    <span>Kota / Kabupaten</span>
                    <select
                        name="lokasi"
                        class="field-input"
                        data-location-select
                        data-placeholder="Pilih kota / kabupaten"
                        data-selected="{{ old('lokasi', $kost->lokasi) }}"
                        required
                    >
                        <option value="">{{ old('lokasi', $kost->lokasi) ?: 'Pilih kota / kabupaten' }}</option>
                    </select>
                    @error('lokasi')
                        <small class="field-error">{{ $message }}</small>
                    @enderror
                </label>
            </div>

            <label class="field-group md:col-span-2">
                <span>Alamat Lengkap</span>
                <textarea name="alamat" rows="4" class="field-input" required>{{ old('alamat', $kost->alamat) }}</textarea>
                @error('alamat')
                    <small class="field-error">{{ $message }}</small>
                @enderror
            </label>

            <div class="md:col-span-2">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <p class="text-sm font-semibold text-[var(--ink)]">Pin lokasi di Google Maps (opsional)</p>
                        <p class="text-xs text-[var(--muted)]">Klik “Buka Google Maps”, pin lokasi, lalu copy link dan tempel di bawah.</p>
                    </div>
                    <a href="https://www.google.com/maps" target="_blank" rel="noopener noreferrer" class="ghost-button">Buka Google Maps</a>
                </div>

                <label class="field-group mt-4">
                    <span>Link Google Maps (opsional)</span>
                    <input type="url" name="google_maps_link" value="{{ old('google_maps_link', $kost->google_maps_link) }}" class="field-input" placeholder="https://maps.app.goo.gl/... atau https://www.google.com/maps?..."
                        data-maps-link>
                    @error('google_maps_link')
                        <small class="field-error">{{ $message }}</small>
                    @enderror
                    <small class="text-xs text-[var(--muted)]">Jika kosong, sistem akan otomatis membuat link dari alamat.</small>
                </label>

                <div class="map-embed-shell mt-4">
                    <iframe
                        src="https://www.google.com/maps?q={{ rawurlencode(trim(old('alamat', $kost->alamat).' '.old('lokasi', $kost->lokasi))) }}&output=embed"
                        loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade"
                        class="map-embed-frame"
                        title="Preview peta"
                        data-maps-preview
                    ></iframe>
                </div>
            </div>

            <div class="md:col-span-2">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <p class="text-sm font-semibold text-[var(--ink)]">Tempat terdekat</p>
                        <p class="text-xs text-[var(--muted)]">Tambahkan lokasi-lokasi menarik di sekitar kost. Anda dapat memasukkan nama, kategori, dan link Google Maps (opsional).</p>
                    </div>
                    <button type="button" class="ghost-button" data-nearby-add>Tambahkan</button>
                </div>

                <div class="mt-4 space-y-4" data-nearby-list>
                    @forelse ($nearbyPlaces as $index => $place)
                        <div class="nearby-card rounded-lg border border-[var(--line)] bg-[var(--surface-muted)] p-4" data-nearby-row data-nearby-index="{{ $index }}">
                            <div class="grid gap-3 md:grid-cols-2">
                                <div class="md:col-span-2 flex items-center justify-between gap-2">
                                    <p class="text-sm font-semibold text-[var(--ink)]">Tempat terdekat #{{ $index + 1 }}</p>
                                    <button type="button" class="table-link table-link-danger text-sm" data-nearby-remove>Hapus</button>
                                </div>

                                <label class="field-group">
                                    <span>Nama Tempat</span>
                                    <input type="text" name="nearby_places[{{ $index }}][label]" value="{{ $place['label'] ?? '' }}" class="field-input" placeholder="Contoh: Universitas Indonesia" required>
                                </label>

                                <label class="field-group">
                                    <span>Kategori</span>
                                    <select name="nearby_places[{{ $index }}][category]" class="field-input">
                                        @php $cat = $place['category'] ?? 'lainnya'; @endphp
                                        <option value="universitas" {{ $cat === 'universitas' ? 'selected' : '' }}>Universitas</option>
                                        <option value="kantor" {{ $cat === 'kantor' ? 'selected' : '' }}>Kantor</option>
                                        <option value="transport" {{ $cat === 'transport' ? 'selected' : '' }}>Transport</option>
                                        <option value="lainnya" {{ $cat === 'lainnya' ? 'selected' : '' }}>Lainnya</option>
                                    </select>
                                </label>

                                <label class="field-group md:col-span-2">
                                    <span>Link Google Maps (opsional)</span>
                                    <input type="url" name="nearby_places[{{ $index }}][google_maps_link]" value="{{ $place['google_maps_link'] ?? '' }}" class="field-input" placeholder="https://maps.app.goo.gl/..." data-nearby-maps-link>
                                    <small class="text-xs text-[var(--muted)]">Buka Google Maps, cari tempat, salin link, dan tempel di sini.</small>
                                </label>

                                <label class="field-group">
                                    <span>Jarak dari Kost (km)</span>
                                    <input type="number" step="0.1" min="0.1" name="nearby_places[{{ $index }}][distance_km]" value="{{ $place['distance_km'] ?? '' }}" class="field-input" placeholder="Contoh: 1.5" required>
                                    <small class="text-xs text-[var(--muted)]">Estimasi jarak dalam kilometer.</small>
                                </label>
                            </div>
                        </div>
                    @empty
                        <div class="nearby-empty rounded-lg border border-[var(--line)] bg-[var(--surface-muted)] p-4 text-center text-sm text-[var(--muted)]">
                            Belum ada tempat terdekat ditambahkan. Klik tombol "Tambahkan" di atas untuk memulai.
                        </div>
                    @endforelse
                </div>

                @error('nearby_places')
                    <small class="field-error mt-2 block">{{ $message }}</small>
                @enderror
            </div>
        </div>
    </section>

    <section class="wizard-panel hidden" data-wizard-step="3">
        <div class="grid gap-5 md:grid-cols-2">
            <label class="field-group">
                <span>Total Kamar</span>
                <input type="number" name="total_kamar" value="{{ old('total_kamar', $kost->room?->total_kamar) }}" class="field-input" min="1" required>
                @error('total_kamar')
                    <small class="field-error">{{ $message }}</small>
                @enderror
            </label>

            <label class="field-group">
                <span>Kamar Tersedia</span>
                <input type="number" name="kamar_tersedia" value="{{ old('kamar_tersedia', $kost->room?->kamar_tersedia) }}" class="field-input" min="0" required>
                @error('kamar_tersedia')
                    <small class="field-error">{{ $message }}</small>
                @enderror
            </label>

            <label class="field-group md:col-span-2">
                <span>Deskripsi</span>
                <textarea name="deskripsi" rows="5" class="field-input" required>{{ old('deskripsi', $kost->deskripsi) }}</textarea>
                @error('deskripsi')
                    <small class="field-error">{{ $message }}</small>
                @enderror
            </label>

            <div class="md:col-span-2">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <p class="text-sm font-semibold text-[var(--ink)]">Fasilitas</p>
                        <p class="text-xs text-[var(--muted)]">Masukkan satu per satu, lalu klik “Tambah fasilitas”.</p>
                    </div>
                    <button type="button" class="ghost-button" data-facility-add>Tambah fasilitas</button>
                </div>

                <div class="mt-4 grid gap-3" data-facility-list>
                    @php $facilityItems = is_array($facilityItems) ? $facilityItems : []; @endphp
                    @forelse ($facilityItems as $index => $item)
                        <div class="nearby-row" data-facility-row>
                            <input type="text" name="fasilitas_items[]" value="{{ $item }}" class="field-input" placeholder="Contoh: WiFi" required>
                            <div class="hidden sm:block"></div>
                            <div class="hidden sm:block"></div>
                            <button type="button" class="table-link table-link-danger" data-facility-remove>Hapus</button>
                        </div>
                    @empty
                        <div class="nearby-row" data-facility-row>
                            <input type="text" name="fasilitas_items[]" value="" class="field-input" placeholder="Contoh: WiFi" required>
                            <div class="hidden sm:block"></div>
                            <div class="hidden sm:block"></div>
                            <button type="button" class="table-link table-link-danger" data-facility-remove>Hapus</button>
                        </div>
                    @endforelse
                </div>

                <input type="hidden" name="fasilitas" value="{{ old('fasilitas', $kost->fasilitas) }}">
                @error('fasilitas_items')
                    <small class="field-error mt-2 block">{{ $message }}</small>
                @enderror
            </div>

            <div class="md:col-span-2">
                <p class="text-sm font-semibold text-[var(--ink)]">{{ $isEdit ? 'Ganti semua foto (opsional)' : 'Foto kost' }}</p>
                <p class="mt-1 text-xs text-[var(--muted)]">Klik area di bawah untuk memilih beberapa foto (maks 8 foto, 2MB per foto).</p>

                <label class="upload-drop mt-3 block">
                    <input type="file" name="images[]" accept="image/*" class="sr-only" multiple {{ $isEdit ? '' : 'required' }} data-upload-input>
                    <div class="upload-drop-inner">
                        <p class="font-semibold text-[var(--ink)]">Klik untuk upload foto</p>
                        <p class="mt-1 text-xs text-[var(--muted)]" data-upload-hint>Belum ada file dipilih</p>
                    </div>
                </label>

                @error('images')
                    <small class="field-error mt-2 block">{{ $message }}</small>
                @enderror
                @error('images.*')
                    <small class="field-error mt-2 block">{{ $message }}</small>
                @enderror
            </div>
        </div>
    </section>

    <section class="wizard-panel hidden" data-wizard-step="4">
        <div class="grid gap-5 md:grid-cols-2">
            <label class="field-group">
                <span>Mata Uang</span>
                <select name="currency" class="field-input" required data-currency-select>
                    @php
                        $selectedCurrency = old('currency', $kost->currency ?? 'IDR');
                    @endphp
                    <option value="IDR" {{ $selectedCurrency === 'IDR' ? 'selected' : '' }}>IDR (Rupiah)</option>
                    <option value="USD" {{ $selectedCurrency === 'USD' ? 'selected' : '' }}>USD</option>
                    <option value="EUR" {{ $selectedCurrency === 'EUR' ? 'selected' : '' }}>EUR</option>
                    <option value="SGD" {{ $selectedCurrency === 'SGD' ? 'selected' : '' }}>SGD</option>
                    <option value="MYR" {{ $selectedCurrency === 'MYR' ? 'selected' : '' }}>MYR</option>
                </select>
                @error('currency')
                    <small class="field-error">{{ $message }}</small>
                @enderror
            </label>

            <div class="hidden md:block"></div>

            <label class="field-group">
                <span>Harga Bulanan (opsional)</span>
                <input
                    type="text"
                    name="harga_bulanan"
                    value="{{ old('harga_bulanan', $kost->harga_bulanan ? number_format($kost->harga_bulanan, 0, ',', '.') : '') }}"
                    class="field-input"
                    placeholder="1.000.000"
                    inputmode="numeric"
                    autocomplete="off"
                    data-money-input
                    data-digits-only
                >
                @error('harga_bulanan')
                    <small class="field-error">{{ $message }}</small>
                @enderror
            </label>

            <label class="field-group">
                <span>Harga Harian (opsional)</span>
                <input
                    type="text"
                    name="harga_harian"
                    value="{{ old('harga_harian', $kost->harga_harian ? number_format($kost->harga_harian, 0, ',', '.') : '') }}"
                    class="field-input"
                    placeholder="150.000"
                    inputmode="numeric"
                    autocomplete="off"
                    data-money-input
                    data-digits-only
                >
                @error('harga_harian')
                    <small class="field-error">{{ $message }}</small>
                @enderror
                <small class="text-xs text-[var(--muted)]">Minimal isi salah satu: harian atau bulanan.</small>
            </label>
        </div>
    </section>

    <div class="mt-8 flex flex-wrap items-center justify-between gap-3">
        <button type="button" class="ghost-button hidden" data-wizard-prev>Back</button>

        <div class="flex flex-wrap items-center gap-3">
            <a href="{{ route('owner.kosts.index') }}" class="rounded-[24px] border border-[var(--line)] px-6 py-4 text-sm font-semibold text-[var(--ink)] transition hover:border-[var(--olive)] hover:bg-[var(--sage)]">
                Batal
            </a>

            <button type="button" class="ghost-button" data-wizard-next>Next</button>

            <button type="submit" class="rounded-[24px] bg-[var(--terracotta)] px-6 py-4 text-sm font-semibold text-white transition hover:bg-[var(--terracotta-deep)] hidden" data-wizard-submit>
                {{ $isEdit ? 'Simpan Perubahan' : 'Tambah Kost' }}
            </button>
        </div>
    </div>

    <div class="mt-4 hidden rounded-[22px] border border-[rgba(210,93,77,0.25)] bg-[rgba(210,93,77,0.08)] p-4 text-sm text-[var(--ink)]" data-wizard-alert role="alert">
        <p class="font-semibold">Masih ada data yang belum diisi.</p>
        <p class="mt-1 text-[var(--muted)]" data-wizard-alert-text>Lengkapi field yang ditandai sebelum lanjut.</p>
    </div>
</div>

<script>
    // Lightweight inline wizard controller (works even if Vite build is not refreshed)
    (function () {
        const root = document.querySelector('[data-kost-wizard]');
        if (!root) return;

        const panels = Array.from(root.querySelectorAll('[data-wizard-step]'));
        const indicators = Array.from(root.querySelectorAll('[data-wizard-step-indicator]'));
        const prevButton = root.querySelector('[data-wizard-prev]');
        const nextButton = root.querySelector('[data-wizard-next]');
        const submitButton = root.querySelector('[data-wizard-submit]');
        const progress = root.querySelector('[data-wizard-progress]');
        const alertBox = root.querySelector('[data-wizard-alert]');
        const alertText = root.querySelector('[data-wizard-alert-text]');

        if (!panels.length) return;

        const clamp = (v) => Math.min(Math.max(v, 1), panels.length);
        let step = 1;

        const getPanel = (n) => panels.find((p) => p.dataset.wizardStep === String(n));

        const hideAlert = () => {
            alertBox?.classList.add('hidden');
        };

        const showAlert = (message) => {
            if (!alertBox) return;
            alertText && (alertText.textContent = message || 'Lengkapi field yang ditandai sebelum lanjut.');
            alertBox.classList.remove('hidden');
        };

        const markInvalid = (el) => {
            if (!el || !el.classList) return;
            el.classList.add('ring-2', 'ring-[rgba(210,93,77,0.35)]');
            el.addEventListener('input', () => el.classList.remove('ring-2', 'ring-[rgba(210,93,77,0.35)]'), { once: true });
            el.addEventListener('change', () => el.classList.remove('ring-2', 'ring-[rgba(210,93,77,0.35)]'), { once: true });
        };

        const validateCurrentStep = () => {
            hideAlert();
            const panel = getPanel(step);
            if (!panel) return true;

            const fields = Array.from(panel.querySelectorAll('input, select, textarea'))
                .filter((el) => !el.disabled && el.type !== 'hidden');

            let firstInvalid = null;
            for (const el of fields) {
                // Skip empty optional URL maps link
                if (el.name === 'google_maps_link' && !el.value) continue;

                if (typeof el.checkValidity === 'function' && !el.checkValidity()) {
                    firstInvalid = firstInvalid || el;
                    markInvalid(el);
                }
            }

            if (firstInvalid) {
                showAlert('Lengkapi field yang wajib diisi pada langkah ini.');
                firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
                firstInvalid.focus?.();
                return false;
            }

            return true;
        };

        const showStep = (target) => {
            step = clamp(target);
            panels.forEach((panel) => panel.classList.toggle('hidden', panel.dataset.wizardStep !== String(step)));
            indicators.forEach((i) => i.classList.toggle('wizard-step-active', i.dataset.wizardStepIndicator === String(step)));
            prevButton?.classList.toggle('hidden', step === 1);
            nextButton?.classList.toggle('hidden', step === panels.length);
            submitButton?.classList.toggle('hidden', step !== panels.length);
            progress && (progress.style.width = `${Math.round((step / panels.length) * 100)}%`);
            hideAlert();
            getPanel(step)?.querySelector('input,select,textarea')?.focus?.();
        };

        indicators.forEach((i) => i.addEventListener('click', () => showStep(Number(i.dataset.wizardStepIndicator))));
        prevButton?.addEventListener('click', () => showStep(step - 1));
        nextButton?.addEventListener('click', () => {
            if (!validateCurrentStep()) return;
            showStep(step + 1);
        });

        // Guard submit when user is on last step
        submitButton?.closest('form')?.addEventListener('submit', (e) => {
            // Validate all steps quickly by checking required fields in the whole form.
            hideAlert();
            const form = e.target;
            const invalid = form.querySelector(':invalid');
            if (invalid) {
                e.preventDefault();
                showAlert('Masih ada field wajib yang kosong. Silakan cek kembali.');
                invalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
                invalid.focus?.();
            }
        });

        showStep(1);
    })();
</script>
