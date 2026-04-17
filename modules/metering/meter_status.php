<?php
include $_SERVER['DOCUMENT_ROOT'].'/wowasco/api/db.php';

$search = "";
$filter = "";
$status_filter = "";
$start_date = "";
$end_date = "";

/* ================= DELETE HANDLER ================= */
if(isset($_GET['delete_id'])){
    $delete_id = intval($_GET['delete_id']);

    $stmt = $conn->prepare("DELETE FROM meters WHERE id = ?");
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();

    header("Location: meter_status.php?deleted=1");
    exit();
}

/* ================= SUCCESS POPUP FLAG ================= */
$deleted = isset($_GET['deleted']) ? true : false;
$updated = isset($_GET['updated']) ? true : false;

/* ================= EDIT HANDLER ================= */
if(isset($_POST['update_meter'])){
    $id = intval($_POST['id']);
    $serial_number = $_POST['serial_number'];
    $model = $_POST['model'];
    $customer_name = $_POST['customer_name'];
    $customer_type = $_POST['customer_type'];
    $meter_type = $_POST['meter_type'];
    $installation_date = $_POST['installation_date'];
    $zone = $_POST['zone'];
    $status = $_POST['status'];

    $stmt = $conn->prepare("
        UPDATE meters 
        SET serial_number=?, model=?, customer_name=?, customer_type=?, meter_type=?, installation_date=?, zone=?, status=? 
        WHERE id=?
    ");

    $stmt->bind_param(
        "ssssssssi",
        $serial_number,
        $model,
        $customer_name,
        $customer_type,
        $meter_type,
        $installation_date,
        $zone,
        $status,
        $id
    );

    $stmt->execute();

    header("Location: meter_status.php?updated=1");
    exit();
}

if(isset($_GET['search'])) $search = $_GET['search'];
if(isset($_GET['customer_type'])) $filter = $_GET['customer_type'];
if(isset($_GET['status'])) $status_filter = $_GET['status'];
if(isset($_GET['start_date'])) $start_date = $_GET['start_date'];
if(isset($_GET['end_date'])) $end_date = $_GET['end_date'];

$sql = "SELECT * FROM meters WHERE 1";

if($search != "") $sql .= " AND serial_number LIKE '%$search%'";
if($filter != "") $sql .= " AND customer_type='$filter'";
if($status_filter != "") $sql .= " AND status='$status_filter'";
if($start_date != "" && $end_date != ""){
    $sql .= " AND installation_date BETWEEN '$start_date' AND '$end_date'";
}

$result = $conn->query($sql);
$total_records = $result->num_rows;

/* EXPORT EXCEL */
if(isset($_GET['export']) && $_GET['export'] == 'excel'){
    header("Content-Type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=meter_report.xls");

    echo "Meter Serial\tModel\tCustomer Name\tCustomer Type\tMeter Type\tInstallation Date\tZone\tStatus\n";

    while($row = $result->fetch_assoc()){
        $status = $row['status'] ?? 'Inactive';

        echo "{$row['serial_number']}\t{$row['model']}\t{$row['customer_name']}\t{$row['customer_type']}\t{$row['meter_type']}\t{$row['installation_date']}\t{$row['zone']}\t{$status}\n";
    }
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Meter Status Reports</title>

<style>

body{
    font-family: Arial;
    background:#eef2f7;
    margin:0;
}

.container{
    width:92%;
    margin:auto;
    margin-top:30px;
}

.report-header{
    text-align:center;
    margin-bottom:20px;
}

.report-header h2{
    color:#003366;
    border-bottom: 3px solid #f7b731;
    display: inline-block;
    padding-bottom: 5px;
}

.controls{
    background:white;
    padding:25px;
    border-radius:10px;
    box-shadow:0 3px 8px rgba(0,0,0,0.08);
    margin-bottom:20px;
}

.controls form{
    display:flex;
    flex-direction:column;
    gap:15px;
}

.form-row{
    display:flex;
    flex-wrap:wrap;
    gap:10px;
}

input, select{
    padding:10px;
    border:1px solid #ccc;
    border-radius:6px;
}

.date-group{
    display:flex;
    align-items:center;
    gap:10px;
    background:#f8fafc;
    padding:12px 15px;
    border-radius:8px;
    border:1px solid #dbe3ea;
}

button, .btn{
    background:#003366;
    color:white;
    border:none;
    padding:8px 12px;
    border-radius:6px;
    cursor:pointer;
    font-size:13px;
}

button:hover, .btn:hover{
    background:#f7b731;
    color:#003366;
}

.actions{
    margin-bottom:15px;
    display:flex;
    gap:10px;
    align-items:center;
}

.dropdown{
    position:relative;
}

.dropdown-content{
    display:none;
    position:absolute;
    background:white;
    min-width:160px;
    box-shadow:0px 4px 8px rgba(0,0,0,0.1);
    border-radius:6px;
    overflow:hidden;
    z-index:1;
}

.dropdown-content a{
    display:block;
    padding:10px;
    text-decoration:none;
    color:#333;
}

.dropdown-content a:hover{
    background:#fff4cc;
}

.dropdown:hover .dropdown-content{
    display:block;
}

/* ================= TABLE ================= */
table{
    width:100%;
    border-collapse:collapse;
    background:white;
}

th{
    background:#003366;
    color:white;
    padding:12px;
}

td{
    padding:10px;
    border-bottom:1px solid #ddd;
    position:relative;
}

tr:hover{
    background:#fff9e6;
}

.active{ color:green; font-weight:bold; }
.inactive{ color:red; font-weight:bold; }

.summary{
    background:#fff8e1;
    padding:10px 15px;
    border-left:5px solid #f7b731;
    margin-bottom:15px;
    font-weight:bold;
}

/* ================= ACTION MENU (3 DOTS) ================= */
.action-menu{
    position:relative;
    display:inline-block;
}

.dots-btn{
    background:none;
    border:none;
    font-size:20px;
    cursor:pointer;
    padding:5px 10px;
    color:#333;
}

.action-dropdown{
    display:none;
    position:absolute;
    right:0;
    top:25px;
    background:white;
    min-width:120px;
    box-shadow:0px 4px 10px rgba(0,0,0,0.15);
    border-radius:6px;
    z-index:10;
}

.action-dropdown a{
    display:block;
    padding:10px;
    text-decoration:none;
    color:#333;
    font-size:13px;
}

.action-menu:hover .action-dropdown{
    display:block;
}

/* ================= POPUP ================= */
.popup {
    display:none;
    position:fixed;
    top:20px;
    right:20px;
    background:#28a745;
    color:white;
    padding:15px 20px;
    border-radius:8px;
    z-index:9999;
    font-weight:bold;
    box-shadow:0 4px 10px rgba(0,0,0,0.2);
}

@media print{
    .controls, .actions, .back-btn{
        display:none;
    }
}

.back-btn{
    margin-top:20px;
    display:inline-block;
    padding:10px;
    background:#6c757d;
    color:white;
    text-decoration:none;
    border-radius:5px;
}

</style>
</head>

<body>

<!-- ================= POPUP ================= -->
<?php if($deleted): ?>
<div class="popup" id="popup">deleted successfully</div>
<?php endif; ?>

<div class="container">

<div class="report-header">
<h2>Smart Meter Status Report</h2>
<p>
<?php 
if($start_date && $end_date){
    echo "<strong>$start_date → $end_date</strong>";
} else {
    echo "All Periods";
}
?>
</p>
</div>

<div class="controls">
<form method="GET">

<div class="form-row">
<input type="text" name="search" placeholder="Search Meter Serial" value="<?php echo $search; ?>">

<select name="customer_type">
<option value="">All Customer Types</option>
<option value="Government entities" <?php if($filter=="Government entities") echo "selected"; ?>>Government entities</option>
<option value="Residential" <?php if($filter=="Residential") echo "selected"; ?>>Residential</option>
<option value="Businesses" <?php if($filter=="Businesses") echo "selected"; ?>>Businesses</option>
<option value="Personal" <?php if($filter=="Personal") echo "selected"; ?>>Personal</option>
</select>

<select name="status">
<option value="">All Status</option>
<option value="Active" <?php if($status_filter=="Active") echo "selected"; ?>>Active</option>
<option value="Inactive" <?php if($status_filter=="Inactive") echo "selected"; ?>>Inactive</option>
</select>
</div>

<div class="date-group">
<strong>📅 Report Period:</strong>

<label>From</label>
<input type="date" name="start_date" value="<?php echo $start_date; ?>">

<label>To</label>
<input type="date" name="end_date" value="<?php echo $end_date; ?>">

</div>

</form>
</div>

<div class="actions">

<button onclick="document.querySelector('form').submit()">Generate Report</button>
<button onclick="window.print()">🖨️ Print</button>

<div class="dropdown">
<button>⬇️ Download ▾</button>
<div class="dropdown-content">
<a href="?<?php echo http_build_query(array_merge($_GET, ['export'=>'excel'])); ?>">📊 Excel</a>
<a href="#" onclick="window.print()">📄 PDF</a>
</div>
</div>

</div>

<div class="summary">
Total Records: <?php echo $total_records; ?>
</div>
<?php
$edit_data = null;

if(isset($_GET['edit_id'])){
    $edit_id = intval($_GET['edit_id']);
    $result_edit = $conn->query("SELECT * FROM meters WHERE id=$edit_id");
    $edit_data = $result_edit->fetch_assoc();
}
?>

<?php if($edit_data): ?>
<div class="controls">
<form method="POST">

<input type="hidden" name="id" value="<?php echo $edit_data['id']; ?>">

<div class="form-row">
<input type="text" name="serial_number" value="<?php echo $edit_data['serial_number']; ?>">
<input type="text" name="model" value="<?php echo $edit_data['model']; ?>">
<input type="text" name="customer_name" value="<?php echo $edit_data['customer_name']; ?>">

<select name="customer_type">
<option value="Residential" <?php if($edit_data['customer_type']=="Residential") echo "selected"; ?>>Residential</option>
<option value="Businesses" <?php if($edit_data['customer_type']=="Businesses") echo "selected"; ?>>Businesses</option>
<option value="Government entities" <?php if($edit_data['customer_type']=="Government entities") echo "selected"; ?>>Government</option>
</select>

<input type="text" name="meter_type" value="<?php echo $edit_data['meter_type']; ?>">
<input type="date" name="installation_date" value="<?php echo $edit_data['installation_date']; ?>">
<input type="text" name="zone" value="<?php echo $edit_data['zone']; ?>">

<select name="status">
<option value="Active" <?php if($edit_data['status']=="Active") echo "selected"; ?>>Active</option>
<option value="Inactive" <?php if($edit_data['status']=="Inactive") echo "selected"; ?>>Inactive</option>
</select>

<button type="submit" name="update_meter">Update Meter</button>
</div>

</form>
</div>
<?php endif; ?>
<table>

<tr>
<th>Meter Serial</th>
<th>Model</th>
<th>Customer Name</th>
<th>Customer Type</th>
<th>Meter Type</th>
<th>Installation Date</th>
<th>Zone</th>
<th>Status</th>
<th>Action</th>
</tr>

<?php
if($result->num_rows > 0){
    $result->data_seek(0);

    while($row = $result->fetch_assoc()){

        $status = $row['status'] ?? 'Inactive';
        $statusClass = ($status === 'Active') ? "active" : "inactive";

        echo "<tr>
        <td>{$row['serial_number']}</td>
        <td>{$row['model']}</td>
        <td>{$row['customer_name']}</td>
        <td>{$row['customer_type']}</td>
        <td>{$row['meter_type']}</td>
        <td>{$row['installation_date']}</td>
        <td>{$row['zone']}</td>
        <td class='$statusClass'>{$status}</td>

        <td>
            <div class='action-menu'>
                <button class='dots-btn'>⋮</button>
                <div class='action-dropdown'>
                    <a href='?edit_id={$row['id']}'>✏️ Edit</a>
                    <a href='?delete_id={$row['id']}' onclick=\"return confirm('Delete this meter?')\">🗑️ Delete</a>
                </div>
            </div>
        </td>

        </tr>";
    }
}
?>

</table>

<a href="/wowasco/index.php" class="back-btn">← Back to Dashboard</a>

</div>

<script>
setTimeout(() => {
    const popup = document.getElementById("popup");
    if(popup){
        popup.style.display = "none";
    }
}, 3000);
</script>

</body>
</html>