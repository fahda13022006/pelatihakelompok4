@extends('skine.layout')

@section('page-title', 'Data Pesanan')

@section('page-content')
    <div class="panel">
        <div class="panel-header">
            <h3>Data Pesanan</h3>
            <button type="button" class="button" id="openOrderModalButton">Tambah Pesanan</button>
        </div>

        <div class="toolbar" style="margin-bottom: 18px;">
            <div class="field" style="flex:1; min-width: 220px;">
                <label for="orderSearch">Cari pesanan</label>
                <input id="orderSearch" type="text" placeholder="Cari nama pelanggan atau kode" />
            </div>
            <div class="field">
                <label for="orderCityFilter">Filter daerah</label>
                <select id="orderCityFilter">
                    <option value="all">Semua</option>
                </select>
            </div>
            <div class="field">
                <label for="orderStatusFilter">Filter status</label>
                <select id="orderStatusFilter">
                    <option value="all">Semua</option>
                    <option value="Diproses">Diproses</option>
                    <option value="Dikemas">Dikemas</option>
                    <option value="Dikirim">Dikirim</option>
                    <option value="Tiba">Tiba</option>
                    <option value="Dibatalkan">Dibatalkan</option>
                </select>
            </div>
            <div class="field">
                <label for="orderDateFilter">Filter tanggal</label>
                <input id="orderDateFilter" type="date" />
            </div>
        </div>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Kode Pesanan</th>
                        <th>Nama Pelanggan</th>
                        <th>Produk</th>
                        <th>Total Pembayaran</th>
                        <th>Daerah Tujuan</th>
                        <th>Tanggal</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="ordersTableBody"></tbody>
            </table>
        </div>
    </div>

    <div class="modal" id="orderModal">
        <div class="modal-card">
            <div class="modal-header">
                <h3 id="orderModalTitle">Tambah Pesanan</h3>
                <button type="button" class="modal-close" data-close-order-modal aria-label="Tutup">×</button>
            </div>

            <form id="orderForm">
                <input type="hidden" id="orderId" />
                <div class="form-grid">
                    <div class="field">
                        <label for="orderCode">Kode Pesanan</label>
                        <input id="orderCode" type="text" readonly />
                    </div>
                    <div class="field">
                        <label for="customerName">Nama Pelanggan</label>
                        <input id="customerName" type="text" required />
                    </div>
                    <div class="field">
                        <label for="customerPhone">Nomor Telepon</label>
                        <input id="customerPhone" type="tel" />
                    </div>
                    <div class="field">
                        <label for="purchaseDate">Tanggal Pembelian</label>
                        <input id="purchaseDate" type="date" required />
                    </div>
                    <div class="field">
                        <label for="orderStatus">Status Pesanan</label>
                        <select id="orderStatus">
                            <option value="Diproses">Diproses</option>
                            <option value="Dikemas">Dikemas</option>
                            <option value="Dikirim">Dikirim</option>
                            <option value="Tiba">Tiba</option>
                            <option value="Dibatalkan">Dibatalkan</option>
                        </select>
                    </div>
                    <div class="field"></div>

                    <div class="field full-span">
                        <label>Informasi Produk</label>
                        <div id="orderItemsContainer" class="order-items"></div>
                        <button type="button" class="button secondary" id="addOrderItemButton" style="margin-top: 8px; width: fit-content;">Tambah Produk</button>
                    </div>

                    <div class="field full-span">
                        <label>Penentuan Koordinat Tujuan</label>
                        <div style="display:flex; flex-wrap:wrap; gap:10px; align-items:center; margin-bottom:8px;">
                            <button type="button" class="button secondary" id="saveOrderCoordinatesButton">Simpan Koordinat</button>
                        </div>
                        <div id="orderMapPicker" style="height: 220px; width: 100%; border: 1px solid var(--border); border-radius: 12px; overflow: hidden;"></div>
                    </div>

                    <div class="field full-span">
                        <label>Informasi Pengiriman</label>
                    </div>
                    <div class="field">
                        <label for="deliveryAddress">Alamat Tujuan</label>
                        <input id="deliveryAddress" type="text" required />
                    </div>
                    <div class="field">
                        <label for="deliveryCity">Kabupaten/Kota</label>
                        <input id="deliveryCity" type="text" required />
                    </div>
                    <div class="field">
                        <label for="deliveryProvince">Provinsi</label>
                        <input id="deliveryProvince" type="text" required />
                    </div>
                    <div class="field">
                        <label for="deliveryLatitude">Latitude</label>
                        <input id="deliveryLatitude" type="number" step="any" min="-90" max="90" required />
                    </div>
                    <div class="field">
                        <label for="deliveryLongitude">Longitude</label>
                        <input id="deliveryLongitude" type="number" step="any" min="-180" max="180" required />
                    </div>
                    <div class="field full-span">
                        <label for="deliveryNotes">Catatan Pengiriman</label>
                        <textarea id="deliveryNotes"></textarea>
                    </div>

                    <div class="field full-span" style="display:flex; justify-content:space-between; flex-wrap: wrap; gap:12px; align-items:center; margin-top: 10px;">
                        <div style="font-size: 1rem; font-weight: 700; color: var(--muted);">Total Pembayaran: <span id="orderTotalDisplay">Rp 0</span></div>
                        <div style="display:flex; gap:10px;">
                            <button type="button" class="button secondary" data-close-order-modal>Batal</button>
                            <button type="submit" class="button">Simpan Pesanan</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('page-script')
