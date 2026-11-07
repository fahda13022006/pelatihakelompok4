<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="icon" type="image/x-icon"
href="https://awsimages.detik.net.id/content/2014/05/30/1562/prabowosubianto.jp
eg">
<title>Pengenalan GIS</title>
<!-- Leaflet CSS via CDN -->
<link rel="stylesheet"
href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
body {
font-family: Arial, sans-serif;
margin: 20px;
background-color: #f4f6f9;
}
.container {
max-width: 1100px;
margin: 0 auto;
background: white;
padding: 20px;
border-radius: 8px;
box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}
.toolbar {
display: flex;
align-items: center;
gap: 15px;
margin-bottom: 15px;
}
select {
padding: 8px 12px;
border-radius: 4px;
border: 1px solid #ccc;
font-size: 14px;
cursor: pointer;
}
#map {
height: 600px;
width: 100%;
border-radius: 6px;
}
.loading {
display: none;
color: #007bff;
font-weight: bold;
}
.depot-panel {
    background: #f8fafc;
    padding: 15px;
    margin-bottom: 15px;
    border-radius: 8px;
    border: 1px solid #ddd;
}

#depotForm {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 12px;
}

#depotForm label {
    display: block;
    font-weight: bold;
    margin-bottom: 5px;
}

#depotForm input {
    width: 100%;
    box-sizing: border-box;
    padding: 9px;
    border: 1px solid #ccc;
    border-radius: 5px;
}

#depotForm button,
.depot-row button {
    padding: 9px 12px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    background: #2563eb;
    color: white;
}

.depot-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 10px;
    padding: 10px;
    border-bottom: 1px solid #ddd;
}

.depot-pin {
    width: 28px;
    height: 28px;
    background: #e63946;
    border: 2px solid white;
    border-radius: 50% 50% 50% 0;
    transform: rotate(-45deg);
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 2px 5px #777;
}

.depot-pin span {
    color: white;
    font-weight: bold;
    transform: rotate(45deg);
}

@media (max-width: 700px) {
    #depotForm {
        grid-template-columns: 1fr;
    }
}
</style>
</head>
<body>
<div class="container">
<h2>Geographic Information System</h2>
<div class="toolbar">
<label for="levelSelect"><b>Pilih Level Wilayah:</b></label>
<select id="levelSelect" onchange="loadGadmLevel(this.value)">
<option value="0" selected>Negara</option>
<option value="1">Provinsi</option>
<option value="2">Kabupaten / Kota</option>
<option value="3">Kecamatan</option>
<option value="4">Desa / Kelurahan</option>
</select>
<span id="loadingIndicator" class="loading">Memuat data
GeoJSON...</span>
</div>
<!-- FORM INPUT DEPOT -->
<div class="depot-panel">

    <h3>Manajemen Data Depot</h3>

    <form id="depotForm">

        <input type="hidden" id="depotId">

        <div>
            <label>Nama Depot</label>
            <input type="text" id="depotName"
                placeholder="Masukkan nama depot" required>
        </div>

        <div>
            <label>Latitude</label>
            <input type="number" id="depotLat"
                step="any" placeholder="-6.326" required>
        </div>

        <div>
            <label>Longitude</label>
            <input type="number" id="depotLng"
                step="any" placeholder="108.320" required>
        </div>

        <button type="submit" id="saveDepotBtn">
            Simpan Depot
        </button>

        <button type="button" onclick="resetDepotForm()">
            Batal
        </button>

    </form>

    <h4>Daftar Depot</h4>

    <div id="depotList"></div>

</div>
<div id="map"></div>
</div>
<!-- Leaflet JS via CDN -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
// 1. Inisialisasi Peta (Center Indonesia)
const map = L.map('map').setView([-2.548926, 118.014863], 5);
// 2. Basemap OpenStreetMap
L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
maxZoom: 19,
attribution: '© OpenStreetMap'
}).addTo(map);
let currentGeoJsonLayer = null;
// 3. Fungsi Load GeoJSON dari Endpoint Laravel
function loadGadmLevel(level) {
const loading = document.getElementById('loadingIndicator');
loading.style.display = 'inline';
if (currentGeoJsonLayer) {
map.removeLayer(currentGeoJsonLayer);
}
fetch(`/api/geojson/gadm/${level}`)
.then(res => {
if (!res.ok) throw new Error(`GeoJSON Level ${level} tidak
ditemukan.`);
return res.json();
})
.then(data => {
currentGeoJsonLayer = L.geoJSON(data, {
style: {
color: '#1d3557',
weight: 1.5,
fillColor: '#457b9d',
fillOpacity: 0.4
},
onEachFeature: function(feature, layer) {
const p = feature.properties || {};
const namaWilayah = p.NAME_4 || p.NAME_3 || p.NAME_2
|| p.NAME_1 || p.NAME_0 ||
'Wilayah';
const tipeWilayah = p.TYPE_2 || p.TYPE_1 || '';
layer.bindPopup(`
<div style="font-family: sans-serif;">
<strong style="font-size: 14px;">${namaWilayah}</strong><br>
<small style="color: #666;">${tipeWilayah} (GADM Level
${level})</small>
</div>
`);
}
}).addTo(map);
map.fitBounds(currentGeoJsonLayer.getBounds());
})
.catch(err => {
alert(err.message);
})
.finally(() => {
loading.style.display = 'none';
});
}
// Default Load Level 0 saat pertama kali dibuka
loadGadmLevel(0);
// =====================================
// 1. MEMBUAT ICON DEPOT
// =====================================

