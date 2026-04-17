<?php
require_once __DIR__ . '/../../api/db.php';

/* DELETE HANDLER */
if (isset($_POST['delete_id'])) {
    $id = (int) $_POST['delete_id'];

    $stmt = $conn->prepare("DELETE FROM assets WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    header("Location: view_assets.php?deleted=1");
    exit;
}

// Filters
$typeFilter = $_GET['type'] ?? '';
$subtypeFilter = $_GET['subtype'] ?? '';

// Query
$sql = "SELECT * FROM assets WHERE 1=1";

if (!empty($typeFilter)) {
    $sql .= " AND asset_type = '" . $conn->real_escape_string($typeFilter) . "'";
}

if (!empty($subtypeFilter)) {
    $sql .= " AND subtype = '" . $conn->real_escape_string($subtypeFilter) . "'";
}

$sql .= " ORDER BY id DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
<title>Assets Dashboard</title>

<style>
body{
    margin:0;
    font-family:"Segoe UI", sans-serif;
    background:#eef3fb;
}

.header{
    background:linear-gradient(135deg,#0b3d91,#1366d6);
    color:white;
    padding:18px 25px;
    font-size:20px;
    font-weight:bold;
}

.container{
    padding:25px;
}

/* FILTERS */
.filters{
    display:flex;
    gap:10px;
    flex-wrap:wrap;
    background:white;
    padding:12px;
    border-radius:10px;
    box-shadow:0 4px 12px rgba(0,0,0,0.08);
    margin-bottom:15px;
}

.filters select{
    padding:7px 10px;
    font-size:13px;
    border-radius:6px;
    border:1px solid #ddd;
    width:170px;
}

/* BUTTONS */
.btn{
    padding:8px 12px;
    border:none;
    border-radius:6px;
    cursor:pointer;
    font-size:13px;
    font-weight:600;
}

.btn-primary{background:#0b3d91;color:white;}
.btn-green{background:#1a8f3c;color:white;}
.btn-yellow{background:#f4b400;color:black;}

.btn:hover{opacity:0.9;}

/* TABLE */
.table-card{
    background:white;
    border-radius:12px;
    box-shadow:0 4px 12px rgba(0,0,0,0.08);
    overflow:visible; /* 🔥 FIXED */
}

table{
    width:100%;
    border-collapse:collapse;
}

thead{
    background:#0b3d91;
    color:white;
}

th,td{
    padding:12px;
    font-size:13px;
}

tbody tr{
    border-bottom:1px solid #eee;
}

tbody tr:hover{
    background:#f4f8ff;
}

/* STATUS */
.badge{
    padding:4px 10px;
    border-radius:20px;
    font-size:12px;
}

.active{background:#e6f7ee;color:#1a8f3c;}
.inactive{background:#fff3cd;color:#b8860b;}
.faulty{background:#fdecea;color:#d32f2f;}

/* ACTION */
.action-wrapper{
    position:relative;
}

.action-btn{
    background:#1a8f3c;
    color:white;
    border:none;
    padding:6px 10px;
    border-radius:6px;
    cursor:pointer;
}

.dropdown-menu{
    display:none;
    position:absolute;
    right:0;
    top:35px;
    background:white;
    border-radius:8px;
    box-shadow:0 6px 15px rgba(0,0,0,0.15);
    overflow:hidden;
    min-width:130px;
    z-index:999;
}

.dropdown-menu a,
.dropdown-menu button{
    display:block;
    width:100%;
    padding:10px;
    border:none;
    background:none;
    text-align:left;
    cursor:pointer;
    font-size:13px;
}

.dropdown-menu a:hover,
.dropdown-menu button:hover{
    background:#f1f6ff;
}
</style>
</head>

<body>

<div class="header">Asset Management Dashboard</div>

<div class="container">

<?php if(isset($_GET['deleted'])): ?>
    <div style="background:#d1fae5;color:#065f46;padding:10px;border-radius:8px;margin-bottom:10px;">
        Asset deleted successfully
    </div>
<?php endif; ?>

<form method="GET" class="filters">

<select name="type">
    <option value="">All Types</option>
    <option value="Field Asset" <?= $typeFilter=='Field Asset'?'selected':'' ?>>Field Asset</option>
    <option value="Office Asset" <?= $typeFilter=='Office Asset'?'selected':'' ?>>Office Asset</option>
</select>

<select name="subtype">
    <option value="">All Subtypes</option>
    <option value="Fixed Asset" <?= $subtypeFilter=='Fixed Asset'?'selected':'' ?>>Fixed Asset</option>
    <option value="Digital Asset" <?= $subtypeFilter=='Digital Asset'?'selected':'' ?>>Digital Asset</option>
</select>

<button class="btn btn-primary">Filter</button>
<button type="button" class="btn btn-green" onclick="window.print()">Print</button>
<button type="button" class="btn btn-yellow" onclick="exportCSV()">Download</button>

</form>

<div class="table-card">
<table>
<thead>
<tr>
<th>ID</th>
<th>Name</th>
<th>Type</th>
<th>Subtype</th>
<th>Serial</th>
<th>Location</th>
<th>Status</th>
<th>Date</th>
<th>Action</th>
</tr>
</thead>

<tbody>
<?php while($row = $result->fetch_assoc()) { ?>
<tr>
<td><?= $row['id'] ?></td>
<td><?= $row['asset_name'] ?></td>
<td><?= $row['asset_type'] ?></td>
<td><?= $row['subtype'] ?></td>
<td><?= $row['serial_number'] ?></td>
<td><?= $row['location'] ?></td>

<td>
<span class="badge <?= strtolower($row['status']) ?>">
<?= $row['status'] ?>
</span>
</td>

<td><?= $row['date_added'] ?? '' ?></td>

<td>
<div class="action-wrapper">
<button class="action-btn" onclick="toggleMenu(this)">⋮</button>

<div class="dropdown-menu">

<a href="add_asset.php?edit=<?= $row['id'] ?>">Edit</a>

<form method="POST" onsubmit="return confirm('Delete asset?')">
<input type="hidden" name="delete_id" value="<?= $row['id'] ?>">
<button type="submit">Delete</button>
</form>

</div>
</div>
</td>
</tr>
<?php } ?>
</tbody>
</table>

</div>
<a class="back" href="/wowasco/index.php">← Back</a>
</div>

<script>
function exportCSV(){
    let rows = document.querySelectorAll("table tr");
    let csv = [];

    rows.forEach(row=>{
        let cols = row.querySelectorAll("th,td");
        let data = [];
        cols.forEach(c=>data.push(c.innerText));
        csv.push(data.join(","));
    });

    let blob = new Blob([csv.join("\n")],{type:"text/csv"});
    let a = document.createElement("a");
    a.href = URL.createObjectURL(blob);
    a.download = "assets_report.csv";
    a.click();
}

function toggleMenu(btn){
    let menu = btn.nextElementSibling;

    document.querySelectorAll('.dropdown-menu').forEach(m=>{
        if(m!==menu) m.style.display='none';
    });

    menu.style.display = menu.style.display === 'block' ? 'none' : 'block';
}

document.addEventListener("click", function(e){
    if(!e.target.closest(".action-wrapper")){
        document.querySelectorAll(".dropdown-menu").forEach(m=>m.style.display="none");
    }
});
</script>

</body>
</html>