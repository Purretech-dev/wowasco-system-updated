<?php
// No DB needed yet — static schedule (can be upgraded later)
?>

<!DOCTYPE html>
<html>
<head>
<title>WOWASCO Water Rationing Schedule</title>

<style>
body{
    margin:0;
    font-family:"Segoe UI", Arial;
    background:#f4f6fb;
}

/* HEADER */
.header{
    background:linear-gradient(135deg,#0b3d91,#1a5edb);
    color:#fff;
    padding:20px;
    font-size:22px;
    font-weight:600;
    text-align:center;
}

/* GRID */
.container{
    padding:20px;
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(280px,1fr));
    gap:20px;
}

/* CARD */
.card{
    background:white;
    border-radius:12px;
    box-shadow:0 4px 15px rgba(0,0,0,0.08);
    padding:15px;
    transition:0.3s;
}

.card:hover{
    transform:translateY(-3px);
}

/* DAY TITLE */
.day{
    font-weight:600;
    color:#0b3d91;
    margin-bottom:10px;
    font-size:18px;
}

/* ENTRY */
.entry{
    padding:10px;
    border-radius:8px;
    margin-bottom:8px;
    font-size:14px;
}

/* SOURCES */
.kaiti{
    background:#e3f2fd;
    border-left:5px solid #1a5edb;
}

.mwaani{
    background:#e8f5e9;
    border-left:5px solid #2e7d32;
}

.mixed{
    background:#fff3e0;
    border-left:5px solid #ef6c00;
}

/* LABELS */
.label{
    font-weight:600;
}

/* FOOTER NOTE */
.note{
    margin:20px;
    padding:15px;
    background:white;
    border-radius:10px;
    text-align:center;
    font-size:14px;
}
</style>
</head>

<body>

<div class="header">💧 WOWASCO Weekly Water Rationing Schedule 2026</div>

<div class="container">

<!-- MONDAY -->
<div class="card">
<div class="day">Monday</div>

<div class="entry kaiti">
<span class="label">Kaiti Source:</span><br>
Kasarani & Town Zone<br>
⏰ 6:00 AM – 7:00 PM
</div>

<div class="entry mwaani">
<span class="label">Mwaani Source:</span><br>
Westlands Zone<br>
⏰ 6:00 AM – 7:00 PM
</div>
</div>

<!-- TUESDAY -->
<div class="card">
<div class="day">Tuesday</div>

<div class="entry kaiti">
<span class="label">Kaiti Source:</span><br>
Kasarani & Town Zone<br>
⏰ 6:00 AM – 7:00 PM
</div>

<div class="entry mwaani">
<span class="label">Mwaani Source:</span><br>
Muambani & Mwaani Zones<br>
⏰ 6:00 AM – 7:00 PM
</div>
</div>

<!-- WEDNESDAY -->
<div class="card">
<div class="day">Wednesday</div>

<div class="entry kaiti">
<span class="label">Kaiti Source:</span><br>
Westlands Zone<br>
⏰ 6:00 AM – 7:00 PM
</div>

<div class="entry mwaani">
<span class="label">Mwaani Source:</span><br>
Return Zone<br>
⏰ 6:00 AM – 7:00 PM
</div>
</div>

<!-- THURSDAY -->
<div class="card">
<div class="day">Thursday</div>

<div class="entry kaiti">
<span class="label">Kaiti Source:</span><br>
Westlands Zone<br>
⏰ 6:00 AM – 7:00 PM
</div>

<div class="entry mwaani">
<span class="label">Mwaani Source:</span><br>
Shimo Zone<br>
⏰ 6:00 AM – 7:00 PM
</div>
</div>

<!-- FRIDAY -->
<div class="card">
<div class="day">Friday</div>

<div class="entry mixed">
<span class="label">Kaiti & Mwaani Sources:</span><br>
Kasarani & Town Zones<br>
⏰ 6:00 AM – 7:00 PM
</div>
</div>

<!-- SATURDAY -->
<div class="card">
<div class="day">Saturday</div>

<div class="entry kaiti">
<span class="label">Kaiti Source:</span><br>
Westlands Zone<br>
⏰ 6:00 AM – 7:00 PM
</div>

<div class="entry mwaani">
<span class="label">Mwaani Source:</span><br>
Kundakindu & Malawi Zones<br>
⏰ 6:00 AM – 7:00 PM
</div>
</div>

<!-- SUNDAY -->
<div class="card">
<div class="day">Sunday</div>

<div class="entry kaiti">
<span class="label">Kaiti Source:</span><br>
Kasarani Zone<br>
⏰ 6:00 AM – 7:00 PM
</div>

<div class="entry mwaani">
<span class="label">Mwaani Source:</span><br>
Kundakindu & Malawi Zones<br>
⏰ 6:00 AM – 7:00 PM
</div>
</div>

</div>

<div class="note">
📢 <b>Note:WASREB COMPLIANCE</b> <li>Priority shall be given to health facilities, schools and other essential services.</li>
<li>Consumers are encouraged to store water  responsibly during supply hours.</li>
<li>Wowasco shall issue timely public notices incase incase of to this schedule.</li>
<li>Illegal connections and water wastage are prohibited under  WASREB regulations.</li>
</div>

</body>
</html>