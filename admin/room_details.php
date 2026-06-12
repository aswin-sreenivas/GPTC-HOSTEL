<?php
session_start();

if(!isset($_SESSION['user_id']) || $_SESSION['role']!="admin"){
header("Location: ../index.php");
exit();
}

include("../config/db.php");

$room_id = $_GET['id'];

/* get room details */

$room = mysqli_fetch_assoc(mysqli_query($conn,"
SELECT rooms.*, hostel_blocks.block_name
FROM rooms
LEFT JOIN hostel_blocks
ON rooms.block_id = hostel_blocks.block_id
WHERE rooms.room_id='$room_id'
"));

if(!$room){
die("Room not found");
}

/* get students in room */

$students = mysqli_query($conn,"
SELECT students.*
FROM room_allocations
LEFT JOIN students
ON room_allocations.student_id = students.student_id
WHERE room_allocations.room_id='$room_id'
AND room_allocations.status='active'
");

?>

<!DOCTYPE html>
<html>

<head>
<title>Room Details</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-black text-zinc-200">

<div class="flex">

<?php include("layout/sidebar.php"); ?>

<div class="flex-1 p-10">

<?php include("layout/header.php"); ?>

<h1 class="text-3xl font-bold mb-6">
Room <?php echo $room['room_number']; ?>
</h1>

<div class="bg-zinc-900 border border-zinc-800 rounded-xl p-6 mb-6">

<div class="text-zinc-400 mb-2">
Block: <?php echo $room['block_name']; ?>
</div>

<div class="text-zinc-400">
Capacity: <?php echo $room['current_occupancy']; ?>/<?php echo $room['capacity']; ?>
</div>

</div>

<h2 class="text-xl font-semibold mb-4">Students in Room</h2>

<div class="bg-zinc-900 border border-zinc-800 rounded-xl p-6">

<?php if(mysqli_num_rows($students)>0){ ?>

<ul class="space-y-3">

<?php while($s=mysqli_fetch_assoc($students)){ ?>

<li class="border-b border-zinc-800 pb-2">
<?php echo $s['full_name']; ?>
</li>

<?php } ?>

</ul>

<?php } else { ?>

<div class="text-zinc-500">
No students assigned
</div>

<?php } ?>

</div>

</div>

</div>

</body>
</html>