@extends('skine.layout')

@section('page-title', 'Data Produk')

@section('page-content')
    <div class="panel">
        <div class="panel-header">
            <h3>Data Produk</h3>
            <button type="button" class="button" data-open-product-modal>Tambah Produk</button>
        </div>

        <div class="toolbar" style="margin-bottom: 18px;">
            <div class="field" style="flex:1; min-width: 220px;">
                <label for="productSearch">Cari produk</label>
                <input id="productSearch" type="text" placeholder="Cari nama atau kode produk" />
            </div>
            <div class="field">
                <label for="categoryFilter">Filter kategori</label>
                <select id="categoryFilter">
                    <option value="all">Semua</option>
                    <option value="Facial Wash">Facial Wash</option>
                    <option value="Toner">Toner</option>
                    <option value="Serum">Serum</option>
                    <option value="Moisturizer">Moisturizer</option>
                    <option value="Sunscreen">Sunscreen</option>
                    <option value="Face Mask">Face Mask</option>
                    <option value="Other">Other</option>
                </select>
            </div>
            <div class="field">
                <label for="stockFilter">Filter stok</label>
                <select id="stockFilter">
                    <option value="all">Semua</option>
                    <option value="available">Tersedia</option>
                    <option value="low">Stok rendah</option>
                    <option value="empty">Habis</option>
                </select>
            </div>
        </div>

        <div style="margin-bottom: 12px; color: var(--muted); font-weight: 600;" id="productMeta">0 produk</div>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Foto Produk</th>
                        <th>Kode Produk</th>
                        <th>Nama Produk</th>
                        <th>Brand</th>
                        <th>Kategori</th>
                        <th>Harga</th>
                        <th>Stok</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="productsTableBody"></tbody>
            </table>
        </div>
    </div>

    <div class="modal" id="productModal">
        <div class="modal-card">
            <div class="modal-header">
                <h3 id="productModalTitle">Tambah Produk</h3>
                <button type="button" class="modal-close" data-close-product-modal aria-label="Tutup">×</button>
            </div>

            <form id="productForm">
                <input type="hidden" id="productId">
                <div class="form-grid">
                    <div class="field">
                        <label for="productCode">Kode Produk</label>
                        <input id="productCode" type="text" readonly placeholder="Auto-generated" />
                    </div>
                    <div class="field">
                        <label for="productName">Nama Produk</label>
                        <input id="productName" type="text" required />
                    </div>
                    <div class="field">
                        <label for="productBrand">Brand</label>
                        <input id="productBrand" type="text" required />
                    </div>
                    <div class="field">
                        <label for="productCategory">Kategori</label>
                        <select id="productCategory" required>
                            <option value="Facial Wash">Facial Wash</option>
                            <option value="Toner">Toner</option>
                            <option value="Serum">Serum</option>
                            <option value="Moisturizer">Moisturizer</option>
                            <option value="Sunscreen">Sunscreen</option>
                            <option value="Face Mask">Face Mask</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div class="field">
                        <label for="productPrice">Harga</label>
                        <input id="productPrice" type="number" min="0" step="1000" required />
                    </div>
                    <div class="field">
                        <label for="productStock">Stok</label>
                        <input id="productStock" type="number" min="0" step="1" required />
                    </div>
                    <div class="field full-span">
                        <label for="productImage">URL Foto Produk</label>
                        <input id="productImage" type="url" placeholder="https://..." />
                    </div>
                    <div class="field full-span">
                        <label for="productDescription">Deskripsi</label>
                        <textarea id="productDescription" placeholder="Deskripsi produk..."></textarea>
                    </div>
                    <div class="field full-span" style="display:flex; justify-content:flex-end; gap:10px; margin-top: 8px;">
                        <button type="button" class="button secondary" data-close-product-modal>Batal</button>
                        <button type="submit" class="button">Simpan Produk</button>
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

    const sampleProducts = [
        { id: 'prod-001', code: 'SK-001', name: 'Hydrating Cleanser', brand: 'SKINÉ', category: 'Facial Wash', price: 120000, stock: 28, description: 'Gel pembersih lembut untuk kulit sensitif.', image: 'https://images.unsplash.com/photo-1556228578-8c89e6adf883?auto=format&fit=crop&w=300&q=80', createdAt: '2026-11-01' },
        { id: 'prod-002', code: 'SK-002', name: 'Vitamin C Glow Serum', brand: 'SKINÉ', category: 'Serum', price: 185000, stock: 19, description: 'Serum pencerah dengan vitamin C.', image: 'https://images.unsplash.com/photo-1571781926291-c477ebfd024b?auto=format&fit=crop&w=300&q=80', createdAt: '2026-11-03' },
        { id: 'prod-003', code: 'SK-003', name: 'Daily Repair Toner', brand: 'SKINÉ', category: 'Toner', price: 135000, stock: 24, description: 'Toner menenangkan dan menyeimbangkan pH.', image: 'https://images.unsplash.com/photo-1522335789203-aabd1fc54bc9?auto=format&fit=crop&w=300&q=80', createdAt: '2026-11-04' },
        { id: 'prod-004', code: 'SK-004', name: 'Barrier Cream', brand: 'SKINÉ', category: 'Moisturizer', price: 210000, stock: 16, description: 'Krim pelembap untuk kulit rusak dan kering.', image: 'https://images.unsplash.com/photo-1556228720-195a672e8a03?auto=format&fit=crop&w=300&q=80', createdAt: '2026-11-06' }
    ];

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

    function ensureProducts() {
        const products = readJson(PRODUCT_STORAGE_KEY, sampleProducts);
        if (!Array.isArray(products) || products.length === 0) {
            writeJson(PRODUCT_STORAGE_KEY, sampleProducts);
            return sampleProducts;
        }
        return products;
    }

    function formatCurrency(value) {
        return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(Number(value || 0));
    }

    function slugText(raw) {
        return String(raw || '').replace(/[<>]/g, '').trim();
    }

    function renderProductsTable() {
        const products = ensureProducts();
        const searchValue = document.getElementById('productSearch').value.toLowerCase();
        const categoryValue = document.getElementById('categoryFilter').value;
        const stockValue = document.getElementById('stockFilter').value;

        const filteredProducts = products.filter((product) => {
            const queryMatch = !searchValue || `${product.name} ${product.code}`.toLowerCase().includes(searchValue);
            const categoryMatch = categoryValue === 'all' || product.category === categoryValue;
            let stockMatch = true;
            if (stockValue === 'available') stockMatch = Number(product.stock) > 0;
            if (stockValue === 'low') stockMatch = Number(product.stock) > 0 && Number(product.stock) <= 10;
            if (stockValue === 'empty') stockMatch = Number(product.stock) === 0;
            return queryMatch && categoryMatch && stockMatch;
        });

        const tbody = document.getElementById('productsTableBody');
        tbody.innerHTML = '';

        filteredProducts.forEach((product) => {
            const row = document.createElement('tr');
            const image = product.image || 'https://via.placeholder.com/300x300?text=SKINÉ';
            row.innerHTML = `
                <td><img src="${image}" alt="${slugText(product.name)}" style="width: 60px; height: 60px; object-fit: cover; border-radius: 12px; border: 1px solid #E9E7E0;" /></td>
                <td>${slugText(product.code)}</td>
                <td>${slugText(product.name)}</td>
                <td>${slugText(product.brand)}</td>
                <td>${slugText(product.category)}</td>
                <td>${formatCurrency(product.price)}</td>
                <td>${Number(product.stock || 0)}</td>
                <td><span class="badge ${Number(product.stock) > 0 ? 'status-tiba' : 'status-dibatalkan'}">${Number(product.stock) > 0 ? 'Tersedia' : 'Habis'}</span></td>
                <td>
                    <div class="actions">
                        <button type="button" class="mini-btn primary" data-edit-product="${product.id}">Edit</button>
                        <button type="button" class="mini-btn danger" data-delete-product="${product.id}">Hapus</button>
                    </div>
                </td>
            `;
            tbody.appendChild(row);
        });

        document.getElementById('productMeta').textContent = `${filteredProducts.length} produk`;
    }

    function openProductModal() {
        document.getElementById('productModal').classList.add('open');
    }

    function closeProductModal() {
        document.getElementById('productModal').classList.remove('open');
        document.getElementById('productForm').reset();
        document.getElementById('productId').value = '';
        document.getElementById('productCode').value = '';
        document.getElementById('productModalTitle').textContent = 'Tambah Produk';
    }

    function generateProductCode() {
        const prefix = 'SK';
        const products = ensureProducts();
        const maxNumber = products.reduce((max, item) => {
            const numeric = Number(String(item.code || '').replace(/\D/g, '')) || 0;
            return Math.max(max, numeric);
        }, 0);
        return `${prefix}-${String(maxNumber + 1).padStart(3, '0')}`;
    }

    function saveProduct(product) {
        const products = ensureProducts();
        const existingIndex = products.findIndex((item) => String(item.id) === String(product.id));
        if (existingIndex >= 0) {
            products[existingIndex] = { ...products[existingIndex], ...product };
        } else {
            products.push({ ...product, id: product.id || `prod-${Date.now()}` });
        }
        writeJson(PRODUCT_STORAGE_KEY, products);
        renderProductsTable();
    }

    function deleteProduct(productId) {
        const products = ensureProducts();
        const orders = readJson(ORDER_STORAGE_KEY, []);
        const hasOrderUsage = orders.some((order) => (order.items || []).some((item) => String(item.productId) === String(productId)));

        if (hasOrderUsage) {
            alert('Produk ini sudah digunakan dalam transaksi dan tidak dihapus agar riwayat pesanan tetap utuh.');
            return;
        }

        if (!confirm('Apakah Anda yakin ingin menghapus produk ini?')) return;

        const nextProducts = products.filter((item) => String(item.id) !== String(productId));
        writeJson(PRODUCT_STORAGE_KEY, nextProducts);
        renderProductsTable();
    }

    document.querySelector('[data-open-product-modal]').addEventListener('click', openProductModal);
    document.querySelectorAll('[data-close-product-modal]').forEach((button) => {
        button.addEventListener('click', closeProductModal);
    });

    document.getElementById('productForm').addEventListener('submit', function (event) {
        event.preventDefault();
        const products = ensureProducts();

        const id = document.getElementById('productId').value;
        const name = document.getElementById('productName').value.trim();
        const brand = document.getElementById('productBrand').value.trim();
        const category = document.getElementById('productCategory').value;
        const price = Number(document.getElementById('productPrice').value);
        const stock = Number(document.getElementById('productStock').value);
        const description = document.getElementById('productDescription').value.trim();
        const image = document.getElementById('productImage').value.trim();

        if (!name) {
            alert('Nama produk wajib diisi.');
            return;
        }
        if (price < 0) {
            alert('Harga tidak boleh negatif.');
            return;
        }
        if (stock < 0) {
            alert('Stok tidak boleh negatif.');
            return;
        }

        const nextCode = id ? products.find((item) => String(item.id) === String(id))?.code || generateProductCode() : generateProductCode();
        const payload = {
            id: id || `prod-${Date.now()}`,
            code: nextCode,
            name,
            brand,
            category,
            price,
            stock,
            description,
            image,
            createdAt: id ? (products.find((item) => String(item.id) === String(id))?.createdAt || new Date().toISOString().slice(0, 10)) : new Date().toISOString().slice(0, 10)
        };

        saveProduct(payload);
        closeProductModal();
    });

    document.getElementById('productSearch').addEventListener('input', renderProductsTable);
    document.getElementById('categoryFilter').addEventListener('change', renderProductsTable);
    document.getElementById('stockFilter').addEventListener('change', renderProductsTable);

    document.getElementById('productsTableBody').addEventListener('click', function (event) {
        const target = event.target;
        const deleteButton = target.closest('[data-delete-product]');
        const editButton = target.closest('[data-edit-product]');

        if (deleteButton) {
            deleteProduct(deleteButton.dataset.deleteProduct);
        }

        if (editButton) {
            const products = ensureProducts();
            const product = products.find((item) => String(item.id) === String(editButton.dataset.editProduct));
            if (!product) return;
            document.getElementById('productId').value = product.id;
            document.getElementById('productCode').value = product.code;
            document.getElementById('productName').value = product.name;
            document.getElementById('productBrand').value = product.brand;
            document.getElementById('productCategory').value = product.category;
            document.getElementById('productPrice').value = product.price;
            document.getElementById('productStock').value = product.stock;
            document.getElementById('productDescription').value = product.description || '';
            document.getElementById('productImage').value = product.image || '';
            document.getElementById('productModalTitle').textContent = 'Edit Produk';
            openProductModal();
        }
    });

    renderProductsTable();
</script>
@endsection
