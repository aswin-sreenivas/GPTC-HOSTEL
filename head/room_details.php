<?php
session_start();

if(!isset($_SESSION['user_id']) || $_SESSION['role']!="head"){
header("Location: ../index.php");
exit();
}

include("../config/db.php");

if(!isset($_GET['id'])){
header("Location: rooms.php");
exit();
}

$id=$_GET['id'];

/* room details */

$room=mysqli_fetch_assoc(mysqli_query($conn,"
SELECT rooms.*, hostel_blocks.block_name

FROM rooms

LEFT JOIN hostel_blocks
ON rooms.block_id = hostel_blocks.block_id

WHERE rooms.room_id='$id'
"));

if(!$room){
die("Room not found");
}

/* students in room */

$students=mysqli_query($conn,"
SELECT students.*

FROM room_allocations

LEFT JOIN students
ON room_allocations.student_id = students.student_id

WHERE room_allocations.room_id='$id'
AND room_allocations.status='active'
");

?>

<!DOCTYPE html>
<html>

<head>

<title>Room Details</title>

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

<?php include("layout/sidebar.php"); ?>

<div class="flex-1 p-10">

<!-- HEADER -->

<div class="flex justify-between items-center mb-8">

<div>

<h1 class="text-4xl font-bold text-white">
Room <?php echo $room['room_number']; ?>
</h1>

<p class="text-zinc-500 mt-2">
Room occupancy and student details
</p>

</div>

<a href="rooms.php"
class="bg-zinc-800 hover:bg-zinc-700 px-5 py-3 rounded-xl">

← Back

</a>

</div>

<!-- ROOM INFO -->

<div class="grid grid-cols-3 gap-6 mb-8">

<div class="card rounded-2xl p-6">

<div class="text-zinc-400 text-sm">
Hostel Block
</div>

<div class="text-3xl font-bold mt-3 text-white">
<?php echo $room['block_name']; ?>
</div>

</div>

<div class="card rounded-2xl p-6">

<div class="text-zinc-400 text-sm">
Capacity
</div>

<div class="text-3xl font-bold mt-3 text-white">
<?php echo $room['capacity']; ?>
</div>

</div>

<div class="card rounded-2xl p-6">

<div class="text-zinc-400 text-sm">
Current Occupancy
</div>

<div class="text-3xl font-bold mt-3 text-emerald-400">
<?php echo $room['current_occupancy']; ?>
</div>

</div>

</div>

<!-- STUDENTS -->

<div class="card rounded-2xl overflow-hidden">

<div class="p-6 border-b border-zinc-800">

<h2 class="text-2xl font-semibold">
Students in Room
</h2>

</div>

<table class="w-full text-left">

<thead class="bg-zinc-900 border-b border-zinc-800 text-zinc-400 text-sm">

<tr>

<th class="p-5">Student</th>
<th>Admission</th>
<th>Phone</th>
<th>Course</th>
<th>Year</th>

</tr>

</thead>

<tbody>

<?php if(mysqli_num_rows($students)>0){ ?>

<?php while($row=mysqli_fetch_assoc($students)){ ?>

<tr class="border-b border-zinc-800 hover:bg-zinc-900 transition">

<td class="p-5">

<div class="font-semibold text-white">
<?php echo $row['full_name']; ?>
</div>

<div class="text-sm text-zinc-500">
<?php echo $row['email']; ?>
</div>

</td>

<td>
<?php echo $row['admission_no']; ?>
</td>

<td>
<?php echo $row['phone']; ?>
</td>

<td>
<?php echo $row['course']; ?>
</td>

<td>
Year <?php echo $row['year']; ?>
</td>

</tr>

<?php } ?>

<?php } else { ?>

<tr>

<td colspan="5" class="p-10 text-center text-zinc-500">
No students allocated to this room
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