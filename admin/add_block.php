<?php
session_start();

if(!isset($_SESSION['user_id']) || $_SESSION['role']!="admin"){
header("Location: ../index.php");
}

include("../config/db.php");

if(isset($_POST['save'])){

$block=$_POST['block_name'];
$type=$_POST['type'];
$floors=$_POST['floors'];
$desc=$_POST['desc'];

mysqli_query($conn,"INSERT INTO hostel_blocks
(block_name,block_type,total_floors,description)
VALUES
('$block','$type','$floors','$desc')");

$msg="Block Added Successfully";

}
?>

<!DOCTYPE html>
<html>

<head>
<title>Add Hostel Block</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-black text-zinc-200">

<div class="flex">

<?php include("layout/sidebar.php"); ?>

<div class="flex-1 p-10">

<?php include("layout/header.php"); ?>

<h1 class="text-3xl font-bold mb-6">Add Hostel Block</h1>

<?php if(isset($msg)){ ?>

<div class="bg-emerald-600/20 text-emerald-400 p-3 rounded mb-6">
<?php echo $msg; ?>
</div>

<?php } ?>

<form method="POST" class="bg-zinc-900 border border-zinc-800 p-8 rounded-xl space-y-6">

<div>
<label class="text-sm text-zinc-400">Block Name</label>
<input type="text" name="block_name" required class="w-full bg-zinc-800 p-3 rounded mt-1">
</div>

<div>
<label class="text-sm text-zinc-400">Block Type</label>

<select name="type" class="w-full bg-zinc-800 p-3 rounded mt-1">
<option value="boys">Boys</option>
<option value="girls">Girls</option>
<option value="mixed">Mixed</option>
</select>

</div>

<div>
<label class="text-sm text-zinc-400">Total Floors</label>
<input type="number" name="floors" class="w-full bg-zinc-800 p-3 rounded mt-1">
</div>

<div>
<label class="text-sm text-zinc-400">Description</label>
<textarea name="desc" class="w-full bg-zinc-800 p-3 rounded mt-1"></textarea>
</div>

<button name="save" class="bg-emerald-600 hover:bg-emerald-500 px-6 py-3 rounded-lg text-white">
Add Block
</button>

</form>

</div>
</div>
</body>
</html>