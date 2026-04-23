@extends('layouts.app')

@section('title', 'Daftar - KostFinder')

@section('content')
    <div class="mx-auto grid max-w-5xl gap-8 lg:grid-cols-[0.95fr_1.05fr] lg:items-center">
        <section class="hero-card">
            <p class="eyebrow">Buat Akun</p>
            <h1 class="font-display text-4xl font-semibold leading-tight">Gabung ke KostFinder sebagai owner atau pencari kost.</h1>
            <p class="mt-5 text-base leading-8 text-[var(--muted)]">
                Pilih role yang sesuai. Owner akan diarahkan ke dashboard pengelolaan kost, sedangkan user biasa bisa langsung menjelajahi listing.
            </p>
        </section>

        <section class="form-card">
            <h2 class="font-display text-3xl font-semibold">Registrasi</h2>

            <form action="{{ route('register') }}" method="POST" class="mt-6 space-y-5">
                @csrf

                <label class="field-group">
                    <span>Nama</span>
                    <input type="text" name="name" value="{{ old('name') }}" class="field-input" required>
                    @error('name')
                        <small class="field-error">{{ $message }}</small>
                    @enderror
                </label>

                <label class="field-group">
                    <span>Email</span>
                    <input type="email" name="email" value="{{ old('email') }}" class="field-input" required>
                    @error('email')
                        <small class="field-error">{{ $message }}</small>
                    @enderror
                </label>

                <label class="field-group">
                    <span>Role Akun</span>
                    <select name="role" class="field-input" required>
                        <option value="">Pilih role</option>
                        <option value="owner" @selected(old('role') === 'owner')>Owner / Pemilik Kost</option>
                        <option value="user" @selected(old('role') === 'user')>User / Pencari Kost</option>
                    </select>
                    @error('role')
                        <small class="field-error">{{ $message }}</small>
                    @enderror
                </label>

                <label class="field-group">
                    <span>Password</span>
                    <input type="password" name="password" class="field-input" required>
                    @error('password')
                        <small class="field-error">{{ $message }}</small>
                    @enderror
                </label>

                <label class="field-group">
                    <span>Konfirmasi Password</span>
                    <input type="password" name="password_confirmation" class="field-input" required>
                </label>

                <button type="submit" class="w-full rounded-[24px] bg-[var(--terracotta)] px-5 py-4 text-sm font-semibold text-white transition hover:bg-[var(--terracotta-deep)]">
                    Buat Akun
                </button>
            </form>
        </section>
    </div>
@endsection
