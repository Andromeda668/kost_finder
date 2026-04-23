@extends('layouts.app')

@section('title', 'Login - KostFinder')

@section('content')
    <div class="mx-auto grid max-w-5xl gap-8 lg:grid-cols-[0.95fr_1.05fr] lg:items-center">
        <section class="hero-card">
            <p class="eyebrow">Masuk ke Akun</p>
            <h1 class="font-display text-4xl font-semibold leading-tight">Masuk untuk memasang atau mencari kost dengan lebih cepat.</h1>
            <p class="mt-5 text-base leading-8 text-[var(--muted)]">
                Owner bisa mengelola listing kost, sementara pencari kost bisa fokus menjelajahi lokasi dan membandingkan harga.
            </p>
        </section>

        <section class="form-card">
            <h2 class="font-display text-3xl font-semibold">Login</h2>

            <form action="{{ route('login') }}" method="POST" class="mt-6 space-y-5">
                @csrf

                <label class="field-group">
                    <span>Email</span>
                    <input type="email" name="email" value="{{ old('email') }}" class="field-input" required autofocus>
                    @error('email')
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

                <label class="inline-flex items-center gap-3 text-sm text-[var(--muted)]">
                    <input type="checkbox" name="remember" value="1" class="rounded border-[var(--line)] text-[var(--terracotta)] focus:ring-[var(--terracotta)]">
                    Ingat saya
                </label>

                <button type="submit" class="w-full rounded-[24px] bg-[var(--ink)] px-5 py-4 text-sm font-semibold text-white transition hover:bg-[var(--olive)]">
                    Masuk
                </button>
            </form>
        </section>
    </div>
@endsection
