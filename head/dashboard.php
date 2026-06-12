<?php
session_start();

if(!isset($_SESSION['user_id']) || $_SESSION['role']!="head"){
header("Location: ../index.php");
exit();
}

include("../config/db.php");

/* ================= COUNTS ================= */

$total_students=mysqli_num_rows(mysqli_query($conn,"
SELECT * FROM students
"));

$total_rooms=mysqli_num_rows(mysqli_query($conn,"
SELECT * FROM rooms
"));

$occupied_rooms=mysqli_num_rows(mysqli_query($conn,"
SELECT * FROM rooms
WHERE current_occupancy > 0
"));

$total_complaints=mysqli_num_rows(mysqli_query($conn,"
SELECT * FROM complaints
WHERE status!='Resolved'
"));

$total_fees=mysqli_fetch_assoc(mysqli_query($conn,"
SELECT SUM(amount) AS total FROM fees
"));

/* ================= ATTENDANCE ================= */

$present=mysqli_num_rows(mysqli_query($conn,"
SELECT * FROM attendance
WHERE status='present'
"));

$absent=mysqli_num_rows(mysqli_query($conn,"
SELECT * FROM attendance
WHERE status='absent'
"));

/* ================= CHART DATA ================= */

$pending=mysqli_num_rows(mysqli_query($conn,"
SELECT * FROM complaints
WHERE status='Pending'
"));

$progress=mysqli_num_rows(mysqli_query($conn,"
SELECT * FROM complaints
WHERE status='In Progress'
"));

$resolved=mysqli_num_rows(mysqli_query($conn,"
SELECT * FROM complaints
WHERE status='Resolved'
"));

?>

<!DOCTYPE html>
<html>

<head>

<title>Head Dashboard</title>

<script src="https://cdn.tailwindcss.com"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>

<style>

body{
background:#020617;
font-family:system-ui;
}

.card{
background:#18181b;
border:1px solid #27272a;
}

</style>

</head>

<body class="text-zinc-200">

<div class="flex">

<!-- SIDEBAR -->

<div class="w-64 min-h-screen bg-zinc-900 border-r border-zinc-800 flex flex-col">

<div class="p-6 text-2xl font-bold text-white">
GPTC HOSTEL
</div>

<nav class="flex-1 px-4 space-y-2">

<a href="dashboard.php"
class="block p-3 rounded-lg bg-emerald-600 text-white">
Dashboard
</a>

<a href="students.php"
class="block p-3 rounded-lg hover:bg-zinc-800">
Students
</a>

<a href="attendance.php"
class="block p-3 rounded-lg hover:bg-zinc-800">
Attendance
</a>

<a href="rooms.php"
class="block p-3 rounded-lg hover:bg-zinc-800">
Rooms
</a>

<a href="fees.php"
class="block p-3 rounded-lg hover:bg-zinc-800">
Fees
</a>

<a href="complaints.php"
class="block p-3 rounded-lg hover:bg-zinc-800">
Complaints
</a>

<a href="../auth/logout.php"
class="block p-3 rounded-lg hover:bg-red-600 mt-10">
Logout
</a>

</nav>

</div>

<!-- MAIN -->

<div class="flex-1 p-10">

<!-- TOP -->

<div class="flex justify-between items-center mb-10">

<div>

<h1 class="text-4xl font-bold text-white">
College Head Dashboard
</h1>

<p class="text-zinc-500 mt-2">
Monitor hostel activities and reports
</p>

</div>

<div class="bg-zinc-900 border border-zinc-800 px-5 py-3 rounded-xl">

<div class="text-sm text-zinc-400">
Logged in as
</div>

<div class="font-semibold">
<?php echo $_SESSION['username']; ?>
</div>

</div>

</div>

<!-- CARDS -->

<div class="grid grid-cols-5 gap-6 mb-10">

<div class="card rounded-2xl p-6">

<div class="text-zinc-400 text-sm">
Total Students
</div>

<div class="text-4xl font-bold mt-3 text-white">
<?php echo $total_students; ?>
</div>

</div>

<div class="card rounded-2xl p-6">

<div class="text-zinc-400 text-sm">
Total Rooms
</div>

<div class="text-4xl font-bold mt-3 text-white">
<?php echo $total_rooms; ?>
</div>

</div>

<div class="card rounded-2xl p-6">

<div class="text-zinc-400 text-sm">
Occupied Rooms
</div>

<div class="text-4xl font-bold mt-3 text-white">
<?php echo $occupied_rooms; ?>
</div>

</div>

<div class="card rounded-2xl p-6">

<div class="text-zinc-400 text-sm">
Pending Complaints
</div>

<div class="text-4xl font-bold mt-3 text-red-400">
<?php echo $total_complaints; ?>
</div>

</div>

<div class="card rounded-2xl p-6">

<div class="text-zinc-400 text-sm">
Fee Collection
</div>

<div class="text-4xl font-bold mt-3 text-emerald-400">
₹<?php echo $total_fees['total'] ? $total_fees['total'] : 0; ?>
</div>

</div>

</div>

<!-- CHARTS -->

<div class="grid grid-cols-2 gap-8">

<!-- ATTENDANCE -->

<div class="card rounded-2xl p-6 h-[400px]">

<h2 class="text-xl font-semibold mb-6">
Attendance Overview
</h2>

<div class="h-[300px]">
<canvas id="attendanceChart"></canvas>
</div>

</div>

<!-- COMPLAINT -->

<div class="card rounded-2xl p-6 h-[400px]">

<h2 class="text-xl font-semibold mb-6">
Complaint Status
</h2>

<div class="h-[300px]">
<canvas id="complaintChart"></canvas>
</div>

</div>

</div>

<!-- RECENT COMPLAINTS -->

<div class="card rounded-2xl p-6 mt-10">

<h2 class="text-xl font-semibold mb-6">
Recent Complaints
</h2>

<table class="w-full text-left">

<thead class="border-b border-zinc-800 text-zinc-400 text-sm">

<tr>

<th class="py-3">Student</th>
<th>Title</th>
<th>Status</th>

</tr>

</thead>

<tbody>

<?php

$recent=mysqli_query($conn,"
SELECT complaints.*, students.full_name
FROM complaints
LEFT JOIN students
ON complaints.student_id = students.student_id
ORDER BY created_at DESC
LIMIT 5
");

while($row=mysqli_fetch_assoc($recent)){

?>

<tr class="border-b border-zinc-800">

<td class="py-4">
<?php echo $row['full_name']; ?>
</td>

<td>
<?php echo $row['title']; ?>
</td>

<td>

<?php if($row['status']=="Pending"){ ?>

<span class="bg-yellow-500/20 text-yellow-400 px-3 py-1 rounded-full text-xs">
Pending
</span>

<?php } ?>

<?php if($row['status']=="In Progress"){ ?>

<span class="bg-blue-500/20 text-blue-400 px-3 py-1 rounded-full text-xs">
In Progress
</span>

<?php } ?>

<?php if($row['status']=="Resolved"){ ?>

<span class="bg-emerald-500/20 text-emerald-400 px-3 py-1 rounded-full text-xs">
Resolved
</span>

<?php } ?>

</td>

</tr>

<?php } ?>

</tbody>

</table>

</div>

</div>

</div>

<!-- CHART SCRIPT -->

<script>

window.onload=function(){

/* ================= ATTENDANCE CHART ================= */

const attendanceCanvas=document.getElementById('attendanceChart');

if(attendanceCanvas){

new Chart(attendanceCanvas,{

type:'doughnut',

data:{

labels:['Present','Absent'],

datasets:[{

data:[
<?php echo (int)$present; ?>,
<?php echo (int)$absent; ?>
],

backgroundColor:[
'#10b981',
'#ef4444'
],

borderWidth:0

}]

},

options:{

responsive:true,

maintainAspectRatio:false,

plugins:{

legend:{

labels:{
color:'white'
}

}

}

}

});

}

/* ================= COMPLAINT CHART ================= */

const complaintCanvas=document.getElementById('complaintChart');

if(complaintCanvas){

new Chart(complaintCanvas,{

type:'bar',

data:{

labels:['Pending','In Progress','Resolved'],

datasets:[{

label:'Complaints',

data:[
<?php echo (int)$pending; ?>,
<?php echo (int)$progress; ?>,
<?php echo (int)$resolved; ?>
],

backgroundColor:[
'#facc15',
'#3b82f6',
'#10b981'
],

borderRadius:10

}]

},

options:{

responsive:true,

maintainAspectRatio:false,

plugins:{

legend:{

labels:{
color:'white'
}

}

},

scales:{

x:{

ticks:{
color:'white'
},

grid:{
color:'#27272a'
}

},

y:{

beginAtZero:true,

ticks:{
color:'white'
},

grid:{
color:'#27272a'
}

}

}

}

});

}

};

</script>

</body>
</html>