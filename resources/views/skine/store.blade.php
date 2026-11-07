@extends('skine.layout')

@section('page-title', 'Lokasi Toko')

@section('page-content')
    <div class="row-grid">
        <div class="panel">
            <div class="panel-header">
                <h3>Lokasi Toko</h3>
            </div>

            <form id="storeForm">
                <div class="form-grid">
                    <div class="field">
                        <label for="storeName">Nama toko</label>
                        <input id="storeName" type="text" required />
                    </div>
                    <div class="field">
                        <label for="storeAddress">Alamat toko</label>
                        <input id="storeAddress" type="text" required />
                    </div>
                    <div class="field">
                        <label for="storeCity">Kabupaten/Kota</label>
                        <input id="storeCity" type="text" required />
                    </div>
                    <div class="field">
                        <label for="storeProvince">Provinsi</label>
                        <input id="storeProvince" type="text" required />
                    </div>
                    <div class="field">
                        <label for="storeLatitude">Latitude</label>
                        <input id="storeLatitude" type="number" step="any" min="-90" max="90" required />
                    </div>
                    <div class="field">
                        <label for="storeLongitude">Longitude</label>
                        <input id="storeLongitude" type="number" step="any" min="-180" max="180" required />
                    </div>
                    <div class="field full-span" style="display:flex; justify-content:flex-end; gap:10px;">
                        <button type="button" class="button secondary" id="storeResetButton">Reset</button>
                        <button type="submit" class="button">Simpan Perubahan</button>
                    </div>
                </div>
            </form>
        </div>

        <div class="panel">
            <div class="panel-header">
                <h3>Pilih Lokasi di Peta</h3>
            </div>
            <div id="storeMap" class="map-panel"></div>
        </div>
    </div>
@endsection

@section('page-script')
<script>
    const STORE_STORAGE_KEY = 'skine_store_v1';

    const defaultStore = {
        id: 'store-001',
        name: 'SKINÉ Skincare Store',
        address: 'Jl. Pahlawan No. 88, Indramayu',
        city: 'Indramayu',
        province: 'Jawa Barat',
        latitude: -6.3260,
        longitude: 108.3200
    };

    function readJson(key, fallback) {
        try {
            const value = localStorage.getItem(key);
            if (!value) return fallback;
            const parsed = JSON.parse(value);
            return parsed && typeof parsed === 'object' ? parsed : fallback;
        } catch (error) {
            return fallback;
        }
    }

    function writeJson(key, value) {
        localStorage.setItem(key, JSON.stringify(value));
    }

    function loadStoreForm() {
        const store = readJson(STORE_STORAGE_KEY, defaultStore);
        document.getElementById('storeName').value = store.name || defaultStore.name;
        document.getElementById('storeAddress').value = store.address || defaultStore.address;
        document.getElementById('storeCity').value = store.city || defaultStore.city;
        document.getElementById('storeProvince').value = store.province || defaultStore.province;
        document.getElementById('storeLatitude').value = store.latitude ?? defaultStore.latitude;
        document.getElementById('storeLongitude').value = store.longitude ?? defaultStore.longitude;
    }

    function renderStoreMap() {
        if (typeof L === 'undefined') return;

        const map = L.map('storeMap', { scrollWheelZoom: false });
        map.setView([-6.3260, 108.3200], 11);
        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        const marker = L.marker([Number(document.getElementById('storeLatitude').value || -6.3260), Number(document.getElementById('storeLongitude').value || 108.3200)], {
            draggable: true
        }).addTo(map);

        marker.on('dragend', function (event) {
            const point = event.target.getLatLng();
            document.getElementById('storeLatitude').value = point.lat;
            document.getElementById('storeLongitude').value = point.lng;
        });

        map.on('click', function (event) {
            const lat = event.latlng.lat;
            const lng = event.latlng.lng;
            marker.setLatLng([lat, lng]);
            document.getElementById('storeLatitude').value = lat;
            document.getElementById('storeLongitude').value = lng;
        });

        document.getElementById('storeForm').addEventListener('submit', function (event) {
            event.preventDefault();
            const payload = {
                id: 'store-001',
                name: document.getElementById('storeName').value.trim(),
                address: document.getElementById('storeAddress').value.trim(),
                city: document.getElementById('storeCity').value.trim(),
                province: document.getElementById('storeProvince').value.trim(),
                latitude: Number(document.getElementById('storeLatitude').value),
                longitude: Number(document.getElementById('storeLongitude').value)
            };

            if (!payload.name || !payload.address || !payload.city || !payload.province) {
                alert('Semua field toko wajib diisi.');
                return;
            }
            if (payload.latitude < -90 || payload.latitude > 90 || payload.longitude < -180 || payload.longitude > 180) {
                alert('Latitude dan longitude tidak valid.');
                return;
            }

            writeJson(STORE_STORAGE_KEY, payload);
            alert('Lokasi toko berhasil disimpan.');
        });

        document.getElementById('storeResetButton').addEventListener('click', function () {
            writeJson(STORE_STORAGE_KEY, defaultStore);
            loadStoreForm();
            marker.setLatLng([defaultStore.latitude, defaultStore.longitude]);
            map.setView([defaultStore.latitude, defaultStore.longitude], 11);
        });
    }

    loadStoreForm();
    renderStoreMap();
</script>
@endsection
