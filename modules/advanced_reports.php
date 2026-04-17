<?php
require_once __DIR__ . '/../api/db.php';

/* ================= SETTINGS ================= */
$section = $_GET['section'] ?? 'executive';

$startDate = $_GET['start_date'] ?? '';
$endDate   = $_GET['end_date'] ?? '';
$status    = $_GET['status'] ?? '';
$type      = $_GET['type'] ?? '';

/* ================= SAFE TABLE CHECK ================= */
function table_exists($conn, $table){
    $result = $conn->query("SHOW TABLES LIKE '$table'");
    return $result && $result->num_rows > 0;
}

/* ================= LOAD TABLE SAFELY ================= */
function loadTable($conn, $table){
    $data = [];

    if (table_exists($conn, $table)) {
        $res = $conn->query("SELECT * FROM `$table`");
        if ($res) {
            while ($row = $res->fetch_assoc()) {
                $data[] = $row;
            }
        }
    }

    return $data;
}

/* ================= GET DATE COLUMN ================= */
function getDateColumn($conn, $table){
    $possible = ['created_at','date','created','timestamp','updated_at'];

    $res = $conn->query("SHOW COLUMNS FROM `$table`");
    if(!$res) return null;

    $cols = [];
    while($r = $res->fetch_assoc()){
        $cols[] = $r['Field'];
    }

    foreach($possible as $p){
        if(in_array($p,$cols)) return $p;
    }

    return null;
}

/* ================= FILTER ENGINE ================= */
function applyFilters($data, $dateColumn, $startDate, $endDate, $status, $statusField = 'status', $type = '', $typeField = 'type'){

    if(!is_array($data)) $data = [];

    return array_filter($data, function($row) use ($dateColumn, $startDate, $endDate, $status, $statusField, $type, $typeField){

        /* DATE FILTER */
        if($dateColumn && ($startDate || $endDate)){
            $rowDate = isset($row[$dateColumn]) ? substr($row[$dateColumn],0,10) : null;

            if($startDate && $rowDate < $startDate) return false;
            if($endDate && $rowDate > $endDate) return false;
        }

        /* STATUS FILTER */
        if($status && isset($row[$statusField]) && strtolower($row[$statusField]) != strtolower($status)){
            return false;
        }

        /* TYPE FILTER */
        if($type && isset($row[$typeField]) && strtolower($row[$typeField]) != strtolower($type)){
            return false;
        }

        return true;
    });
}

/* ================= LOAD MODULES ================= */
$meters        = loadTable($conn,'meters');
$customers     = loadTable($conn,'customers');
$assets        = loadTable($conn,'assets');
$infrastructure = loadTable($conn,'infrastructure'); // ✅ ONLY infrastructure retained

/* ================= DATE COLUMNS ================= */
$mDate = getDateColumn($conn,'meters');
$cDate = getDateColumn($conn,'customers');
$aDate = getDateColumn($conn,'assets');
$iDate = getDateColumn($conn,'infrastructure');

/* ================= APPLY FILTERS ================= */
$meters         = applyFilters($meters,$mDate,$startDate,$endDate,$status,'status',$type,'customer_type');
$customers      = applyFilters($customers,$cDate,$startDate,$endDate,$status,'status',$type,'type');
$assets         = applyFilters($assets,$aDate,$startDate,$endDate,$status,'status',$type,'type');
$infrastructure = applyFilters($infrastructure,$iDate,$startDate,$endDate,$status,'status');

/* ================= ANALYTICS ================= */
$totalMeters = count($meters);
$totalCustomers = count($customers);
$totalAssets = count($assets);
$totalInfrastructure = count($infrastructure);

$active = $faulty = $disc = 0;
$zones = [];

foreach ($meters as $m) {
    if (($m['status'] ?? '') == 'Active') $active++;
    elseif (($m['status'] ?? '') == 'Faulty') $faulty++;
    elseif (($m['status'] ?? '') == 'Disconnected') $disc++;

    $zones[$m['location'] ?? 'Unknown'] = ($zones[$m['location'] ?? 'Unknown'] ?? 0) + 1;
}

$systemAssets = $totalMeters + $totalAssets + $totalInfrastructure;
$issues = $faulty + $disc;
$health = $systemAssets ? round(($active / max($systemAssets,1)) * 100, 1) : 0;

arsort($zones);
$topZone = key($zones);
?>

<!DOCTYPE html>
<html>
<head>
<title>WOWASCO Unified Reporting System</title>

