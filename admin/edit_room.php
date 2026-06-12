<?php
session_start();

if(!isset($_SESSION['user_id']) || $_SESSION['role']!="admin"){
header("Location: ../index.php");
exit();
}

include("../config/db.php");

$id=$_GET['id'];

$success="";

/* ================= UPDATE ROOM ================= */

if(isset($_POST['update_room'])){

$room_number=$_POST['room_number'];
$capacity=$_POST['capacity'];
$block_id=$_POST['block_id'];

mysqli_query($conn,"
UPDATE rooms
SET
room_number='$room_number',
capacity='$capacity',
block_id='$block_id'
WHERE room_id='$id'
");

$success="Room updated successfully.";

}

/* ================= ROOM ================= */

$room=mysqli_fetch_assoc(mysqli_query($conn,"
SELECT *
FROM rooms
WHERE room_id='$id'
"));

/* ================= BLOCKS ================= */

$blocks=mysqli_query($conn,"
SELECT *
FROM hostel_blocks
ORDER BY block_name ASC
");

?>

<!DOCTYPE html>
<html>

<head>

<title>Edit Room</title>

<script src="https://cdn.tailwindcss.com"></script>

<style>

body{
background:#020617;
font-family:system-ui;
}

.card{
background:rgba(24,24,27,.92);
border:1px solid #27272a;
backdrop-filter:blur(12px);
}

.input{
width:100%;
background:#27272a;
border:1px solid #3f3f46;
padding:14px;
border-radius:16px;
outline:none;
color:white;
transition:.3s;
}

.input:focus{
border-color:#10b981;
box-shadow:0 0 0 4px rgba(16,185,129,.1);
}

</style>

</head>

<body class="text-zinc-200">

<div class="flex min-h-screen">

<?php include("layout/sidebar.php"); ?>

<div class="flex-1 p-10">

<?php include("layout/header.php"); ?>

<!-- HEADER -->

<div class="mb-10">

<h1 class="text-4xl font-black text-white">
Edit Room
</h1>

<p class="text-zinc-500 mt-2 text-lg">
Update hostel room information.
</p>

</div>

<!-- SUCCESS -->

<?php if($success!=""){ ?>

<div class="bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 p-4 rounded-2xl mb-6">

<?php echo $success; ?>

</div>

<?php } ?>

<!-- FORM -->

<div class="card rounded-[32px] p-8 max-w-3xl">

<form method="POST">

<div class="grid grid-cols-2 gap-6">

<!-- ROOM NUMBER -->

<div>

<label class="block text-sm text-zinc-400 mb-2">
Room Number
</label>

<input
type="text"
name="room_number"
value="<?php echo $room['room_number']; ?>"
required
class="input"
>

</div>

<!-- CAPACITY -->

<div>

<label class="block text-sm text-zinc-400 mb-2">
Capacity
</label>

<input
type="number"
name="capacity"
value="<?php echo $room['capacity']; ?>"
required
class="input"
>

</div>

<!-- BLOCK -->

<div class="col-span-2">

<label class="block text-sm text-zinc-400 mb-2">
Hostel Block
</label>

<select
name="block_id"
required
class="input"
>

<?php while($block=mysqli_fetch_assoc($blocks)){ ?>

<option
value="<?php echo $block['block_id']; ?>"

<?php
if($room['block_id']==$block['block_id']){
echo "selected";
}
?>

>

<?php echo $block['block_name']; ?>

</option>

<?php } ?>

</select>

</div>

</div>

<!-- BTN -->

<div class="flex gap-4 mt-8">

<button
type="submit"
name="update_room"
class="bg-emerald-600 hover:bg-emerald-500 transition px-8 py-3 rounded-2xl font-semibold">

Update Room

</button>

<a
href="rooms.php"
class="bg-zinc-800 hover:bg-zinc-700 transition px-8 py-3 rounded-2xl font-semibold">

Back

</a>

</div>

</form>

</div>

</div>

</div>

</body>
</html>