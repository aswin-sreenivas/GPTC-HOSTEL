<?php
session_start();

if(!isset($_SESSION['user_id']) || $_SESSION['role']!="admin"){
header("Location: ../index.php");
}

include("../config/db.php");

if(isset($_POST['allocate'])){

$student=$_POST['student'];
$room=$_POST['room'];

$date=date("Y-m-d");

mysqli_query($conn,"
INSERT INTO room_allocations
(student_id,room_id,allocation_date,status)
VALUES
('$student','$room','$date','active')
");

mysqli_query($conn,"
UPDATE rooms
SET current_occupancy = current_occupancy + 1
WHERE room_id='$room'
");

$msg="Room Allocated Successfully";

}

$students=mysqli_query($conn,"
SELECT * FROM students
WHERE student_id NOT IN
(SELECT student_id FROM room_allocations WHERE status='active')
");

$rooms=mysqli_query($conn,"
SELECT * FROM rooms
WHERE current_occupancy < capacity
");

?>

<!DOCTYPE html>
<html>

<head>

<title>Room Allocation</title>

<script src="https://cdn.tailwindcss.com"></script>

</head>

<body class="bg-black text-zinc-200">

<div class="flex">

<?php include("layout/sidebar.php"); ?>

<div class="flex-1 p-10">

<?php include("layout/header.php"); ?>

<h1 class="text-3xl font-bold mb-6">
Room Allocation
</h1>

<?php if(isset($msg)){ ?>

<div class="bg-emerald-600/20 text-emerald-400 p-3 rounded mb-6">
<?php echo $msg; ?>
</div>

<?php } ?>

<form method="POST" class="bg-zinc-900 border border-zinc-800 p-8 rounded-xl grid grid-cols-2 gap-6">

<div>

<label class="text-sm text-zinc-400">Select Student</label>

<select name="student" required class="w-full bg-zinc-800 p-3 rounded mt-1">

<option value="">Choose Student</option>

<?php while($row=mysqli_fetch_assoc($students)){ ?>

<option value="<?php echo $row['student_id']; ?>">
<?php echo $row['full_name']; ?>
</option>

<?php } ?>

</select>

</div>

<div>

<label class="text-sm text-zinc-400">Select Room</label>

<select name="room" required class="w-full bg-zinc-800 p-3 rounded mt-1">

<option value="">Choose Room</option>

<?php while($room=mysqli_fetch_assoc($rooms)){ ?>

<option value="<?php echo $room['room_id']; ?>">
Room <?php echo $room['room_number']; ?>
</option>

<?php } ?>

</select>

</div>

<div class="col-span-2">

<button name="allocate" class="bg-emerald-600 hover:bg-emerald-500 px-6 py-3 rounded-lg text-white">
Allocate Room
</button>

</div>

</form>

</div>

</div>

</body>
</html>