<?php
session_start();

// Redirect to login if user is not logged in
if(!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Wote Water & Sanitation Company (WOWASCO)</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- CSS -->
    <link rel="stylesheet" href="assets/css/sidebar.css">
    <link rel="stylesheet" href="assets/css/main.css">
    <link rel="stylesheet" href="assets/css/dashboard.css">

    <!-- UI ENHANCEMENTS -->
    <style>

        body {
            background: #eef3f8;
        }

        /* HEADINGS */
        h2 {
            color: #003366;
            border-left: 5px solid #f7b731;
            padding-left: 10px;
            margin-bottom: 15px;
        }

        /* CARDS */
        .card {
            border-radius: 10px;
            padding: 20px;
            color: white;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            transition: 0.3s;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .card.green {
            background: linear-gradient(135deg, #2e7d32, #4caf50);
        }

        .card.blue {
            background: linear-gradient(135deg, #003366, #00509e);
        }

        .card.yellow {
            background: linear-gradient(135deg, #f7b731, #f5c542);
            color: #003366;
        }

        /* GRID */
        .cards-grid {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }

        /* CHARTS */
        .charts-row {
            display: flex;
            gap: 20px;
            margin-top: 20px;
            flex-wrap: wrap;
        }

        canvas {
            background: white;
            padding: 10px;
            border-radius: 10px;
            box-shadow: 0 3px 8px rgba(0,0,0,0.08);
        }

        .chart-wrapper {
            background: white;
            padding: 10px;
            border-radius: 10px;
            box-shadow: 0 3px 8px rgba(0,0,0,0.08);
        }

        /* OVERLAY */
        .overlay {
            padding: 20px;
        }

        /* BUTTON STYLE (GLOBAL IF ANY USED) */
        button {
            background: #003366;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 6px;
            cursor: pointer;
        }

        button:hover {
            background: #f7b731;
            color: #003366;
        }

        /* ================= LOGOUT BUTTON ================= */
        .logout-btn {
            position: absolute;
            top: 15px;
            right: 20px;
            background: #e53935;
            color: white;
            padding: 8px 14px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 14px;
            z-index: 9999;
        }

        .logout-btn:hover {
            background: #c62828;
        }

        /* MAKE HEADER AREA RELATIVE FOR POSITIONING */
        .main-content {
            position: relative;
        }

    </style>

</head>
<body>

<div class="layout">

    <!-- Sidebar -->
    <?php include 'includes/sidebar.php'; ?>

    <!-- Main Content -->
    <div class="main-content">

        <!-- 🔴 LOGOUT BUTTON (TOP RIGHT CORNER) -->
        <a href="logout.php" class="logout-btn"
           onclick="return confirm('Are you sure you want to logout?')">
            Logout
        </a>

        <!-- Header -->
        <?php include 'includes/header.php'; ?>

        <!-- Dashboard -->
        <div class="overlay">

            <!-- 1️⃣ TODAY'S ANALYSIS -->
            <section class="analysis-section">
                <h2>Today's Analysis</h2>
                <div class="cards-grid">
                    <div class="card green">
                        <h3>Revenue Today</h3>
                        <p>KES 125,400</p>
                    </div>
                    <div class="card yellow">
                        <h3>Active Meters</h3>
                        <p>4,320</p>
                    </div>
                    <div class="card blue">
                        <h3>Inactive Meters</h3>
                        <p>380</p>
                    </div>
                </div>
                <div class="charts-row">
                    <canvas id="todayRevenueChart"></canvas>
                    <div class="chart-wrapper">
                        <canvas id="todayMetersChart" class="doughnut-chart"></canvas>
                    </div>
                </div>
            </section>

            <!-- 2️⃣ LAST MONTH'S ANALYSIS -->
            <section class="analysis-section">
                <h2>Last Month's Analysis</h2>
                <div class="cards-grid">
                    <div class="card blue">
                        <h3>Revenue Last Month</h3>
                        <p>KES 2,540,000</p>
                    </div>
                    <div class="card green">
                        <h3>Active Meters</h3>
                        <p>4,100</p>
                    </div>
                    <div class="card yellow">
                        <h3>Inactive Meters</h3>
                        <p>400</p>
                    </div>
                </div>
                <div class="charts-row">
                    <canvas id="lastMonthRevenueChart"></canvas>
                    <div class="chart-wrapper">
                        <canvas id="lastMonthMetersChart" class="doughnut-chart"></canvas>
                    </div>
                </div>
            </section>

            <!-- 3️⃣ TREND GRAPHS -->
            <section class="analysis-section">
                <h2>Trend Graphs</h2>
                <div class="charts-row">
                    <canvas id="revenueTrendChart"></canvas>
                    <canvas id="metersTrendChart"></canvas>
                </div>
            </section>

        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="assets/js/dashboard.js"></script>
<script src="assets/js/sidebar.js"></script>

</body>
</html>