<?php include '../../api/db.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <title>GIS Zoning Dashboard</title>

    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css"/>
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

    <style>
        body {
            margin: 0;
            font-family: Arial;
        }

        .topbar {
            background: #0b3d91;
            color: white;
            padding: 12px;
        }

        .controls {
            padding: 10px;
            background: #f4f4f4;
        }

        button {
            padding: 8px 12px;
            margin-right: 5px;
            cursor: pointer;
        }

        #map {
            height: 85vh;
            width: 100%;
        }
    </style>
</head>

<body>

<div class="topbar">
    🌍 GIS Zoning & Meter Mapping Dashboard
</div>

<div class="controls">
    <button onclick="loadMeters()">📍 Load Meters</button>
    <button onclick="loadZones()">🧭 Load Zones</button>
    <button onclick="clearMap()">🧹 Clear Map</button>
</div>

<div id="map"></div>

<script>
let map = L.map('map').setView([-1.780, 37.630], 12);

// Base map
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: 'WOWASCO GIS'
}).addTo(map);

// Layers
let meterLayer = L.layerGroup().addTo(map);
let zoneLayer = L.layerGroup().addTo(map);

/* ================= ICONS ================= */
function getMeterIcon(status) {
    let color;

    if (status === "Active") color = "green";
    else if (status === "Faulty") color = "red";
    else if (status === "Disconnected") color = "gray";
    else color = "blue";

    return L.icon({
        iconUrl: `https://maps.google.com/mapfiles/ms/icons/${color}-dot.png`,
        iconSize: [32, 32]
    });
}

/* ================= CLEAR MAP ================= */
function clearMap() {
    meterLayer.clearLayers();
    zoneLayer.clearLayers();
}

/* ================= LOAD METERS ================= */
function loadMeters() {

    meterLayer.clearLayers();

    // ===== SAMPLE METERS (ALL UPDATED TO 2026) =====
    let sampleMeters = [
        {serial_number:"WM-001", customer_name:"John Mutiso", customer_type:"Domestic", installation_date:"2026-01-15", meter_location:"Town", status:"Active", latitude:-1.780, longitude:37.630},
        {serial_number:"WM-002", customer_name:"Mary Wambui", customer_type:"Commercial", installation_date:"2026-02-10", meter_location:"Kasarani", status:"Faulty", latitude:-1.745, longitude:37.660},
        {serial_number:"WM-003", customer_name:"Peter Mwanzia", customer_type:"Residential", installation_date:"2026-03-20", meter_location:"Kaiti", status:"Active", latitude:-1.790, longitude:37.605},
        {serial_number:"WM-004", customer_name:"Grace Nduku", customer_type:"Institutional", installation_date:"2026-01-05", meter_location:"Mukuyuni", status:"Disconnected", latitude:-1.830, longitude:37.610},
        {serial_number:"WM-005", customer_name:"Samuel Kioko", customer_type:"Business", installation_date:"2026-04-18", meter_location:"Westlands", status:"Active", latitude:-1.730, longitude:37.630},

        {serial_number:"WM-006", customer_name:"Makueni County Offices", customer_type:"Government", installation_date:"2026-02-12", meter_location:"Town", status:"Active", latitude:-1.779, longitude:37.628},
        {serial_number:"WM-007", customer_name:"Kasarani Supermarket", customer_type:"Business", installation_date:"2026-03-25", meter_location:"Kasarani", status:"Active", latitude:-1.746, longitude:37.662},
        {serial_number:"WM-008", customer_name:"Jane Wanjiku", customer_type:"Residential", installation_date:"2026-01-14", meter_location:"Muambani", status:"Faulty", latitude:-1.850, longitude:37.640},
        {serial_number:"WM-009", customer_name:"Green Valley School", customer_type:"Government", installation_date:"2026-02-28", meter_location:"Mwaani", status:"Active", latitude:-1.860, longitude:37.670},
        {serial_number:"WM-010", customer_name:"David Kioko", customer_type:"Domestic", installation_date:"2026-04-05", meter_location:"Kilala", status:"Disconnected", latitude:-1.770, longitude:37.600}
    ];

    // Render meters
    sampleMeters.forEach(m => {
        L.marker([m.latitude, m.longitude], {
            icon: getMeterIcon(m.status)
        })
        .bindPopup(`
            <b>🔢 ${m.serial_number}</b><br>
            👤 ${m.customer_name}<br>
            🧾 Type: ${m.customer_type}<br>
            📅 Installed: ${m.installation_date}<br>
            📍 ${m.meter_location}<br>
            ⚡ Status: <b>${m.status}</b>
        `)
        .addTo(meterLayer);
    });

    // ===== LOAD FROM DB =====
    fetch('map_data.php?type=meters')
    .then(res => res.json())
    .then(data => {
        data.forEach(m => {
            if (m.latitude && m.longitude) {
                L.marker([m.latitude, m.longitude], {
                    icon: getMeterIcon(m.status)
                })
                .bindPopup(`
                    <b>🔢 ${m.serial_number}</b><br>
                    👤 ${m.customer_name}<br>
                    🧾 Type: ${m.customer_type}<br>
                    📅 Installed: ${m.installation_date}<br>
                    📍 ${m.meter_location}<br>
                    ⚡ Status: <b>${m.status || 'Unknown'}</b>
                `)
                .addTo(meterLayer);
            }
        });
    })
    .catch(() => {
        console.log("Using sample meter data");
    });
}

