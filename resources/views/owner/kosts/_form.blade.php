@php
    $contact = $kost->owner?->ownerContact;
    $isEdit = $kost->exists;
@endphp

<div class="grid gap-5 md:grid-cols-2">
    <label class="field-group">
        <span>Nama Kost</span>
        <input type="text" name="nama_kost" value="{{ old('nama_kost', $kost->nama_kost) }}" class="field-input" required>
        @error('nama_kost')
            <small class="field-error">{{ $message }}</small>
        @enderror
    </label>

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

    <label class="field-group">
        <span>Link Google Maps</span>
        <input type="url" name="google_maps_link" value="{{ old('google_maps_link', $kost->google_maps_link) }}" class="field-input" required>
        @error('google_maps_link')
            <small class="field-error">{{ $message }}</small>
        @enderror
    </label>

    <label class="field-group">
        <span>Harga Sewa</span>
        <input type="text" id="harga-input" name="harga" value="{{ old('harga', $kost->harga ? 'Rp ' . number_format($kost->harga, 0, ',', '.') : '') }}" class="field-input" placeholder="Rp 1.000.000" required>
        @error('harga')
            <small class="field-error">{{ $message }}</small>
        @enderror
    </label>

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

    <label class="field-group">
        <span>Nomor HP Owner</span>
        <input type="text" name="phone" value="{{ old('phone', $contact?->phone) }}" class="field-input" required>
        @error('phone')
            <small class="field-error">{{ $message }}</small>
        @enderror
    </label>

    <label class="field-group">
        <span>Email Owner</span>
        <input type="email" name="contact_email" value="{{ old('contact_email', $contact?->email ?? auth()->user()->email) }}" class="field-input" required>
        @error('contact_email')
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

    <label class="field-group md:col-span-2">
        <span>Fasilitas</span>
        <textarea name="fasilitas" rows="5" class="field-input" placeholder="Satu fasilitas per baris" required>{{ old('fasilitas', $kost->fasilitas) }}</textarea>
        @error('fasilitas')
            <small class="field-error">{{ $message }}</small>
        @enderror
    </label>

    <label class="field-group md:col-span-2">
        <span>{{ $isEdit ? 'Ganti Semua Foto Kost (opsional)' : 'Foto Kost' }}</span>
        <input type="file" name="images[]" accept="image/*" class="field-input py-3" multiple {{ $isEdit ? '' : 'required' }}>
        <small class="text-xs text-[var(--muted)]">Bisa pilih beberapa foto sekaligus, maksimal 8 foto dan 2 MB per foto.</small>
        @error('images')
            <small class="field-error">{{ $message }}</small>
        @enderror
        @error('images.*')
            <small class="field-error">{{ $message }}</small>
        @enderror
    </label>
</div>

<div class="mt-8 flex flex-wrap gap-3">
    <button type="submit" class="rounded-[24px] bg-[var(--terracotta)] px-6 py-4 text-sm font-semibold text-white transition hover:bg-[var(--terracotta-deep)]">
        {{ $isEdit ? 'Simpan Perubahan' : 'Tambah Kost' }}
    </button>
    <a href="{{ route('owner.kosts.index') }}" class="rounded-[24px] border border-[var(--line)] px-6 py-4 text-sm font-semibold text-[var(--ink)] transition hover:border-[var(--olive)] hover:bg-[var(--sage)]">
        Batal
    </a>
</div>
