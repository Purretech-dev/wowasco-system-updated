<?php
error_reporting(E_ALL);
ini_set('display_errors',1);

include $_SERVER['DOCUMENT_ROOT'].'/wowasco/api/db.php';

/* ================= KPIs ================= */
$total = $conn->query("SELECT COUNT(*) as t FROM meters")->fetch_assoc()['t'];
$active = $conn->query("SELECT COUNT(*) as t FROM meters WHERE status='Active'")->fetch_assoc()['t'];
$inactive = $conn->query("SELECT COUNT(*) as t FROM meters WHERE status='Inactive'")->fetch_assoc()['t'];
$alerts = $conn->query("SELECT * FROM meter_alerts WHERE status='Active'");

$alert_count = $alerts->num_rows;

$efficiency = ($total > 0) ? round(($active / $total) * 100, 1) : 0;

/* ================= CUSTOMER TYPES ================= */
$types = ["Government entities","Residential","Businesses","Personal"];
$typeCounts = [];

foreach($types as $type){
    $count = $conn->query("SELECT COUNT(*) as t FROM meters WHERE customer_type='$type'")->fetch_assoc()['t'];
    $typeCounts[] = $count;
}

/* ================= CONSUMPTION ================= */
$consumption = $conn->query("
SELECT reading_date, SUM(reading_value) as total 
FROM meter_readings 
GROUP BY reading_date 
ORDER BY reading_date DESC 
LIMIT 7
");

$dates = [];
$values = [];

while($row = $consumption->fetch_assoc()){
    $dates[] = $row['reading_date'];
    $values[] = $row['total'];
}

$dates = array_reverse($dates);
$values = array_reverse($values);

/* ================= HEALTH ================= */
$healthScore = 100;

if($inactive > ($total * 0.3)) $healthScore -= 30;
if($alert_count > 10) $healthScore -= 20;
if($efficiency < 70) $healthScore -= 20;

$healthStatus = "Stable";
if($healthScore < 70) $healthStatus = "Warning ⚠️";
if($healthScore < 50) $healthStatus = "Critical 🔴";

/* ================= AVG ================= */
$avg = (count($values) > 0) ? array_sum($values) / count($values) : 0;
?>

<!DOCTYPE html>
<html>
<head>
<title>Smart Meter Dashboard FIXED CHARTS</title>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>

<style>

body{
    font-family: Arial;
    background:#f4f6f9;
    margin:0;
}

.container{
    width:95%;
    margin:auto;
    padding:20px;
}

h2{color:#003366;}

/* KPI */
.cards{
    display:grid;
    grid-template-columns: repeat(auto-fit, minmax(180px,1fr));
    gap:15px;
    margin-bottom:25px;
}

.card{
    background:white;
    padding:20px;
    border-radius:8px;
    box-shadow:0 2px 6px rgba(0,0,0,0.1);
    text-align:center;
}

.card h3{
    margin:0;
    font-size:26px;
    color:#003366;
}

/* ALERT */
.alert-btn{
    background:#dc3545;
    color:white;
    border:none;
    padding:10px 15px;
    border-radius:6px;
    cursor:pointer;
    font-weight:bold;
    width:100%;
}

.alert-panel{
    display:none;
    background:white;
    padding:15px;
    border-radius:8px;
    margin-top:10px;
    box-shadow:0 2px 6px rgba(0,0,0,0.1);
}

.alert-item{
    padding:8px;
    border-bottom:1px solid #eee;
    font-size:13px;
}

/* CHART GRID */
.charts{
    display:grid;
    grid-template-columns: repeat(auto-fit, minmax(300px,1fr));
    gap:20px;
}

/* CARD */
.chart-box{
    background:white;
    padding:15px;
    border-radius:8px;
    box-shadow:0 2px 6px rgba(0,0,0,0.1);
}

/* 🔥 PIE FIX (perfect circle) */
.pie-wrap{
    width:260px;
    height:260px;
    margin:0 auto;
}

/* 🔥 FIX BAR + LINE HEIGHT CONTROL */
.chart-box canvas{
    max-width:100%;
}

/* specific sizing wrappers */
.bar-wrap,
.line-wrap{
    height:260px;
}

/* MAP */
#map{
    width:100%;
    height:300px;
    border-radius:8px;
}

/* BACK */
.back-btn{
    display:inline-block;
    margin-top:20px;
    padding:10px 15px;
    background:#6c757d;
    color:white;
    text-decoration:none;
    border-radius:5px;
}

</style>
</head>

<body>

<div class="container">

<h2>📊 Smart Meter Control Dashboard</h2>

<!-- KPI -->
<div class="cards">

<div class="card">
<h3><?= $total ?></h3>
<p>Total Meters</p>
</div>

<div class="card">
<h3><?= $active ?></h3>
<p>Active</p>
</div>

<div class="card">
<h3><?= $inactive ?></h3>
<p>Inactive</p>
</div>

<div class="card">
<h3><?= $efficiency ?>%</h3>
<p>Efficiency</p>
</div>

<div class="card">
<button class="alert-btn" onclick="toggleAlerts()">
🚨 Alerts (<?= $alert_count ?>)
</button>

<div class="alert-panel" id="alertPanel">
<?php while($a = $alerts->fetch_assoc()): ?>
<div class="alert-item">
<b>Meter:</b> <?= $a['meter_id'] ?? 'N/A' ?><br>
<?= $a['message'] ?? 'Alert triggered' ?>
</div>
<?php endwhile; ?>
</div>

</div>

</div>

<!-- HEALTH -->
<div class="status-panel">
<h3>🧠 System Intelligence</h3>
<p><b>Status:</b> <?= $healthStatus ?></p>
<p><b>Health Score:</b> <?= $healthScore ?>/100</p>
<p><b>Avg Consumption:</b> <?= round($avg,2) ?></p>
</div>

<!-- CHARTS -->
<div class="charts">

<!-- PIE -->
<div class="chart-box">
<h3>Status</h3>
<div class="pie-wrap">
<canvas id="statusChart"></canvas>
</div>
</div>

<!-- BAR -->
<div class="chart-box">
<h3>Customer Types</h3>
<div class="bar-wrap">
<canvas id="customerChart"></canvas>
</div>
</div>

<!-- LINE -->
<div class="chart-box">
<h3>Consumption Trend</h3>
<div class="line-wrap">
<canvas id="consumptionChart"></canvas>
</div>
</div>

<div class="chart-box">
<h3>📍 Network Map</h3>
<div id="map"></div>
</div>

</div>

<a href="/wowasco/index.php" class="back-btn">← Back</a>

</div>

<script>

function toggleAlerts(){
    let panel = document.getElementById("alertPanel");
    panel.style.display = (panel.style.display === "block") ? "none" : "block";
}

/* ================= PIE ================= */
new Chart(document.getElementById('statusChart'), {
type:'doughnut',
data:{
labels:['Active','Inactive'],
datasets:[{
data:[<?= $active ?>,<?= $inactive ?>],
backgroundColor:['#28a745','#dc3545']
}]
},
options:{
responsive:true,
maintainAspectRatio:true,
cutout:'60%'
}
});

/* ================= BAR (FIXED HEIGHT) ================= */
new Chart(document.getElementById('customerChart'), {
type:'bar',
data:{
labels:['Gov','Residential','Business','Personal'],
datasets:[{
data:<?= json_encode($typeCounts) ?>,
backgroundColor:['#003366','#007bff','#ffc107','#17a2b8']
}]
},
options:{
responsive:true,
maintainAspectRatio:false
}
});

/* ================= LINE (FIXED HEIGHT) ================= */
new Chart(document.getElementById('consumptionChart'), {
type:'line',
data:{
labels:<?= json_encode($dates) ?>,
datasets:[{
label:'Consumption',
data:<?= json_encode($values) ?>,
borderColor:'#007bff',
fill:false
}]
},
options:{
responsive:true,
maintainAspectRatio:false
}
});

/* MAP */
var map = L.map('map').setView([-1.5, 37.6], 7);

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',{
maxZoom:19
}).addTo(map);

L.marker([-1.5, 37.6]).addTo(map).bindPopup("Central Zone");
L.marker([-1.3, 37.2]).addTo(map).bindPopup("North Zone");
L.marker([-1.7, 37.9]).addTo(map).bindPopup("South Zone");

setTimeout(()=>map.invalidateSize(),300);

</script>

</body>
</html>