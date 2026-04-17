<?php
require_once __DIR__ . '/../../api/db.php';

/* ================= SAFE INPUT ================= */
function clean($v){ return isset($v) ? trim($v) : ''; }

/* ================= KENYA DEDUCTIONS ================= */
function calculateDeductions($gross){

    $nssf = min($gross * 0.06, 1080);
    $sha = $gross * 0.0275;
    $housing = $gross * 0.015;

    if ($gross <= 24000) $paye = $gross * 0.10;
    elseif ($gross <= 32333) $paye = $gross * 0.25;
    else $paye = $gross * 0.30;

    return [
        'nssf'=>round($nssf,2),
        'sha'=>round($sha,2),
        'housing'=>round($housing,2),
        'paye'=>round($paye,2),
        'net'=>round($gross-($nssf+$sha+$housing+$paye),2)
    ];
}

/* ================= DELETE EMPLOYEE ================= */
if(isset($_GET['delete'])){
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM payroll WHERE id=$id");
}

/* ================= PAYSLIP GENERATOR (FIXED) ================= */
$payslip = null;

if(isset($_POST['generate_payslip'])){
    $emp_no = clean($_POST['employee_id']);
    $month = clean($_POST['month']);

    $stmt = $conn->prepare("
        SELECT * FROM payroll 
        WHERE employee_id = ? 
           OR employee_id = ?
        LIMIT 1
    ");

    $stmt->bind_param("ss", $emp_no, $emp_no);
    $stmt->execute();
    $result = $stmt->get_result();
    $payslip = $result->fetch_assoc();

    if($payslip){
        $calc = calculateDeductions($payslip['gross_salary']);
        $payslip['month'] = $month;
        $payslip['calc'] = $calc;
    } else {
        $payslip = null;
    }
}

/* ================= ADD / UPDATE ================= */
if(isset($_POST['save_employee'])){

    $id = $_POST['id'] ?? null;
    $name = clean($_POST['employee_name']);
    $emp_no = clean($_POST['employee_id']);
    $position = clean($_POST['position']);
    $department = clean($_POST['department']);
    $bank = clean($_POST['bank_name']);
    $account = clean($_POST['account_number']);
    $gross = floatval($_POST['gross_salary']);
    $status = clean($_POST['status']);

    $calc = calculateDeductions($gross);

    if($id){
        $stmt = $conn->prepare("
        UPDATE payroll SET
        employee_name=?, employee_id=?, position=?, department=?,
        bank_name=?, account_number=?, gross_salary=?,
        nssf=?, sha=?, housing_levy=?, tax=?, net_salary=?, status=?
        WHERE id=?
        ");

        $stmt->bind_param(
            "ssssssddddddsi",
            $name,$emp_no,$position,$department,
            $bank,$account,$gross,
            $calc['nssf'],$calc['sha'],$calc['housing'],$calc['paye'],
            $calc['net'],$status,$id
        );
        $stmt->execute();

    } else {

        $stmt = $conn->prepare("
        INSERT INTO payroll
        (employee_name, employee_id, position, department,
        bank_name, account_number, gross_salary,
        nssf, sha, housing_levy, tax, net_salary, status)
        VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)
        ");

        $stmt->bind_param(
            "ssssssdddddds",
            $name,$emp_no,$position,$department,
            $bank,$account,$gross,
            $calc['nssf'],$calc['sha'],$calc['housing'],$calc['paye'],
            $calc['net'],$status
        );
        $stmt->execute();
    }
}

/* ================= DATA ================= */
$employees = $conn->query("SELECT * FROM payroll ORDER BY id DESC");

/* ================= TOTAL PAYROLL ================= */
$totalPayroll = $conn->query("SELECT SUM(net_salary) as t FROM payroll")
->fetch_assoc()['t'] ?? 0;

/* ================= DEPARTMENT COST ================= */
$deptCost = $conn->query("
SELECT department,
       SUM(gross_salary) as total_gross,
       SUM(net_salary) as total_net,
       COUNT(*) as employees
FROM payroll
GROUP BY department
ORDER BY total_net DESC
");
?>

<!DOCTYPE html>
<html>
<head>
<title>Payroll System Advanced</title>

<style>
body{font-family:Arial;background:#eef3f8;margin:0;}
.container{width:95%;margin:auto;padding:20px;}
h2{text-align:center;color:#003366;}

.card{
    background:#fff;
    padding:15px;
    margin-bottom:15px;
    border-radius:8px;
    box-shadow:0 2px 8px rgba(0,0,0,0.1);
}

input,select{
    padding:8px;
    margin:5px;
    border:1px solid #ccc;
    border-radius:5px;
}

button{
    padding:10px 15px;
    background:#28a745;
    color:#fff;
    border:none;
    border-radius:5px;
    cursor:pointer;
}

button:hover{background:#218838;}

table{
    width:100%;
    border-collapse:collapse;
}

th,td{
    padding:10px;
    text-align:left;
    border-bottom:1px solid #ddd;
}

th{background:#003366;color:white;}

.toggle-btn{
    background:#003366;
    color:white;
    padding:10px 15px;
    border:none;
    border-radius:5px;
    cursor:pointer;
    margin-right:10px;
}

.hidden{display:none;}

.payslip{
    background:#fff;
    padding:15px;
    margin-bottom:15px;
    border-radius:8px;
    box-shadow:0 2px 8px rgba(0,0,0,0.2);
}

/* ================= NEW: SCREEN MODE (SHOW ONLY PAYSLIP) ================= */
<?php if($payslip): ?>
body.payslip-view .card:not(.payslip){
    display:none;
}
<?php endif; ?>

/* ================= NEW: PRINT MODE (ONLY PAYSLIP PRINTS) ================= */
@media print {
    body * {
        visibility: hidden;
    }

    .payslip, .payslip * {
        visibility: visible;
    }

    .payslip {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
    }

    button {
        display: none !important;
    }
}
</style>
</head>

<body class="<?php if($payslip) echo 'payslip-view'; ?>">

<div class="container">

<h2>🇰🇪 Advanced Payroll System</h2>

<?php if($payslip): ?>
<div class="card payslip">
<h3>🧾 Monthly Payslip (<?= $payslip['month'] ?>)</h3>

<p><b>Employee:</b> <?= $payslip['employee_name'] ?></p>
<p><b>Employee No:</b> <?= $payslip['employee_id'] ?></p>
<p><b>Department:</b> <?= $payslip['department'] ?></p>

<hr>

<p>Gross: <?= number_format($payslip['gross_salary'],2) ?></p>
<p>NSSF: <?= number_format($payslip['calc']['nssf'],2) ?></p>
<p>SHA: <?= number_format($payslip['calc']['sha'],2) ?></p>
<p>Housing: <?= number_format($payslip['calc']['housing'],2) ?></p>
<p>PAYE: <?= number_format($payslip['calc']['paye'],2) ?></p>

<hr>

<h3>Net Pay: <?= number_format($payslip['calc']['net'],2) ?></h3>

<button onclick="window.print()">🖨 Print Payslip</button>
<button onclick="window.location.href='payroll.php'">⬅ Back</button>

</div>
<?php endif; ?>

<div class="card">
<h3>Total Payroll: <?= number_format($totalPayroll,2) ?></h3>
</div>

<div class="card">
<h3>Add Employee</h3>

<form method="POST">
<input type="hidden" name="id">

<input name="employee_name" placeholder="Name" required>
<input name="employee_id" placeholder="Emp No" required>
<input name="position" placeholder="Position">
<input name="department" placeholder="Department">

<input name="bank_name" placeholder="Bank Name">
<input name="account_number" placeholder="Account No">

<input type="number" name="gross_salary" placeholder="Gross Salary" required>

<select name="status">
<option value="Active">Active</option>
<option value="Inactive">Inactive</option>
</select>

<button name="save_employee">Save</button>
</form>
</div>

<div class="card">
<h3>🧾 Generate Monthly Payslip</h3>

<form method="POST">
<input name="employee_number" placeholder="Employee Number" required>
<input type="month" name="month" required>
<button name="generate_payslip">Generate Payslip</button>
</form>
</div>

<div class="card">
<button class="toggle-btn" onclick="toggleTable()">👁 View Employee Records</button>
<button class="toggle-btn" onclick="toggleDept()">🏢 View Department Payroll Cost</button>
</div>

<div class="card hidden" id="tableBox">
<table>
<tr>
<th>Name</th>
<th>Dept</th>
<th>Gross</th>
<th>Net</th>
<th>Status</th>
</tr>

<?php while($e=$employees->fetch_assoc()): ?>
<tr>
<td><?= $e['employee_name'] ?></td>
<td><?= $e['department'] ?></td>
<td><?= number_format($e['gross_salary'],2) ?></td>
<td><b><?= number_format($e['net_salary'],2) ?></b></td>
<td><?= $e['status'] ?></td>
</tr>
<?php endwhile; ?>
</table>
</div>

<div class="card hidden" id="deptBox">
<h3>🏢 Department Payroll Cost Analysis</h3>

<table>
<tr>
<th>Department</th>
<th>Employees</th>
<th>Total Gross</th>
<th>Total Net</th>
</tr>

<?php while($d=$deptCost->fetch_assoc()): ?>
<tr>
<td><?= $d['department'] ?></td>
<td><?= $d['employees'] ?></td>
<td><?= number_format($d['total_gross'],2) ?></td>
<td><b><?= number_format($d['total_net'],2) ?></b></td>
</tr>
<?php endwhile; ?>
</table>
</div>

</div>

<script>
function toggleTable(){
    document.getElementById("tableBox").classList.toggle("hidden");
}

function toggleDept(){
    document.getElementById("deptBox").classList.toggle("hidden");
}
</script>

</body>
</html>