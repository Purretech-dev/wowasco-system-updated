<?php
// db.php - Database connection

// Enable error reporting (helps during development)
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Database credentials
$servername = "localhost";
$username   = "root";
$password   = "";
$dbname     = "wowasco2";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Set charset
$conn->set_charset("utf8mb4");
?>