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
        .travel-hero,
        .search-result-card,
        .page-heading,
        .content-card,
        .booking-sticky-card,
        .dashboard-table-card,
        .gallery-shell,
        .owner-hero {
            border: 1px solid var(--line);
            border-radius: 32px;
            background: rgba(255, 255, 255, 0.9);
            box-shadow: 0 8px 30px rgba(61, 51, 43, 0.05);
        }
        .hero-card, .form-card, .detail-panel, .owner-card, .empty-state { padding: 1.5rem; }
        .kost-card { padding: 1rem; }
        .travel-hero, .page-heading, .content-card, .booking-sticky-card, .dashboard-table-card, .gallery-shell, .owner-hero { padding: 1.5rem; }
        .page-shell { min-height: 100vh; }
        .detail-layout, .owner-dashboard-shell, .dashboard-grid, .owner-hero-copy, .owner-quick-list { display: grid; gap: 1.5rem; }
        .table-shell { overflow-x: auto; }
        .dashboard-table { min-width: 100%; border-collapse: separate; border-spacing: 0 .75rem; font-size: .875rem; }
        .dashboard-table th { padding: 0 1rem .5rem; text-align: left; font-size: .75rem; letter-spacing: .18em; text-transform: uppercase; color: var(--muted); }
        .dashboard-table td { padding: 1rem; background: var(--surface-muted); color: var(--ink); vertical-align: top; }
        .dashboard-section-head { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 1rem; margin-bottom: 1rem; }
        .dashboard-section-title, .owner-hero-heading { font-size: 2rem; font-weight: 600; line-height: 1.15; }
        .dashboard-section-copy, .owner-hero-text { color: var(--muted); line-height: 1.7; }
        .owner-hero { background: linear-gradient(180deg, rgba(255,255,255,.98), rgba(249,252,254,.96)); }
        .owner-hero-strip { display: grid; gap: .75rem; }
        .owner-hero-stat, .owner-quick-card { border-radius: 24px; border: 1px solid rgba(195,106,78,.12); padding: 1rem; background: linear-gradient(180deg, #fff, #f9fbfc); }
        .owner-hero-stat-warm { border-color: rgba(245, 158, 11, .18); background: linear-gradient(180deg, #fffdf7, #fff6e5); }
        .owner-hero-stat-muted { border-color: rgba(24, 49, 83, .08); background: linear-gradient(180deg, #fff, #f5f8fc); }
        .owner-hero-stat-label { font-size: .75rem; font-weight: 700; letter-spacing: .18em; text-transform: uppercase; color: var(--muted); }
        .owner-hero-stat-value { margin-top: .5rem; font-size: 1.875rem; font-weight: 700; color: var(--ink); }
        .owner-hero-stat-copy { margin-top: .5rem; color: var(--muted); line-height: 1.6; font-size: .875rem; }
        .owner-quick-item { display: flex; gap: .75rem; border-radius: 20px; background: rgba(255,255,255,.85); padding: 1rem; }
        .owner-quick-icon, .facility-icon { display: inline-flex; align-items: center; justify-content: center; width: 2rem; height: 2rem; border-radius: 999px; font-size: .75rem; font-weight: 700; }
        .owner-quick-icon { background: rgba(195,106,78,.12); color: var(--terracotta-deep); }
        .gallery-main-wrap { position: relative; overflow: hidden; border-radius: 28px; }
        .gallery-main-image { width: 100%; height: 330px; object-fit: cover; border-radius: 28px; }
        .gallery-nav-button { position: absolute; top: 50%; transform: translateY(-50%); display: flex; width: 2.75rem; height: 2.75rem; align-items: center; justify-content: center; border-radius: 999px; border: 1px solid var(--line); background: rgba(255,255,255,.9); color: var(--ink); font-size: 1.875rem; font-weight: 600; line-height: 1; box-shadow: 0 10px 24px rgba(61,51,43,.12); transition: background .2s ease; }
        .gallery-nav-button:hover { background: #fff; }
        .gallery-nav-prev { left: .75rem; }
        .gallery-nav-next { right: .75rem; }
        .gallery-thumbs { display: flex; gap: .75rem; margin-top: 1rem; padding-bottom: .5rem; overflow-x: auto; overscroll-behavior-x: contain; scrollbar-width: thin; scrollbar-color: rgba(196,106,74,.45) transparent; }
        .gallery-thumbs::-webkit-scrollbar { height: 8px; }
        .gallery-thumbs::-webkit-scrollbar-track { background: transparent; }
        .gallery-thumbs::-webkit-scrollbar-thumb { border-radius: 999px; background: rgba(196,106,74,.45); }
        .gallery-thumb { flex: 0 0 auto; width: calc((100% - 2.25rem) / 4); min-width: 11rem; height: 6rem; border-radius: 20px; overflow: hidden; border: 2px solid transparent; cursor: pointer; }
        .gallery-thumb-active { border-color: rgba(195,106,78,.35); }
        .facility-grid { display: grid; gap: .75rem; margin-top: 1.25rem; }
        .facility-tile { display: flex; align-items: center; gap: .75rem; border-radius: 22px; background: var(--surface-muted); padding: 1rem; }
        .facility-icon { background: rgba(106,122,82,.16); color: var(--olive); }
        .price-stack { margin-top: .5rem; display: flex; flex-wrap: wrap; align-items: flex-end; justify-content: space-between; gap: .75rem; }
        .price-value { font-family: 'Fraunces', 'Plus Jakarta Sans', ui-sans-serif, system-ui, sans-serif; font-weight: 700; color: var(--ink); line-height: 1.05; white-space: nowrap; font-size: clamp(1.9rem, 5vw, 2.45rem); }
        .price-period { font-size: .875rem; color: var(--muted); white-space: nowrap; }
        .nearby-row { display: grid; gap: .75rem; align-items: center; }
        @media (min-width: 640px) { .nearby-row { grid-template-columns: minmax(0,1.4fr) 180px 140px auto; } }
        .wizard-shell { border-radius: 28px; border: 1px solid var(--line); background: #fff; padding: 1rem; box-shadow: 0 10px 30px rgba(15,23,42,0.05); }
        .wizard-progress { height: 8px; overflow: hidden; border-radius: 999px; background: var(--surface-muted); }
        .wizard-progress-bar { height: 100%; width: 0%; border-radius: 999px; background: linear-gradient(90deg, var(--primary), var(--accent)); transition: width .25s ease; }
        .wizard-steps { margin-top: 1rem; display: grid; gap: .5rem; }
        @media (min-width: 640px) { .wizard-steps { grid-template-columns: repeat(4, minmax(0,1fr)); } }
        .wizard-step { border-radius: 18px; border: 1px solid var(--line); background: #fff; padding: .75rem 1rem; text-align: left; font-size: .875rem; font-weight: 700; color: var(--muted); }
        .wizard-step-active { background: var(--secondary); color: var(--primary-deep); border-color: rgba(15,118,110,.22); }
        .wizard-panel { margin-top: 1.5rem; border-radius: 30px; border: 1px solid var(--line); background: #fff; padding: 1.25rem; }
        .upload-drop { cursor: pointer; border-radius: 28px; border: 1px solid rgba(15,118,110,.12); background: var(--surface-muted); padding: 1.5rem; transition: background .2s ease, border-color .2s ease; }
        .upload-drop:hover { border-color: rgba(15,118,110,.22); background: rgba(15,118,110,.04); }
        .upload-drop-inner { text-align: center; }
        .owner-mini-cover { width: 48px; height: 48px; border-radius: 16px; object-fit: cover; flex-shrink: 0; display: block; }
        .owner-card-row { display: flex; flex-wrap: wrap; align-items: flex-start; justify-content: space-between; gap: 1rem; }
        .owner-kost-grid { display: grid; gap: 1.25rem; }
        @media (min-width: 1024px) { .owner-kost-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
        .owner-kost-card-compact .home-kost-media { height: 150px; }
        .owner-kost-card-compact .home-kost-body { padding: 1.25rem; }
        .owner-kost-card-compact .home-kost-footer { margin-top: 1rem; padding-top: 1rem; }
        .owner-kost-list { display: grid; gap: 1.5rem; }
        .owner-kost-row { display: flex; align-items: stretch; gap: 1rem; border-radius: 20px; border: 1px solid var(--line); background: #fff; padding: 1rem; box-shadow: 0 24px 60px rgba(15, 23, 42, 0.08); }
        .owner-kost-image-wrapper { position: relative; width: 140px; height: 140px; flex: 0 0 140px; }
        .owner-kost-image { position: relative; display: block; width: 100%; height: 100%; overflow: hidden; border-radius: 16px; border: 1px solid rgba(15,118,110,.08); background: var(--surface-muted); }
        .owner-kost-image img { display: block; width: 100%; height: 100%; object-fit: cover; }
        .owner-kost-info { display: flex; min-width: 0; flex: 1 1 auto; flex-direction: column; justify-content: center; }
        .owner-kost-header { margin-bottom: .5rem; }
        .owner-kost-header h3 { margin: 0; }
        .owner-kost-header a { display: block; overflow: hidden; color: var(--ink); font-size: 1rem; font-weight: 700; text-overflow: ellipsis; white-space: nowrap; }
        .owner-kost-meta { display: flex; flex-wrap: wrap; align-items: center; gap: .75rem 1rem; font-size: .75rem; }
        .owner-kost-meta-item { display: inline-flex; align-items: center; gap: .375rem; }
        .owner-kost-meta-item span:first-child { color: var(--muted); font-weight: 400; }
        .owner-kost-meta-item span:last-child { color: var(--ink); font-weight: 700; }
        .owner-kost-actions { display: flex; min-width: 140px; flex: 0 0 140px; flex-direction: column; align-items: flex-end; justify-content: space-between; gap: .75rem; }
        .owner-kost-price { text-align: right; }
        .owner-kost-price p:first-child { color: var(--muted); font-size: .75rem; }
        .owner-kost-price p:last-child { margin-top: .25rem; color: var(--primary); font-size: 1.125rem; font-weight: 800; }
        .owner-kost-buttons { display: flex; width: 100%; flex-direction: column; gap: .5rem; }
        .owner-kost-buttons a, .owner-kost-buttons button { border-radius: 12px; padding: .5rem .75rem; font-size: .75rem; font-weight: 700; text-align: center; }
        .owner-kost-buttons .solid-button, .owner-kost-buttons .ghost-button { min-height: auto; box-shadow: none; }
        @media (max-width: 768px) {
            .owner-kost-row { flex-wrap: wrap; }
            .owner-kost-image-wrapper { width: 100px; height: 100px; flex-basis: 100px; }
            .owner-kost-actions { min-width: 100%; flex-basis: 100%; flex-direction: row; align-items: center; }
            .owner-kost-buttons { width: auto; flex-direction: row; }
        }
        .owner-kost-item { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 1.25rem; border-radius: 28px; border: 1px solid var(--line); background: #fff; padding: 1.25rem; box-shadow: 0 24px 60px rgba(15, 23, 42, 0.08); }
        @media (min-width: 768px) { .owner-kost-item { flex-wrap: nowrap; } }
        .owner-kost-left { display: flex; min-width: 0; align-items: center; gap: 1rem; }
        .owner-kost-thumb { width: 72px; height: 72px; border-radius: 22px; overflow: hidden; flex-shrink: 0; background: var(--surface-muted); border: 1px solid rgba(15,118,110,0.08); }
        .owner-kost-thumb img { width: 100%; height: 100%; object-fit: cover; display: block; }
        .owner-kost-center { min-width: 0; }
        .owner-kost-title { font-weight: 700; color: var(--ink); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 44ch; }
        .owner-kost-subtitle { margin-top: .25rem; font-size: .75rem; color: var(--muted); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 54ch; }
        .owner-kost-meta { display: flex; flex-wrap: wrap; align-items: center; gap: .75rem; font-size: .875rem; color: var(--muted); }
        .owner-kost-meta strong { color: var(--ink); }
        .owner-kost-actions { display: flex; flex-wrap: wrap; align-items: center; justify-content: flex-end; gap: .5rem; }
        .status-badge { display: inline-flex; align-items: center; border-radius: 999px; padding: .55rem 1rem; font-size: .875rem; font-weight: 600; }
        .status-available { background: rgba(106,122,82,.14); color: #4d6d55; }
        .status-limited { background: rgba(228,182,87,.18); color: #93691a; }
        .status-full { background: rgba(209,91,91,.16); color: #a44747; }
        .payment-choice { display: flex; cursor: pointer; align-items: center; gap: .75rem; border-radius: 22px; border: 1px solid var(--line); background: #fff; padding: .75rem 1rem; font-size: .875rem; font-weight: 700; color: var(--ink); transition: border-color .2s ease, background .2s ease, color .2s ease; }
        .payment-choice:has(input:checked) { border-color: rgba(15,118,110,.32); background: var(--secondary); color: var(--primary-deep); }
        .payment-detail-panel { border-radius: 24px; border: 1px solid var(--line); background: var(--surface-muted); padding: 1rem; }
        .mini-meta { font-size: .875rem; color: var(--muted); }
        .map-embed-shell { overflow: hidden; border-radius: 28px; border: 1px solid var(--line); background: var(--surface-muted); }
        .map-embed-frame { width: 100%; height: 320px; border: 0; }
        .contact-action { display: inline-flex; align-items: center; justify-content: center; border-radius: 24px; padding: 1rem 1.25rem; font-size: .875rem; font-weight: 600; }
        .contact-action-wa { background: #e8f6eb; color: #2b7d48; }
        .contact-action-email { background: #fff3eb; color: var(--terracotta-deep); }
        .contact-action-map { background: #f4f1ed; color: var(--ink); }
        .table-kost-title { display: flex; flex-direction: column; gap: .25rem; }
        .table-actions { display: flex; flex-wrap: wrap; gap: .5rem; }
        .table-link { display: inline-flex; align-items: center; border-radius: 999px; padding: .5rem 1rem; font-size: .875rem; font-weight: 600; color: var(--ink); }
        .table-link-success { color: var(--olive); }
        .table-link-danger { color: #b74e4e; }
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
        .home-search-panel.is-submitting {
            transform: translateY(-2px);
            border-color: rgba(15,118,110,.26);
            box-shadow: 0 34px 80px rgba(15,23,42,.13);
        }
        .home-search-button { position: relative; }
        .home-search-panel.is-submitting .home-search-button {
            color: transparent;
            pointer-events: none;
        }
        .home-search-panel.is-submitting .home-search-button::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 1.15rem;
            height: 1.15rem;
            margin: -.575rem 0 0 -.575rem;
            border-radius: 999px;
            border: 2px solid rgba(255,255,255,.45);
            border-top-color: #fff;
            animation: spin .75s linear infinite;
        }
        [data-search-results].is-loading {
            opacity: .82;
            transition: opacity .25s ease;
        }
        [data-loading-region].is-loading .home-kost-card,
        [data-loading-region].is-loading .search-result-card {
            position: relative;
            overflow: hidden;
            opacity: .72;
            transform: translateY(2px);
            transition: opacity .25s ease, transform .25s ease;
        }
        [data-loading-region].is-loading .home-kost-card::after,
        [data-loading-region].is-loading .search-result-card::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,.65), transparent);
            animation: shimmer 1s linear infinite;
        }
        @keyframes shimmer {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
        }
        @keyframes spin {
            to { transform: rotate(360deg); }
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
        .solid-button, .ghost-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 999px;
            padding: .95rem 1.5rem;
            font-size: .875rem;
            font-weight: 600;
            transition: .2s ease;
        }
        .solid-button {
            background: linear-gradient(135deg, var(--primary), var(--primary-deep));
            box-shadow: 0 18px 40px rgba(195, 106, 78, .18);
            color: #fff;
        }
        .ghost-button {
            border: 1px solid var(--line);
            background: #fff;
            color: var(--ink);
        }
        .search-result-card {
            display: grid;
            gap: 1.25rem;
            padding: 1rem;
            overflow: hidden;
        }
        .search-result-media {
            overflow: hidden;
            border-radius: 26px;
            background: var(--surface-muted);
            min-height: 220px;
        }
        .search-result-main {
            padding: .25rem 0;
        }
        .search-result-side {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            gap: 1.25rem;
            border-radius: 26px;
            background: var(--surface-muted);
            padding: 1.25rem;
        }
        .search-result-location {
            color: var(--olive);
            font-size: .875rem;
            font-weight: 600;
            letter-spacing: .18em;
            text-transform: uppercase;
        }
        @media (min-width: 1024px) {
            .detail-layout {
                grid-template-columns: minmax(0, 1fr) 360px;
            }
            .search-result-card {
                grid-template-columns: 280px minmax(0, 1fr) 220px;
                padding: 1.25rem;
            }
            .owner-hero {
                grid-template-columns: minmax(0, 1.3fr) 340px;
                align-items: start;
            }
            .owner-hero-strip {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }
            .sticky-panel {
                position: sticky;
                top: 6rem;
                align-self: start;
            }
            .travel-hero {
                padding: 2rem;
            }
        }
    </style>
    <script>
        function formatCurrency(input) {
            let value = input.value.replace(/[^\d]/g, '');
            value = value.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            input.value = value ? 'Rp ' + value : '';
        }

        function cleanCurrency(input) {
            return input.value.replace(/[^\d]/g, '');
        }

        document.addEventListener('DOMContentLoaded', function() {
            document.body.classList.add('app-ready');

            const menuToggle = document.querySelector('[data-menu-toggle]');
            const mobileMenu = document.querySelector('[data-mobile-menu]');
            if (menuToggle && mobileMenu) {
                menuToggle.addEventListener('click', function() {
                    mobileMenu.classList.toggle('hidden');
                });
            }

            const mainImage = document.querySelector('[data-gallery-main]');
            const thumbs = document.querySelectorAll('[data-gallery-thumb]');
            const prevButton = document.querySelector('[data-gallery-prev]');
            const nextButton = document.querySelector('[data-gallery-next]');
            let activeIndex = Array.from(thumbs).findIndex(function(thumb) {
                return thumb.classList.contains('gallery-thumb-active');
            });
            activeIndex = activeIndex >= 0 ? activeIndex : 0;

            function setActiveImage(index) {
                if (!mainImage || !thumbs.length) {
                    return;
                }

                activeIndex = (index + thumbs.length) % thumbs.length;
                const activeThumb = thumbs[activeIndex];

                if (activeThumb.dataset.image) {
                    mainImage.src = activeThumb.dataset.image;
                }

                thumbs.forEach(function(item) {
                    item.classList.remove('gallery-thumb-active');
                });
                activeThumb.classList.add('gallery-thumb-active');
            }

            thumbs.forEach(function(thumb, index) {
                thumb.addEventListener('click', function() {
                    setActiveImage(index);
                });
            });

            if (prevButton) {
                prevButton.addEventListener('click', function() {
                    setActiveImage(activeIndex - 1);
                });
            }

            if (nextButton) {
                nextButton.addEventListener('click', function() {
                    setActiveImage(activeIndex + 1);
                });
            }

            const searchForm = document.querySelector('[data-search-form]');
            const loadingRegion = document.querySelector('[data-loading-region]') || document.querySelector('[data-search-results]');
            const resultsRegion = document.querySelector('[data-search-results]');

            if (searchForm) {
                const query = new URLSearchParams(window.location.search);
                const shouldFocusResults = sessionStorage.getItem('kostFinderSearchSubmitted') === '1'
                    || ['search', 'quick_location', 'max_price', 'sort'].some(function(key) {
                        return query.has(key) && query.get(key);
                    });

                if (resultsRegion && shouldFocusResults) {
                    sessionStorage.removeItem('kostFinderSearchSubmitted');
                    window.setTimeout(function() {
                        resultsRegion.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    }, 160);
                }

                searchForm.addEventListener('submit', function() {
                    sessionStorage.setItem('kostFinderSearchSubmitted', '1');
                    searchForm.classList.add('is-submitting');
                    searchForm.setAttribute('aria-busy', 'true');
                    if (loadingRegion) {
                        loadingRegion.classList.add('is-loading');
                    }
                });
            }

            // Format on input for price fields
            document.querySelectorAll('input[name="harga"], input[name="max_price"]').forEach(function(input) {
                input.addEventListener('input', function() {
                    formatCurrency(input);
                });
            });

            // Initial format for existing values
            document.querySelectorAll('input[name="harga"], input[name="max_price"]').forEach(function(input) {
                if (input.value) {
                    formatCurrency(input);
                }
            });

            // Clean on submit
            document.querySelectorAll('input[name="harga"], input[name="max_price"]').forEach(function(input) {
                input.form?.addEventListener('submit', function() {
                    input.value = cleanCurrency(input);
                });
            });
        });
    </script>
@endif
