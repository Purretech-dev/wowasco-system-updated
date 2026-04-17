<?php
session_start();

$conn = new mysqli("localhost", "root", "", "wowasco2");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

/* SAVE / UPDATE */
if (isset($_POST['save_report'])) {

    $id = $_POST['report_id'] ?? null;
    $interaction_id = $_POST['interaction_id'];
    $customer_name = $_POST['customer_name'];

    $lodge_status = !empty($_POST['lodge_status']) ? $_POST['lodge_status'] : 'Pending';
    $notes = $_POST['escalation_notes'] ?? '';
    $escalated_to = $_POST['escalated_to'] ?? '';

    $solved_at = null;
    $turnaround_time = null;

    if ($lodge_status === "Solved") {
        $solved_at = date("Y-m-d H:i:s");

        if ($id) {
            $res = $conn->query("SELECT created_at FROM lodge_reports WHERE id = $id");
            $row = $res->fetch_assoc();

            $start = new DateTime($row['created_at']);
            $end = new DateTime($solved_at);
            $interval = $start->diff($end);

            $turnaround_time = $interval->format('%d days %h hrs %i mins');
        }
    }

    if ($id) {

        $stmt = $conn->prepare("
            UPDATE lodge_reports 
            SET lodge_status=?, escalation_notes=?, escalated_to=?, solved_at=?, turnaround_time=?
            WHERE id=?
        ");

        $stmt->bind_param("sssssi", $lodge_status, $notes, $escalated_to, $solved_at, $turnaround_time, $id);
        $stmt->execute();

        $_SESSION['success'] = "Report updated successfully!";
        header("Location: crm_reports.php");
        exit;
    }

    // Prevent duplicates
    $check = $conn->prepare("SELECT id FROM lodge_reports WHERE customer_name = ?");
    $check->bind_param("s", $customer_name);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        $_SESSION['error'] = "This customer already has a lodge report.";
        header("Location: crm_reports.php");
        exit;
    }

    $stmt = $conn->prepare("
        INSERT INTO lodge_reports 
        (interaction_id, customer_name, lodge_status, escalation_notes, escalated_to, created_at)
        VALUES (?, ?, ?, ?, ?, NOW())
    ");

    $stmt->bind_param("issss", $interaction_id, $customer_name, $lodge_status, $notes, $escalated_to);
    $stmt->execute();

    $_SESSION['success'] = "Report saved successfully!";
    header("Location: crm_reports.php");
    exit;
}

/* EDIT FETCH */
$editData = null;
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $res = $conn->query("SELECT * FROM lodge_reports WHERE id = $id");
    $editData = $res->fetch_assoc();
}

