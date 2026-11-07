<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SKINÉ — Skincare Store & Distribution Management System</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        :root {
            --bg: #F7F6F2;
            --primary: #66765F;
            --secondary: #E8E4D9;
            --card: #FFFFFF;
            --text: #343A34;
            --accent: #B99B68;
            --border: #E9E7E0;
            --muted: #6c757d;
            --danger: #d75b5b;
            --warning: #d6b460;
            --success: #4f8d67;
            --info: #3d7ca5;
            --shadow: 0 16px 32px rgba(52, 58, 52, 0.08);
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            background: var(--bg);
            color: var(--text);
        }

        a { text-decoration: none; color: inherit; }
        button, input, select, textarea {
            font: inherit;
        }

        .skine-shell {
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: 260px;
            background: var(--card);
            border-right: 1px solid var(--border);
            padding: 24px 18px;
            position: sticky;
            top: 0;
            height: 100vh;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 10px 20px;
            border-bottom: 1px solid var(--border);
            margin-bottom: 18px;
        }

        .brand-mark {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            background: linear-gradient(135deg, var(--primary), #82957a);
            color: white;
            display: grid;
            place-items: center;
            font-weight: 700;
            font-size: 1.15rem;
        }

        .brand-text h1 {
            margin: 0;
            font-size: 1.4rem;
            letter-spacing: 0.08em;
        }

        .brand-text small {
            color: var(--muted);
        }

        .sidebar-nav {
            display: flex;
            flex-direction: column;
            gap: 8px;
            margin-top: 18px;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 14px;
            border-radius: 12px;
            color: var(--text);
            transition: 0.2s ease;
            border: 1px solid transparent;
            font-weight: 600;
        }

        .nav-link:hover {
            background: rgba(102, 118, 95, 0.06);
            border-color: var(--border);
        }

        .nav-link.active {
            background: rgba(102, 118, 95, 0.12);
            border-color: rgba(102, 118, 95, 0.15);
            color: var(--primary);
        }

        .nav-icon {
            width: 22px;
            text-align: center;
            font-size: 1rem;
        }

        .main-panel {
            flex: 1;
            padding: 28px;
        }

        .topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 24px;
            gap: 16px;
        }

        .topbar h2 {
            margin: 0;
            font-size: clamp(1.5rem, 2vw, 2.2rem);
        }

        .topbar-badge {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 999px;
            padding: 10px 16px;
            color: var(--primary);
            font-weight: 600;
        }

        .content {
            display: flex;
            flex-direction: column;
            gap: 22px;
        }

        .card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 18px;
            box-shadow: var(--shadow);
        }

        .summary-grid {
            display: grid;
            grid-template-columns: repeat(6, minmax(140px, 1fr));
            gap: 16px;
        }

        .summary-card {
            padding: 18px 18px 14px;
            border-radius: 18px;
            background: var(--card);
            border: 1px solid var(--border);
            box-shadow: var(--shadow);
        }

        .summary-card small {
            display: block;
            color: var(--muted);
            margin-bottom: 10px;
        }

        .summary-card strong {
            font-size: clamp(1.4rem, 2vw, 2.2rem);
            display: block;
        }

        .summary-card .trend {
            margin-top: 8px;
            font-size: 0.8rem;
            color: var(--success);
            font-weight: 600;
        }

        .row-grid {
            display: grid;
            grid-template-columns: 1.3fr 1fr;
            gap: 18px;
        }

        .panel {
            padding: 22px;
            border-radius: 18px;
            background: var(--card);
            border: 1px solid var(--border);
            box-shadow: var(--shadow);
        }

        .panel-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 18px;
        }

        .panel-header h3 {
            margin: 0;
            font-size: 1.2rem;
        }

        .button {
            border: 0;
            background: var(--primary);
            color: white;
            padding: 10px 16px;
            border-radius: 12px;
            cursor: pointer;
            font-weight: 600;
            transition: 0.2s ease;
        }

        .button:hover { filter: brightness(0.98); }
        .button.secondary { background: var(--secondary); color: var(--text); }
        .button.danger { background: #d95e5e; }
        .button.accent { background: var(--accent); color: var(--text); }

        .chart-bars {
            display: flex;
            align-items: end;
            justify-content: space-between;
            gap: 14px;
            height: 220px;
            padding: 18px 8px 0;
            border-top: 1px solid var(--border);
            border-bottom: 1px solid var(--border);
        }

        .chart-bar-wrap {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
        }

        .chart-bar {
            width: 100%;
            max-width: 40px;
            background: linear-gradient(180deg, #9ea98f 0%, var(--primary) 100%);
            border-radius: 10px 10px 0 0;
            min-height: 8px;
            position: relative;
        }

        .chart-label {
            font-size: 0.78rem;
            color: var(--muted);
            font-weight: 600;
            text-align: center;
        }

        .chart-value {
            font-size: 0.75rem;
            font-weight: 700;
            color: var(--text);
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
            padding: 12px 10px;
            border-bottom: 1px solid var(--border);
            text-align: left;
            vertical-align: top;
        }

        th {
            color: var(--muted);
            font-size: 0.76rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            font-weight: 700;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 6px 10px;
            border-radius: 999px;
            font-size: 0.74rem;
            font-weight: 700;
            border: 1px solid transparent;
            white-space: nowrap;
        }

        .badge.status-diproses { background: rgba(108, 117, 125, 0.1); color: #4e595e; }
        .badge.status-dikemas { background: rgba(214, 180, 96, 0.15); color: #8a6b20; }
        .badge.status-dikirim { background: rgba(61, 124, 165, 0.12); color: #21588f; }
        .badge.status-tiba { background: rgba(79, 141, 103, 0.12); color: #2f6844; }
        .badge.status-dibatalkan { background: rgba(217, 92, 92, 0.12); color: #8d2e2e; }

        .actions {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .mini-btn {
            border: 1px solid var(--border);
            background: white;
            color: var(--text);
            padding: 7px 10px;
            border-radius: 10px;
            cursor: pointer;
            font-weight: 600;
        }

        .mini-btn.danger { border-color: rgba(217, 92, 92, 0.2); color: var(--danger); }
        .mini-btn.primary { border-color: rgba(102, 118, 95, 0.2); color: var(--primary); }

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
            border-radius: 12px;
            object-fit: cover;
            border: 1px solid var(--border);
            background: #f3f0ea;
        }

        .product-meta h4 { margin: 0 0 4px; }
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
            font-size: 0.8rem;
            color: var(--muted);
            font-weight: 700;
        }

        input, select, textarea {
            width: 100%;
            border: 1px solid var(--border);
            border-radius: 10px;
            background: var(--card);
            color: var(--text);
            padding: 10px 12px;
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
            background: rgba(20, 22, 21, 0.45);
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
            border-radius: 20px;
            border: 1px solid var(--border);
            box-shadow: var(--shadow);
            padding: 22px;
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
            font-size: 1.4rem;
        }

        .modal-close {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            border: 1px solid var(--border);
            background: white;
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
            border-radius: 14px;
            background: #fafaf7;
            align-items: end;
        }

        .map-panel {
            min-height: 270px;
            border-radius: 18px;
            overflow: hidden;
            border: 1px solid var(--border);
        }

        #distributionMap, #miniMap, #storeMap {
            width: 100%;
            height: 360px;
            background: #edf2eb;
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
            border-radius: 12px;
            background: #fafaf7;
        }

        .empty-state {
            padding: 20px;
            text-align: center;
            color: var(--muted);
            border: 1px dashed var(--border);
            border-radius: 12px;
            background: #fafaf7;
        }

        @media (max-width: 1100px) {
            .summary-grid { grid-template-columns: repeat(3, minmax(140px, 1fr)); }
            .row-grid { grid-template-columns: 1fr; }
        }

        @media (max-width: 820px) {
            .skine-shell { display: block; }
            .sidebar {
                width: 100%;
                height: auto;
                position: static;
                border-right: none;
                border-bottom: 1px solid var(--border);
            }
            .main-panel { padding: 20px 16px 28px; }
            .summary-grid { grid-template-columns: repeat(2, minmax(140px, 1fr)); }
            .split-grid, .form-grid { grid-template-columns: 1fr; }
        }

        @media (max-width: 540px) {
            .summary-grid { grid-template-columns: 1fr; }
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
