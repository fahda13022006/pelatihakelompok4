@extends('skine.layout')

@section('page-title', 'Distribusi Produk (GIS)')

@section('page-content')
    <div class="panel">
        <div class="panel-header">
            <h3>Distribusi Produk (GIS)</h3>
        </div>

        <div class="map-toolbar">
            <div class="field">
                <label for="distributionStatusFilter">Filter status</label>
                <select id="distributionStatusFilter">
                    <option value="all">Semua Pesanan</option>
                    <option value="Diproses">Diproses</option>
                    <option value="Dikemas">Dikemas</option>
                    <option value="Dikirim">Dikirim</option>
                    <option value="Tiba">Tiba</option>
                    <option value="Dibatalkan">Dibatalkan</option>
                </select>
            </div>
            <div class="field">
                <label for="distributionCityFilter">Filter kota</label>
                <select id="distributionCityFilter">
                    <option value="all">Semua Kota</option>
                </select>
            </div>
            <div class="field" style="flex: 1; min-width: 220px;">
                <label for="distributionSearch">Cari daerah</label>
                <input id="distributionSearch" type="text" placeholder="Cari nama daerah tujuan" />
            </div>
        </div>

        <div class="row-grid">
            <div class="map-panel">
                <div id="distributionMap"></div>
            </div>
            <div class="panel">
                <div class="panel-header">
                    <h3>Ringkasan Distribusi</h3>
                </div>
                <div id="distributionSummary" class="distribution-summary"></div>
            </div>
        </div>
    </div>
@endsection

