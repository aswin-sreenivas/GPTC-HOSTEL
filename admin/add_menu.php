<?php
session_start();

if(!isset($_SESSION['user_id']) || $_SESSION['role']!="admin"){
header("Location: ../index.php");
exit();
}

include("../config/db.php");

if(isset($_POST['save'])){

$day=mysqli_real_escape_string($conn,$_POST['day']);
$breakfast=mysqli_real_escape_string($conn,$_POST['breakfast']);
$lunch=mysqli_real_escape_string($conn,$_POST['lunch']);
$dinner=mysqli_real_escape_string($conn,$_POST['dinner']);
$snacks=mysqli_real_escape_string($conn,$_POST['snacks']);

mysqli_query($conn,"
INSERT INTO mess_menu
(day_of_week,breakfast,lunch,dinner,snacks)

VALUES

('$day','$breakfast','$lunch','$dinner','$snacks')
");

$msg="Menu Added Successfully";

}
?>

<!DOCTYPE html>
<html>

<head>

<title>Add Menu</title>

<script src="https://cdn.tailwindcss.com"></script>

</head>

<body class="bg-black text-zinc-200">

<div class="flex">

<?php include("layout/sidebar.php"); ?>

<div class="flex-1 p-10">

<?php include("layout/header.php"); ?>

<h1 class="text-3xl font-bold mb-6">
Add Mess Menu
</h1>

<!-- SUCCESS MESSAGE -->

<?php if(isset($msg)){ ?>

<div id="toast"
class="bg-emerald-600/20 border border-emerald-500/30 text-emerald-400 p-4 rounded mb-6 flex justify-between items-center">

<div>
<?php echo $msg; ?>
</div>

<button
type="button"
onclick="document.getElementById('toast').style.display='none'"
class="text-emerald-300 hover:text-white text-2xl leading-none">

&times;

</button>

</div>

<?php } ?>

<!-- FORM -->

<form method="POST"
class="bg-zinc-900 border border-zinc-800 p-8 rounded-xl grid grid-cols-2 gap-6">

<!-- DAY -->

<div>

<label class="text-sm text-zinc-400">
Day
</label>

<select
name="day"
required
class="w-full bg-zinc-800 border border-zinc-700 p-3 rounded mt-1 text-white">

<option value="">
Select Day
</option>

<option value="Monday">
Monday
</option>

<option value="Tuesday">
Tuesday
</option>

<option value="Wednesday">
Wednesday
</option>

<option value="Thursday">
Thursday
</option>

<option value="Friday">
Friday
</option>

<option value="Saturday">
Saturday
</option>

<option value="Sunday">
Sunday
</option>

</select>

</div>

<!-- BREAKFAST -->

<div>

<label class="text-sm text-zinc-400">
Breakfast
</label>

<input
type="text"
name="breakfast"
required
placeholder="Enter Breakfast Menu"
class="w-full bg-zinc-800 border border-zinc-700 p-3 rounded mt-1 text-white">

</div>

<!-- LUNCH -->

<div>

<label class="text-sm text-zinc-400">
Lunch
</label>

<input
type="text"
name="lunch"
required
placeholder="Enter Lunch Menu"
class="w-full bg-zinc-800 border border-zinc-700 p-3 rounded mt-1 text-white">

</div>

<!-- DINNER -->

<div>

<label class="text-sm text-zinc-400">
Dinner
</label>

<input
type="text"
name="dinner"
required
placeholder="Enter Dinner Menu"
class="w-full bg-zinc-800 border border-zinc-700 p-3 rounded mt-1 text-white">

</div>

<!-- SNACKS -->

<div class="col-span-2">

<label class="text-sm text-zinc-400">
Snacks
</label>

<input
type="text"
name="snacks"
required
placeholder="Enter Snacks Menu"
class="w-full bg-zinc-800 border border-zinc-700 p-3 rounded mt-1 text-white">

</div>

<!-- BUTTON -->

<div class="col-span-2">

<button
name="save"
class="bg-emerald-600 hover:bg-emerald-500 transition px-6 py-3 rounded-lg text-white">

Add Menu

</button>

</div>

</form>

</div>

</div>

</body>

</html>