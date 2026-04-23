@php
    $hasViteAssets = file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot'));
@endphp

@if ($hasViteAssets)
    @vite(['resources/css/app.css', 'resources/js/app.js'])
@else
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                        display: ['Fraunces', 'Plus Jakarta Sans', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                    },
                },
            },
        };
    </script>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800|fraunces:600,700" rel="stylesheet" />
    <style>
        :root {
            --primary: #c46a4a;
            --primary-deep: #a95538;
            --secondary: #f5e9e2;
            --accent: #6d8b74;
            --background: #ffffff;
            --text: #2b2b2b;
            --muted: #7b746f;
            --line: #ead9cf;
            --surface-muted: #fcf6f2;
            --terracotta: #c46a4a;
            --terracotta-deep: #a95538;
            --sand: #ffffff;
            --ink: #2b2b2b;
            --olive: #6d8b74;
            --soft-red: #c9695d;
            --cream-card: #fcf6f2;
        }
        html { scroll-behavior: smooth; }
        body {
            font-family: 'Plus Jakarta Sans', ui-sans-serif, system-ui, sans-serif;
            background:
                radial-gradient(circle at top left, rgba(196, 106, 74, 0.12), transparent 24%),
                linear-gradient(180deg, #ffffff 0%, #fdf8f5 100%);
        }
        h1, h2, h3, h4 { font-family: 'Fraunces', 'Plus Jakarta Sans', ui-sans-serif, system-ui, sans-serif; }
        .hero-card, .form-card, .detail-panel, .owner-card, .kost-card, .empty-state {
            border: 1px solid var(--line);
            border-radius: 34px;
            background: rgba(255, 255, 255, 0.75);
            box-shadow: 0 8px 30px rgba(61, 51, 43, 0.05);
            backdrop-filter: blur(8px);
        }
        .hero-card, .form-card, .detail-panel, .owner-card, .empty-state { padding: 1.5rem; }
        .kost-card { padding: 1rem; }
        .eyebrow {
            color: var(--terracotta-deep);
            font-size: .75rem;
            font-weight: 700;
            letter-spacing: .3em;
            text-transform: uppercase;
        }
        .field-group { display: flex; flex-direction: column; gap: .5rem; color: var(--ink); font-size: .875rem; font-weight: 500; }
        .field-input {
            width: 100%;
            border: 1px solid var(--line);
            border-radius: 22px;
            background: #fff;
            padding: .875rem 1rem;
            outline: none;
        }
        .field-input:focus { border-color: var(--terracotta); box-shadow: 0 0 0 4px rgba(195, 106, 78, 0.14); }
        .field-error { color: #b94a48; font-size: .8125rem; }
        .stat-card, .stat-card-alt {
            border-radius: 30px;
            padding: 1.25rem;
            border: 1px solid rgba(195, 106, 78, .15);
            background: linear-gradient(180deg, rgba(255,255,255,.75), rgba(251,243,232,.95));
        }
        .stat-card-alt {
            border-color: rgba(106, 122, 82, .18);
            background: linear-gradient(180deg, rgba(236,242,228,.9), rgba(220,228,207,.95));
        }
        .stat-value { font-size: 1.875rem; font-weight: 600; }
        .stat-label { margin-top: .5rem; font-size: .875rem; line-height: 1.75rem; color: var(--muted); }
        .price-chip, .facility-chip {
            display: inline-flex;
            align-items: center;
            border-radius: 999px;
            padding: .5rem 1rem;
            font-size: .875rem;
            font-weight: 600;
        }
        .price-chip { background: rgba(195, 106, 78, .12); color: var(--terracotta-deep); }
        .facility-chip { background: rgba(106, 122, 82, .12); color: var(--olive); }
        .placeholder-cover {
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, rgba(195,106,78,.2), rgba(106,122,82,.16)), #f2e6d5;
            color: var(--ink);
            font-size: .875rem;
            font-weight: 600;
        }
        .nav-link, .mobile-link {
            color: var(--ink);
            border-radius: 999px;
            padding: .75rem 1rem;
            font-size: .875rem;
            font-weight: 600;
            transition: .2s ease;
        }
        .nav-link:hover, .mobile-link:hover, .nav-link-active { background: rgba(195, 106, 78, .1); color: var(--terracotta-deep); }
        .mobile-link { display: block; border-radius: 1rem; }
        .mobile-link-strong { background: rgba(195,106,78,.12); color: var(--terracotta-deep); }
        .menu-bar, .menu-bar::before, .menu-bar::after {
            display: block;
            width: 1.25rem;
            height: 2px;
            border-radius: 999px;
            background: var(--ink);
            content: '';
        }
        .menu-bar { position: relative; }
        .menu-bar::before { position: absolute; top: -.375rem; }
        .menu-bar::after { position: absolute; top: .375rem; }
        .section-title { font-size: 1.5rem; font-weight: 600; }
        .price-box {
            border-radius: 28px;
            padding: 1rem 1.25rem;
            background: rgba(195, 106, 78, .12);
            color: var(--terracotta-deep);
            font-size: 1.5rem;
            font-weight: 700;
        }
        .price-box span { margin-left: .5rem; color: var(--muted); font-size: .875rem; font-weight: 500; }
    </style>
@endif
