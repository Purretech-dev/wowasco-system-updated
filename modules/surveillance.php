<?php
// surveillance.php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Surveillance Module</title>

    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #0a1f44, #1e3c72);
            color: #fff;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            text-align: center;
        }

        .container {
            background: rgba(255, 255, 255, 0.05);
            padding: 50px;
            border-radius: 12px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.3);
            max-width: 500px;
        }

        h1 {
            font-size: 32px;
            margin-bottom: 10px;
        }

        .badge {
            display: inline-block;
            padding: 6px 12px;
            background: #ffcc00;
            color: #000;
            border-radius: 20px;
            font-size: 14px;
            margin-bottom: 20px;
        }

        p {
            font-size: 16px;
            opacity: 0.9;
        }

        .icon {
            font-size: 60px;
            margin-bottom: 20px;
        }

        .btn {
            margin-top: 25px;
            padding: 10px 20px;
            border: none;
            background: #00c6ff;
            color: #fff;
            border-radius: 6px;
            cursor: pointer;
            text-decoration: none;
        }

        .btn:hover {
            background: #0072ff;
        }
    </style>
</head>

<body>

<div class="container">
    <div class="icon">📡</div>

    <div class="badge">COMING SOON</div>

    <h1>Surveillance Module</h1>

    <p>
        This module is currently under development.<br>
        It will enable real-time monitoring, alerts, and infrastructure surveillance.
    </p>

    <a href="/wowasco/index.php" class="btn">⬅ Back to Dashboard</a>
</div>

</body>
</html>