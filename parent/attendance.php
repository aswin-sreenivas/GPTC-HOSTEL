<?php
session_start();

if(!isset($_SESSION['user_id']) || $_SESSION['role']!="parent"){
header("Location: ../index.php");
exit();
}

include("../config/db.php");

$user_id=$_SESSION['user_id'];

/* ================= STUDENT INFO ================= */

$result=mysqli_query($conn,"
SELECT 

students.*,
rooms.room_number,
hostel_blocks.block_name,

parents.father_name

FROM students

LEFT JOIN parents
ON students.parent_id = parents.parent_id

LEFT JOIN room_allocations
ON students.student_id = room_allocations.student_id
AND room_allocations.status='active'

LEFT JOIN rooms
ON room_allocations.room_id = rooms.room_id

LEFT JOIN hostel_blocks
ON rooms.block_id = hostel_blocks.block_id

WHERE parents.user_id='$user_id'
");

if(!$result){
die("SQL Error: ".mysqli_error($conn));
}

$student=mysqli_fetch_assoc($result);

if(!$student){
die("No student linked to this parent.");
}

$student_id=$student['student_id'];

/* ================= ATTENDANCE ================= */

$attendance=mysqli_query($conn,"
SELECT *
FROM attendance
WHERE student_id='$student_id'
ORDER BY date DESC
");

/* ================= ATTENDANCE STATS ================= */

$total_days=mysqli_fetch_assoc(mysqli_query($conn,"
SELECT COUNT(*) as total
FROM attendance
WHERE student_id='$student_id'
"))['total'];

$present_days=mysqli_fetch_assoc(mysqli_query($conn,"
SELECT COUNT(*) as total
FROM attendance
WHERE student_id='$student_id'
AND status='present'
"))['total'];

$absent_days=mysqli_fetch_assoc(mysqli_query($conn,"
SELECT COUNT(*) as total
FROM attendance
WHERE student_id='$student_id'
AND status='absent'
"))['total'];

$percentage=0;

if($total_days>0){
$percentage=round(($present_days/$total_days)*100);
}

?>

<!DOCTYPE html>
<html>

<head>

<title>Attendance Monitoring</title>

<script src="https://cdn.tailwindcss.com"></script>

<style>

body{
background:#020617;
font-family:system-ui;
}

/* CARD */

.card{
background:rgba(24,24,27,.88);
border:1px solid #27272a;
backdrop-filter:blur(14px);
transition:.3s;
}

.card:hover{
border-color:rgba(16,185,129,.25);
transform:translateY(-2px);
}

/* TABLE */

.table-row{
transition:.25s;
}

.table-row:hover{
background:#18181b;
}

/* SCROLL */

::-webkit-scrollbar{
width:6px;
}

::-webkit-scrollbar-thumb{
background:#27272a;
border-radius:999px;
}

</style>

</head>

<body class="text-zinc-200">

<div class="flex min-h-screen">

<?php include("layout/sidebar.php"); ?>

<div class="flex-1 p-10">

<!-- TOP HEADER -->

<div class="flex justify-between items-center mb-10">

<div>

<h1 class="text-4xl font-black text-white">
Attendance Monitoring
</h1>

<p class="text-zinc-500 mt-2 text-lg">
Track your child's attendance performance in real time.
</p>

</div>

<div class="card px-5 py-4 rounded-2xl">

<div class="text-zinc-400 text-sm">
Student
</div>

<div class="font-semibold text-white mt-1">
<?php echo $student['full_name']; ?>
</div>

</div>

</div>

<!-- HERO CARD -->

<div class="card rounded-[32px] p-8 mb-8 relative overflow-hidden">

<div class="absolute top-0 right-0 w-60 h-60 bg-emerald-500/5 rounded-full blur-3xl"></div>

<div class="relative z-10 flex justify-between items-center">

<div>

<div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-sm mb-5">

<div class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></div>

Attendance Analytics

</div>

<h2 class="text-5xl font-black text-white">

<?php echo $percentage; ?>%

</h2>

<p class="text-zinc-500 text-lg mt-3">
Overall Attendance Percentage
</p>

</div>

<div class="hidden lg:flex items-center justify-center">

<div class="w-40 h-40 rounded-full border-[14px] border-emerald-500/20 flex items-center justify-center">

<div class="text-center">

<div class="text-4xl font-black text-emerald-400">
<?php echo $present_days; ?>
</div>

<div class="text-zinc-500 text-sm mt-1">
Present Days
</div>

</div>

</div>

</div>

</div>

</div>

<!-- STATS -->

<div class="grid grid-cols-4 gap-6 mb-8">

<!-- PRESENT -->

<div class="card rounded-3xl p-6">

<div class="flex justify-between items-start">

<div>

<div class="text-zinc-400 text-sm">
PRESENT
</div>

<h2 class="text-4xl font-black text-emerald-400 mt-4">
<?php echo $present_days; ?>
</h2>

<p class="text-zinc-500 text-sm mt-2">
Attendance marked present
</p>

</div>

<div class="text-4xl">
✅
</div>

</div>

</div>

<!-- ABSENT -->

<div class="card rounded-3xl p-6">

<div class="flex justify-between items-start">

<div>

<div class="text-zinc-400 text-sm">
ABSENT
</div>

<h2 class="text-4xl font-black text-red-400 mt-4">
<?php echo $absent_days; ?>
</h2>

<p class="text-zinc-500 text-sm mt-2">
Attendance marked absent
</p>

</div>

<div class="text-4xl">
❌
</div>

</div>

</div>

<!-- TOTAL -->

<div class="card rounded-3xl p-6">

<div class="flex justify-between items-start">

<div>

<div class="text-zinc-400 text-sm">
TOTAL DAYS
</div>

<h2 class="text-4xl font-black text-blue-400 mt-4">
<?php echo $total_days; ?>
</h2>

<p class="text-zinc-500 text-sm mt-2">
Total attendance records
</p>

</div>

<div class="text-4xl">
📅
</div>

</div>

</div>

<!-- ROOM -->

<div class="card rounded-3xl p-6">

<div class="flex justify-between items-start">

<div>

<div class="text-zinc-400 text-sm">
HOSTEL ROOM
</div>

<h2 class="text-3xl font-black text-white mt-4">

<?php
echo $student['room_number']
? "Room ".$student['room_number']
: "N/A";
?>

</h2>

<p class="text-zinc-500 text-sm mt-2">

<?php
echo $student['block_name']
? $student['block_name']
: "No Block";
?>

</p>

</div>

<div class="text-4xl">
🛏
</div>

</div>

</div>

</div>

<!-- TABLE -->

<div class="card rounded-[32px] overflow-hidden">

<!-- TABLE HEADER -->

<div class="p-7 border-b border-zinc-800 flex justify-between items-center">

<div>

<h2 class="text-2xl font-bold text-white">
Attendance Records
</h2>

<p class="text-zinc-500 text-sm mt-1">
Daily attendance logs and status history
</p>

</div>

<div class="bg-zinc-900 border border-zinc-800 px-4 py-2 rounded-xl text-sm text-zinc-400">

Total Records:
<?php echo mysqli_num_rows($attendance); ?>

</div>

</div>

<!-- TABLE -->

<div class="overflow-x-auto">

<table class="w-full text-left">

<thead class="bg-zinc-900 text-zinc-400 text-sm border-b border-zinc-800">

<tr>

<th class="p-5">Date</th>
<th>Status</th>
<th>Performance</th>

</tr>

</thead>

<tbody>

<?php if(mysqli_num_rows($attendance)>0){ ?>

<?php while($row=mysqli_fetch_assoc($attendance)){ ?>

<tr class="table-row border-b border-zinc-800">

<!-- DATE -->

<td class="p-5">

<div class="font-medium text-white">

<?php echo date("d M Y",strtotime($row['date'])); ?>

</div>

<div class="text-zinc-500 text-sm mt-1">

<?php echo date("l",strtotime($row['date'])); ?>

</div>

</td>

<!-- STATUS -->

<td>

<?php if($row['status']=="present"){ ?>

<span class="bg-emerald-500/15 text-emerald-400 px-4 py-2 rounded-full text-sm border border-emerald-500/10">

Present

</span>

<?php } else { ?>

<span class="bg-red-500/15 text-red-400 px-4 py-2 rounded-full text-sm border border-red-500/10">

Absent

</span>

<?php } ?>

</td>

<!-- PERFORMANCE -->

<td>

<?php if($row['status']=="present"){ ?>

<div class="text-emerald-400 text-sm">
Good Attendance
</div>

<?php } else { ?>

<div class="text-red-400 text-sm">
Needs Attention
</div>

<?php } ?>

</td>

</tr>

<?php } ?>

<?php } else { ?>

<tr>

<td colspan="3" class="p-16 text-center">

<div class="text-6xl mb-4">
📭
</div>

<div class="text-xl font-semibold text-white">
No Attendance Records
</div>

<p class="text-zinc-500 mt-2">
Attendance data will appear here once marked by the admin.
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