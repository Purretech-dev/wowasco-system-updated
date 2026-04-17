<?php
$conn = new mysqli("localhost", "root", "", "wowasco2");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$customer_id = $_GET['customer_id'] ?? 1;

/* ================= BILL HISTORY ================= */
$bills = $conn->query("
    SELECT * FROM bills 
    WHERE customer_id = $customer_id
    ORDER BY created_at DESC
");

/* ================= PAYMENT HISTORY ================= */
$payments = $conn->query("
    SELECT * FROM payments 
    WHERE customer_id = $customer_id
    ORDER BY payment_date DESC
");

/* ================= CONSUMPTION DATA ================= */
$consumption = $conn->query("
    SELECT month, units_used 
    FROM consumption 
    WHERE customer_id = $customer_id
    ORDER BY month ASC
");

/* ================= COMPLAINT SUBMISSION ================= */
if(isset($_POST['submit_complaint'])){

    $stmt = $conn->prepare("
        INSERT INTO complaints (customer_id, subject, message, status)
        VALUES (?, ?, ?, 'Pending')
    ");

    $stmt->bind_param(
        "iss",
        $customer_id,
        $_POST['subject'],
        $_POST['message']
    );

    $stmt->execute();

    $msg = "Complaint submitted successfully.";
}

/* ================= RATIONING SCHEDULE ================= */
$schedule = $conn->query("
    SELECT * FROM rationing_schedule
    ORDER BY schedule_date DESC
");
?>

<!DOCTYPE html>
<html>
<head>
<title>Customer Self-Service Portal</title>

<style>
body {
    font-family: Arial;
    background: #f4f6f9;
    margin: 0;
}

.header {
    background: #0b2d5c;
    color: white;
    padding: 15px;
    font-size: 18px;
    font-weight: bold;
}

.container {
    padding: 20px;
}

/* TABS */
.tabs {
    display: flex;
    gap: 10px;
    margin-bottom: 15px;
}

.tab-btn {
    padding: 10px 15px;
    background: #ddd;
    border: none;
    cursor: pointer;
    border-radius: 6px;
}

.tab-btn.active {
    background: #0b2d5c;
    color: white;
}

/* PANELS */
.panel {
    display: none;
    background: white;
    padding: 15px;
    border-radius: 10px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.1);
}

.panel.active {
    display: block;
}

/* TABLE */
table {
    width: 100%;
    border-collapse: collapse;
}

th {
    background: #0b2d5c;
    color: white;
}

th, td {
    padding: 10px;
    border-bottom: 1px solid #ddd;
}

/* FORM */
input, textarea {
    width: 100%;
    padding: 10px;
    margin: 6px 0;
    border: 1px solid #ccc;
    border-radius: 6px;
}

button {
    padding: 10px 15px;
    background: #28a745;
    color: white;
    border: none;
    border-radius: 6px;
    cursor: pointer;
}

.success {
    color: green;
    margin-bottom: 10px;
}
</style>
</head>

<body>

<div class="header">
Customer Self-Service Portal
</div>

<div class="container">

<div class="tabs">
<button class="tab-btn active" onclick="openTab('bills')">Bills</button>
<button class="tab-btn" onclick="openTab('payments')">Payments</button>
<button class="tab-btn" onclick="openTab('consumption')">Consumption</button>
<button class="tab-btn" onclick="openTab('complaints')">Complaints</button>
<button class="tab-btn" onclick="openTab('rationing')">Rationing</button>
</div>

<?php if(isset($msg)): ?>
<p class="success"><?= $msg ?></p>
<?php endif; ?>

<!-- ================= BILLS ================= -->
<div id="bills" class="panel active">
<h3>📄 Bills</h3>

<table>
<tr>
<th>Month</th>
<th>Amount</th>
<th>Status</th>
</tr>

<?php while($b = $bills->fetch_assoc()): ?>
<tr>
<td><?= $b['bill_month'] ?></td>
<td><?= number_format($b['amount'],2) ?></td>
<td><?= $b['status'] ?></td>
</tr>
<?php endwhile; ?>
</table>
</div>

<!-- ================= PAYMENTS ================= -->
<div id="payments" class="panel">
<h3>💳 Payment History</h3>

<table>
<tr>
<th>Date</th>
<th>Amount</th>
<th>Method</th>
</tr>

<?php while($p = $payments->fetch_assoc()): ?>
<tr>
<td><?= $p['payment_date'] ?></td>
<td><?= number_format($p['amount'],2) ?></td>
<td><?= $p['method'] ?></td>
</tr>
<?php endwhile; ?>
</table>
</div>

<!-- ================= CONSUMPTION ================= -->
<div id="consumption" class="panel">
<h3>📊 Consumption Trends</h3>

<table>
<tr>
<th>Month</th>
<th>Units Used</th>
</tr>

<?php while($c = $consumption->fetch_assoc()): ?>
<tr>
<td><?= $c['month'] ?></td>
<td><?= $c['units_used'] ?></td>
</tr>
<?php endwhile; ?>
</table>
</div>

<!-- ================= COMPLAINTS ================= -->
<div id="complaints" class="panel">
<h3>📢 Report Fault / Complaint</h3>

<form method="POST">

<input type="text" name="subject" placeholder="Subject" required>
<textarea name="message" placeholder="Describe the issue..." required></textarea>

<button type="submit" name="submit_complaint">
Submit Complaint
</button>

</form>
</div>

<!-- ================= RATIONING ================= -->
<div id="rationing" class="panel">
<h3>🚰 Water Rationing Schedule</h3>

<table>
<tr>
<th>Date</th>
<th>Area</th>
<th>Time</th>
<th>Status</th>
</tr>

<?php while($r = $schedule->fetch_assoc()): ?>
<tr>
<td><?= $r['schedule_date'] ?></td>
<td><?= $r['area'] ?></td>
<td><?= $r['time_slot'] ?></td>
<td><?= $r['status'] ?></td>
</tr>
<?php endwhile; ?>
</table>
</div>

</div>

<script>
function openTab(tabId){

    let panels = document.querySelectorAll(".panel");
    panels.forEach(p => p.classList.remove("active"));

    let buttons = document.querySelectorAll(".tab-btn");
    buttons.forEach(b => b.classList.remove("active"));

    document.getElementById(tabId).classList.add("active");
    event.target.classList.add("active");
}
</script>

</body>
</html>