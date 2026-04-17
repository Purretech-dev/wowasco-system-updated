<?php
// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include database connection
include $_SERVER['DOCUMENT_ROOT'].'/wowasco/api/db.php';

// Handle form submission
$message = '';
$success = false;
$registered_serial = '';

if($_SERVER['REQUEST_METHOD'] == 'POST'){

    $serial_number = $_POST['serial_number'] ?? '';
    $model = $_POST['model'] ?? '';
    $customer_type = $_POST['customer_type'] ?? '';
    $customer_name = $_POST['customer_name'] ?? '';
    $meter_type = $_POST['meter_type'] ?? '';
    $installation_date = $_POST['installation_date'] ?? '';
    $zone = $_POST['zone'] ?? '';

    // VALIDATION
    if(
        empty($serial_number) ||
        empty($model) ||
        empty($customer_type) ||
        empty($customer_name) ||
        empty($meter_type) ||
        empty($installation_date) ||
        empty($zone)
    ){
        $message = "Error: Please fill in all required fields before submitting.";
        $success = false;
    } 
    // ✅ NEW VALIDATION: Prevent FUTURE dates
    elseif(strtotime($installation_date) > strtotime(date('Y-m-d'))){
        $message = "Error: Installation date cannot be in the future.";
        $success = false;
    }
    else {

        $stmt = $conn->prepare("
            INSERT INTO meters 
            (serial_number, model, customer_type, customer_name, meter_type, installation_date, zone) 
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");

        $stmt->bind_param(
            "sssssss",
            $serial_number,
            $model,
            $customer_type,
            $customer_name,
            $meter_type,
            $installation_date,
            $zone
        );

        if($stmt->execute() && $stmt->affected_rows > 0){
            $message = "Meter registered successfully!";
            $success = true;
            $registered_serial = $serial_number;
        } else {
            $message = "Error: " . $stmt->error;
            $success = false;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Register Meter - WOWASCO</title>

<style>
body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background:#eef3f8;
    margin:0;
}

.container {
    max-width: 600px;
    margin: 50px auto;
    background:#fff;
    padding: 40px;
    border-radius: 14px;
    box-shadow: 0 6px 18px rgba(0,0,0,0.08);
}

h2 {
    text-align:center;
    color:#003366;
    margin-bottom:25px;
}

.message {
    padding:12px;
    margin-bottom:20px;
    border-radius:6px;
    text-align:center;
    font-weight:bold;
}
.success {background:#d4edda;color:#155724;}
.error {background:#f8d7da;color:#721c24;}

form {
    display: flex;
    flex-direction: column;
    gap: 14px;
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: 6px;
}

label {
    font-weight:600;
    color:#333;
}

input, select {
    width:100%;
    padding:12px;
    border-radius:8px;
    border:1px solid #ccc;
    font-size:14px;
}

input:focus, select:focus {
    border-color:#003366;
    box-shadow:0 0 5px rgba(0,51,102,0.2);
    outline:none;
}

.helper {
    font-size:12px;
    color:#666;
}

button {
    width:100%;
    margin-top:10px;
    padding:12px;
    background:#003366;
    color:#fff;
    border:none;
    border-radius:8px;
    font-size:16px;
    cursor:pointer;
}

button:hover {
    background:#00509e;
}

.back-btn {
    display:block;
    margin-top:10px;
    padding:10px 15px;
    background:#6c757d;
    color:white;
    text-decoration:none;
    border-radius:6px;
    text-align:center;
}

.view-btn {
    display:block;
    margin-top:15px;
    padding:12px;
    background:#28a745;
    color:white;
    text-align:center;
    border-radius:8px;
    text-decoration:none;
    font-weight:bold;
}
</style>
</head>

<body>

<div class="container">

<h2>Register New Meter</h2>

<?php if($message != ''): ?>
<div class="message <?php echo ($success === true) ? 'success' : 'error'; ?>">
    <?php echo $message; ?>
</div>
<?php endif; ?>

<?php if($success === true): ?>
<a href="/wowasco/modules/metering/meter_status.php?serial=<?php echo urlencode($registered_serial); ?>" class="view-btn">
🔍 View Meter Status
</a>
<?php endif; ?>

<form method="POST">

<div class="form-group">
<label>Meter Serial</label>
<input type="text" name="serial_number" placeholder="Enter Meter Serial" required>
</div>

<div class="form-group">
<label>Model</label>
<input type="text" name="model" placeholder="Enter meter Model" required>
</div>

<div class="form-group">
<label>Customer Type</label>
<select name="customer_type" required>
<option value="">-- Select Customer Type --</option>
<option value="Government Entities">Government Entities</option>
<option value="Residential">Residential</option>
<option value="Commercial">Commercial</option>
<option value="Domestic">Domestic</option>
</select>
</div>

<div class="form-group">
<label>Customer Name</label>
<input type="text" name="customer_name" placeholder="Enter Customer Name" required>
<div class="helper">Enter the full name of the customer assigned to this meter</div>
</div>

<div class="form-group">
<label>Meter Type</label>
<input type="text" name="meter_type" placeholder="e.g. Smart Meter, Analog Meter" required>
</div>

<div class="form-group">
<label>Installation Date</label>
<!-- ✅ Prevent selecting FUTURE dates -->
<input type="date" name="installation_date" max="<?php echo date('Y-m-d'); ?>" required>
</div>

<div class="form-group">
<label>Zone</label>
<select name="zone" required>
<option value="">-- Select Zone --</option>
<option>Westlands</option>
<option>Shimo</option>
<option>Malawi</option>
<option>KundaKindu</option>
<option>Return</option>
<option>Town</option>
<option>Unoa</option>
<option>Kitikyumu</option>
<option>Mukuyuni</option>
<option>Muambani</option>
<option>Mwaani</option>
<option>Kaiti</option>
<option>Kilala</option>
</select>
</div>

<button type="submit">Register Meter</button>

<a href="/wowasco/index.php" class="back-btn">← Back to Dashboard</a>

</form>

</div>

</body>
</html>