const depotIcon = L.divIcon({
    className: 'depot-marker-wrap',
    html: '<div class="depot-pin"><span>D</span></div>',
    iconSize: [32, 32],
    iconAnchor: [16, 32],
    popupAnchor: [0, -30]
});


// =====================================
// 2. PENYIMPANAN DATA DEPOT
// =====================================

let depots = [];

try {
    depots = JSON.parse(
        localStorage.getItem('gis_depots') || '[]'
    );
} catch (error) {
    depots = [];
}

let depotMarkers = [];


// =====================================
// 3. MENYIMPAN DATA
// =====================================

function saveDepotData() {

    localStorage.setItem(
        'gis_depots',
        JSON.stringify(depots)
    );

}


// =====================================
// 4. MENAMPILKAN DATA DEPOT
// =====================================

function renderDepotData() {

    // Menghapus marker lama
    depotMarkers.forEach(marker => {
        map.removeLayer(marker);
    });

    depotMarkers = [];

    const list = document.getElementById('depotList');

    list.innerHTML = '';

    depots.forEach(depot => {

        // Membuat marker pada peta
        const marker = L.marker(
            [Number(depot.lat), Number(depot.lng)],
            { icon: depotIcon }
        ).addTo(map);

        marker.bindPopup(`
            <b>${depot.name}</b><br>
            Latitude: ${depot.lat}<br>
            Longitude: ${depot.lng}<br><br>

            <button onclick="editDepot('${depot.id}')">
                Edit
            </button>

            <button onclick="deleteDepot('${depot.id}')">
                Hapus
            </button>
        `);

        depotMarkers.push(marker);


        // Menampilkan daftar depot
        const row = document.createElement('div');

        row.className = 'depot-row';

        const info = document.createElement('span');

        info.textContent =
            `${depot.name} | Lat: ${depot.lat}, Lng: ${depot.lng}`;

        const editButton = document.createElement('button');

        editButton.textContent = 'Edit';
        editButton.type = 'button';

        editButton.onclick = () => editDepot(depot.id);


        const deleteButton = document.createElement('button');

        deleteButton.textContent = 'Hapus';
        deleteButton.type = 'button';

        deleteButton.onclick = () => deleteDepot(depot.id);


        row.append(info, editButton, deleteButton);

        list.appendChild(row);

    });

}


// =====================================
// 5. CREATE DAN UPDATE DEPOT
// =====================================

document.getElementById('depotForm')
.addEventListener('submit', function(event) {

    event.preventDefault();

    const id = document.getElementById('depotId').value;

    const name = document.getElementById('depotName').value.trim();

    const lat = Number(document.getElementById('depotLat').value);

    const lng = Number(document.getElementById('depotLng').value);


    // Validasi koordinat
    if (
        !name ||
        !Number.isFinite(lat) ||
        !Number.isFinite(lng) ||
        lat < -90 || lat > 90 ||
        lng < -180 || lng > 180
    ) {

        alert('Nama atau koordinat tidak valid!');

        return;

    }


    const depot = {
        id: id || String(Date.now()),
        name: name,
        lat: lat,
        lng: lng
    };


    if (id) {

        const index = depots.findIndex(
            item => String(item.id) === String(id)
        );

        if (index !== -1) {
            depots[index] = depot;
        }

    } else {

        depots.push(depot);

    }


    saveDepotData();

    renderDepotData();

    resetDepotForm();


    // Memindahkan tampilan peta ke lokasi depot
    map.setView([lat, lng], 13);

});


// =====================================
// 6. EDIT DEPOT
// =====================================

function editDepot(id) {

    const depot = depots.find(
        item => String(item.id) === String(id)
    );

    if (!depot) return;

    document.getElementById('depotId').value = depot.id;

    document.getElementById('depotName').value = depot.name;

    document.getElementById('depotLat').value = depot.lat;

    document.getElementById('depotLng').value = depot.lng;

    document.getElementById('saveDepotBtn').textContent =
        'Perbarui Depot';

    map.setView([depot.lat, depot.lng], 13);

}


// =====================================
// 7. DELETE DEPOT
// =====================================

function deleteDepot(id) {

    if (!confirm('Apakah Anda yakin ingin menghapus depot?')) {
        return;
    }

    depots = depots.filter(
        item => String(item.id) !== String(id)
    );

    saveDepotData();

    renderDepotData();

}


// =====================================
// 8. RESET FORM
// =====================================

function resetDepotForm() {

    document.getElementById('depotForm').reset();

    document.getElementById('depotId').value = '';

    document.getElementById('saveDepotBtn').textContent =
        'Simpan Depot';

}


// =====================================
// 9. MENAMPILKAN DATA SAAT APLIKASI DIBUKA
// =====================================

renderDepotData();
</script>
</body>
</html>