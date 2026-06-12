<?php
session_start();

if(!isset($_SESSION['user_id']) || $_SESSION['role']!="admin"){
header("Location: ../index.php");
exit();
}

include("../config/db.php");

$visitors = mysqli_query($conn,"
SELECT visitors.*, students.full_name
FROM visitors
LEFT JOIN students
ON visitors.student_id = students.student_id
ORDER BY check_in DESC
");

?>

<!DOCTYPE html>
<html>

<head>

<title>Visitor Log</title>

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
Visitor Log
</h1>

<p class="text-zinc-500">
Track hostel visitors and their visit records.
</p>

</div>

<!-- FIXED BUTTON -->

<a href="add_visitor.php"
class="bg-emerald-600 hover:bg-emerald-500 px-5 py-3 rounded-lg text-white">
+ Add Visitor
</a>

</div>

<div class="bg-zinc-900 border border-zinc-800 rounded-xl p-6">

<div class="flex justify-between mb-6">

<input
type="text"
placeholder="Search visitor..."
class="bg-zinc-800 px-4 py-3 rounded-lg w-96 text-white outline-none"
/>

<button class="bg-zinc-800 px-4 py-3 rounded-lg">
Filter
</button>

</div>

<table class="w-full text-left">

<thead class="text-zinc-500 text-sm border-b border-zinc-800">

<tr>

<th class="py-3">Visitor</th>
<th>Student</th>
<th>Relation</th>
<th>Check In</th>
<th>Check Out</th>
<th>Status</th>

</tr>

</thead>

<tbody>

<?php while($row=mysqli_fetch_assoc($visitors)){ ?>

<tr class="border-b border-zinc-800">

<td class="py-4">

<div class="text-white font-semibold">
<?php echo $row['visitor_name']; ?>
</div>

<div class="text-zinc-500 text-sm">
<?php echo $row['phone']; ?>
</div>

</td>

<td>
<?php echo $row['full_name']; ?>
</td>

<td>
<?php echo $row['relation']; ?>
</td>

<td>
<?php echo $row['check_in']; ?>
</td>

<td>
<?php echo $row['check_out'] ? $row['check_out'] : "-"; ?>
</td>

<td>

<?php if($row['check_out']==NULL){ ?>

<span class="bg-emerald-600/20 text-emerald-400 px-3 py-1 rounded-full text-xs">
Inside
</span>

<a href="checkout_visitor.php?id=<?php echo $row['visitor_id']; ?>"
class="ml-3 text-red-400 text-sm">
Check Out
</a>

<?php } else { ?>

<span class="bg-zinc-700 text-zinc-300 px-3 py-1 rounded-full text-xs">
Exited
</span>

<?php } ?>

</td>

</tr>

<?php } ?>

</tbody>

</table>

</div>

</div>

</div>

</body>
</html>