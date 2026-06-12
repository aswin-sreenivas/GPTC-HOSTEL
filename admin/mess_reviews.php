<?php
session_start();

if(!isset($_SESSION['user_id']) || $_SESSION['role']!="admin"){
header("Location: ../index.php");
exit();
}

include("../config/db.php");

/* FETCH REVIEWS */

$reviews=mysqli_query($conn,"
SELECT 

food_ratings.*,
students.full_name,
mess_menu.day_of_week

FROM food_ratings

LEFT JOIN students
ON food_ratings.student_id = students.student_id

LEFT JOIN mess_menu
ON food_ratings.menu_id = mess_menu.menu_id

ORDER BY food_ratings.created_at DESC
");

/* QUERY ERROR CHECK */

if(!$reviews){
die(mysqli_error($conn));
}

?>

<!DOCTYPE html>
<html>

<head>

<title>Mess Menu Reviews</title>

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

<?php include("layout/header.php"); ?>

<!-- HEADER -->

<div class="mb-8">

<h1 class="text-4xl font-bold text-white">
Mess Menu Reviews
</h1>

<p class="text-zinc-500 mt-2">
Student feedback and ratings for hostel food menu
</p>

</div>

<!-- REVIEW LIST -->

<div class="space-y-6">

<?php if(mysqli_num_rows($reviews)>0){ ?>

<?php while($row=mysqli_fetch_assoc($reviews)){ ?>

<div class="card rounded-2xl p-6">

<div class="flex justify-between items-start mb-4">

<div>

<h2 class="text-xl font-semibold text-white">

<?php 
echo $row['full_name'] 
? $row['full_name']
: 'Unknown Student';
?>

</h2>

<p class="text-zinc-500 text-sm mt-1">

Menu Day:
<span class="text-emerald-400">

<?php 
echo $row['day_of_week']
? $row['day_of_week']
: 'N/A';
?>

</span>

</p>

</div>

<div class="text-right">

<div class="bg-emerald-500/20 text-emerald-400 px-4 py-2 rounded-xl text-lg font-bold inline-block">

⭐ <?php echo $row['rating']; ?>/5

</div>

<p class="text-zinc-500 text-xs mt-2">

<?php echo date("d M Y h:i A", strtotime($row['created_at'])); ?>

</p>

</div>

</div>

<!-- COMMENT -->

<div class="bg-zinc-900 border border-zinc-800 rounded-xl p-4">

<p class="text-zinc-300 leading-relaxed">

<?php 
echo $row['comments'] 
? nl2br($row['comments'])
: 'No comments added.';
?>

</p>

</div>

</div>

<?php } ?>

<?php } else { ?>

<!-- NO REVIEWS -->

<div class="card rounded-2xl p-10 text-center">

<h2 class="text-2xl font-bold text-white mb-3">
No Reviews Found
</h2>

<p class="text-zinc-500">
No students have submitted mess menu reviews yet.
</p>

</div>

<?php } ?>

</div>

</div>

</div>

</body>

</html>