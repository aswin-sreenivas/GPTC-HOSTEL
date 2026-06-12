<?php
session_start();

if(!isset($_SESSION['user_id']) || $_SESSION['role']!="admin"){
header("Location: ../index.php");
}

include("../config/db.php");

$blocks=mysqli_query($conn,"SELECT * FROM hostel_blocks");

if(isset($_POST['save'])){

$block=$_POST['block'];
$room=$_POST['room'];
$floor=$_POST['floor'];
$type=$_POST['type'];
$capacity=$_POST['capacity'];

mysqli_query($conn,"INSERT INTO rooms
(block_id,room_number,floor,capacity,room_type,current_occupancy)
VALUES
('$block','$room','$floor','$capacity','$type','0')");

$msg="Room Added Successfully";

}
?>

<!DOCTYPE html>
<html>

<head>
<title>Add Room</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-black text-zinc-200">

<div class="flex">

<?php include("layout/sidebar.php"); ?>

<div class="flex-1 p-10">

<?php include("layout/header.php"); ?>

<h1 class="text-3xl font-bold mb-6">Add Room</h1>

<?php if(isset($msg)){ ?>

<div class="bg-emerald-600/20 text-emerald-400 p-3 rounded mb-6">
<?php echo $msg; ?>
</div>

<?php } ?>

<form method="POST" class="bg-zinc-900 border border-zinc-800 p-8 rounded-xl grid grid-cols-2 gap-6">

<div>

<label class="text-sm text-zinc-400">Hostel Block</label>

<select name="block" class="w-full bg-zinc-800 p-3 rounded mt-1">

<?php while($b=mysqli_fetch_assoc($blocks)){ ?>

<option value="<?php echo $b['block_id']; ?>">
<?php echo $b['block_name']; ?>
</option>

<?php } ?>

</select>

</div>

<div>

<label class="text-sm text-zinc-400">Room Number</label>

<input type="text" name="room" class="w-full bg-zinc-800 p-3 rounded mt-1">

</div>

<div>

<label class="text-sm text-zinc-400">Floor</label>

<input type="number" name="floor" class="w-full bg-zinc-800 p-3 rounded mt-1">

</div>

<div>

<label class="text-sm text-zinc-400">Room Type</label>

<select name="type" class="w-full bg-zinc-800 p-3 rounded mt-1">

<option value="single">Single</option>
<option value="double">Double</option>
<option value="triple">Triple</option>
<option value="dormitory">Dormitory</option>

</select>

</div>

<div>

<label class="text-sm text-zinc-400">Capacity</label>

<input type="number" name="capacity" class="w-full bg-zinc-800 p-3 rounded mt-1">

</div>

<div class="col-span-2">

<button name="save" class="bg-emerald-600 hover:bg-emerald-500 px-6 py-3 rounded-lg text-white">

Add Room

</button>

</div>

</form>

</div>
</div>
</body>
</html>