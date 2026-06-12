<?php
session_start();

if(!isset($_SESSION['user_id']) || $_SESSION['role']!="admin"){
header("Location: ../index.php");
exit();
}

include("../config/db.php");

/* ================= UPDATE STATUS ================= */

if(isset($_GET['approve'])){

$id=$_GET['approve'];

mysqli_query($conn,"
UPDATE leave_requests
SET status='Approved'
WHERE leave_id='$id'
");

header("Location: leave_management.php");
exit();

}

if(isset($_GET['reject'])){

$id=$_GET['reject'];

mysqli_query($conn,"
UPDATE leave_requests
SET status='Rejected'
WHERE leave_id='$id'
");

header("Location: leave_management.php");
exit();

}

/* ================= GET LEAVES ================= */

$leaves=mysqli_query($conn,"
SELECT 

leave_requests.*,
students.full_name,
students.admission_no

FROM leave_requests

LEFT JOIN students
ON leave_requests.student_id = students.student_id

ORDER BY leave_requests.created_at DESC
");

?>

<!DOCTYPE html>
<html>

<head>

<title>Leave Management</title>

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

tr:hover{
background:#18181b;
}

</style>

</head>

<body class="bg-black text-zinc-200">

<div class="flex min-h-screen">

<?php include("layout/sidebar.php"); ?>

<div class="flex-1 p-10">

<?php include("layout/header.php"); ?>

<!-- HEADER -->

<div class="flex justify-between items-center mb-8">

<div>

<h1 class="text-4xl font-black text-white">
Leave Management
</h1>

<p class="text-zinc-500 mt-2">
Manage student leave and sick leave requests.
</p>

</div>

</div>

<!-- TABLE -->

<div class="card rounded-[28px] overflow-hidden">

<div class="overflow-x-auto">

<table class="w-full text-left">

<thead class="bg-zinc-950 border-b border-zinc-800 text-zinc-400 text-sm">

<tr>

<th class="p-5">Student</th>
<th>Leave Type</th>
<th>From</th>
<th>To</th>
<th>Reason</th>
<th>Proof</th>
<th>Status</th>
<th>Action</th>

</tr>

</thead>

<tbody>

<?php if(mysqli_num_rows($leaves)>0){ ?>

<?php while($row=mysqli_fetch_assoc($leaves)){ ?>

<tr class="border-b border-zinc-800 transition">

<!-- STUDENT -->

<td class="p-5">

<div>

<div class="text-white font-semibold">

<?php echo $row['full_name']; ?>

</div>

<div class="text-zinc-500 text-sm mt-1">

<?php echo $row['admission_no']; ?>

</div>

</div>

</td>

<!-- TYPE -->

<td>

<?php if($row['leave_type']=="Sick Leave"){ ?>

<span class="bg-red-500/10 text-red-400 border border-red-500/20 px-3 py-1 rounded-full text-xs">

🤒 Sick Leave

</span>

<?php } else { ?>

<span class="bg-blue-500/10 text-blue-400 border border-blue-500/20 px-3 py-1 rounded-full text-xs">

📄 Normal Leave

</span>

<?php } ?>

</td>

<!-- FROM -->

<td>

<div class="text-white">

<?php echo $row['leave_from']; ?>

</div>

</td>

<!-- TO -->

<td>

<div class="text-white">

<?php echo $row['leave_to']; ?>

</div>

</td>

<!-- REASON -->

<td>

<div class="text-zinc-300 max-w-xs">

<?php echo $row['reason']; ?>

</div>

</td>

<!-- PROOF -->

<td>

<?php if(isset($row['medical_proof']) && $row['medical_proof']!=""){ ?>

<a
href="../uploads/medical/<?php echo $row['medical_proof']; ?>"
target="_blank"
class="text-emerald-400 hover:text-emerald-300 text-sm">

View Proof

</a>

<?php } else { ?>

<span class="text-zinc-500 text-sm">
No File
</span>

<?php } ?>

</td>

<!-- STATUS -->

<td>

<?php if($row['status']=="Pending"){ ?>

<span class="bg-yellow-500/10 text-yellow-400 border border-yellow-500/20 px-3 py-1 rounded-full text-xs">

Pending

</span>

<?php } ?>

<?php if($row['status']=="Approved"){ ?>

<span class="bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 px-3 py-1 rounded-full text-xs">

Approved

</span>

<?php } ?>

<?php if($row['status']=="Rejected"){ ?>

<span class="bg-red-500/10 text-red-400 border border-red-500/20 px-3 py-1 rounded-full text-xs">

Rejected

</span>

<?php } ?>

</td>

<!-- ACTION -->

<td>

<?php if($row['status']=="Pending"){ ?>

<div class="flex gap-3">

<a
href="?approve=<?php echo $row['leave_id']; ?>"
class="text-emerald-400 hover:text-emerald-300 text-sm">

Approve

</a>

<a
href="?reject=<?php echo $row['leave_id']; ?>"
class="text-red-400 hover:text-red-300 text-sm">

Reject

</a>

</div>

<?php } else { ?>

<span class="text-zinc-500 text-sm">

No Action

</span>

<?php } ?>

</td>

</tr>

<?php } ?>

<?php } else { ?>

<tr>

<td colspan="8" class="p-16 text-center">

<div class="text-7xl mb-5">
📭
</div>

<h2 class="text-3xl font-bold text-white">
No Leave Requests
</h2>

<p class="text-zinc-500 mt-3">

No leave applications found.

</p>

</td>

</tr>

<?php } ?>

</tbody>

</table>

</div>

</div>

</div>

</div>

</body>
</html>