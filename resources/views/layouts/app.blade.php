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
    <div class="page-shell">
        @include('partials.navbar')

        <main class="mx-auto w-full max-w-7xl px-4 pb-16 pt-6 sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="mb-6 rounded-3xl border border-[var(--line)] bg-white/80 px-5 py-4 text-sm text-[var(--ink)] shadow-sm">
                    {{ session('status') }}
                </div>
            @endif

            @yield('content')
        </main>
    </div>
</body>
</html>
