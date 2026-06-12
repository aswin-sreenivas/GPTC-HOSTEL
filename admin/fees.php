<?php
session_start();

if(!isset($_SESSION['user_id']) || $_SESSION['role']!="student"){
header("Location: ../index.php");
exit();
}

include("../config/db.php");

$user_id=$_SESSION['user_id'];

/* get student safely */

$result=mysqli_query($conn,"
SELECT student_id FROM students
WHERE user_id='$user_id'
");

if(!$result){
die("SQL Error: ".mysqli_error($conn));
}

$student=mysqli_fetch_assoc($result);

if(!$student){
die("Student not linked to this account. Contact admin.");
}

$student_id=$student['student_id'];

/* get fees */

$fees=mysqli_query($conn,"
SELECT * FROM fees
WHERE student_id='$student_id'
");

?>
<!DOCTYPE html>
<html>
<head>

<title>Fee Management</title>

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
Fee Management
</h1>

<p class="text-zinc-500">
Track payments and pending dues across the hostel.
</p>

</div>

<button class="bg-emerald-600 hover:bg-emerald-500 text-white px-6 py-3 rounded-lg">
Generate Report
</button>

</div>

<!-- STAT CARDS -->

<div class="grid grid-cols-3 gap-6 mb-8">

<div class="bg-zinc-900 border border-zinc-800 p-6 rounded-xl">

<p class="text-zinc-500 text-sm">Total Collected</p>

<h2 class="text-3xl font-bold text-white mt-2">
$<?php echo $total ? $total : 0; ?>
</h2>

<p class="text-emerald-400 text-sm mt-2">
✔ All clear
</p>

</div>

<div class="bg-zinc-900 border border-zinc-800 p-6 rounded-xl">

<p class="text-zinc-500 text-sm">Pending Dues</p>

<h2 class="text-3xl font-bold text-white mt-2">
$<?php echo $pending ? $pending : 0; ?>
</h2>

<p class="text-yellow-400 text-sm mt-2">
0 students pending
</p>

</div>

<div class="bg-zinc-900 border border-zinc-800 p-6 rounded-xl">

<p class="text-zinc-500 text-sm">Last Payment</p>

<h2 class="text-3xl font-bold text-white mt-2">
-
</h2>

<p class="text-zinc-500 text-sm mt-2">
Updated today
</p>

</div>

</div>

<!-- TABLE -->

<div class="bg-zinc-900 border border-zinc-800 rounded-xl p-6">

<div class="flex justify-between mb-6">

<input
type="text"
placeholder="Search by student name or receipt..."
class="bg-zinc-800 px-4 py-3 rounded-lg w-96 text-white outline-none"
/>

<button class="bg-zinc-800 px-4 py-3 rounded-lg">
Filter
</button>

</div>

<table class="w-full text-left">

<thead class="text-zinc-500 text-sm border-b border-zinc-800">

<tr>

<th class="py-3">Student</th>

<th>Month/Year</th>

<th>Amount</th>

<th>Status</th>

<th>Date</th>

<th>Action</th>

</tr>

</thead>

<tbody>

<?php while($row=mysqli_fetch_assoc($payments)){ ?>

<tr class="border-b border-zinc-800">

<td class="py-4">
<?php echo $row['full_name']; ?>
</td>

<td>Mar 2026</td>

<td>$<?php echo $row['amount']; ?></td>

<td>

<?php if($row['status']=="paid"){ ?>

<span class="bg-emerald-600/20 text-emerald-400 px-3 py-1 rounded-full text-xs">
PAID
</span>

<?php } else { ?>

<span class="bg-yellow-600/20 text-yellow-400 px-3 py-1 rounded-full text-xs">
PENDING
</span>

<?php } ?>

</td>

<td>
<?php echo date("d M Y",strtotime($row['created_at'])); ?>
</td>

<td>
<button class="text-zinc-400 hover:text-white">
View
</button>
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