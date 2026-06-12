<?php
session_start();

if(!isset($_SESSION['user_id']) || $_SESSION['role']!="admin"){
header("Location: ../index.php");
exit();
}

include("../config/db.php");

/* ================= REMOVE STUDENT ================= */

if(isset($_GET['remove_student'])){

$allocation_id=$_GET['remove_student'];
$room_id=$_GET['room_id'];

/* remove allocation */

mysqli_query($conn,"
UPDATE room_allocations

SET
status='removed'

WHERE allocation_id='$allocation_id'
");

/* decrease occupancy */

mysqli_query($conn,"
UPDATE rooms

SET current_occupancy = current_occupancy - 1

WHERE room_id='$room_id'
AND current_occupancy > 0
");

header("Location: rooms.php");
exit();

}

/* ================= DELETE ROOM ================= */

if(isset($_GET['delete'])){

$room_id=$_GET['delete'];

/* check active students */

$check=mysqli_query($conn,"
SELECT *
FROM room_allocations
WHERE room_id='$room_id'
AND status='active'
");

if(mysqli_num_rows($check)>0){

echo "
<script>
alert('Cannot delete room. Students are allocated.');
window.location='rooms.php';
</script>
";

exit();

}

/* delete room */

mysqli_query($conn,"
DELETE FROM rooms
WHERE room_id='$room_id'
");

header("Location: rooms.php");
exit();

}

/* ================= ROOMS ================= */

$rooms = mysqli_query($conn,"
SELECT 

rooms.*,
hostel_blocks.block_name

FROM rooms

LEFT JOIN hostel_blocks
ON rooms.block_id = hostel_blocks.block_id

ORDER BY rooms.room_number ASC
");

?>

<!DOCTYPE html>
<html>

<head>

<title>Room Management</title>

<script src="https://cdn.tailwindcss.com"></script>

<style>

body{
background:#020617;
font-family:system-ui;
}

.room-card{
background:#18181b;
border:1px solid #27272a;
transition:.3s;
}

.room-card:hover{
transform:translateY(-3px);
border-color:#10b98130;
}

</style>

</head>

<body class="bg-black text-zinc-200">

<div class="flex min-h-screen">

<?php include("layout/sidebar.php"); ?>

<div class="flex-1 p-10">

<?php include("layout/header.php"); ?>

<!-- HEADER -->

<div class="flex justify-between items-center mb-8">

<div>

<h1 class="text-4xl font-black text-white">
Room Management
</h1>

<p class="text-zinc-500 mt-2">
Monitor room occupancy and hostel allocations.
</p>

</div>

<div class="flex gap-4">

<a
href="add_block.php"
class="bg-zinc-800 hover:bg-zinc-700 transition px-5 py-3 rounded-2xl">

+ Add Block

</a>

<a
href="add_room.php"
class="bg-emerald-600 hover:bg-emerald-500 transition px-5 py-3 rounded-2xl text-white font-semibold">

+ Add Room

</a>

</div>

</div>

<!-- ROOM GRID -->

<div class="grid lg:grid-cols-3 gap-6">

<?php while($room=mysqli_fetch_assoc($rooms)){ ?>

<?php

$capacity = $room['capacity'];
$current = $room['current_occupancy'];

$percentage = 0;

if($capacity > 0){
$percentage = ($current/$capacity)*100;
}

/* status */

$status="Available";
$status_color="emerald";

if($current >= $capacity){

$status="Full";
$status_color="red";

}

elseif($current > 0){

$status="Occupied";
$status_color="yellow";

}

/* students */

$students=mysqli_query($conn,"
SELECT 

students.full_name,
students.student_id,
room_allocations.allocation_id

FROM room_allocations

LEFT JOIN students
ON room_allocations.student_id = students.student_id

WHERE room_allocations.room_id='".$room['room_id']."'
AND room_allocations.status='active'
");

?>

<!-- CARD -->

<div class="room-card rounded-[28px] p-6">

<!-- TOP -->

<div class="flex justify-between items-center mb-5">

<div class="text-4xl">
🛏
</div>

<?php if($status_color=="emerald"){ ?>

<div class="bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 text-xs px-4 py-2 rounded-full">

<?php echo $status; ?>

</div>

<?php } ?>

<?php if($status_color=="yellow"){ ?>

<div class="bg-yellow-500/10 text-yellow-400 border border-yellow-500/20 text-xs px-4 py-2 rounded-full">

<?php echo $status; ?>

</div>

<?php } ?>

<?php if($status_color=="red"){ ?>

<div class="bg-red-500/10 text-red-400 border border-red-500/20 text-xs px-4 py-2 rounded-full">

<?php echo $status; ?>

</div>

<?php } ?>

</div>

<!-- ROOM -->

<h2 class="text-2xl font-bold text-white">

Room <?php echo $room['room_number']; ?>

</h2>

<p class="text-zinc-500 mt-2 text-sm">

<?php echo $room['block_name']; ?>

</p>

<!-- OCCUPANCY -->

<div class="mt-6">

<div class="flex justify-between text-sm mb-3">

<div class="text-zinc-400">
Occupancy
</div>

<div class="text-white">
<?php echo $current; ?>/<?php echo $capacity; ?>
</div>

</div>

<div class="w-full bg-zinc-800 rounded-full h-3 overflow-hidden">

<div
class="bg-emerald-500 h-3 rounded-full"
style="width: <?php echo $percentage; ?>%">
</div>

</div>

</div>

<!-- STUDENTS -->

<div class="mt-6 space-y-3">

<?php if(mysqli_num_rows($students)>0){ ?>

<?php while($stu=mysqli_fetch_assoc($students)){ ?>

<div class="bg-zinc-950 border border-zinc-800 rounded-2xl p-4 flex justify-between items-center">

<div>

<div class="text-white font-medium">
<?php echo $stu['full_name']; ?>
</div>

<div class="text-zinc-500 text-xs mt-1">
Student ID:
<?php echo $stu['student_id']; ?>
</div>

</div>

<!-- REMOVE BUTTON -->

<a
href="rooms.php?remove_student=<?php echo $stu['allocation_id']; ?>&room_id=<?php echo $room['room_id']; ?>"
onclick="return confirm('Remove this student from room?')"
class="bg-red-500/10 hover:bg-red-500/20 text-red-400 px-3 py-2 rounded-xl text-xs transition">

Remove

</a>

</div>

<?php } ?>

<?php } else { ?>

<div class="bg-zinc-950 border border-zinc-800 rounded-2xl p-4 text-zinc-500 text-sm text-center">

No students allocated

</div>

<?php } ?>

</div>

<!-- ACTIONS -->

<div class="flex justify-between items-center mt-7 text-sm">

<a
href="allocate_room.php?room_id=<?php echo $room['room_id']; ?>"
class="text-zinc-400 hover:text-white transition">

👥 Allocate

</a>

<a
href="rooms.php?delete=<?php echo $room['room_id']; ?>"
onclick="return confirm('Delete this room permanently?')"
class="text-red-400 hover:text-red-300 transition">

🗑 Remove Room

</a>

<a
href="room_details.php?id=<?php echo $room['room_id']; ?>"
class="text-emerald-400 hover:text-emerald-300 transition">

View Details

</a>

</div>

</div>

<?php } ?>

</div>

</div>

</div>

</body>
</html>