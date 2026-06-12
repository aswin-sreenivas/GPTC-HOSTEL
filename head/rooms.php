<?php
session_start();

if(!isset($_SESSION['user_id']) || $_SESSION['role']!="head"){
header("Location: ../index.php");
exit();
}

include("../config/db.php");

/* rooms */

$rooms=mysqli_query($conn,"
SELECT rooms.*, hostel_blocks.block_name

FROM rooms

LEFT JOIN hostel_blocks
ON rooms.block_id = hostel_blocks.block_id

ORDER BY rooms.room_number ASC
");

/* stats */

$total_rooms=mysqli_num_rows(mysqli_query($conn,"
SELECT * FROM rooms
"));

$occupied=mysqli_num_rows(mysqli_query($conn,"
SELECT * FROM rooms
WHERE current_occupancy > 0
"));

$available=mysqli_num_rows(mysqli_query($conn,"
SELECT * FROM rooms
WHERE current_occupancy < capacity
"));

?>

<!DOCTYPE html>
<html>

<head>

<title>Room Monitoring</title>

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
Room Monitoring
</h1>

<p class="text-zinc-500 mt-2">
Monitor hostel rooms and occupancy
</p>

</div>

<div class="text-sm text-zinc-500">
Read Only Access
</div>

</div>

<!-- STATS -->

<div class="grid grid-cols-3 gap-6 mb-8">

<div class="card rounded-2xl p-6">

<div class="text-zinc-400 text-sm">
Total Rooms
</div>

<div class="text-4xl font-bold text-white mt-3">
<?php echo $total_rooms; ?>
</div>

</div>

<div class="card rounded-2xl p-6">

<div class="text-zinc-400 text-sm">
Occupied Rooms
</div>

<div class="text-4xl font-bold text-emerald-400 mt-3">
<?php echo $occupied; ?>
</div>

</div>

<div class="card rounded-2xl p-6">

<div class="text-zinc-400 text-sm">
Available Rooms
</div>

<div class="text-4xl font-bold text-blue-400 mt-3">
<?php echo $available; ?>
</div>

</div>

</div>

<!-- ROOM GRID -->

<div class="grid grid-cols-3 gap-6">

<?php while($room=mysqli_fetch_assoc($rooms)){ 

$capacity=$room['capacity'];
$current=$room['current_occupancy'];

$percentage=0;

if($capacity>0){
$percentage=($current/$capacity)*100;
}

?>

<a
href="room_details.php?id=<?php echo $room['room_id']; ?>"
class="card rounded-2xl p-6 hover:border-emerald-500 hover:scale-[1.02] transition block">

<div class="flex justify-between items-center mb-4">

<div class="text-2xl">
🛏
</div>

<?php if($current >= $capacity){ ?>

<span class="bg-red-500/20 text-red-400 px-3 py-1 rounded-full text-xs">
Full
</span>

<?php } else { ?>

<span class="bg-emerald-500/20 text-emerald-400 px-3 py-1 rounded-full text-xs">
Available
</span>

<?php } ?>

</div>

<h2 class="text-2xl font-bold text-white">

Room <?php echo $room['room_number']; ?>

</h2>

<p class="text-zinc-500 text-sm mt-1 mb-5">

<?php echo $room['block_name']; ?>

</p>

<div class="text-sm text-zinc-400 mb-2">

Occupancy:
<?php echo $current; ?>/<?php echo $capacity; ?>

</div>

<div class="w-full bg-zinc-800 rounded-full h-3 mb-5">

<div
class="bg-emerald-500 h-3 rounded-full"
style="width: <?php echo $percentage; ?>%">
</div>

</div>

<div class="flex justify-between text-sm">

<div class="text-zinc-500">
Capacity
</div>

<div class="font-semibold text-white">
<?php echo $capacity; ?>
</div>

</div>

<div class="mt-5 text-center">

<div class="bg-zinc-800 hover:bg-zinc-700 py-2 rounded-lg text-sm transition">
View Room Details
</div>

</div>

</a>

<?php } ?>

</div>

</div>

</div>

</body>
</html>