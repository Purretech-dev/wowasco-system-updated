<?php
// Include DB connection
include $_SERVER['DOCUMENT_ROOT'].'/wowasco/api/db.php';

/* ==========================
   GENERATE DUMMY READINGS IF NONE
========================== */
$meters = $conn->query("SELECT id FROM meters");

while($m = $meters->fetch_assoc()){
    $meter_id = $m['id'];
    
    $check = $conn->query("SELECT COUNT(*) as total FROM meter_readings WHERE meter_id = $meter_id");
    $row_check = $check->fetch_assoc();
    
    if($row_check['total'] == 0){
        for($i=0; $i<7; $i++){
            $date = date('Y-m-d H:i:s', strtotime("-$i days"));
            $volume = rand(50, 500);
            $conn->query("INSERT INTO meter_readings (meter_id, reading_value, reading_date) VALUES ($meter_id, $volume, '$date')");
        }
    }
}

/* ==========================
   FILTER
========================== */
$start = $_GET['start_date'] ?? '';
$end = $_GET['end_date'] ?? '';

$filter_sql = "";
if($start && $end){
    $filter_sql = " AND r.reading_date BETWEEN '$start 00:00:00' AND '$end 23:59:59'";
}

/* ==========================
   PER METER
========================== */
$sql = "SELECT m.serial_number, m.customer_name,
               SUM(r.reading_value) as pumped_volume
        FROM meters m
        LEFT JOIN meter_readings r ON m.id = r.meter_id $filter_sql
        GROUP BY m.id
        ORDER BY m.serial_number ASC";

$result = $conn->query($sql);

/* ==========================
   CUSTOMER TYPE ANALYSIS
========================== */
$type_sql = "SELECT m.customer_type,
                    SUM(r.reading_value) as total_volume
             FROM meters m
             LEFT JOIN meter_readings r ON m.id = r.meter_id $filter_sql
             GROUP BY m.customer_type";

$type_result = $conn->query($type_sql);

$total_volume_all = 0;
$type_data = [];

while($row = $type_result->fetch_assoc()){
    $type = $row['customer_type'] ?? 'Unknown';
    $volume = $row['total_volume'] ?? 0;

    // ================= LABEL TRANSFORMATION ONLY =================
    if($type == "Businesses"){
        $type = "Commercial";
    } elseif($type == "Personal"){
        $type = "Domestic";
    }

    $type_data[$type] = $volume;
    $total_volume_all += $volume;
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Pumped Volumes - WOWASCO</title>

<style>
body {
    font-family: 'Segoe UI', sans-serif;
    background: #eef3f8;
    margin: 0;
}

.container {
    width: 90%;
    margin: 30px auto;
}

h2 {
    text-align: center;
    color: #003366;
}

/* CARD */
.card {
    background: #fff;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 3px 12px rgba(0,0,0,0.1);
    margin-bottom: 20px;
}

/* TABLE (FULL FIX) */
table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 15px;
    table-layout: fixed; /* ✅ FIX */
}

th, td {
    padding: 12px 15px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

/* HEADER */
th {
    background: #003366;
    color: #fff;
    text-align: left;
}

/* COLUMN WIDTHS */
th:nth-child(1), td:nth-child(1) {
    width: 30%;
}

th:nth-child(2), td:nth-child(2) {
    width: 40%;
}

th:nth-child(3), td:nth-child(3) {
    width: 30%;
    text-align: right; /* ✅ PERFECT ALIGNMENT */
}

/* STRIPES */
tr:nth-child(even) {
    background: #f2f2f2;
}

/* BUTTON */
.toggle-btn {
    background: #003366;
    color: white;
    border: none;
    padding: 10px 15px;
    border-radius: 6px;
    cursor: pointer;
    font-weight: bold;
}

.toggle-btn:hover {
    background: #00509e;
}

/* HIDDEN */
.hidden {
    display: none;
}

/* FILTER */
.filter-form {
    display: flex;
    flex-wrap: wrap;
    align-items: flex-end;
}

.filter-form input, button {
    padding: 10px;
    margin-right: 10px;
}

/* ANALYSIS */
.analysis-item {
    padding: 10px;
    border-bottom: 1px solid #ddd;
}

.analysis-item span {
    float: right;
    font-weight: bold;
    color: #003366;
}

/* BACK */
.back-btn {
    display: inline-block;
    margin-top: 15px;
    padding: 10px 15px;
    background: #6c757d;
    color: white;
    text-decoration: none;
    border-radius: 5px;
}
</style>
</head>

<body>

<div class="container">

<h2>Pumped Volumes Per Meter</h2>

<!-- FILTER -->
<div class="card">
<form method="GET" class="filter-form">
    <label>From:</label>
    <input type="date" name="start_date" value="<?= htmlspecialchars($start) ?>" required>

    <label>To:</label>
    <input type="date" name="end_date" value="<?= htmlspecialchars($end) ?>" required>

    <button type="submit" class="toggle-btn">Filter</button>
</form>
</div>

<!-- ANALYSIS -->
<div class="card">
<h3>Consumption by Customer Type</h3>

<?php if(!empty($type_data)): ?>
    <?php foreach($type_data as $type => $volume): 
        $percentage = ($total_volume_all > 0) ? round(($volume/$total_volume_all)*100,1) : 0;
    ?>
        <div class="analysis-item">
            <?= htmlspecialchars($type) ?>
            <span><?= number_format($volume) ?> m³ (<?= $percentage ?>%)</span>
        </div>
    <?php endforeach; ?>

    <p><b>Total:</b> <?= number_format($total_volume_all) ?> m³</p>
<?php else: ?>
    <p>No data available.</p>
<?php endif; ?>
</div>

<!-- BUTTON + TABLE -->
<div class="card">

<button class="toggle-btn" onclick="toggleTable()">
View Consumption per Customer
</button>

<div id="tableBox" class="hidden">

<table>
<tr>
    <th>Meter Serial</th>
    <th>Customer Name</th>
    <th>Pumped Volume (m³)</th>
</tr>

<?php if($result->num_rows > 0): ?>
    <?php while($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($row['serial_number']) ?></td>
            <td><?= htmlspecialchars($row['customer_name']) ?></td>
            <td><?= number_format($row['pumped_volume'] ?? 0) ?></td>
        </tr>
    <?php endwhile; ?>
<?php else: ?>
<tr>
<td colspan="3" style="text-align:center;">No data available.</td>
</tr>
<?php endif; ?>

</table>

</div>
</div>

<a href="/wowasco/index.php" class="back-btn">← Back</a>

</div>

<script>
function toggleTable(){
    let box = document.getElementById("tableBox");
    box.style.display = (box.style.display === "block") ? "none" : "block";
}
</script>

</body>
</html>