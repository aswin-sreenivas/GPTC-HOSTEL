<?php
session_start();

if(!isset($_SESSION['user_id']) || $_SESSION['role']!="head"){
header("Location: ../index.php");
exit();
}

include("../config/db.php");

/* filter date */

$date = isset($_GET['date']) ? $_GET['date'] : date("Y-m-d");

/* attendance */

$attendance=mysqli_query($conn,"
SELECT attendance.*, students.full_name,
rooms.room_number

FROM attendance

LEFT JOIN students
ON attendance.student_id = students.student_id

LEFT JOIN room_allocations
ON students.student_id = room_allocations.student_id
AND room_allocations.status='active'

LEFT JOIN rooms
ON room_allocations.room_id = rooms.room_id

WHERE attendance.date='$date'

ORDER BY attendance.attendance_id DESC
");

/* counts */

$present=mysqli_num_rows(mysqli_query($conn,"
SELECT * FROM attendance
WHERE date='$date'
AND status='present'
"));

$absent=mysqli_num_rows(mysqli_query($conn,"
SELECT * FROM attendance
WHERE date='$date'
AND status='absent'
"));

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

.card{
background:#18181b;
border:1px solid #27272a;
}

</style>

</head>

<body class="text-zinc-200">

<div class="flex">

<!-- SIDEBAR -->

<?php include("layout/sidebar.php"); ?>

<!-- MAIN -->

<div class="flex-1 p-10">

<!-- HEADER -->

<div class="flex justify-between items-center mb-8">

<div>

<h1 class="text-4xl font-bold text-white">
Attendance Monitoring
</h1>

<p class="text-zinc-500 mt-2">
Monitor daily student attendance
</p>

</div>

<div class="text-sm text-zinc-500">
Read Only Access
</div>

</div>

<!-- FILTER -->

<div class="card rounded-2xl p-5 mb-8">

<form method="GET" class="flex items-center gap-4">

<input
type="date"
name="date"
value="<?php echo $date; ?>"
class="bg-zinc-800 border border-zinc-700 px-4 py-3 rounded-xl outline-none"
/>

<button
class="bg-emerald-600 hover:bg-emerald-500 px-5 py-3 rounded-xl">

Filter

</button>

</form>

</div>

<!-- STATS -->

<div class="grid grid-cols-2 gap-6 mb-8">

<div class="card rounded-2xl p-6">

<div class="text-zinc-400 text-sm">
Present Students
</div>

<div class="text-4xl font-bold text-emerald-400 mt-3">
<?php echo $present; ?>
</div>

</div>

<div class="card rounded-2xl p-6">

<div class="text-zinc-400 text-sm">
Absent Students
</div>

<div class="text-4xl font-bold text-red-400 mt-3">
<?php echo $absent; ?>
</div>

</div>

</div>

<!-- TABLE -->

<div class="card rounded-2xl overflow-hidden">

<table class="w-full text-left">

<thead class="bg-zinc-900 border-b border-zinc-800 text-zinc-400 text-sm">

<tr>

<th class="p-5">Student</th>
<th>Room</th>
<th>Date</th>
<th>Status</th>

</tr>

</thead>

<tbody>

<?php if(mysqli_num_rows($attendance)>0){ ?>

<?php while($row=mysqli_fetch_assoc($attendance)){ ?>

<tr class="border-b border-zinc-800 hover:bg-zinc-900 transition">

<td class="p-5">

<div class="font-semibold text-white">
<?php echo $row['full_name']; ?>
</div>

</td>

<td>

<?php
echo $row['room_number']
? "Room ".$row['room_number']
: "N/A";
?>

</td>

<td>
<?php echo $row['date']; ?>
</td>

<td>

<?php if($row['status']=="present"){ ?>

<span class="bg-emerald-500/20 text-emerald-400 px-3 py-1 rounded-full text-xs">
Present
</span>

<?php } else { ?>

<span class="bg-red-500/20 text-red-400 px-3 py-1 rounded-full text-xs">
Absent
</span>

<?php } ?>

</td>

</tr>

<?php } ?>

<?php } else { ?>

<tr>

<td colspan="4" class="p-10 text-center text-zinc-500">
No attendance records found
</td>

</tr>

<?php } ?>

</tbody>

</table>

</div>

</div>

</div>

</body>
</html>