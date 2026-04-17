<?php
// Include DB connection
include $_SERVER['DOCUMENT_ROOT'].'/wowasco/api/db.php';

/* ==========================
   CONFIGURATION
========================== */
$unit_rate = 50; // Ksh per m³, adjust as needed
$message = '';

/* ==========================
   GENERATE BILLS
========================== */
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Fetch all meters
    $meters = $conn->query("SELECT id, serial_number, customer_name FROM meters");
    
    if($meters && $meters->num_rows > 0){
        while($meter = $meters->fetch_assoc()){
            $meter_id = $meter['id'];
            $meter_serial = $meter['serial_number'];
            $customer_name = $meter['customer_name'];

            // Calculate pumped volume
            $volume_result = $conn->query("SELECT SUM(reading_value) as total_volume FROM meter_readings WHERE meter_id=$meter_id");
            $volume_row = $volume_result->fetch_assoc();
            $pumped_volume = $volume_row['total_volume'] ?? 0;

            if($pumped_volume > 0){
                $amount = $pumped_volume * $unit_rate;
                $billing_period = date('Y-m'); // Current month, adjust as needed
                $status = 'Unpaid';

                // Check if invoice already exists for this meter and period
                $check = $conn->query("SELECT COUNT(*) as total FROM invoices2 WHERE meter_serial='$meter_serial' AND billing_period='$billing_period'");
                $row_check = $check->fetch_assoc();

                if($row_check['total'] == 0){
                    // Insert invoice
                    $stmt = $conn->prepare("INSERT INTO invoices2 (serial_number, customer_name, billing_period, pumped_volume, unit_rate, amount, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
                    $stmt->bind_param("sssddds", $serial_number, $customer_name, $billing_period, $pumped_volume, $unit_rate, $amount, $status);
                    $stmt->execute();
                }
            }
        }
        $message = "Bills generated successfully!";
    } else {
        $message = "No meters found to generate bills.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Generate Bills - WOWASCO</title>
<style>
body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: #eef3f8;
    margin: 0;
    padding: 0;
}
.container {
    width: 90%;
    margin: 30px auto;
}
h2 {
    text-align: center;
    color: #003366;
    margin-bottom: 20px;
}
.card {
    background: #fff;
    padding: 25px;
    border-radius: 10px;
    box-shadow: 0 3px 12px rgba(0,0,0,0.1);
    margin-bottom: 20px;
    text-align: center;
}
button {
    background: #FFC107; /* yellow */
    color: #003366;
    font-weight: bold;
    padding: 12px 20px;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-size: 16px;
    transition: background 0.3s;
}
button:hover {
    background: #e6b800;
}
.message {
    margin-top: 15px;
    font-weight: bold;
    color: #155724;
}
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
</head>
<body>

<div class="container">
<h2>Generate Bills</h2>

<div class="card">
<form method="POST">
    <button type="submit">Generate All Bills</button>
</form>
<?php if($message != ''): ?>
    <div class="message"><?= htmlspecialchars($message) ?></div>
<?php endif; ?>
</div>

<a href="/wowasco/modules/billing/invoices.php" class="back-btn">View Invoices →</a>
</div>

</body>
</html>