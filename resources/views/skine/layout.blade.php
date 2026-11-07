<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SKINÉ — Skincare Store & Distribution Management System</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,500;12..96,700&family=Instrument+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        :root {
            --bg: #EFEDF2;
            --card: #FAF9FB;
            --ink: #231A2B;
            --plum: #3A2347;
            --plum-soft: #5B3F6B;
            --serum: #D9EA7A;
            --border: #DDD8E3;
            --muted: #756D80;

            /* alias lama, supaya halaman lain tidak rusak */
            --primary: var(--plum-soft);
            --secondary: #E6E1EC;
            --text: var(--ink);
            --accent: var(--serum);
            --danger: #C9484F;
            --warning: #C79A2E;
            --success: #3E7F5D;
            --info: #3B6FA8;

            --font-display: "Bricolage Grotesque", "Segoe UI", sans-serif;
            --font-body: "Instrument Sans", "Segoe UI", sans-serif;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            font-family: var(--font-body);
            background: var(--bg);
            color: var(--ink);
            line-height: 1.5;
        }

        a { text-decoration: none; color: inherit; }
        button, input, select, textarea { font: inherit; }

        :focus-visible {
            outline: 2px solid var(--plum-soft);
            outline-offset: 2px;
        }

        .skine-shell {
            display: flex;
            min-height: 100vh;
        }

        /* ---------- Sidebar: satu-satunya blok gelap ---------- */
        .sidebar {
            width: 264px;
            background: var(--plum);
            color: #EDE6F2;
            padding: 28px 18px;
            position: sticky;
            top: 0;
            height: 100vh;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 0 10px 24px;
            border-bottom: 1px solid rgba(237, 230, 242, 0.16);
        }

        .brand-mark {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            background: var(--serum);
            color: var(--plum);
            display: grid;
            place-items: center;
            font-family: var(--font-display);
            font-weight: 700;
            font-size: 1.25rem;
        }

        .brand-text h1 {
            margin: 0;
            font-family: var(--font-display);
            font-size: 1.45rem;
            font-weight: 700;
            letter-spacing: 0.04em;
            line-height: 1.1;
        }

        .brand-text small {
            color: rgba(237, 230, 242, 0.6);
            font-size: 0.8rem;
        }

        .sidebar-nav {
            display: flex;
            flex-direction: column;
            gap: 2px;
            margin-top: 22px;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 11px 12px;
            border-radius: 6px;
            color: rgba(237, 230, 242, 0.72);
            font-weight: 500;
            border-left: 3px solid transparent;
            transition: background 0.15s ease, color 0.15s ease;
        }

        .nav-link:hover {
            background: rgba(237, 230, 242, 0.07);
            color: #fff;
        }

        .nav-link.active {
            background: rgba(237, 230, 242, 0.1);
            border-left-color: var(--serum);
            color: #fff;
            font-weight: 600;
        }

        .nav-icon {
            width: 22px;
            text-align: center;
            font-size: 1rem;
            opacity: 0.9;
        }

        /* ---------- Panel utama ---------- */
        .main-panel {
            flex: 1;
            min-width: 0;
            padding: 32px 36px;
        }

        .topbar {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            margin-bottom: 28px;
            gap: 16px;
        }

        .topbar h2 {
            margin: 0;
            font-family: var(--font-display);
            font-weight: 700;
            font-size: clamp(1.9rem, 3vw, 2.8rem);
            letter-spacing: -0.03em;
            line-height: 1;
        }

        .topbar-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: var(--muted);
            font-weight: 500;
            font-size: 0.92rem;
        }

        .topbar-badge::before {
            content: "";
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: var(--success);
        }

        .content {
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        .card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 10px;
        }

        /* ---------- Ringkasan: satu strip, bukan 6 kartu ---------- */
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(6, minmax(130px, 1fr));
            gap: 0;
            border-top: 3px solid var(--ink);
            border-bottom: 1px solid var(--border);
        }

        .summary-card {
            padding: 20px 20px 18px;
            background: transparent;
            border: 0;
            border-right: 1px solid var(--border);
            border-radius: 0;
            box-shadow: none;
        }

        .summary-card:last-child { border-right: 0; }

        .summary-card small {
            display: block;
            color: var(--muted);
            font-size: 0.86rem;
            margin-bottom: 14px;
            min-height: 2.6em;
        }

        .summary-card strong {
            display: block;
            font-family: var(--font-display);
            font-weight: 700;
            font-size: clamp(2.2rem, 3.4vw, 3.4rem);
            line-height: 1;
            letter-spacing: -0.04em;
            color: var(--plum);
        }

        .summary-card .trend {
            margin-top: 12px;
            font-size: 0.82rem;
            color: var(--muted);
            font-weight: 500;
        }

        .row-grid {
            display: grid;
            grid-template-columns: 1.3fr 1fr;
            gap: 20px;
            align-items: stretch;
        }

        .panel {
            padding: 24px;
            border-radius: 10px;
            background: var(--card);
            border: 1px solid var(--border);
            display: flex;
            flex-direction: column;
        }

        .panel-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 18px;
        }

        .panel-header h3 {
            margin: 0;
            font-family: var(--font-display);
            font-weight: 700;
            font-size: 1.3rem;
            letter-spacing: -0.02em;
        }

        .button {
            border: 0;
            background: var(--plum);
            color: #fff;
            padding: 10px 18px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: background 0.15s ease;
        }

        .button:hover { background: var(--plum-soft); }
        .button.secondary { background: var(--secondary); color: var(--ink); }
        .button.secondary:hover { background: #DAD3E2; }
        .button.danger { background: var(--danger); }
        .button.accent { background: var(--serum); color: var(--plum); }
        .button.accent:hover { background: #CFE266; }

        /* ---------- Grafik: tinggi mengikuti peta ---------- */
        .chart-bars {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            gap: 18px;
            flex: 1;
            height: 360px;
            padding: 22px 8px 0;
            border-bottom: 2px solid var(--ink);
        }

        .chart-bar-wrap {
            flex: 1;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
            align-items: center;
            gap: 8px;
        }

        .chart-bar {
            width: 100%;
            max-width: 56px;
            background: var(--plum);
            border-radius: 4px 4px 0 0;
            min-height: 8px;
            position: relative;
        }

        .chart-bar-wrap:hover .chart-bar { background: var(--serum); }

        .chart-label {
            font-size: 0.8rem;
            color: var(--muted);
            font-weight: 500;
            text-align: center;
            position: relative;
            top: 30px;
            margin-top: -22px;
        }

        .chart-value {
            font-family: var(--font-display);
            font-size: 1rem;
            font-weight: 700;
            color: var(--ink);
        }

        .table-wrap {
            overflow: auto;
            width: 100%;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 720px;
        }

        th, td {
            padding: 14px 10px;
            border-bottom: 1px solid var(--border);
            text-align: left;
            vertical-align: top;
        }

        th {
            color: var(--muted);
            font-size: 0.84rem;
            font-weight: 600;
            border-bottom: 2px solid var(--ink);
        }

        .badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 5px 10px;
            border-radius: 6px;
            font-size: 0.78rem;
            font-weight: 600;
            white-space: nowrap;
        }

        .badge.status-diproses { background: #E6E1EC; color: #4A4256; }
        .badge.status-dikemas { background: #F4E9C4; color: #7A5C10; }
        .badge.status-dikirim { background: var(--serum); color: var(--plum); }
        .badge.status-tiba { background: #CFE7D8; color: #24573D; }
        .badge.status-dibatalkan { background: #F2D4D6; color: #8A2A31; }

        .actions {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .mini-btn {
            border: 1px solid var(--border);
            background: var(--card);
            color: var(--ink);
            padding: 7px 11px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 500;
        }

        .mini-btn:hover { border-color: var(--plum-soft); }
        .mini-btn.danger { border-color: rgba(201, 72, 79, 0.3); color: var(--danger); }
        .mini-btn.primary { border-color: rgba(91, 63, 107, 0.3); color: var(--plum-soft); }

        .product-card {
            display: flex;
            gap: 12px;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid var(--border);
        }

        .product-thumb {
            width: 56px;
            height: 56px;
            border-radius: 8px;
            object-fit: cover;
            border: 1px solid var(--border);
            background: #E6E1EC;
        }

        .product-meta h4 { margin: 0 0 4px; font-family: var(--font-display); }
        .product-meta small { color: var(--muted); }

        .toolbar {
            display: flex;
            gap: 12px;
            align-items: center;
            flex-wrap: wrap;
        }

        .field, .input-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
            min-width: 140px;
        }

        .field label, .input-group label {
            font-size: 0.85rem;
            color: var(--muted);
            font-weight: 600;
        }

        input, select, textarea {
            width: 100%;
            border: 1px solid var(--border);
            border-radius: 6px;
            background: #fff;
            color: var(--ink);
            padding: 10px 12px;
        }

        input:focus, select:focus, textarea:focus {
            border-color: var(--plum-soft);
            outline: 2px solid rgba(91, 63, 107, 0.2);
        }

        textarea {
            min-height: 90px;
            resize: vertical;
        }

        .split-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(220px, 1fr));
            gap: 16px;
        }

        .modal {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(35, 26, 43, 0.55);
            z-index: 999;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .modal.open { display: flex; }

        .modal-card {
            width: min(1000px, 100%);
            max-height: 90vh;
            overflow: auto;
            background: var(--card);
            border-radius: 12px;
            border: 1px solid var(--border);
            padding: 24px;
        }

        .modal-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 18px;
        }

        .modal-header h3 {
            margin: 0;
            font-family: var(--font-display);
            font-size: 1.5rem;
            letter-spacing: -0.02em;
        }

        .modal-close {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            border: 1px solid var(--border);
            background: #fff;
            cursor: pointer;
            font-size: 1.2rem;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(180px, 1fr));
            gap: 16px;
        }

        .full-span { grid-column: 1 / -1; }

        .order-items {
            display: flex;
            flex-direction: column;
            gap: 12px;
            margin-top: 8px;
        }

        .order-item-row {
            display: grid;
            grid-template-columns: 1.6fr 0.8fr 0.8fr 0.8fr auto;
            gap: 10px;
            padding: 12px;
            border: 1px solid var(--border);
            border-radius: 8px;
            background: #F4F2F7;
            align-items: end;
        }

        .map-panel {
            min-height: 270px;
            border-radius: 10px;
            overflow: hidden;
            border: 1px solid var(--border);
        }

        #distributionMap, #miniMap, #storeMap {
            width: 100%;
            height: 360px;
            background: #E6E1EC;
        }

        .map-toolbar {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-bottom: 16px;
        }

        .distribution-summary {
            display: grid;
            gap: 10px;
        }

        .summary-item {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            padding: 12px 14px;
            border: 1px solid var(--border);
            border-radius: 8px;
            background: #F4F2F7;
        }

        .empty-state {
            padding: 20px;
            text-align: center;
            color: var(--muted);
            border: 1px dashed var(--border);
            border-radius: 8px;
            background: #F4F2F7;
        }

        @media (max-width: 1100px) {
            .summary-grid { grid-template-columns: repeat(3, minmax(130px, 1fr)); }
            .summary-card { border-bottom: 1px solid var(--border); }
            .summary-card:nth-child(3n) { border-right: 0; }
            .summary-card:nth-last-child(-n+3) { border-bottom: 0; }
            .row-grid { grid-template-columns: 1fr; }
        }

        @media (max-width: 820px) {
            .skine-shell { display: block; }
            .sidebar {
                width: 100%;
                height: auto;
                position: static;
            }
            .main-panel { padding: 22px 16px 28px; }
            .summary-grid { grid-template-columns: repeat(2, minmax(130px, 1fr)); }
            .summary-card:nth-child(3n) { border-right: 1px solid var(--border); }
            .summary-card:nth-child(2n) { border-right: 0; }
            .summary-card:nth-last-child(-n+3) { border-bottom: 1px solid var(--border); }
            .summary-card:nth-last-child(-n+2) { border-bottom: 0; }
            .split-grid, .form-grid { grid-template-columns: 1fr; }
        }

        @media (max-width: 540px) {
            .summary-grid { grid-template-columns: 1fr; }
            .summary-card { border-right: 0 !important; border-bottom: 1px solid var(--border) !important; }
            .summary-card:last-child { border-bottom: 0 !important; }
            .topbar { flex-direction: column; align-items: flex-start; }
            .order-item-row { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <div class="skine-shell">
        <aside class="sidebar">
            <div class="brand">
                <div class="brand-mark">S</div>
                <div class="brand-text">
                    <h1>SKINÉ</h1>
                    <small>Skincare Store</small>
                </div>
            </div>

            <nav class="sidebar-nav">
                <a href="{{ route('skine.dashboard') }}" class="nav-link {{ request()->routeIs('skine.dashboard') ? 'active' : '' }}">
                    <span class="nav-icon">◫</span>
                    Dashboard
                </a>
                <a href="{{ route('skine.products') }}" class="nav-link {{ request()->routeIs('skine.products') ? 'active' : '' }}">
                    <span class="nav-icon">▣</span>
                    Data Produk
                </a>
                <a href="{{ route('skine.orders') }}" class="nav-link {{ request()->routeIs('skine.orders') ? 'active' : '' }}">
                    <span class="nav-icon">☰</span>
                    Data Pesanan
                </a>
                <a href="{{ route('skine.distribution') }}" class="nav-link {{ request()->routeIs('skine.distribution') ? 'active' : '' }}">
                    <span class="nav-icon">⌖</span>
                    Distribusi Produk (GIS)
                </a>
                <a href="{{ route('skine.store') }}" class="nav-link {{ request()->routeIs('skine.store') ? 'active' : '' }}">
                    <span class="nav-icon">⌂</span>
                    Lokasi Toko
                </a>
                <a href="#" class="nav-link">
                    <span class="nav-icon">⚙</span>
                    Pengaturan
                </a>
                <a href="/" class="nav-link">
                    <span class="nav-icon">🗺</span>
                    GIS Praktikum Lama
                </a>
            </nav>
        </aside>

        <main class="main-panel">
            <header class="topbar">
                <h2>@yield('page-title', 'SKINÉ')</h2>
                <div class="topbar-badge">Indramayu Distribution Center</div>
            </header>

            <div class="content">
                @yield('page-content')
            </div>
        </main>
    </div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    @yield('page-script')
</body>
</html>