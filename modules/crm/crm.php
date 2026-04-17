<?php
session_start();

$conn = new mysqli("localhost", "root", "", "wowasco2");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

/* DEFAULT VIEW FIX */
if (!isset($_GET['view'])) {
    $_GET['view'] = 'register';
}

/* DELETE CUSTOMER */
if (isset($_GET['delete'])) {
    $meter = $_GET['delete'];

    $conn->query("DELETE FROM customer1 WHERE meter_serial = '$meter'");

    header("Location: ?view=list");
    exit;
}

/* AJAX AUTO-FETCH */
if (isset($_GET['fetch_customer'])) {
    $name = $_GET['name'];

    $result = $conn->query("
        SELECT customer_name, customer_type, meter_serial, location 
        FROM meters 
        WHERE customer_name = '$name'
        LIMIT 1
    ");

    echo json_encode($result ? $result->fetch_assoc() : []);
    exit;
}

/* SAVE CUSTOMER */
if (isset($_POST['save_customer'])) {

    $customer_name = $_POST['customer_name'];
    $email = $_POST['email'];

    // CHECK IF EMAIL ALREADY EXISTS
    $check = $conn->prepare("SELECT id FROM customer1 WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        $_SESSION['error'] = "Customer already exists with this email.";
        header("Location: ?view=register");
        exit;
    }

    $stmt = $conn->prepare("
        INSERT INTO customer1 (customer_name, meter_serial, phone, email)
        VALUES (?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE 
        customer_name=VALUES(customer_name),
        phone=VALUES(phone), 
        email=VALUES(email)
    ");

    $stmt->bind_param(
        "ssss",
        $customer_name,
        $_POST['meter_serial'],
        $_POST['phone'],
        $email
    );

    $stmt->execute();

    $_SESSION['show_registered'] = true;

    header("Location: ?view=list");
    exit;
}

/* SAVE INTERACTION */
if (isset($_POST['save_interaction'])) {

    $customer_name = $_POST['customer_name'];
    $action = $_POST['action'];
    $interaction_type = $_POST['interaction_type'];
    $notes = $_POST['notes'];
    $staff = $_POST['staff_assigned'];

    $stmt = $conn->prepare("
        INSERT INTO customer_interactions 
        (customer_name, action, interaction_type, staff_assigned, notes, logged_at)
        VALUES (?, ?, ?, ?, ?, NOW())
    ");

    $stmt->bind_param(
        "sssss",
        $customer_name,
        $action,
        $interaction_type,
        $staff,
        $notes
    );

    $stmt->execute();
}

/* FETCH DATA */
$customers = $conn->query("
    SELECT 
        m.customer_name,
        m.customer_type,
        m.serial_number,
        m.zone,
        c.phone,
        c.email
    FROM customer1 c
    INNER JOIN meters m 
        ON m.serial_number = c.serial_number
    ORDER BY m.id DESC
");
?>

<!DOCTYPE html>
<html>
<head>
<title>WOWASCO CRM</title>

<style>
* { box-sizing: border-box; }

body {
    font-family: 'Segoe UI';
    margin: 0;
    background: #f4f7fb;
}

.header {
    background: #1e3a8a;
    color: white;
    padding: 15px 25px;
    font-size: 20px;
    font-weight: bold;
}

.nav {
    padding: 10px 25px;
    background: white;
    border-bottom: 1px solid #ddd;
}

.nav a {
    text-decoration: none;
    color: #1e3a8a;
    font-weight: bold;
    margin-right: 15px;
}

.container { padding: 20px 25px; }

.card {
    background: white;
    padding: 20px;
    border-radius: 12px;
    margin-bottom: 20px;
    box-shadow: 0 4px 10px rgba(0,0,0,0.05);
}

.form-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    gap: 15px;
}

.form-group {
    display: flex;
    flex-direction: column;
}

input, select, textarea {
    padding: 10px;
    border-radius: 6px;
    border: 1px solid #ccc;
    width: 100%;
    font-size: 14px;
}

button {
    background: #16a34a;
    color: white;
    border: none;
    padding: 10px;
    border-radius: 6px;
    cursor: pointer;
}

.delete-btn {
    background: red;
    padding: 6px 10px;
    border-radius: 5px;
    color: white;
    text-decoration: none;
}

table {
    width: 100%;
    border-collapse: collapse;
}

th {
    background: #1e3a8a;
    color: white;
    padding: 10px;
}

td {
    padding: 10px;
    border-bottom: 1px solid #eee;
}

tr:hover { background: #f1f5ff; }

.highlight { border-left: 5px solid #facc15; }
</style>
</head>

<body>

<div class="header">WOWASCO CRM</div>

<div class="nav">
    <a href="?view=register">Register Customer</a>
    <a href="?view=list">Customer Records</a>
    <a href="?view=interaction">Interactions</a>
</div>

<div class="container">

<?php if ($_GET['view'] == 'register'): ?>

<div class="card highlight">
<h3>Register Customer</h3>

<?php
if (isset($_SESSION['error'])) {
    echo "<p style='color:red;'>".$_SESSION['error']."</p>";
    unset($_SESSION['error']);
}
?>

<form method="POST">

<div class="form-grid">

<div class="form-group">
<label>Customer Name</label>
<select id="customer_name" name="customer_name" onchange="fetchCustomer()">
<option value="">Select Customer</option>
<?php
$list = $conn->query("SELECT DISTINCT customer_name FROM meters");
while($row = $list->fetch_assoc()):
?>
<option value="<?= $row['customer_name'] ?>">
<?= $row['customer_name'] ?>
</option>
<?php endwhile; ?>
</select>
</div>

<div class="form-group">
<label>Customer Type</label>
<input type="text" id="customer_type" readonly>
</div>

<div class="form-group">
<label>Meter Serial</label>
<input type="text" id="meter_serial" name="meter_serial" readonly>
</div>

<div class="form-group">
<label>Location</label>
<input type="text" id="zone" readonly>
</div>

<div class="form-group">
<label>Phone</label>
<input type="text" name="phone">
</div>

<div class="form-group">
<label>Email</label>
<input type="email" name="email">
</div>

</div>

<br>
<button type="submit" name="save_customer">Save Customer</button>

</form>
</div>

<?php endif; ?>

<?php if ($_GET['view'] == 'interaction'): ?>

<div class="card highlight">
<h3>Customer Interactions</h3>

<form method="POST">

<div class="form-grid">

<div class="form-group">
<label>Customer Name</label>
<input type="text" name="customer_name" required placeholder="Enter customer name">
</div>

<div class="form-group">
<label>Action</label>
<select name="action" required>
<option value="call">Call</option>
<option value="office visit">Office Visit</option>
</select>
</div>

<div class="form-group">
<label>Issue Logged</label>
<select name="interaction_type" required>
<option value="complain">Complain</option>
<option value="enquiry">Enquiry</option>
<option value="follow up">Follow Up</option>
</select>
</div>

<div class="form-group">
<label>Notes</label>
<textarea name="notes"></textarea>
</div>

<div class="form-group">
<label>Staff Assigned</label>
<input type="text" name="staff_assigned">
</div>

</div>

<br>
<button type="submit" name="save_interaction">Save Interaction</button>

</form>
</div>

<?php
$where = "WHERE 1=1";

if (!empty($_GET['from']) && !empty($_GET['to'])) {
    $from = $_GET['from'];
    $to = $_GET['to'];
    $where .= " AND logged_at BETWEEN '$from' AND '$to'";
}

$interactions = $conn->query("
    SELECT * FROM customer_interactions
    $where
    ORDER BY logged_at DESC
");
?>

<div class="card">
<table>
<tr>
<th>Customer</th>
<th>Action</th>
<th>Issue</th>
<th>Staff</th>
<th>Notes</th>
<th>Time</th>
</tr>

<?php while($row = $interactions->fetch_assoc()): ?>
<tr>
<td><?= $row['customer_name'] ?></td>
<td><?= $row['action'] ?></td>
<td><?= $row['interaction_type'] ?></td>
<td><?= $row['staff_assigned'] ?></td>
<td><?= $row['notes'] ?></td>
<td><?= $row['logged_at'] ?></td>
</tr>
<?php endwhile; ?>

</table>
</div>

<?php endif; ?>

<?php if ($_GET['view'] == 'list'): ?>

<div class="card">
<h3>Customer Records</h3>

<table>
<tr>
<th>Name</th>
<th>Type</th>
<th>Meter</th>
<th>Location</th>
<th>Phone</th>
<th>Email</th>
<th>Action</th>
</tr>

<?php while($row = $customers->fetch_assoc()): ?>
<tr>
<td><?= $row['customer_name'] ?></td>
<td><?= $row['customer_type'] ?></td>
<td><?= $row['meter_serial'] ?></td>
<td><?= $row['location'] ?></td>
<td><?= $row['phone'] ?? '-' ?></td>
<td><?= $row['email'] ?? '-' ?></td>
<td>
<a class="delete-btn"
onclick="return confirm('Delete this customer?')"
href="?view=list&delete=<?= $row['meter_serial'] ?>">
Delete
</a>
</td>
</tr>
<?php endwhile; ?>

</table>
</div>

<?php endif; ?>

</div>

<script>
function fetchCustomer() {
    let name = document.getElementById("customer_name").value;

    if (!name) return;

    fetch("?fetch_customer=1&name=" + name)
    .then(res => res.json())
    .then(data => {
        if (data) {
            document.getElementById("customer_type").value = data.customer_type || "";
            document.getElementById("meter_serial").value = data.meter_serial || "";
            document.getElementById("location").value = data.location || "";
        }
    });
}
</script>

</body>
</html>