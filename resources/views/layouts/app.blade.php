<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'KostFinder')</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800|fraunces:600,700" rel="stylesheet" />
    @include('partials.assets')
</head>
<body class="min-h-screen bg-[var(--background)] text-[var(--text)]">
    @php
        $flashMessage = session('error') ?? session('status');
        $flashText = is_string($flashMessage) ? strtolower($flashMessage) : '';
        $flashIsError = session('error')
            || $errors->any()
            || str_contains($flashText, 'gagal')
            || str_contains($flashText, 'maaf')
            || str_contains($flashText, 'penuh')
            || str_contains($flashText, 'sudah diproses');
    @endphp

    <div class="page-shell">
        @include('partials.navbar')

        <main class="mx-auto w-full max-w-7xl px-4 pb-16 pt-6 sm:px-6 lg:px-8">
            @if ($flashMessage || $errors->any())
                <div class="flash-toast {{ $flashIsError ? 'flash-toast-error' : 'flash-toast-success' }}" data-flash-message role="status" aria-live="polite">
                    <div class="flash-toast-icon">{{ $flashIsError ? '!' : 'OK' }}</div>
                    <div>
                        <p class="flash-toast-title">{{ $flashIsError ? 'Gagal' : 'Berhasil' }}</p>
                        <p class="flash-toast-text">{{ $flashMessage ?: 'Periksa kembali data yang diisi.' }}</p>
                    </div>
                </div>
            @endif

            @yield('content')
        </main>
    </div>
</body>
</html>
