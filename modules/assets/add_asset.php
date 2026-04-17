<?php
require_once __DIR__ . '/../../api/db.php';

$success = false;
$editData = null; 

/* LOAD EDIT DATA */
if (isset($_GET['edit'])) {
    $id = (int) $_GET['edit'];
    $res = $conn->query("SELECT * FROM assets WHERE id=$id");
    $editData = $res->fetch_assoc();
}

/* LOAD METERS */
$meters = $conn->query("SELECT serial_number, Zone, status FROM meters");
$meterData = [];
while ($m = $meters->fetch_assoc()) {
    $meterData[] = $m;
}

/* SAVE */
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $date_added = date('Y-m-d');

    $serial_number = ($_POST['asset_type'] === 'Smart Meter')
        ? ($_POST['meter_serial'] ?? '')
        : ($_POST['manual_serial'] ?? '');

    $asset_value = (float) ($_POST['asset_value'] ?? 0);
    $purchase_date = $_POST['purchase_date'] ?? date('Y-m-d');

    /* ================= NEW VALIDATION (NO FUTURE DATES) ================= */
    $today = date('Y-m-d');

    if ($purchase_date > $today) {
        echo "<script>
            alert('Purchase date cannot be in the future!');
            window.history.back();
        </script>";
        exit();
    }

    $years_used = (time() - strtotime($purchase_date)) / (365*24*60*60);
    $rate = 0.20;

    $depreciated = $asset_value * $rate * $years_used;
    if ($depreciated > $asset_value) $depreciated = $asset_value;

    $net = $asset_value - $depreciated;

    if (!empty($_POST['edit_id'])) {

        $id = (int) $_POST['edit_id'];

        $stmt = $conn->prepare("
            UPDATE assets SET
            asset_name=?, asset_type=?, subtype=?, serial_number=?,
            location=?, purchase_date=?, status=?,
            asset_value=?, depreciated_value=?, net_value=?
            WHERE id=?
        ");

        $stmt->bind_param(
            "sssssssdddi",
            $_POST['asset_name'],
            $_POST['asset_type'],
            $_POST['subtype'],
            $serial_number,
            $_POST['location'],
            $_POST['purchase_date'],
            $_POST['status'],
            $asset_value,
            $depreciated,
            $net,
            $id
        );

        $stmt->execute();

    } else {

        $stmt = $conn->prepare("
            INSERT INTO assets
            (asset_name, asset_type, subtype, serial_number, location,
             purchase_date, date_added, status,
             asset_value, depreciated_value, net_value)
            VALUES (?,?,?,?,?,?,?,?,?,?,?)
        ");

        $stmt->bind_param(
            "ssssssssddd",
            $_POST['asset_name'],
            $_POST['asset_type'],
            $_POST['subtype'],
            $serial_number,
            $_POST['location'],
            $_POST['purchase_date'],
            $date_added,
            $_POST['status'],
            $asset_value,
            $depreciated,
            $net
        );

        $stmt->execute();

        $success = true;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>WOWASCO Assets</title>

<style>
:root{
    --blue:#0b2d5c;
    --blue-soft:#1d4ed8;
    --green:#0f6b5f;
    --green-soft:#22c55e;
    --yellow:#facc15;
    --bg:#f4f7fb;
}

body{
    margin:0;
    font-family:Segoe UI, Arial;
    background:var(--bg);
}

.header{
    background:linear-gradient(135deg, var(--blue), var(--blue-soft));
    color:white;
    padding:18px 25px;
    font-size:18px;
    font-weight:700;
    position:relative;
}

.header::after{
    content:"";
    position:absolute;
    bottom:0;
    left:0;
    height:4px;
    width:100%;
    background:var(--yellow);
}

.container{
    max-width:650px;
    margin:20px auto;
    padding:0 12px;
}

.card{
    background:#fff;
    padding:25px;
    border-radius:14px;
    box-shadow:0 10px 25px rgba(0,0,0,0.08);
    border-left:6px solid var(--green);
}

.title{
    font-size:20px;
    font-weight:700;
    color:var(--blue);
    margin-bottom:18px;
}

.grid{
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:14px;
}

.full{grid-column:1 / -1;}

label{
    font-size:12px;
    font-weight:700;
    color:#334155;
    margin-bottom:4px;
    display:block;
}

input, select{
    width:100%;
    padding:6px 10px;
    border-radius:6px;
    border:1px solid #d1d5db;
    font-size:12.5px;
    height:34px;
}

.summary{
    margin-top:15px;
    padding:12px;
    background:#ecfdf5;
    border-left:5px solid var(--green);
    border-radius:8px;
    font-size:13px;
}

.btn{
    width:100%;
    padding:11px;
    border:none;
    border-radius:8px;
    font-size:14px;
    font-weight:700;
    cursor:pointer;
    margin-top:14px;
}

.btn-save{
    background:linear-gradient(135deg, var(--green), var(--green-soft));
    color:white;
}

.back{
    display:inline-block;
    margin-top:12px;
    padding:8px 12px;
    background:var(--blue);
    color:white;
    text-decoration:none;
    border-radius:8px;
    font-size:13px;
}
</style>
</head>

<body>

<div class="header">WOWASCO Asset Register System</div>

<div class="container">
<div class="card">

<div class="title">
<?= $editData ? "Edit Asset Details" : "Add New Asset" ?>
</div>

<?php if($success): ?>
<script>
alert("Asset added successfully");
</script>

<a href="view_assets.php" class="back" style="background:green;margin-bottom:10px;display:inline-block;">
✔ View Asset
</a>
<?php endif; ?>

<form method="POST">
<input type="hidden" name="edit_id" value="<?= $editData['id'] ?? '' ?>">

<div class="grid">

<div class="full">
<label>Asset Name</label>
<input name="asset_name" value="<?= $editData['asset_name'] ?? '' ?>" required>
</div>

<div>
<label>Asset Type</label>
<select name="asset_type" id="asset_type" onchange="toggleSerial()" required>
    <option value="">Select</option>
    <option value="Smart Meter" <?= ($editData['asset_type'] ?? '')=='Smart Meter'?'selected':'' ?>>Smart Meter</option>
    <option value="Field Asset" <?= ($editData['asset_type'] ?? '')=='Field Asset'?'selected':'' ?>>Field Asset</option>
    <option value="Office Asset" <?= ($editData['asset_type'] ?? '')=='Office Asset'?'selected':'' ?>>Office Asset</option>
</select>
</div>

<div>
<label>Asset Subtype</label>
<select name="subtype" required>
    <option value="Fixed Asset" <?= ($editData['subtype'] ?? '')=='Fixed Asset'?'selected':'' ?>>Fixed Asset</option>
    <option value="Digital Asset" <?= ($editData['subtype'] ?? '')=='Digital Asset'?'selected':'' ?>>Digital Asset</option>
</select>
</div>

<div class="full">
<label>Serial Number</label>

<select name="meter_serial" id="meter_serial" style="display:none;">
    <option value="">Select Meter</option>
    <?php foreach ($meterData as $m): ?>
        <option value="<?= $m['serial_number'] ?>"
            data-location="<?= $m['location'] ?>"
            data-status="<?= $m['status'] ?>"
            <?= ($editData['serial_number'] ?? '') == $m['serial_number'] ? 'selected' : '' ?>>
            <?= $m['serial_number'] ?>
        </option>
    <?php endforeach; ?>
</select>

<input type="text" name="manual_serial" id="manual_serial"
value="<?= $editData['serial_number'] ?? '' ?>">
</div>

<div class="full">
<label>Location</label>
<input name="location" id="location" value="<?= $editData['location'] ?? '' ?>" required>
</div>

<div>
<label>Asset Value</label>
<input type="number" step="0.01" id="asset_value" name="asset_value"
value="<?= $editData['asset_value'] ?? '' ?>" required>
</div>

<div>
<label>Purchase Date</label>
<input type="date" name="purchase_date"
value="<?= $editData['purchase_date'] ?? '' ?>" required>
</div>

<div class="full">
<label>Status</label>
<select name="status" id="status">
    <option value="Active" <?= ($editData['status'] ?? '')=='Active'?'selected':'' ?>>Active</option>
    <option value="Inactive" <?= ($editData['status'] ?? '')=='Inactive'?'selected':'' ?>>Inactive</option>
    <option value="Faulty" <?= ($editData['status'] ?? '')=='Faulty'?'selected':'' ?>>Faulty</option>
</select>
</div>

</div>

<div class="summary">
Depreciated: <span id="dep">0</span> KES |
Net: <span id="net">0</span> KES
</div>

<button class="btn btn-save" type="submit">
<?= $editData ? "Update Asset" : "Save Asset" ?>
</button>

</form>

<a class="back" href="/wowasco/index.php">← Back</a>

</div>
</div>

<script>
function toggleSerial(){
    let type = document.getElementById("asset_type").value;

    let meter = document.getElementById("meter_serial");
    let manual = document.getElementById("manual_serial");

    if(type === "Smart Meter"){
        meter.style.display = "block";
        manual.style.display = "none";
    } else {
        meter.style.display = "none";
        manual.style.display = "block";
    }
}

document.getElementById("meter_serial").addEventListener("change", function(){
    let opt = this.options[this.selectedIndex];

    document.getElementById("location").value = opt.dataset.location || "";
    document.getElementById("status").value = opt.dataset.status || "Active";
});

function calc(){
    let v = parseFloat(document.getElementById("asset_value").value) || 0;
    document.getElementById("dep").innerText = (v*0.2).toFixed(2);
    document.getElementById("net").innerText = (v - (v*0.2)).toFixed(2);
}

document.getElementById("asset_value").addEventListener("input", calc);

window.onload = function(){
    toggleSerial();
    calc();
}
</script>

</body>
</html>