<script>
    const PRODUCT_STORAGE_KEY = 'skine_products_v1';
    const ORDER_STORAGE_KEY = 'skine_orders_v1';

    function readJson(key, fallback) {
        try {
            const value = localStorage.getItem(key);
            if (!value) return fallback;
            const parsed = JSON.parse(value);
            return Array.isArray(parsed) ? parsed : fallback;
        } catch (error) {
            return fallback;
        }
    }

    function writeJson(key, value) {
        localStorage.setItem(key, JSON.stringify(value));
    }

    function formatCurrency(value) {
        return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(Number(value || 0));
    }

    function generateOrderCode() {
        const orders = readJson(ORDER_STORAGE_KEY, []);
        const maxNumber = orders.reduce((max, order) => {
            const match = String(order.orderCode || '').match(/(\d+)$/);
            return Math.max(max, match ? Number(match[1]) : 0);
        }, 0);
        return `SK-${String(maxNumber + 1).padStart(4, '0')}`;
    }

    function ensureProducts() {
        const sampleProducts = [
            { id: 'prod-001', code: 'SK-001', name: 'Hydrating Cleanser', brand: 'SKINÉ', category: 'Facial Wash', price: 120000, stock: 28 },
            { id: 'prod-002', code: 'SK-002', name: 'Vitamin C Glow Serum', brand: 'SKINÉ', category: 'Serum', price: 185000, stock: 19 },
            { id: 'prod-003', code: 'SK-003', name: 'Daily Repair Toner', brand: 'SKINÉ', category: 'Toner', price: 135000, stock: 24 },
            { id: 'prod-004', code: 'SK-004', name: 'Barrier Cream', brand: 'SKINÉ', category: 'Moisturizer', price: 210000, stock: 16 }
        ];
        const stored = readJson(PRODUCT_STORAGE_KEY, sampleProducts);
        return Array.isArray(stored) && stored.length ? stored : sampleProducts;
    }

    function getProductOptions() {
        return ensureProducts().map((product) => `<option value="${product.id}">${product.name} (${product.stock} stok)</option>`).join('');
    }

    function createOrderItemRow(productId = '') {
        const row = document.createElement('div');
        row.className = 'order-item-row';

        const productSelect = document.createElement('select');
        productSelect.innerHTML = '<option value="">Pilih produk</option>' + getProductOptions();
        if (productId) productSelect.value = productId;

        const qtyInput = document.createElement('input');
        qtyInput.type = 'number';
        qtyInput.min = '1';
        qtyInput.value = '1';
        qtyInput.placeholder = 'Jumlah';

        const priceDisplay = document.createElement('input');
        priceDisplay.type = 'text';
        priceDisplay.readOnly = true;
        priceDisplay.value = 'Rp 0';

        const subtotalDisplay = document.createElement('div');
        subtotalDisplay.style.fontWeight = '700';
        subtotalDisplay.style.color = '#343A34';
        subtotalDisplay.textContent = 'Rp 0';

        const removeButton = document.createElement('button');
        removeButton.type = 'button';
        removeButton.className = 'mini-btn danger';
        removeButton.textContent = 'Hapus';
        removeButton.addEventListener('click', function () {
            row.remove();
            updateOrderTotal();
        });

        const updateLine = () => {
            const selectedProduct = ensureProducts().find((product) => String(product.id) === String(productSelect.value));
            const quantity = Number(qtyInput.value) || 0;
            const price = Number(selectedProduct?.price || 0);
            priceDisplay.value = formatCurrency(price);
            subtotalDisplay.textContent = formatCurrency(price * quantity);
            updateOrderTotal();
        };

        productSelect.addEventListener('change', updateLine);
        qtyInput.addEventListener('input', updateLine);

        row.appendChild(productSelect);
        row.appendChild(qtyInput);
        row.appendChild(priceDisplay);
        row.appendChild(subtotalDisplay);
        row.appendChild(removeButton);

        updateLine();
        return row;
    }

    function updateOrderTotal() {
        const rows = document.querySelectorAll('#orderItemsContainer .order-item-row');
        let total = 0;
        rows.forEach((row) => {
            const select = row.querySelector('select');
            const qtyInput = row.querySelector('input[type="number"]');
            const selected = ensureProducts().find((product) => String(product.id) === String(select.value));
            const quantity = Number(qtyInput.value) || 0;
            total += Number((selected?.price || 0) * quantity);
        });
        document.getElementById('orderTotalDisplay').textContent = formatCurrency(total);
    }

    function renderOrderItems(items = []) {
        const container = document.getElementById('orderItemsContainer');
        container.innerHTML = '';
        if (!items.length) {
            container.appendChild(createOrderItemRow());
            return;
        }

        items.forEach((item, index) => {
            const row = createOrderItemRow(item.productId);
            const qtyInput = row.querySelector('input[type="number"]');
            const productSelect = row.querySelector('select');
            productSelect.value = item.productId || '';
            qtyInput.value = Number(item.quantity || 1);
            container.appendChild(row);

            const event = new Event('input', { bubbles: true });
            qtyInput.dispatchEvent(event);
        });
        updateOrderTotal();
    }

    function resetOrderForm() {
        document.getElementById('orderForm').reset();
        document.getElementById('orderId').value = '';
        document.getElementById('orderCode').value = generateOrderCode();
        document.getElementById('purchaseDate').value = new Date().toISOString().slice(0, 10);
        document.getElementById('orderStatus').value = 'Diproses';
        const container = document.getElementById('orderItemsContainer');
        container.innerHTML = '';
        container.appendChild(createOrderItemRow());
        document.getElementById('orderTotalDisplay').textContent = 'Rp 0';
    }

    let orderMapPicker = null;
    let orderPreviewMarker = null;

    function syncOrderCoordinateFields(lat, lng) {
        document.getElementById('deliveryLatitude').value = Number(lat).toFixed(4);
        document.getElementById('deliveryLongitude').value = Number(lng).toFixed(4);
    }

    function initOrderMapPicker() {
        const mapEl = document.getElementById('orderMapPicker');
        if (!mapEl || typeof L === 'undefined') return;

        if (orderMapPicker) {
            orderMapPicker.remove();
        }

        orderMapPicker = L.map('orderMapPicker', { scrollWheelZoom: false });
        orderMapPicker.setView([-6.7, 108.0], 6);
        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(orderMapPicker);

        const lat = Number(document.getElementById('deliveryLatitude').value || -6.9175);
        const lng = Number(document.getElementById('deliveryLongitude').value || 107.6191);

        orderPreviewMarker = L.marker([lat, lng], {
            draggable: true,
            icon: L.divIcon({
                className: 'preview-marker',
                html: '<div style="width:18px;height:18px;border-radius:50%;background:#B99B68;border:2px solid white;box-shadow:0 4px 10px rgba(0,0,0,0.25);"></div>',
                iconSize: [18, 18],
                iconAnchor: [9, 9]
            })
        }).addTo(orderMapPicker);

        orderPreviewMarker.on('dragend', function (event) {
            const point = event.target.getLatLng();
            syncOrderCoordinateFields(point.lat, point.lng);
        });

        orderMapPicker.on('click', function (event) {
            const { lat, lng } = event.latlng;
            orderPreviewMarker.setLatLng([lat, lng]);
            syncOrderCoordinateFields(lat, lng);
        });

        document.getElementById('saveOrderCoordinatesButton').onclick = function () {
            const lat = Number(document.getElementById('deliveryLatitude').value || orderPreviewMarker.getLatLng().lat);
            const lng = Number(document.getElementById('deliveryLongitude').value || orderPreviewMarker.getLatLng().lng);
            orderPreviewMarker.setLatLng([lat, lng]);
            syncOrderCoordinateFields(lat, lng);
            orderMapPicker.setView([lat, lng], 10);
        };
    }

    function renderOrdersTable() {
        const orders = readJson(ORDER_STORAGE_KEY, []);
        const search = document.getElementById('orderSearch').value.toLowerCase();
        const city = document.getElementById('orderCityFilter').value;
        const status = document.getElementById('orderStatusFilter').value;
        const date = document.getElementById('orderDateFilter').value;

        const filtered = orders.filter((order) => {
            const orderCity = String(order.city || '');
            const matchesSearch = !search || `${order.orderCode} ${order.customerName}`.toLowerCase().includes(search);
            const matchesCity = city === 'all' || orderCity === city;
            const matchesStatus = status === 'all' || order.status === status;
            const matchesDate = !date || (order.purchaseDate || '') === date;
            return matchesSearch && matchesCity && matchesStatus && matchesDate;
        });

        const tbody = document.getElementById('ordersTableBody');
        tbody.innerHTML = '';

        filtered.forEach((order) => {
            const row = document.createElement('tr');
            const productList = (order.items || []).map((item) => item.name).join(', ');
            row.innerHTML = `
                <td>${order.orderCode}</td>
                <td>${order.customerName}</td>
                <td>${productList || '-'}</td>
                <td>${formatCurrency(order.total || 0)}</td>
                <td>${order.city}</td>
                <td>${order.purchaseDate}</td>
                <td><span class="badge status-${(order.status || 'diproses').toLowerCase().replace(/\s+/g, '')}">${order.status}</span></td>
                <td>
                    <div class="actions">
                        <button type="button" class="mini-btn primary" data-view-order="${order.id}">Detail</button>
                        <button type="button" class="mini-btn primary" data-edit-order="${order.id}">Edit</button>
                        <button type="button" class="mini-btn danger" data-delete-order="${order.id}">Hapus</button>
                    </div>
                </td>
            `;
            tbody.appendChild(row);
        });
    }

    function populateCityFilter() {
        const citySelect = document.getElementById('orderCityFilter');
        const orders = readJson(ORDER_STORAGE_KEY, []);
        const cities = [...new Set(orders.map((order) => order.city).filter(Boolean))].sort();
        citySelect.innerHTML = '<option value="all">Semua</option>' + cities.map((city) => `<option value="${city}">${city}</option>`).join('');
    }

    function applyOrderStock(order, direction) {
        const products = ensureProducts();
        order.items.forEach((item) => {
            const product = products.find((entry) => String(entry.id) === String(item.productId));
            if (!product) return;
            product.stock = Number(product.stock || 0) + (Number(item.quantity || 0) * direction);
        });
        writeJson(PRODUCT_STORAGE_KEY, products);
    }

    function openOrderModal(mode = 'create') {
        const modal = document.getElementById('orderModal');
        modal.classList.add('open');
        if (mode === 'create') {
            document.getElementById('orderModalTitle').textContent = 'Tambah Pesanan';
            resetOrderForm();
        }
        setTimeout(initOrderMapPicker, 50);
    }

    function closeOrderModal() {
        document.getElementById('orderModal').classList.remove('open');
        document.getElementById('orderForm').reset();
        if (orderMapPicker) {
            orderMapPicker.remove();
            orderMapPicker = null;
            orderPreviewMarker = null;
        }
    }

    function deleteOrder(orderId) {
        const orders = readJson(ORDER_STORAGE_KEY, []);
        const order = orders.find((item) => String(item.id) === String(orderId));
        if (!order) return;
        if (!confirm('Apakah Anda yakin ingin menghapus pesanan ini?')) return;
        applyOrderStock(order, 1);
        const nextOrders = orders.filter((item) => String(item.id) !== String(orderId));
        writeJson(ORDER_STORAGE_KEY, nextOrders);
        populateCityFilter();
        renderOrdersTable();
    }

    function viewOrder(orderId) {
        const orders = readJson(ORDER_STORAGE_KEY, []);
        const order = orders.find((item) => String(item.id) === String(orderId));
        if (!order) return;
        let details = `Kode Pesanan: ${order.orderCode}\n`;
        details += `Nama Pelanggan: ${order.customerName}\n`;
        details += `Produk: ${(order.items || []).map((item) => `${item.name} (${item.quantity})`).join(', ')}\n`;
        details += `Total: ${formatCurrency(order.total)}\n`;
        details += `Daerah: ${order.city}\n`;
        details += `Status: ${order.status}`;
        alert(details);
    }

    document.getElementById('openOrderModalButton').addEventListener('click', function () {
        openOrderModal('create');
    });

    document.querySelectorAll('[data-close-order-modal]').forEach((button) => {
        button.addEventListener('click', closeOrderModal);
    });

    document.getElementById('addOrderItemButton').addEventListener('click', function () {
        document.getElementById('orderItemsContainer').appendChild(createOrderItemRow());
        updateOrderTotal();
    });

    document.getElementById('orderForm').addEventListener('submit', function (event) {
        event.preventDefault();
        const orders = readJson(ORDER_STORAGE_KEY, []);
        const orderId = document.getElementById('orderId').value;

        const items = Array.from(document.querySelectorAll('#orderItemsContainer .order-item-row')).map((row) => {
            const select = row.querySelector('select');
            const qtyInput = row.querySelector('input[type="number"]');
            const product = ensureProducts().find((entry) => String(entry.id) === String(select.value));
            const quantity = Number(qtyInput.value) || 0;
            return {
                productId: select.value,
                name: product ? product.name : '',
                price: Number(product ? product.price : 0),
                quantity: quantity
            };
        }).filter((item) => item.productId && item.quantity > 0 && item.name);

        if (!items.length) {
            alert('Minimal pilih satu produk untuk pesanan.');
            return;
        }

        const customerName = document.getElementById('customerName').value.trim();
        const phone = document.getElementById('customerPhone').value.trim();
        const purchaseDate = document.getElementById('purchaseDate').value;
        const orderStatus = document.getElementById('orderStatus').value;
        const address = document.getElementById('deliveryAddress').value.trim();
        const city = document.getElementById('deliveryCity').value.trim();
        const province = document.getElementById('deliveryProvince').value.trim();
        const latitude = Number(document.getElementById('deliveryLatitude').value);
        const longitude = Number(document.getElementById('deliveryLongitude').value);
        const notes = document.getElementById('deliveryNotes').value.trim();

        if (!customerName || !purchaseDate || !address || !city || !province || !Number.isFinite(latitude) || !Number.isFinite(longitude)) {
            alert('Semua data pelanggan dan pengiriman wajib diisi dengan benar.');
            return;
        }

        if (latitude < -90 || latitude > 90 || longitude < -180 || longitude > 180) {
            alert('Latitude dan longitude harus valid.');
            return;
        }

        const shouldCheckStock = orderStatus !== 'Dibatalkan';
        const validItems = items.filter((item) => {
            const product = ensureProducts().find((entry) => String(entry.id) === String(item.productId));
            return product && (!shouldCheckStock || Number(product.stock || 0) >= Number(item.quantity || 0));
        });

        if (shouldCheckStock && validItems.length !== items.length) {
            alert('Jumlah pembelian melebihi stok yang tersedia untuk salah satu produk.');
            return;
        }

        const total = items.reduce((sum, item) => sum + (Number(item.price) * Number(item.quantity)), 0);
        const payload = {
            id: orderId || `ord-${Date.now()}`,
            orderCode: document.getElementById('orderCode').value || generateOrderCode(),
            customerName,
            phone,
            purchaseDate,
            status: orderStatus,
            city,
            province,
            latitude,
            longitude,
            address,
            notes,
            items,
            total
        };

        if (orderId) {
            const previous = orders.find((item) => String(item.id) === String(orderId));
            if (previous) {
                applyOrderStock(previous, 1);
                const index = orders.findIndex((item) => String(item.id) === String(orderId));
                orders[index] = payload;
                if (payload.status !== 'Dibatalkan') {
                    applyOrderStock(payload, -1);
                }
            }
        } else {
            orders.push(payload);
            if (payload.status !== 'Dibatalkan') {
                applyOrderStock(payload, -1);
            }
        }

        writeJson(ORDER_STORAGE_KEY, orders);
        populateCityFilter();
        renderOrdersTable();
        closeOrderModal();
    });

    document.getElementById('ordersTableBody').addEventListener('click', function (event) {
        const viewButton = event.target.closest('[data-view-order]');
        const editButton = event.target.closest('[data-edit-order]');
        const deleteButton = event.target.closest('[data-delete-order]');

        if (viewButton) viewOrder(viewButton.dataset.viewOrder);
        if (deleteButton) deleteOrder(deleteButton.dataset.deleteOrder);

        if (editButton) {
            const orders = readJson(ORDER_STORAGE_KEY, []);
            const order = orders.find((item) => String(item.id) === String(editButton.dataset.editOrder));
            if (!order) return;

            document.getElementById('orderId').value = order.id;
            document.getElementById('orderCode').value = order.orderCode;
            document.getElementById('customerName').value = order.customerName;
            document.getElementById('customerPhone').value = order.phone || '';
            document.getElementById('purchaseDate').value = order.purchaseDate;
            document.getElementById('orderStatus').value = order.status;
            document.getElementById('deliveryAddress').value = order.address || '';
            document.getElementById('deliveryCity').value = order.city || '';
            document.getElementById('deliveryProvince').value = order.province || '';
            document.getElementById('deliveryLatitude').value = order.latitude || '-6.9175';
            document.getElementById('deliveryLongitude').value = order.longitude || '107.6191';
            document.getElementById('deliveryNotes').value = order.notes || '';
            document.getElementById('orderModalTitle').textContent = 'Edit Pesanan';
            renderOrderItems(order.items || []);
            openOrderModal('edit');
        }
    });

    document.getElementById('orderSearch').addEventListener('input', renderOrdersTable);
    document.getElementById('orderCityFilter').addEventListener('change', renderOrdersTable);
    document.getElementById('orderStatusFilter').addEventListener('change', renderOrdersTable);
    document.getElementById('orderDateFilter').addEventListener('change', renderOrdersTable);

    resetOrderForm();
    populateCityFilter();
    renderOrdersTable();
</script>
@endsection