/* CUSTOMERS */
$customers = $conn->query("
    SELECT id, customer_name 
    FROM customer_interactions 
    ORDER BY customer_name ASC
");

/* FILTER */
$where = "WHERE 1=1";

if (!empty($_GET['from']) && !empty($_GET['to'])) {
    $from = $_GET['from'];
    $to = $_GET['to'];
    $where .= " AND DATE(created_at) BETWEEN '$from' AND '$to'";
}

$reports = $conn->query("
    SELECT * FROM lodge_reports 
    $where
    ORDER BY created_at DESC
");
?>

<!DOCTYPE html>
<html>
<head>
<title>Lodge Reports</title>

<style>
body {
    font-family: 'Segoe UI', Tahoma, sans-serif;
    margin: 0;
    background: #eef2f7;
    color: #333;
}

.header {
    background: linear-gradient(90deg, #1e3a8a, #2563eb);
    color: white;
    padding: 18px 25px;
    font-size: 22px;
    font-weight: 600;
}

.container {
    padding: 25px;
}

.card {
    background: #fff;
    padding: 25px;
    border-radius: 14px;
    margin-bottom: 25px;
    box-shadow: 0 6px 18px rgba(0,0,0,0.06);
}

h3 {
    color: #1e3a8a;
    margin-bottom: 15px;
}

/* FORM GRID FIX */
.form-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
    gap: 20px;
    align-items: end;
}

/* FORM ELEMENTS */
label {
    font-size: 13px;
    margin-bottom: 5px;
}

input, select, textarea {
    padding: 10px;
    border-radius: 8px;
    border: 1px solid #ccc;
    background: #fafafa;
    width: 100%;
}

/* BUTTONS FIX */
button {
    width: 100%;
    padding: 12px;
    border-radius: 8px;
    border: none;
    cursor: pointer;
    font-weight: 600;
    transition: 0.3s;
}

/* PRIMARY BUTTON */
.btn-primary {
    background: linear-gradient(90deg, #16a34a, #22c55e);
    color: white;
}

.btn-primary:hover {
    background: linear-gradient(90deg, #15803d, #16a34a);
    transform: translateY(-2px);
}

/* SECONDARY BUTTON */
.btn-secondary {
    background: #1e3a8a;
    color: white;
}

.btn-secondary:hover {
    background: #2563eb;
}

/* ACTION BUTTON */
.action-btn {
    background: #2563eb;
    color: white;
    padding: 6px 12px;
    border-radius: 6px;
    text-decoration: none;
    display: inline-block;
}

.action-btn:hover {
    background: #1e40af;
}

/* TABLE */
table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 10px;
}

th {
    background: #1e3a8a;
    color: white;
    padding: 12px;
}

td {
    padding: 12px;
    border-bottom: 1px solid #eee;
}

tr:nth-child(even) {
    background: #f9fafb;
}

tr:hover {
    background: #eef2ff;
}

/* STATUS */
.status-pending,
.status-solved,
.status-escalated {
    padding: 5px 10px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: bold;
}

.status-pending { background: #fef3c7; color: #92400e; }
.status-solved { background: #dcfce7; color: #166534; }
.status-escalated { background: #fee2e2; color: #991b1b; }

/* MESSAGES */
.message {
    padding: 12px;
    border-radius: 8px;
    margin-bottom: 15px;
}

.error { background: #fee2e2; color: #991b1b; }
.success { background: #dcfce7; color: #166534; }
</style>

</head>

<body>

<div class="header">Lodge Reports</div>

<div class="container">

<?php
if (isset($_SESSION['error'])) {
    echo "<div class='message error'>".$_SESSION['error']."</div>";
    unset($_SESSION['error']);
}

if (isset($_SESSION['success'])) {
    echo "<div class='message success'>".$_SESSION['success']."</div>";
    unset($_SESSION['success']);
}
?>

<!-- FORM -->
<div class="card">
<h3><?= $editData ? "Edit Report" : "Add Report" ?></h3>

<form method="POST">

<input type="hidden" name="report_id" value="<?= $editData['id'] ?? '' ?>">

<div class="form-grid">

<div>
<label>Customer</label>
<select name="interaction_id" id="interactionSelect" required>
<option value="">Select Customer</option>
<?php while($c = $customers->fetch_assoc()): ?>
<option value="<?= $c['id'] ?>" data-name="<?= $c['customer_name'] ?>"
<?= ($editData && $editData['customer_name'] == $c['customer_name']) ? 'selected' : '' ?>>
<?= $c['customer_name'] ?>
</option>
<?php endwhile; ?>
</select>
<input type="hidden" name="customer_name" id="customer_name"
value="<?= $editData['customer_name'] ?? '' ?>">
</div>

<div>
<label>Status</label>
<select name="lodge_status" id="statusSelect">
<option value="Pending" <?= (!isset($editData) || $editData['lodge_status']=="Pending")?'selected':'' ?>>Pending</option>
<option value="Solved" <?= (isset($editData) && $editData['lodge_status']=="Solved")?'selected':'' ?>>Solved</option>
<option value="Escalated" <?= (isset($editData) && $editData['lodge_status']=="Escalated")?'selected':'' ?>>Escalated</option>
</select>
</div>

<div id="escalationFields" style="display:none;">
<label>Escalation Notes</label>
<textarea name="escalation_notes"><?= $editData['escalation_notes'] ?? '' ?></textarea>

<label>Escalated To</label>
<input type="text" name="escalated_to" value="<?= $editData['escalated_to'] ?? '' ?>">
</div>

<!-- BUTTON -->
<div>
<label>&nbsp;</label>
<button type="submit" name="save_report" class="btn-primary">
<?= $editData ? "Update Report" : "Save Report" ?>
</button>
</div>

</div>

</form>
</div>

<!-- TABLE -->
<div class="card">
<h3>Reports</h3>

<form method="GET" class="form-grid">

<div>
<label>From</label>
<input type="date" name="from">
</div>

<div>
<label>To</label>
<input type="date" name="to">
</div>

<div>
<label>&nbsp;</label>
<button class="btn-secondary">Filter</button>
</div>

</form>

<table>
<tr>
<th>Customer</th>
<th>Status</th>
<th>Notes</th>
<th>Escalated To</th>
<th>Turnaround</th>
<th>Date</th>
<th>Action</th>
</tr>

<?php while($r = $reports->fetch_assoc()): ?>
<tr>
<td><?= $r['customer_name'] ?></td>

<td>
<span class="status-<?= strtolower($r['lodge_status']) ?>">
<?= $r['lodge_status'] ?>
</span>
</td>

<td><?= $r['escalation_notes'] ?></td>
<td><?= $r['escalated_to'] ?></td>
<td><?= $r['turnaround_time'] ?></td>
<td><?= $r['created_at'] ?></td>

<td>
<a class="action-btn" href="?edit=<?= $r['id'] ?>">Edit</a>
</td>
</tr>
<?php endwhile; ?>

</table>

</div>

</div>

<script>
document.getElementById("interactionSelect").addEventListener("change", function() {
    let name = this.options[this.selectedIndex].getAttribute("data-name");
    document.getElementById("customer_name").value = name;
});

document.getElementById("statusSelect").addEventListener("change", function() {
    document.getElementById("escalationFields").style.display =
        this.value === "Escalated" ? "block" : "none";
});

window.onload = function() {
    let status = document.getElementById("statusSelect").value;
    if (status === "Escalated") {
        document.getElementById("escalationFields").style.display = "block";
    }
};
</script>

</body>
</html>