<header class="sticky top-0 z-40 border-b border-[var(--line)] bg-[color:rgba(255,255,255,0.88)] backdrop-blur-xl">
    <div class="mx-auto flex w-full max-w-7xl items-center justify-between gap-4 px-4 py-4 sm:px-6 lg:px-8">
        <a href="{{ route('home') }}" class="flex items-center gap-3">
            <img src="{{ asset('images/logo.png') }}" alt="KostFinder Logo" class="h-11 w-11 rounded-xl">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.28em] text-[var(--terracotta-deep)]">Kost Booking</p>
                <p class="text-xl font-bold text-[var(--ink)]">KostFinder</p>
            </div>
        </a>

        <button type="button" class="inline-flex h-11 w-11 items-center justify-center rounded-2xl border border-[var(--line)] bg-white md:hidden" data-menu-toggle aria-label="Buka menu">
            <span class="menu-bar"></span>
        </button>

        <nav class="hidden items-center gap-2 md:flex" data-nav-menu>
            @auth
                @if (auth()->user()->isOwner())
                    <a href="{{ route('owner.dashboard') }}" class="nav-link {{ request()->routeIs('owner.dashboard') ? 'nav-link-active' : '' }}">Dashboard Owner</a>
                    <a href="{{ route('owner.history') }}" class="nav-link {{ request()->routeIs('owner.history') ? 'nav-link-active' : '' }}">Riwayat</a>
                    <a href="{{ route('owner.bookings.index') }}" class="nav-link {{ request()->routeIs('owner.bookings.*') ? 'nav-link-active' : '' }}">Booking</a>
                @else
                    <a href="{{ route('home') }}" class="nav-link {{ request()->routeIs('home') ? 'nav-link-active' : '' }}">Cari Kost</a>
                    <a href="{{ route('bookings.index') }}" class="nav-link {{ request()->routeIs('bookings.*') ? 'nav-link-active' : '' }}">Booking Saya</a>
                    <a href="{{ route('favorites.index') }}" class="nav-link {{ request()->routeIs('favorites.*') ? 'nav-link-active' : '' }}">Wishlist</a>
                @endif

                <form action="{{ route('logout') }}" method="POST" class="ml-2">
                    @csrf
                    <button type="submit" class="solid-button solid-button-dark">Logout</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="nav-link {{ request()->routeIs('login') ? 'nav-link-active' : '' }}">Login</a>
                <a href="{{ route('register') }}" class="solid-button">Daftar</a>
            @endauth
        </nav>
    </div>

    <div class="mx-auto hidden w-full max-w-7xl px-4 pb-4 md:hidden sm:px-6 lg:px-8" data-mobile-menu>
        <div class="space-y-3 rounded-[28px] border border-[var(--line)] bg-white p-4 shadow-[0_20px_50px_rgba(43,43,43,0.08)]">
            <a href="{{ route('home') }}" class="mobile-link">Cari Kost</a>

            @auth
                @if (auth()->user()->isOwner())
                    <a href="{{ route('owner.dashboard') }}" class="mobile-link">Dashboard Owner</a>
                    <a href="{{ route('owner.history') }}" class="mobile-link">Riwayat</a>
                    <a href="{{ route('owner.bookings.index') }}" class="mobile-link">Booking</a>
                @else
                    <a href="{{ route('bookings.index') }}" class="mobile-link">Booking Saya</a>
                    <a href="{{ route('favorites.index') }}" class="mobile-link">Wishlist</a>
                @endif

                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="solid-button solid-button-dark w-full">Logout</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="mobile-link">Login</a>
                <a href="{{ route('register') }}" class="mobile-link mobile-link-strong">Daftar</a>
            @endauth
        </div>
    </div>
</header>
