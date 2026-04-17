<?php
$conn = new mysqli("localhost", "root", "", "wowasco2");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = "";
$report = false;
$editData = null;

/* ================= DELETE INFRASTRUCTURE ================= */
if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    $conn->query("DELETE FROM infrastructure WHERE id=$id");
    header("Location: infrastructure.php");
    exit();
}

/* ================= LOAD EDIT DATA ================= */
if (isset($_GET['edit'])) {
    $id = (int) $_GET['edit'];
    $res = $conn->query("SELECT * FROM infrastructure WHERE id=$id");
    $editData = $res->fetch_assoc();
}

/* ================= IMAGE UPLOAD FUNCTION ================= */
function uploadImage($file){
    if (!isset($file) || $file['error'] != 0) return null;

    $targetDir = "uploads/";
    if (!file_exists($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    $fileName = time() . "_" . basename($file["name"]);
    $targetFile = $targetDir . $fileName;

    if (move_uploaded_file($file["tmp_name"], $targetFile)) {
        return $targetFile;
    }

    return null;
}

/* ================= TOGGLE REPORT ================= */
if (isset($_POST['generate_report'])) {

    $report = true;

    $total = $conn->query("SELECT COUNT(*) as t FROM infrastructure")->fetch_assoc()['t'] ?? 0;
    $active = $conn->query("SELECT COUNT(*) as t FROM infrastructure WHERE status='Active'")->fetch_assoc()['t'] ?? 0;
    $maintenance = $conn->query("SELECT COUNT(*) as t FROM infrastructure WHERE status='Under Maintenance'")->fetch_assoc()['t'] ?? 0;
}

/* ================= EXPORT EXCEL ================= */
if(isset($_GET['export']) && $_GET['export'] == 'excel'){

    header("Content-Type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=infrastructure_report.xls");

    $result = $conn->query("SELECT * FROM infrastructure ORDER BY id DESC");

    echo "Name\tType\tCategory\tActivity\tLocation\tStatus\n";

    while($row = $result->fetch_assoc()){
        echo $row['name']."\t".$row['type']."\t".$row['asset_category']."\t".$row['activity']."\t".$row['location']."\t".$row['status']."\n";
    }
    exit;
}

/* ================= ADD / UPDATE INFRASTRUCTURE ================= */
if (isset($_POST['add'])) {

    $name = $_POST['name'];
    $type = $_POST['type'];
    $location = $_POST['location'];
    $status = $_POST['status'];

    $asset_category = $_POST['asset_category'];
    $activity = $_POST['activity'];

    $photo = uploadImage($_FILES['photo']);

    /* ================= UPDATE MODE ================= */
    if (!empty($_POST['edit_id'])) {

        $id = (int) $_POST['edit_id'];

        $stmt = $conn->prepare("
            UPDATE infrastructure SET
            name=?, type=?, location=?, status=?,
            asset_category=?, activity=?, photo=COALESCE(?, photo)
            WHERE id=?
        ");

        $stmt->bind_param(
            "sssssssi",
            $name,
            $type,
            $location,
            $status,
            $asset_category,
            $activity,
            $photo,
            $id
        );

        if ($stmt->execute()) {
            $message = "Infrastructure updated successfully.";
        } else {
            $message = "Error updating infrastructure.";
        }

    } else {

        /* ================= INSERT MODE ================= */
        $stmt = $conn->prepare("
            INSERT INTO infrastructure 
            (name, type, location, status, asset_category, activity, photo)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");

        $stmt->bind_param(
            "sssssss",
            $name,
            $type,
            $location,
            $status,
            $asset_category,
            $activity,
            $photo
        );

        if ($stmt->execute()) {
            $message = "Infrastructure added successfully.";
        } else {
            $message = "Error adding infrastructure.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Infrastructure Management</title>

<style>
body {
    font-family: Arial;
    background: #f4f6f9;
    padding: 20px;
}

.container {
    max-width: 1000px;
    margin: auto;
}

h2 {
    margin-bottom: 20px;
    color: #0b2d5c;
}

.form-card, .table-card, .report-card {
    background: #fff;
    padding: 20px;
    border-radius: 12px;
    margin-bottom: 20px;
    box-shadow: 0 3px 10px rgba(0,0,0,0.08);
}

input, select {
    width: 100%;
    padding: 10px;
    margin: 8px 0;
    border-radius: 6px;
    border: 1px solid #ccc;
}

button {
    padding: 10px 15px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    background: #007bff;
    color: white;
}

table {
    width: 100%;
    border-collapse: collapse;
}

table th, table td {
    padding: 12px;
    border-bottom: 1px solid #ddd;
    text-align: left;
}

table th {
    background: #0b2d5c;
    color: white;
}

.badge {
    padding: 5px 10px;
    border-radius: 20px;
    font-size: 12px;
    color: white;
}

.Active { background: green; }
.Inactive { background: gray; }
.UnderMaintenance { background: orange; }

img.thumb {
    width: 60px;
    height: 60px;
    object-fit: cover;
    border-radius: 6px;
}

.actions {
    display: flex;
    gap: 6px;
}

.edit-btn {
    background: #ffc107;
    color: black;
    padding: 6px 10px;
    border-radius: 5px;
    text-decoration: none;
    font-size: 13px;
}

.delete-btn {
    background: #dc3545;
    color: white;
    padding: 6px 10px;
    border-radius: 5px;
    text-decoration: none;
    font-size: 13px;
}

.tab-btn {
    background: #0b2d5c;
    margin-bottom: 10px;
}

.download-btn {
    background: #28a745;
    margin-left: 10px;
}

.print-btn {
    background: #6c757d;
    margin-left: 10px;
}

.hidden {
    display: none;
}

@media print {
    body * {
        visibility: hidden;
    }

    #printArea, #printArea * {
        visibility: visible;
    }

    #printArea {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
    }
}
</style>
</head>

<body>

<div class="container">

<h2>🏗 Infrastructure Management</h2>

<?php if ($message): ?>
    <p style="color: green;"><?= $message ?></p>
<?php endif; ?>

<!-- ADD / EDIT FORM -->
<div class="form-card">
<h3><?= $editData ? "Edit Infrastructure" : "Add Infrastructure" ?></h3>

<form method="POST" enctype="multipart/form-data">

<input type="hidden" name="edit_id" value="<?= $editData['id'] ?? '' ?>">

<input type="text" name="name" placeholder="Infrastructure Name"
value="<?= $editData['name'] ?? '' ?>" required>

<input type="text" name="type" placeholder="Type"
value="<?= $editData['type'] ?? '' ?>" required>

<select name="asset_category" required>
    <option value="">Asset Category</option>
    <option value="Fixed Asset" <?= ($editData['asset_category'] ?? '')=='Fixed Asset'?'selected':'' ?>>Fixed Asset</option>
    <option value="Digital Asset" <?= ($editData['asset_category'] ?? '')=='Digital Asset'?'selected':'' ?>>Digital Asset</option>
</select>

<select name="activity" required>
    <option value="">Activity</option>
    <option value="Repair" <?= ($editData['activity'] ?? '')=='Repair'?'selected':'' ?>>Repair</option>
    <option value="Maintenance" <?= ($editData['activity'] ?? '')=='Maintenance'?'selected':'' ?>>Maintenance</option>
    <option value="Overhaul" <?= ($editData['activity'] ?? '')=='Overhaul'?'selected':'' ?>>Overhaul</option>
</select>

<input type="text" name="location" placeholder="Location"
value="<?= $editData['location'] ?? '' ?>">

<input type="file" name="photo">

<select name="status">
    <option value="Active" <?= ($editData['status'] ?? '')=='Active'?'selected':'' ?>>Active</option>
    <option value="Inactive" <?= ($editData['status'] ?? '')=='Inactive'?'selected':'' ?>>Inactive</option>
    <option value="Under Maintenance" <?= ($editData['status'] ?? '')=='Under Maintenance'?'selected':'' ?>>Under Maintenance</option>
</select>

<button type="submit" name="add">
<?= $editData ? "Update Infrastructure" : "Add Infrastructure" ?>
</button>

</form>
</div>

<!-- REPORT -->
<div class="report-card">
<h3>📊 Infrastructure Reports</h3>

<form method="POST">
<button id="reportBtn" class="tab-btn" type="submit" name="generate_report">
Generate Infrastructure Report
</button>
</form>

<?php if ($report): ?>
<div id="reportSection">

<hr>

<p>Total Assets: <b><?= $total ?></b></p>
<p>Active: <b><?= $active ?></b></p>
<p>Under Maintenance: <b><?= $maintenance ?></b></p>

<br>

<a href="?export=excel" class="download-btn" style="padding:10px;color:white;text-decoration:none;border-radius:6px;">
⬇ Download Excel
</a>

<button onclick="printReport()" class="print-btn">
🖨 Print PDF
</button>

</div>
<?php endif; ?>

</div>

<!-- TABLE -->
<div class="table-card">
<h3>Existing Infrastructure</h3>

<div id="printArea">

<table>
<tr>
<th>Photo</th>
<th>Name</th>
<th>Type</th>
<th>Category</th>
<th>Activity</th>
<th>Location</th>
<th>Status</th>
<th>Actions</th>
</tr>

<?php
$result = $conn->query("SELECT * FROM infrastructure ORDER BY id DESC");

while ($row = $result->fetch_assoc()):
?>
<tr>
<td>
<?php if (!empty($row['photo'])): ?>
<img src="<?= $row['photo'] ?>" class="thumb">
<?php else: ?>No Image<?php endif; ?>
</td>

<td><?= $row['name'] ?></td>
<td><?= $row['type'] ?></td>
<td><?= $row['asset_category'] ?></td>
<td><?= $row['activity'] ?></td>
<td><?= $row['location'] ?></td>

<td>
<span class="badge <?= str_replace(' ', '', $row['status']) ?>">
<?= $row['status'] ?>
</span>
</td>

<td class="actions">
<a class="edit-btn" href="?edit=<?= $row['id'] ?>">Edit</a>
<a class="delete-btn" href="?delete=<?= $row['id'] ?>" onclick="return confirm('Delete this record?')">Delete</a>
</td>

</tr>
<?php endwhile; ?>

</table>

</div>
</div>

</div>

<script>
function printReport(){
    window.print();
}
</script>

</body>
</html>