<style>
body{margin:0;font-family:Segoe UI;background:#f5f7fb;}
.header{background:linear-gradient(135deg,#0b3d91,#1a5edb);color:#fff;padding:18px;font-size:20px;}
.nav{display:flex;gap:8px;padding:12px;background:#fff;}
.nav a{padding:8px 12px;background:#f1f3f9;text-decoration:none;border-radius:6px;}
.grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:15px;padding:20px;}
.card{background:#fff;padding:15px;border-radius:12px;}
.card b{font-size:20px;color:#0b3d91;}
.table{margin:20px;background:#fff;border-radius:10px;overflow:hidden;}
table{width:100%;border-collapse:collapse;}
th,td{padding:10px;border-bottom:1px solid #eee;font-size:14px;}
th{background:#f1f3f9;}
.filter-box{background:#fff;margin:15px;padding:15px;border-radius:10px;}
</style>
</head>

<body>

<div class="header">📊 WOWASCO Unified Periodic Reporting System</div>

<div class="nav">
    <a href="?section=executive">Executive</a>
    <a href="?section=meters">Meters</a>
    <a href="?section=crm">CRM</a>
    <a href="?section=assets">Assets</a>
    <a href="?section=infrastructure">Infrastructure</a>
</div>

<!-- FILTER -->
<div class="filter-box">
<form method="GET">
<input type="hidden" name="section" value="<?= $section ?>">

<label>Start Date:</label>
<input type="date" name="start_date" value="<?= $startDate ?>">

<label>End Date:</label>
<input type="date" name="end_date" value="<?= $endDate ?>">

<label>Status:</label>
<input type="text" name="status" value="<?= $status ?>">

<label>Type:</label>
<input type="text" name="type" value="<?= $type ?>">

<button type="submit">Generate Report</button>
</form>
</div>

<!-- EXECUTIVE -->
<?php if($section=='executive'): ?>

<div class="grid">
<div class="card"><b><?= $totalMeters ?></b><br>Meters</div>
<div class="card"><b><?= $totalCustomers ?></b><br>Customers</div>
<div class="card"><b><?= $totalAssets ?></b><br>Assets</div>
<div class="card"><b><?= $totalInfrastructure ?></b><br>Infrastructure</div>
<div class="card"><b><?= $health ?>%</b><br>System Health</div>
</div>

<div class="card" style="margin:20px;">
<h3>📍 Executive Insight</h3>
<ul>
<li>Top operational zone: <b><?= $topZone ?></b></li>
<li>Total system issues: <b><?= $issues ?></b></li>
<li>Infrastructure fully integrated into reporting system</li>
<li>CRM + Assets + Meters + Infrastructure unified analytics active</li>
</ul>
</div>

<?php endif; ?>

<!-- METERS -->
<?php if($section=='meters'): ?>
<div class="table">
<table>
<tr><th>Serial</th><th>Customer</th><th>Type</th><th>Location</th><th>Status</th></tr>
<?php foreach($meters as $m): ?>
<tr>
<td><?= $m['serial_number'] ?? '' ?></td>
<td><?= $m['customer_name'] ?? '' ?></td>
<td><?= $m['customer_type'] ?? '' ?></td>
<td><?= $m['location'] ?? '' ?></td>
<td><?= $m['status'] ?? '' ?></td>
</tr>
<?php endforeach; ?>
</table>
</div>
<?php endif; ?>

<!-- CRM -->
<?php if($section=='crm'): ?>
<div class="table">
<table>
<tr><th>Name</th><th>Type</th><th>Contact</th></tr>
<?php foreach($customers as $c): ?>
<tr>
<td><?= $c['name'] ?? '' ?></td>
<td><?= $c['type'] ?? '' ?></td>
<td><?= $c['phone'] ?? '' ?></td>
</tr>
<?php endforeach; ?>
</table>
</div>
<?php endif; ?>

<!-- ASSETS -->
<?php if($section=='assets'): ?>
<div class="table">
<table>
<tr><th>Name</th><th>Type</th><th>Status</th></tr>
<?php foreach($assets as $a): ?>
<tr>
<td><?= $a['name'] ?? '' ?></td>
<td><?= $a['type'] ?? '' ?></td>
<td><?= $a['status'] ?? '' ?></td>
</tr>
<?php endforeach; ?>
</table>
</div>
<?php endif; ?>

<!-- INFRASTRUCTURE -->
<?php if($section=='infrastructure'): ?>
<div class="table">
<table>
<tr><th>Name</th><th>Type</th><th>Status</th></tr>
<?php foreach($infrastructure as $i): ?>
<tr>
<td><?= $i['name'] ?? '' ?></td>
<td><?= $i['type'] ?? '' ?></td>
<td><?= $i['status'] ?? '' ?></td>
</tr>
<?php endforeach; ?>
</table>
</div>
<?php endif; ?>

</body>
</html>