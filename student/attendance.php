<?php
session_start();

if(!isset($_SESSION['user_id']) || $_SESSION['role']!="student"){
header("Location: ../index.php");
exit();
}

include("../config/db.php");

$user_id=$_SESSION['user_id'];

/* get student */

$student_query=mysqli_query($conn,"
SELECT student_id FROM students
WHERE user_id='$user_id'
");

$student=mysqli_fetch_assoc($student_query);

if(!$student){
die("Student record not found. Please contact admin.");
}

$student_id=$student['student_id'];

/* attendance records */

$attendance=mysqli_query($conn,"
SELECT attendance.*, rooms.room_number
FROM attendance
LEFT JOIN room_allocations
ON attendance.student_id = room_allocations.student_id
AND room_allocations.status='active'
LEFT JOIN rooms
ON room_allocations.room_id = rooms.room_id
WHERE attendance.student_id='$student_id'
ORDER BY date DESC
");

?>

<!DOCTYPE html>
<html>

<head>

<title>My Attendance</title>

<script src="https://cdn.tailwindcss.com"></script>

</head>

<body class="bg-black text-zinc-200">

<div class="flex">

<?php include("layout/sidebar.php"); ?>

<div class="flex-1 p-10">

<?php include("layout/header.php"); ?>

<h1 class="text-3xl font-bold text-white mb-2">
My Attendance
</h1>

<p class="text-zinc-500 mb-8">
Track your daily presence in the hostel.
</p>

<div class="bg-zinc-900 border border-zinc-800 rounded-xl">

<div class="p-6 border-b border-zinc-800 flex justify-between items-center">

<div class="text-lg font-semibold">
Attendance Records
</div>

<input
type="text"
placeholder="Search date..."
class="bg-zinc-800 px-4 py-2 rounded-lg outline-none"
/>

</div>

<table class="w-full text-left">

<thead class="text-zinc-500 text-sm border-b border-zinc-800">

<tr>

<th class="p-4">Date</th>
<th>Room</th>
<th>Status</th>

</tr>

</thead>

<tbody>

<?php if(mysqli_num_rows($attendance)>0){ ?>

<?php while($row=mysqli_fetch_assoc($attendance)){ ?>

<tr class="border-b border-zinc-800">

<td class="p-4">
<?php echo $row['date']; ?>
</td>

<td>
Room <?php echo $row['room_number'] ? $row['room_number'] : "Not Assigned"; ?>
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
<td colspan="3" class="text-center p-6 text-zinc-500">
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