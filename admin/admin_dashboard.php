<?php
session_start();

if(!isset($_SESSION['user_id']) || $_SESSION['role']!="admin"){
header("Location: ../index.php");
}

include("../config/db.php");

$students = mysqli_num_rows(mysqli_query($conn,"SELECT * FROM students"));
$rooms = mysqli_num_rows(mysqli_query($conn,"SELECT * FROM rooms"));
$complaints = mysqli_num_rows(mysqli_query($conn,"SELECT * FROM complaints WHERE status='pending'"));

?>

<!DOCTYPE html>
<html>

<head>

<title>Dashboard - HostelHub</title>

<script src="https://cdn.tailwindcss.com"></script>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

</head>

<body class="bg-black text-zinc-200">

<div class="flex">

<?php include("layout/sidebar.php"); ?>

<div class="flex-1 p-10">

<?php include("layout/header.php"); ?>

<h1 class="text-3xl font-bold text-white mb-2">
Dashboard Overview
</h1>

<p class="text-zinc-500 mb-8">
Welcome back! Here's what's happening today.
</p>

<div class="grid grid-cols-4 gap-6">

<div class="bg-zinc-900 p-6 rounded-xl border border-zinc-800">
<p class="text-zinc-500 text-sm">Total Students</p>
<h2 class="text-3xl font-bold mt-2"><?php echo $students; ?></h2>
</div>

<div class="bg-zinc-900 p-6 rounded-xl border border-zinc-800">
<p class="text-zinc-500 text-sm">Occupied Rooms</p>
<h2 class="text-3xl font-bold mt-2"><?php echo $rooms; ?></h2>
</div>

<div class="bg-zinc-900 p-6 rounded-xl border border-zinc-800">
<p class="text-zinc-500 text-sm">Pending Complaints</p>
<h2 class="text-3xl font-bold mt-2"><?php echo $complaints; ?></h2>
</div>

<div class="bg-zinc-900 p-6 rounded-xl border border-zinc-800">
<p class="text-zinc-500 text-sm">Fees Collected</p>
<h2 class="text-3xl font-bold mt-2">0</h2>
</div>

</div>

<div class="grid grid-cols-3 gap-6 mt-10">

<div class="col-span-2 bg-zinc-900 p-6 rounded-xl border border-zinc-800">

<h2 class="text-lg mb-4">Fee Collection Trends</h2>

<canvas id="feesChart"></canvas>

</div>

<div class="bg-zinc-900 p-6 rounded-xl border border-zinc-800">

<h2 class="text-lg mb-4">Room Occupancy</h2>

<canvas id="roomChart"></canvas>

</div>

</div>

</div>

</div>

<script>

new Chart(document.getElementById('feesChart'),{

type:'line',

data:{
labels:['Jan','Feb','Mar','Apr','May','Jun'],
datasets:[{
label:'Fees',
data:[4000,3000,2000,2800,1900,2400],
borderColor:'#10b981',
backgroundColor:'rgba(16,185,129,0.2)',
fill:true
}]
}

});

new Chart(document.getElementById('roomChart'),{

type:'doughnut',

data:{
labels:['Occupied','Available'],
datasets:[{
data:[1,2],
backgroundColor:['#10b981','#27272a']
}]
}

});

</script>

</body>
</html>