/* ================= LOAD ZONES ================= */
function loadZones() {

    zoneLayer.clearLayers();

    let zones = [
        {name:"Town", coords:[[-1.78,37.63],[-1.77,37.64],[-1.79,37.65]]},
        {name:"Shimo", coords:[[-1.80,37.62],[-1.79,37.63],[-1.81,37.64]]},
        {name:"Unoa", coords:[[-1.76,37.62],[-1.75,37.63],[-1.77,37.64]]},
        {name:"Kasarani", coords:[[-1.74,37.65],[-1.73,37.66],[-1.75,37.67]]},
        {name:"Kundakindu", coords:[[-1.82,37.66],[-1.81,37.67],[-1.83,37.68]]},
        {name:"Return", coords:[[-1.78,37.67],[-1.77,37.68],[-1.79,37.69]]},
        {name:"Westlands", coords:[[-1.73,37.62],[-1.72,37.63],[-1.74,37.64]]},
        {name:"Muambani", coords:[[-1.85,37.63],[-1.84,37.64],[-1.86,37.65]]},
        {name:"Mwaani", coords:[[-1.86,37.66],[-1.85,37.67],[-1.87,37.68]]},
        {name:"Kaiti", coords:[[-1.79,37.60],[-1.78,37.61],[-1.80,37.62]]},
        {name:"Kilala", coords:[[-1.77,37.59],[-1.76,37.60],[-1.78,37.61]]},
        {name:"Mukuyuni", coords:[[-1.83,37.60],[-1.82,37.61],[-1.84,37.62]]},
        {name:"Kitikyumu", coords:[[-1.75,37.68],[-1.74,37.69],[-1.76,37.70]]}
    ];

    zones.forEach(z => {
        L.polygon(z.coords, {
            color: "#3388ff"
        })
        .bindPopup(`<b>🧭 ${z.name}</b>`)
        .addTo(zoneLayer);
    });

    // Load from DB
    fetch('map_data.php?type=zones')
    .then(res => res.json())
    .then(data => {
        data.forEach(z => {
            let coords = JSON.parse(z.zone_polygon);

            L.polygon(coords, {
                color: z.zone_color || "#ff6600"
            })
            .bindPopup(`
                <b>🧭 ${z.zone_name}</b><br>
                Code: ${z.zone_code}
            `)
            .addTo(zoneLayer);
        });
    })
    .catch(() => {
        console.log("Using sample zones");
    });
}
</script>

</body>
</html>