@section('page-script')
<script>
    const STORAGE_KEYS = {
        products: 'skine_products_v1',
        orders: 'skine_orders_v1',
        store: 'skine_store_v1'
    };

    function readJson(key, fallback) {
        try {
            const value = localStorage.getItem(key);
            if (!value) return fallback;
            return JSON.parse(value);
        } catch (error) {
            return fallback;
        }
    }

    function safeStore() {
        const value = readJson(STORAGE_KEYS.store, null);
        return value || {
            name: 'SKINÉ Skincare Store',
            address: 'Jl. Pahlawan No. 88, Indramayu',
            city: 'Indramayu',
            province: 'Jawa Barat',
            latitude: -6.3260,
            longitude: 108.3200
        };
    }

    function safeOrders() {
        const value = readJson(STORAGE_KEYS.orders, []);
        return Array.isArray(value) ? value : [];
    }

    function getStatusColor(status) {
        const map = {
            Diproses: '#7a7d82',
            Dikemas: '#d6b460',
            Dikirim: '#3d7ca5',
            Tiba: '#4f8d67',
            Dibatalkan: '#d95e5e'
        };
        return map[status] || '#66765F';
    }

    function escapeHtml(raw) {
        return String(raw || '').replace(/[&<>"']/g, (char) => ({
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        }[char]));
    }

    function createPopupHtml(order) {
        const productNames = (order.items || []).map((item) => `${item.name} (${item.quantity})`).join(', ');
        return `
            <div style="font-family:Segoe UI,sans-serif;line-height:1.5;min-width:220px;">
                <strong style="font-size:14px;display:block;margin-bottom:6px;">${escapeHtml(order.orderCode)}</strong>
                <div><strong>Nama:</strong> ${escapeHtml(order.customerName)}</div>
                <div><strong>Produk:</strong> ${escapeHtml(productNames)}</div>
                <div><strong>Jumlah:</strong> ${(order.items || []).reduce((sum, item) => sum + Number(item.quantity || 0), 0)}</div>
                <div><strong>Total:</strong> ${new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(Number(order.total || 0))}</div>
                <div><strong>Daerah:</strong> ${escapeHtml(order.city || '-')}</div>
                <div><strong>Tanggal:</strong> ${escapeHtml(order.purchaseDate || '-')}</div>
                <div><strong>Status:</strong> ${escapeHtml(order.status || 'Diproses')}</div>
                <div><strong>Latitude:</strong> ${escapeHtml(order.latitude || '-')}</div>
                <div><strong>Longitude:</strong> ${escapeHtml(order.longitude || '-')}</div>
                <div style="margin-top:10px;">
                    <a href="/skincare/orders" style="display:inline-block;background:#66765F;color:white;padding:7px 10px;border-radius:8px;text-decoration:none;">Lihat Detail Pesanan</a>
                </div>
            </div>
        `;
    }

    function renderCityOptions() {
        const citySelect = document.getElementById('distributionCityFilter');
        const orders = safeOrders();
        const cities = [...new Set(orders.map((order) => order.city).filter(Boolean))].sort();
        citySelect.innerHTML = '<option value="all">Semua Kota</option>' + cities.map((city) => `<option value="${city}">${city}</option>`).join('');
    }

    function getFilteredOrders() {
        const orders = safeOrders();
        const statusValue = document.getElementById('distributionStatusFilter').value;
        const cityValue = document.getElementById('distributionCityFilter').value;
        const searchValue = document.getElementById('distributionSearch').value.trim().toLowerCase();

        return orders.filter((order) => {
            const matchesStatus = statusValue === 'all' || order.status === statusValue;
            const matchesCity = cityValue === 'all' || order.city === cityValue;
            const matchesSearch = !searchValue || String(order.city || '').toLowerCase().includes(searchValue);
            return matchesStatus && matchesCity && matchesSearch;
        });
    }

    function renderSummary(filteredOrders) {
        const summary = document.getElementById('distributionSummary');
        summary.innerHTML = '';

        const group = {};
        filteredOrders.forEach((order) => {
            const city = order.city || 'Lainnya';
            if (!group[city]) {
                group[city] = { city, total: 0, products: 0, processed: 0, shipped: 0, arrived: 0 };
            }
            group[city].total += 1;
            group[city].products += (order.items || []).reduce((sum, item) => sum + Number(item.quantity || 0), 0);
            if (order.status === 'Diproses') group[city].processed += 1;
            if (order.status === 'Dikirim') group[city].shipped += 1;
            if (order.status === 'Tiba') group[city].arrived += 1;
        });

        const entries = Object.values(group).sort((a, b) => b.total - a.total);
        if (!entries.length) {
            summary.innerHTML = '<div class="empty-state">Belum ada data distribusi untuk filter yang dipilih.</div>';
            return;
        }

        entries.forEach((entry) => {
            const item = document.createElement('div');
            item.className = 'summary-item';
            item.innerHTML = `
                <div>
                    <strong>${escapeHtml(entry.city)}</strong>
                    <div style="color: var(--muted); font-size: 0.8rem;">${entry.total} pesanan</div>
                </div>
                <div style="font-size: 0.8rem; text-align: right; line-height: 1.7;">
                    <div>Produk: ${entry.products}</div>
                    <div>Diproses: ${entry.processed}</div>
                    <div>Dikirim: ${entry.shipped}</div>
                    <div>Tiba: ${entry.arrived}</div>
                </div>
            `;
            summary.appendChild(item);
        });
    }

    function renderDistributionMap() {
        const mapElement = document.getElementById('distributionMap');
        const store = safeStore();
        const filteredOrders = getFilteredOrders();

        if (!mapElement || typeof L === 'undefined') return;

        const map = L.map('distributionMap', { scrollWheelZoom: false });
        map.setView([-6.8, 108.0], 6);
        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        const storeIcon = L.divIcon({
            className: 'skine-store-marker',
            html: '<div style="width:18px;height:18px;border-radius:50%;background:#66765F;border:2px solid white;box-shadow:0 4px 10px rgba(0,0,0,0.2);"></div>',
            iconSize: [18, 18],
            iconAnchor: [9, 9]
        });

        L.marker([Number(store.latitude || -6.3260), Number(store.longitude || 108.3200)], { icon: storeIcon }).addTo(map)
            .bindPopup(`<strong>${escapeHtml(store.name || 'SKINÉ Skincare Store')}</strong><br>${escapeHtml(store.address || '')}<br>${escapeHtml(store.city || 'Indramayu')}`);

        filteredOrders.forEach((order) => {
            if (!Number.isFinite(Number(order.latitude)) || !Number.isFinite(Number(order.longitude))) return;
            const marker = L.circleMarker([Number(order.latitude), Number(order.longitude)], {
                radius: 8,
                color: '#ffffff',
                weight: 2,
                fillColor: getStatusColor(order.status),
                fillOpacity: 1
            }).addTo(map);

            marker.bindPopup(createPopupHtml(order));

            L.polyline(
                [
                    [Number(store.latitude || -6.3260), Number(store.longitude || 108.3200)],
                    [Number(order.latitude), Number(order.longitude)]
                ],
                {
                    color: '#B99B68',
                    weight: 2,
                    dashArray: '8 10'
                }
            ).addTo(map);
        });

        const bounds = filteredOrders.map((order) => [Number(order.latitude), Number(order.longitude)]).filter((value) => value[0] && value[1]);
        if (bounds.length) {
            map.fitBounds([ [Number(store.latitude), Number(store.longitude)], ...bounds ], { padding: [30, 30] });
        } else {
            map.setView([Number(store.latitude || -6.3260), Number(store.longitude || 108.3200)], 6);
        }

        renderSummary(filteredOrders);
    }

    document.getElementById('distributionStatusFilter').addEventListener('change', renderDistributionMap);
    document.getElementById('distributionCityFilter').addEventListener('change', renderDistributionMap);
    document.getElementById('distributionSearch').addEventListener('input', renderDistributionMap);

    renderCityOptions();
    renderDistributionMap();
</script>
@endsection
