<?php
session_start();

if(!isset($_SESSION['user_id']) || $_SESSION['role']!="admin"){
header("Location: ../index.php");
exit();
}

include("../config/db.php");

$menus = mysqli_query($conn,"SELECT * FROM mess_menu");

?>

<!DOCTYPE html>
<html>

<head>

<title>Mess Menu</title>

<script src="https://cdn.tailwindcss.com"></script>

</head>

<body class="bg-black text-zinc-200">

<div class="flex">

<?php include("layout/sidebar.php"); ?>

<div class="flex-1 p-10">

<?php include("layout/header.php"); ?>

<div class="flex justify-between items-center mb-8">

<div>

<h1 class="text-3xl font-bold text-white">
Mess Menu
</h1>

<p class="text-zinc-500">
Manage weekly hostel meal schedules.
</p>

</div>

<a href="add_menu.php"
class="bg-emerald-600 hover:bg-emerald-500 px-5 py-3 rounded-lg text-white">
+ Add Menu
</a>

</div>

<div class="grid grid-cols-3 gap-6">

<?php if(mysqli_num_rows($menus)>0){ ?>

<?php while($row=mysqli_fetch_assoc($menus)){ ?>

<div class="bg-zinc-900 border border-zinc-800 rounded-xl p-6">

<h2 class="text-xl font-semibold text-white mb-4">
<?php echo $row['day_of_week']; ?>
</h2>

<div class="space-y-3 text-sm">

<div>
<span class="text-zinc-400">Breakfast:</span>
<div class="text-white">
<?php echo $row['breakfast']; ?>
</div>
</div>

<div>
<span class="text-zinc-400">Lunch:</span>
<div class="text-white">
<?php echo $row['lunch']; ?>
</div>
</div>

<div>
<span class="text-zinc-400">Dinner:</span>
<div class="text-white">
<?php echo $row['dinner']; ?>
</div>
</div>

<div>
<span class="text-zinc-400">Snacks:</span>
<div class="text-white">
<?php echo $row['snacks']; ?>
</div>
</div>

</div>

<div class="mt-6 flex gap-3">



<a href="delete_menu.php?id=<?php echo $row['menu_id']; ?>"
class="bg-red-600 hover:bg-red-500 px-4 py-2 rounded-lg text-sm text-white">
Delete
</a>

</div>

</div>

<?php } ?>

<?php } else { ?>

<div class="col-span-3 bg-zinc-900 border border-zinc-800 rounded-xl p-10 text-center">

<h2 class="text-2xl font-bold text-white mb-2">
No Menu Added
</h2>

<p class="text-zinc-500 mb-5">
There are no mess menu records available.
</p>

<a href="add_menu.php"
class="bg-emerald-600 hover:bg-emerald-500 px-5 py-3 rounded-lg text-white inline-block">

+ Add First Menu

</a>

</div>

<?php } ?>

</div>

</div>

</div>

</body>
</html>