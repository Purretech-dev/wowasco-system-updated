<?php
include $_SERVER['DOCUMENT_ROOT'].'/wowasco/api/db.php';

// Get date range
$from_date = "";
$to_date = "";

if(isset($_GET['from_date']) && !empty($_GET['from_date'])){
    $from_date = $_GET['from_date'];
}

if(isset($_GET['to_date']) && !empty($_GET['to_date'])){
    $to_date = $_GET['to_date'];
}

// Base query
$sql = "SELECT * FROM invoices2 WHERE 1";

// Apply date range filter
if($from_date != "" && $to_date != ""){
    $sql .= " AND billing_period BETWEEN '$from_date' AND '$to_date'";
} elseif($from_date != "") {
    $sql .= " AND billing_period >= '$from_date'";
} elseif($to_date != "") {
    $sql .= " AND billing_period <= '$to_date'";
}

$sql .= " ORDER BY billing_period DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Invoices - WOWASCO</title>

<style>
body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: #eef3f8;
    margin: 0;
}

.container {
    width: 95%;
    margin: 30px auto;
}

h2 {
    text-align: center;
    color: #003366;
}

.card {
    background: #fff;
    padding: 25px;
    border-radius: 10px;
    box-shadow: 0 3px 12px rgba(0,0,0,0.1);
}

/* FILTER */
.filter-box {
    margin-bottom: 15px;
    display: flex;
    gap: 10px;
    align-items: center;
    flex-wrap: wrap;
}

input[type="date"] {
    padding: 8px;
    border-radius: 6px;
    border: 1px solid #ccc;
}

button {
    border: none;
    padding: 8px 12px;
    border-radius: 6px;
    cursor: pointer;
}

.filter-btn {
    background: #003366;
    color: white;
}

.filter-btn:hover {
    background: #f7b731;
    color: #003366;
}

/* TABLE */
table {
    width: 100%;
    border-collapse: collapse;
}

th {
    background: #003366;
    color: white;
    padding: 12px;
}

td {
    padding: 10px;
    border-bottom: 1px solid #ddd;
}

tr:nth-child(even) {
    background: #f2f2f2;
}

/* PRINT */
.print-btn {
    background: #FFC107;
    color: #003366;
}

.print-btn:hover {
    background: #e6b800;
}

/* BACK */
.back-btn {
    display: inline-block;
    margin-top: 20px;
    padding: 10px 15px;
    background: #6c757d;
    color: white;
    text-decoration: none;
    border-radius: 5px;
}

.back-btn:hover {
    background: #5a6268;
}
</style>

<script>
function printInvoice(meterSerial, customerName, billingPeriod, pumpedVolume, unitRate, amount) {
    let invoiceWindow = window.open('', '_blank', 'width=800,height=600');

    invoiceWindow.document.write('<html><head><title>Invoice</title></head><body>');
    invoiceWindow.document.write('<h2 style="text-align:center;">WOWASCO Invoice</h2>');
    invoiceWindow.document.write('<p><strong>Meter Serial:</strong> ' + meterSerial + '</p>');
    invoiceWindow.document.write('<p><strong>Customer Name:</strong> ' + customerName + '</p>');
    invoiceWindow.document.write('<p><strong>Billing Period:</strong> ' + billingPeriod + '</p>');
    invoiceWindow.document.write('<p><strong>Pumped Volume:</strong> ' + pumpedVolume + ' m³</p>');
    invoiceWindow.document.write('<p><strong>Unit Rate:</strong> Ksh ' + unitRate + '</p>');
    invoiceWindow.document.write('<p><strong>Total Amount:</strong> Ksh ' + amount + '</p>');
    invoiceWindow.document.write('<hr><p style="text-align:center;">Thank you!</p>');
    invoiceWindow.document.write('</body></html>');

    invoiceWindow.document.close();
    invoiceWindow.print();
}
</script>

</head>

<body>

<div class="container">
<h2>Invoices</h2>

<div class="card">

<!-- FILTER -->
<form method="GET" class="filter-box">

    <label><strong>From:</strong></label>
    <input type="date" name="from_date" value="<?= htmlspecialchars($from_date) ?>">

    <label><strong>To:</strong></label>
    <input type="date" name="to_date" value="<?= htmlspecialchars($to_date) ?>">

    <button type="submit" class="filter-btn">Filter</button>

</form>

<table>
<tr>
    <th>Meter Serial</th>
    <th>Customer Name</th>
    <th>Billing Period</th>
    <th>Pumped Volume</th>
    <th>Unit Rate</th>
    <th>Amount</th>
    <th>Status</th>
    <th>Print</th>
</tr>

<?php if($result && $result->num_rows > 0): ?>
    <?php while($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($row['meter_serial']) ?></td>
            <td><?= htmlspecialchars($row['customer_name']) ?></td>
            <td><?= htmlspecialchars($row['billing_period']) ?></td>
            <td><?= $row['pumped_volume'] ?></td>
            <td><?= $row['unit_rate'] ?></td>
            <td><?= $row['amount'] ?></td>
            <td><?= htmlspecialchars($row['status']) ?></td>
            <td>
                <button class="print-btn" onclick="printInvoice(
                    '<?= $row['meter_serial'] ?>',
                    '<?= $row['customer_name'] ?>',
                    '<?= $row['billing_period'] ?>',
                    '<?= $row['pumped_volume'] ?>',
                    '<?= $row['unit_rate'] ?>',
                    '<?= $row['amount'] ?>'
                )">Print</button>
            </td>
        </tr>
    <?php endwhile; ?>
<?php else: ?>
    <tr>
        <td colspan="8" style="text-align:center;">No invoices found.</td>
    </tr>
<?php endif; ?>
</table>

</div>

<a href="/wowasco/index.php" class="back-btn">← Back to Dashboard</a>

</div>

</body>
</html>