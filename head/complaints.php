<?php
session_start();

if(!isset($_SESSION['user_id']) || $_SESSION['role']!="head"){
header("Location: ../index.php");
exit();
}

include("../config/db.php");

/* ================= COMPLAINTS ================= */

$complaints=mysqli_query($conn,"
SELECT 

complaints.*,
students.full_name,
rooms.room_number

FROM complaints

LEFT JOIN students
ON complaints.student_id = students.student_id

LEFT JOIN room_allocations
ON students.student_id = room_allocations.student_id
AND room_allocations.status='active'

LEFT JOIN rooms
ON room_allocations.room_id = rooms.room_id

ORDER BY complaints.created_at DESC
");

/* ================= STATS ================= */

$pending=mysqli_num_rows(mysqli_query($conn,"
SELECT * FROM complaints
WHERE LOWER(status)='pending'
"));

$progress=mysqli_num_rows(mysqli_query($conn,"
SELECT * FROM complaints
WHERE LOWER(status)='in progress'
OR LOWER(status)='in_progress'
"));

$resolved=mysqli_num_rows(mysqli_query($conn,"
SELECT * FROM complaints
WHERE LOWER(status)='resolved'
"));

?>

<!DOCTYPE html>
<html>

<head>

<title>Complaint Monitoring</title>

<script src="https://cdn.tailwindcss.com"></script>

<style>

body{
background:#020617;
font-family:system-ui;
}

.card{
background:#18181b;
border:1px solid #27272a;
}

tr:hover{
background:#18181b;
}

</style>

</head>

<body class="text-zinc-200">

<div class="flex min-h-screen">

<!-- SIDEBAR -->

<?php include("layout/sidebar.php"); ?>

<!-- MAIN -->

<div class="flex-1 p-10">

<!-- HEADER -->

<div class="flex justify-between items-center mb-8">

<div>

<h1 class="text-4xl font-black text-white">
Complaint Monitoring
</h1>

<p class="text-zinc-500 mt-2">
Track hostel complaints and monitor issue resolution
</p>

</div>

<div class="bg-zinc-900 border border-zinc-800 px-5 py-3 rounded-2xl text-sm text-zinc-400">

👁 Read Only Access

</div>

</div>

<!-- STATS -->

<div class="grid grid-cols-3 gap-6 mb-8">

<!-- PENDING -->

<div class="card rounded-[28px] p-6">

<div class="text-zinc-400 text-sm">
Pending Complaints
</div>

<div class="text-5xl font-black text-yellow-400 mt-4">

<?php echo $pending; ?>

</div>

</div>

<!-- PROGRESS -->

<div class="card rounded-[28px] p-6">

<div class="text-zinc-400 text-sm">
In Progress
</div>

<div class="text-5xl font-black text-blue-400 mt-4">

<?php echo $progress; ?>

</div>

</div>

<!-- RESOLVED -->

<div class="card rounded-[28px] p-6">

<div class="text-zinc-400 text-sm">
Resolved
</div>

<div class="text-5xl font-black text-emerald-400 mt-4">

<?php echo $resolved; ?>

</div>

</div>

</div>

<!-- TABLE -->

<div class="card rounded-[28px] overflow-hidden">

<div class="overflow-x-auto">

<table class="w-full text-left">

<thead class="bg-zinc-950 border-b border-zinc-800 text-zinc-400 text-sm">

<tr>

<th class="p-5">Student</th>
<th>Room</th>
<th>Complaint</th>
<th>Date</th>
<th>Status</th>

</tr>

</thead>

<tbody>

<?php if(mysqli_num_rows($complaints)>0){ ?>

<?php while($row=mysqli_fetch_assoc($complaints)){ ?>

<tr class="border-b border-zinc-800 transition">

<!-- STUDENT -->

<td class="p-5">

<div class="font-semibold text-white">

<?php echo $row['full_name']; ?>

</div>

</td>

<!-- ROOM -->

<td>

<?php

echo $row['room_number']
? "Room ".$row['room_number']
: "N/A";

?>

</td>

<!-- COMPLAINT -->

<td>

<div class="font-semibold text-white">

<?php echo $row['title']; ?>

</div>

<div class="text-sm text-zinc-500 mt-1 max-w-md">

<?php echo $row['description']; ?>

</div>

</td>

<!-- DATE -->

<td>

<?php echo date("d M Y",strtotime($row['created_at'])); ?>

</td>

<!-- STATUS -->

<td>

<?php

$status=strtolower(trim($row['status']));

if($status=="pending"){

?>

<span class="bg-yellow-500/10 text-yellow-400 border border-yellow-500/20 px-4 py-2 rounded-full text-xs">

🟡 Pending

</span>

<?php } elseif($status=="in progress" || $status=="in_progress"){ ?>

<span class="bg-blue-500/10 text-blue-400 border border-blue-500/20 px-4 py-2 rounded-full text-xs">

🔵 In Progress

</span>

<?php } elseif($status=="resolved"){ ?>

<span class="bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 px-4 py-2 rounded-full text-xs">

🟢 Resolved

</span>

<?php } else { ?>

<span class="bg-zinc-700 text-zinc-300 px-4 py-2 rounded-full text-xs">

Unknown

</span>

<?php } ?>

</td>

</tr>

<?php } ?>

<?php } else { ?>

<tr>

<td colspan="5" class="p-16 text-center">

<div class="text-7xl mb-5">
📭
</div>

<h2 class="text-3xl font-black text-white">
No Complaints Found
</h2>

<p class="text-zinc-500 mt-3">

No complaints available in the system.

</p>

</td>

</tr>

<?php } ?>

</tbody>

</table>

</div>

</div>

</div>

</div>

</body>
</html>