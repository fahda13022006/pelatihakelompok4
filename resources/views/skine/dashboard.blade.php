@extends('skine.layout')

@section('page-title', 'Dashboard')

@section('page-content')
    <div class="summary-grid">
        <div class="summary-card">
            <small>Total Produk</small>
            <strong id="totalProductsValue">0</strong>
            <div class="trend">Live inventory</div>
        </div>
        <div class="summary-card">
            <small>Total Stok Produk</small>
            <strong id="totalStockValue">0</strong>
            <div class="trend">Available items</div>
        </div>
        <div class="summary-card">
            <small>Total Pesanan</small>
            <strong id="totalOrdersValue">0</strong>
            <div class="trend">All transactions</div>
        </div>
        <div class="summary-card">
            <small>Pesanan Dalam Pengiriman</small>
            <strong id="shippingOrdersValue">0</strong>
            <div class="trend">In transit</div>
        </div>
        <div class="summary-card">
            <small>Pesanan Sudah Tiba</small>
            <strong id="arrivedOrdersValue">0</strong>
            <div class="trend">Delivered</div>
        </div>
        <div class="summary-card">
            <small>Total Daerah Tujuan</small>
            <strong id="regionsValue">0</strong>
            <div class="trend">Distinct cities</div>
        </div>
    </div>

    <div class="row-grid">
        <div class="panel">
            <div class="panel-header">
                <h3>Grafik Distribusi Pesanan</h3>
            </div>
            <div id="distributionChart" class="chart-bars"></div>
        </div>

        <div class="panel">
            <div class="panel-header">
                <h3>Peta Distribusi Mini</h3>
            </div>
            <div id="miniMap" class="map-panel"></div>
        </div>
    </div>

    <div class="row-grid">
        <div class="panel">
            <div class="panel-header">
                <h3>Pesanan Terbaru</h3>
            </div>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Kode Pesanan</th>
                            <th>Nama Pelanggan</th>
                            <th>Produk</th>
                            <th>Daerah</th>
                            <th>Tgl. Beli</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody id="recentOrdersBody"></tbody>
                </table>
            </div>
        </div>

        <div class="panel">
            <div class="panel-header">
                <h3>Produk Terbaru</h3>
            </div>
            <div id="recentProductsList"></div>
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

    const defaultProducts = [
        { id: 'prod-001', code: 'SK-001', name: 'Hydrating Cleanser', brand: 'SKINÉ', category: 'Facial Wash', price: 120000, stock: 28, description: 'Gel pembersih lembut untuk kulit sensitif.', image: 'https://images.unsplash.com/photo-1556228578-8c89e6adf883?auto=format&fit=crop&w=300&q=80', createdAt: '2026-11-01' },
        { id: 'prod-002', code: 'SK-002', name: 'Vitamin C Glow Serum', brand: 'SKINÉ', category: 'Serum', price: 185000, stock: 19, description: 'Serum pencerah dengan vitamin C.', image: 'https://images.unsplash.com/photo-1571781926291-c477ebfd024b?auto=format&fit=crop&w=300&q=80', createdAt: '2026-11-03' },
        { id: 'prod-003', code: 'SK-003', name: 'Daily Repair Toner', brand: 'SKINÉ', category: 'Toner', price: 135000, stock: 24, description: 'Toner menenangkan dan menyeimbangkan pH.', image: 'https://images.unsplash.com/photo-1522335789203-aabd1fc54bc9?auto=format&fit=crop&w=300&q=80', createdAt: '2026-11-04' },
        { id: 'prod-004', code: 'SK-004', name: 'Barrier Cream', brand: 'SKINÉ', category: 'Moisturizer', price: 210000, stock: 16, description: 'Krim pelembap untuk kulit rusak dan kering.', image: 'https://images.unsplash.com/photo-1556228720-195a672e8a03?auto=format&fit=crop&w=300&q=80', createdAt: '2026-11-06' }
    ];

    const defaultOrders = [
        { id: 'ord-001', orderCode: 'SK-2026001', customerName: 'Ayu Ramadhani', phone: '081234567890', purchaseDate: '2026-11-01', status: 'Dikirim', city: 'Bandung', province: 'Jawa Barat', latitude: -6.9175, longitude: 107.6191, address: 'Jl. Merdeka 12', notes: 'Pemesanan rutin bulanan', items: [{ productId: 'prod-001', name: 'Hydrating Cleanser', price: 120000, quantity: 2 }] },
        { id: 'ord-002', orderCode: 'SK-2026002', customerName: 'Dewi Lestari', phone: '081288889999', purchaseDate: '2026-11-03', status: 'Dikemas', city: 'Subang', province: 'Jawa Barat', latitude: -6.5714, longitude: 107.7587, address: 'Jl. Otista 68', notes: 'Minta packing rapi', items: [{ productId: 'prod-002', name: 'Vitamin C Glow Serum', price: 185000, quantity: 1 }] },
        { id: 'ord-003', orderCode: 'SK-2026003', customerName: 'Rizky Pratama', phone: '081299998888', purchaseDate: '2026-11-04', status: 'Tiba', city: 'Majalengka', province: 'Jawa Barat', latitude: -6.8364, longitude: 108.2277, address: 'Jl. Bypass 15', notes: '', items: [{ productId: 'prod-003', name: 'Daily Repair Toner', price: 135000, quantity: 3 }] },
        { id: 'ord-004', orderCode: 'SK-2026004', customerName: 'Maya Putri', phone: '082277776666', purchaseDate: '2026-11-05', status: 'Diproses', city: 'Cirebon', province: 'Jawa Barat', latitude: -6.7320, longitude: 108.5523, address: 'Jl. Siliwangi 81', notes: 'Jam pengiriman siang', items: [{ productId: 'prod-004', name: 'Barrier Cream', price: 210000, quantity: 2 }] },
        { id: 'ord-005', orderCode: 'SK-2026005', customerName: 'Farah Nabila', phone: '081344445555', purchaseDate: '2026-11-06', status: 'Dikirim', city: 'Sumedang', province: 'Jawa Barat', latitude: -6.8586, longitude: 107.9160, address: 'Jl. Sudirman 22', notes: 'Dikirim pukul 14.00', items: [{ productId: 'prod-001', name: 'Hydrating Cleanser', price: 120000, quantity: 1 }, { productId: 'prod-003', name: 'Daily Repair Toner', price: 135000, quantity: 1 }] }
    ];

    const defaultStore = {
        id: 'store-001',
        name: 'SKINÉ Skincare Store',
        address: 'Jl. Pahlawan No. 88, Indramayu',
        city: 'Indramayu',
        province: 'Jawa Barat',
        latitude: -6.3260,
        longitude: 108.3200
    };

    function readStorage(key, fallback) {
        try {
            const saved = localStorage.getItem(key);
            if (!saved) return fallback;
            const parsed = JSON.parse(saved);
            return parsed && typeof parsed === 'object' ? parsed : fallback;
        } catch (error) {
            return fallback;
        }
    }

    function writeStorage(key, value) {
        localStorage.setItem(key, JSON.stringify(value));
    }

    function safeGetProducts() {
        const raw = readStorage(STORAGE_KEYS.products, defaultProducts);
        return Array.isArray(raw) ? raw : defaultProducts;
    }

    function safeGetOrders() {
        const raw = readStorage(STORAGE_KEYS.orders, defaultOrders);
        return Array.isArray(raw) ? raw : defaultOrders;
    }

    function safeGetStore() {
        const raw = readStorage(STORAGE_KEYS.store, defaultStore);
        return raw && typeof raw === 'object' ? raw : defaultStore;
    }

    function renderDashboard() {
        const products = safeGetProducts();
        const orders = safeGetOrders();

        document.getElementById('totalProductsValue').textContent = products.length;
        document.getElementById('totalStockValue').textContent = products.reduce((sum, item) => sum + Number(item.stock || 0), 0);
        document.getElementById('totalOrdersValue').textContent = orders.length;
        document.getElementById('shippingOrdersValue').textContent = orders.filter(order => ['Diproses', 'Dikemas', 'Dikirim'].includes(order.status)).length;
        document.getElementById('arrivedOrdersValue').textContent = orders.filter(order => order.status === 'Tiba').length;
        document.getElementById('regionsValue').textContent = new Set(orders.map(order => String(order.city || 'Unknown'))).size;

        const regionTotals = {};
        orders.forEach((order) => {
            const city = order.city || 'Lainnya';
            regionTotals[city] = (regionTotals[city] || 0) + 1;
        });

        const chart = document.getElementById('distributionChart');
        const maxValue = Math.max(...Object.values(regionTotals), 1);
        chart.innerHTML = '';

        Object.entries(regionTotals).forEach(([city, count]) => {
            const item = document.createElement('div');
            item.className = 'chart-bar-wrap';
            const bar = document.createElement('div');
            bar.className = 'chart-bar';
            bar.style.height = `${Math.max((count / maxValue) * 170, 18)}px`;
            const value = document.createElement('div');
            value.className = 'chart-value';
            value.textContent = count;
            const label = document.createElement('div');
            label.className = 'chart-label';
            label.textContent = city.slice(0, 7);
            item.appendChild(value);
            item.appendChild(bar);
            item.appendChild(label);
            chart.appendChild(item);
        });

        const recentOrders = [...orders].sort((a, b) => new Date(b.purchaseDate || 0) - new Date(a.purchaseDate || 0)).slice(0, 5);
        const recentOrdersBody = document.getElementById('recentOrdersBody');
        recentOrdersBody.innerHTML = '';
        recentOrders.forEach((order) => {
            const tr = document.createElement('tr');
            const productNames = (order.items || []).map(item => item.name).join(', ');
            tr.innerHTML = `
                <td>${order.orderCode}</td>
                <td>${order.customerName}</td>
                <td>${productNames || '-'}</td>
                <td>${order.city || '-'}</td>
                <td>${order.purchaseDate || '-'}</td>
                <td><span class="badge status-${(order.status || 'diproses').toLowerCase().replace(/\s+/g, '')}">${order.status || 'Diproses'}</span></td>
            `;
            recentOrdersBody.appendChild(tr);
        });

        const recentProducts = [...products].sort((a, b) => new Date(b.createdAt || 0) - new Date(a.createdAt || 0)).slice(0, 4);
        const recentProductsList = document.getElementById('recentProductsList');
        recentProductsList.innerHTML = '';
        recentProducts.forEach((product) => {
            const card = document.createElement('div');
            card.className = 'product-card';
            card.innerHTML = `
                <img class="product-thumb" src="${product.image || 'https://via.placeholder.com/300x300?text=SKINÉ'}" alt="${product.name}" />
                <div class="product-meta">
                    <h4>${product.name}</h4>
                    <small>${product.brand} • ${product.category}</small>
                </div>
            `;
            recentProductsList.appendChild(card);
        });

        renderMiniMap();
    }

    function renderMiniMap() {
        const store = safeGetStore();
        const orders = safeGetOrders();
        const mapEl = document.getElementById('miniMap');
        if (!mapEl || typeof L === 'undefined') return;

        const miniMap = L.map('miniMap', { scrollWheelZoom: false, zoomControl: true });
        miniMap.setView([-6.7, 108.0], 6);
        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(miniMap);

        const storeIcon = L.divIcon({
            className: 'store-badge',
            html: '<div style="width:16px;height:16px;background:#66765F;border:2px solid white;border-radius:50%;box-shadow:0 4px 10px rgba(0,0,0,0.2);"></div>',
            iconSize: [18, 18],
            iconAnchor: [9, 9]
        });

        const storeMarker = L.marker([Number(store.latitude || -6.3260), Number(store.longitude || 108.3200)], { icon: storeIcon }).addTo(miniMap);
        storeMarker.bindPopup(`<div><strong>${store.name || 'SKINÉ Skincare Store'}</strong><br>${store.city || 'Indramayu'}</div>`);

        const recent = [...orders].slice(0, 6);
        recent.forEach((order) => {
            const marker = L.circleMarker([Number(order.latitude), Number(order.longitude)], {
                radius: 7,
                color: '#ffffff',
                weight: 2,
                fillColor: getStatusColor(order.status),
                fillOpacity: 1
            }).addTo(miniMap);
            const label = order.city || 'Daerah';
            marker.bindPopup(`<strong>${label}</strong><br>${order.customerName}<br>${order.status}`);
        });
    }

    function getStatusColor(status) {
        const statusMap = {
            Diproses: '#7a7d82',
            Dikemas: '#d6b460',
            Dikirim: '#3d7ca5',
            Tiba: '#4f8d67',
            Dibatalkan: '#d95e5e'
        };
        return statusMap[status] || '#66765F';
    }

    renderDashboard();
</script>
@endsection
