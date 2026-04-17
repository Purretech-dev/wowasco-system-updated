<?php 
require_once __DIR__ . '/../api/db.php';

$success = false;
$error = "";

/* =========================
   SUCCESS FLAG
========================= */
if (isset($_GET['updated'])) {
    $success = true;
}

/* =========================
   DELETE ASSET (SAFE)
========================= */
if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    $conn->query("DELETE FROM assets WHERE id = $id");
    header("Location: inventory.php");
    exit;
}

/* =========================
   FETCH EDIT DATA
========================= */
$editData = null;
if (isset($_GET['edit'])) {
    $id = (int) $_GET['edit'];
    $resultEdit = $conn->query("SELECT * FROM assets WHERE id = $id");
    $editData = $resultEdit->fetch_assoc();
}

/* =========================
   SAVE / UPDATE ASSET
========================= */
if (isset($_POST['save'])) {

    $date_added = date('Y-m-d');

    $asset_value = (float) $_POST['asset_value'];
    $purchase_date = $_POST['purchase_date'];

    $years_used = (time() - strtotime($purchase_date)) / (365 * 24 * 60 * 60);
    $depreciation_rate = 0.2;

    $depreciated_value = $asset_value * $depreciation_rate * $years_used;

    if ($depreciated_value > $asset_value) {
        $depreciated_value = $asset_value;
    }

    $net_value = $asset_value - $depreciated_value;

    /* =========================
       UPDATE
    ========================= */
    if (!empty($_POST['edit_id'])) {

        $id = (int) $_POST['edit_id'];

        $stmt = $conn->prepare("
            UPDATE assets SET
            asset_name=?,
            asset_type=?,
            subtype=?,
            serial_number=?,
            location=?,
            purchase_date=?,
            status=?,
            asset_value=?,
            depreciated_value=?,
            net_value=?
            WHERE id=?
        ");

        $stmt->bind_param(
            "sssssssdddi",
            $_POST['asset_name'],
            $_POST['asset_type'],
            $_POST['subtype'],
            $_POST['serial_number'],
            $_POST['location'],
            $_POST['purchase_date'],
            $_POST['status'],
            $asset_value,
            $depreciated_value,
            $net_value,
            $id
        );

        $stmt->execute();

        header("Location: inventory.php?updated=1");
        exit;
    }

    /* =========================
       INSERT
    ========================= */
    $stmt = $conn->prepare("
        INSERT INTO assets 
        (asset_name, asset_type, subtype, serial_number, location, purchase_date, date_added, status, asset_value, depreciated_value, net_value)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    $stmt->bind_param(
        "ssssssssddd",
        $_POST['asset_name'],
        $_POST['asset_type'],
        $_POST['subtype'],
        $_POST['serial_number'],
        $_POST['location'],
        $_POST['purchase_date'],
        $date_added,
        $_POST['status'],
        $asset_value,
        $depreciated_value,
        $net_value
    );

    $stmt->execute();
    $success = true;
}
?>

<!DOCTYPE html>
<html>
<head>
<title>WOWASCO Assets</title>

<style>
body { font-family: 'Segoe UI'; background: #f4f7fb; margin: 0; }
.container { width: 90%; margin: auto; }

.header {
    background: #003366;
    color: white;
    padding: 15px;
    margin: 20px 0;
    border-radius: 10px;
}

.btn-add {
    background: #198754;
    color: white;
    padding: 10px 15px;
    border: none;
    cursor: pointer;
    border-radius: 6px;
    margin-right: 10px;
}

.btn-back {
    background: #0d6efd;
    color: white;
    padding: 10px 15px;
    border: none;
    cursor: pointer;
    border-radius: 6px;
}

.modal {
    display: none;
    position: fixed;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.5);
    top: 0;
    left: 0;
}

.modal-content {
    background: white;
    width: 420px;
    margin: 6% auto;
    padding: 20px;
    border-radius: 10px;
}

input, select {
    width: 100%;
    padding: 10px;
    margin-top: 10px;
    border-radius: 6px;
    border: 1px solid #ccc;
}

.success {
    background: #198754;
    color: white;
    padding: 12px;
    margin-top: 15px;
    border-radius: 6px;
}

.error {
    background: #dc3545;
    color: white;
    padding: 12px;
    margin-top: 15px;
    border-radius: 6px;
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
    background: white;
}

th {
    background: #003366;
    color: white;
    padding: 10px;
}

td {
    padding: 10px;
    border-bottom: 1px solid #ddd;
}

#assetsSection { display: none; }
</style>
</head>

<body>

<div class="container">

<div class="header">
<h2>WOWASCO Asset Management</h2>
</div>

<button class="btn-add" onclick="openModal()">+ Add Asset</button>
<button class="btn-add" onclick="toggleAssets()">👁 View Assets</button>

<?php if ($success): ?>
<div class="success">Operation successful!</div>
<?php endif; ?>

<?php if ($error): ?>
<div class="error"><?= $error ?></div>
<?php endif; ?>

<!-- TABLE -->
<div id="assetsSection">
<table>
<tr>
<th>Name</th>
<th>Type</th>
<th>Value</th>
<th>Net Value</th>
<th>Action</th>
</tr>

<?php
$result = $conn->query("SELECT * FROM assets ORDER BY id DESC");
while ($row = $result->fetch_assoc()) {
?>
<tr>
<td><?= htmlspecialchars($row['asset_name']) ?></td>
<td><?= htmlspecialchars($row['asset_type']) ?></td>
<td><?= number_format($row['asset_value'],2) ?></td>
<td><?= number_format($row['net_value'],2) ?></td>
<td>
<select onchange="handleAction(this, <?= $row['id'] ?>)">
<option value="">Action</option>
<option value="edit">Edit</option>
<option value="delete">Delete</option>
</select>
</td>
</tr>
<?php } ?>
</table>
</div>

<!-- MODAL -->
<div id="modal" class="modal">
<div class="modal-content">

<h3><?= $editData ? "Edit Asset" : "Add Asset" ?></h3>

<button class="btn-back" onclick="closeModal()">Close</button>

<form method="POST">

<input type="hidden" name="edit_id" value="<?= $editData['id'] ?? '' ?>">

<input type="text" name="asset_name" placeholder="Asset Name"
value="<?= $editData['asset_name'] ?? '' ?>" required>

<input type="text" name="asset_type" placeholder="Asset Type"
value="<?= $editData['asset_type'] ?? '' ?>" required>

<input type="text" name="subtype" placeholder="Subtype"
value="<?= $editData['subtype'] ?? '' ?>" required>

<input type="number" step="0.01" name="asset_value" placeholder="Asset Value"
value="<?= $editData['asset_value'] ?? '' ?>" required>

<input type="text" name="serial_number" placeholder="Serial Number"
value="<?= $editData['serial_number'] ?? '' ?>">

<input type="text" name="location" placeholder="Location"
value="<?= $editData['location'] ?? '' ?>" required>

<select name="status">
<option>Active</option>
<option>Inactive</option>
<option>Faulty</option>
</select>

<input type="date" name="purchase_date"
value="<?= $editData['purchase_date'] ?? '' ?>" required>

<br><br>

<button type="submit" name="save" class="btn-add">
Save Asset
</button>

</form>

</div>
</div>

</div>

<!-- ✅ FINAL WORKING JAVASCRIPT -->
<script>
const modal = document.getElementById("modal");
const assetsSection = document.getElementById("assetsSection");

function openModal() {
    modal.style.display = "block";
}

function closeModal() {
    modal.style.display = "none";
}

function toggleAssets() {
    assetsSection.style.display =
        assetsSection.style.display === "block" ? "none" : "block";
}

function handleAction(select, id) {
    if (select.value === "delete") {
        if (confirm("Delete asset?")) {
            window.location.href = "?delete=" + id;
        }
    }

    if (select.value === "edit") {
        window.location.href = "?edit=" + id;
    }

    select.value = "";
}

window.onclick = function(event) {
    if (event.target === modal) {
        closeModal();
    }
};
</script>

</body>
</html>