<?php
session_start();

if(!isset($_SESSION['user_id']) || $_SESSION['role']!="admin"){
header("Location: ../index.php");
exit();
}

include("../config/db.php");

$success="";

/* ================= UPDATE STATUS ================= */

if(isset($_POST['update_status'])){

$complaint_id=$_POST['complaint_id'];
$status=$_POST['status'];

mysqli_query($conn,"
UPDATE complaints
SET status='$status'
WHERE complaint_id='$complaint_id'
");

$success="Complaint status updated successfully.";

}

/* ================= FILTER ================= */

$filter = isset($_GET['status']) ? $_GET['status'] : '';

$where="";

if($filter!=""){
$where="WHERE complaints.status='$filter'";
}

/* ================= COMPLAINTS ================= */

$complaints = mysqli_query($conn,"
SELECT 

complaints.*,
students.full_name,
students.phone,
rooms.room_number,
hostel_blocks.block_name

FROM complaints

LEFT JOIN students
ON complaints.student_id = students.student_id

LEFT JOIN room_allocations
ON students.student_id = room_allocations.student_id
AND room_allocations.status='active'

LEFT JOIN rooms
ON room_allocations.room_id = rooms.room_id

LEFT JOIN hostel_blocks
ON rooms.block_id = hostel_blocks.block_id

$where

ORDER BY complaints.submitted_date DESC
");

/* ================= COUNTS ================= */

$pending=mysqli_fetch_assoc(mysqli_query($conn,"
SELECT COUNT(*) as total
FROM complaints
WHERE status='pending'
"))['total'];

$progress=mysqli_fetch_assoc(mysqli_query($conn,"
SELECT COUNT(*) as total
FROM complaints
WHERE status='in_progress'
"))['total'];

$resolved=mysqli_fetch_assoc(mysqli_query($conn,"
SELECT COUNT(*) as total
FROM complaints
WHERE status='resolved'
"))['total'];

?>

<!DOCTYPE html>
<html>

<head>

<title>Complaints Management</title>

<script src="https://cdn.tailwindcss.com"></script>

<style>

body{
background:#020617;
font-family:system-ui;
}

.card{
background:#18181b;
border:1px solid #27272a;
transition:.3s;
}

.card:hover{
border-color:#10b98130;
transform:translateY(-2px);
}

.select{
background:#27272a;
border:1px solid #3f3f46;
padding:10px 14px;
border-radius:14px;
outline:none;
color:white;
}

.btn{
transition:.3s;
}

.btn:hover{
transform:translateY(-1px);
}

</style>

</head>

<body class="bg-black text-zinc-200">

<div class="flex min-h-screen">

<?php include("layout/sidebar.php"); ?>

<div class="flex-1 p-10">

<?php include("layout/header.php"); ?>

<!-- HEADER -->

<div class="flex justify-between items-center mb-10">

<div>

<h1 class="text-4xl font-black text-white">
Complaints Management
</h1>

<p class="text-zinc-500 mt-2">
Manage and resolve student complaints efficiently.
</p>

</div>

<form method="GET">

<select
name="status"
onchange="this.form.submit()"
class="select">

<option value="">
All Complaints
</option>

<option value="pending"
<?php if($filter=="pending"){ echo "selected"; } ?>>
Pending
</option>

<option value="in_progress"
<?php if($filter=="in_progress"){ echo "selected"; } ?>>
In Progress
</option>

<option value="resolved"
<?php if($filter=="resolved"){ echo "selected"; } ?>>
Resolved
</option>

</select>

</form>

</div>

<!-- SUCCESS -->

<?php if($success!=""){ ?>

<div class="bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 p-4 rounded-2xl mb-6">

<?php echo $success; ?>

</div>

<?php } ?>

<!-- STATS -->

<div class="grid grid-cols-3 gap-6 mb-8">

<!-- PENDING -->

<div class="card rounded-3xl p-6">

<div class="text-zinc-400 text-sm">
PENDING
</div>

<h2 class="text-5xl font-black text-yellow-400 mt-4">
<?php echo $pending; ?>
</h2>

</div>

<!-- PROGRESS -->

<div class="card rounded-3xl p-6">

<div class="text-zinc-400 text-sm">
IN PROGRESS
</div>

<h2 class="text-5xl font-black text-blue-400 mt-4">
<?php echo $progress; ?>
</h2>

</div>

<!-- RESOLVED -->

<div class="card rounded-3xl p-6">

<div class="text-zinc-400 text-sm">
RESOLVED
</div>

<h2 class="text-5xl font-black text-emerald-400 mt-4">
<?php echo $resolved; ?>
</h2>

</div>

</div>

<!-- COMPLAINTS -->

<div class="space-y-6">

<?php if(mysqli_num_rows($complaints)>0){ ?>

<?php while($row=mysqli_fetch_assoc($complaints)){ ?>

<div class="card rounded-[28px] p-7">

<div class="flex justify-between items-start gap-8">

<!-- LEFT -->

<div class="flex gap-5 flex-1">

<div class="w-14 h-14 rounded-2xl bg-zinc-800 flex items-center justify-center text-2xl">

⚠

</div>

<div class="flex-1">

<!-- TOP -->

<div class="flex items-center flex-wrap gap-3 mb-4">

<span class="bg-zinc-800 text-zinc-300 text-xs px-3 py-1 rounded-full">

<?php echo strtoupper($row['category']); ?>

</span>

<?php

if($row['status']=="pending"){

echo "
<span class='bg-yellow-500/10 text-yellow-400 border border-yellow-500/20 text-xs px-3 py-1 rounded-full'>
PENDING
</span>
";

}

elseif($row['status']=="in_progress"){

echo "
<span class='bg-blue-500/10 text-blue-400 border border-blue-500/20 text-xs px-3 py-1 rounded-full'>
IN PROGRESS
</span>
";

}

else{

echo "
<span class='bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 text-xs px-3 py-1 rounded-full'>
RESOLVED
</span>
";

}

?>

</div>

<!-- TITLE -->

<h2 class="text-2xl font-bold text-white mb-2">

<?php echo $row['title']; ?>

</h2>

<!-- DESCRIPTION -->

<p class="text-zinc-400 leading-relaxed mb-5">

<?php echo $row['description']; ?>

</p>

<!-- INFO -->

<div class="grid grid-cols-2 gap-4 text-sm">

<div class="bg-zinc-950 border border-zinc-800 rounded-2xl p-4">

<div class="text-zinc-500">
Student
</div>

<div class="text-white font-semibold mt-1">
<?php echo $row['full_name']; ?>
</div>

</div>

<div class="bg-zinc-950 border border-zinc-800 rounded-2xl p-4">

<div class="text-zinc-500">
Phone
</div>

<div class="text-white font-semibold mt-1">
<?php echo $row['phone']; ?>
</div>

</div>

<div class="bg-zinc-950 border border-zinc-800 rounded-2xl p-4">

<div class="text-zinc-500">
Room
</div>

<div class="text-white font-semibold mt-1">

<?php

echo $row['room_number']
? "Room ".$row['room_number']
: "Not Assigned";

?>

</div>

</div>

<div class="bg-zinc-950 border border-zinc-800 rounded-2xl p-4">

<div class="text-zinc-500">
Block
</div>

<div class="text-white font-semibold mt-1">

<?php

echo $row['block_name']
? $row['block_name']
: "N/A";

?>

</div>

</div>

</div>

<!-- DATE -->

<div class="text-zinc-500 text-sm mt-5">

Submitted:
<?php echo date("d M Y • h:i A",strtotime($row['submitted_date'])); ?>

</div>

</div>

</div>

<!-- RIGHT -->

<div class="w-72">

<form method="POST" class="space-y-4">

<input
type="hidden"
name="complaint_id"
value="<?php echo $row['complaint_id']; ?>"
>

<label class="block text-sm text-zinc-400">
Update Status
</label>

<select
name="status"
class="select w-full">

<option value="pending"
<?php if($row['status']=="pending"){ echo "selected"; } ?>>
Pending
</option>

<option value="in_progress"
<?php if($row['status']=="in_progress"){ echo "selected"; } ?>>
In Progress
</option>

<option value="resolved"
<?php if($row['status']=="resolved"){ echo "selected"; } ?>>
Resolved
</option>

</select>

<button
type="submit"
name="update_status"
class="btn w-full bg-emerald-600 hover:bg-emerald-500 py-3 rounded-2xl text-white font-semibold">

Update Complaint

</button>

</form>

</div>

</div>

</div>

<?php } ?>

<?php } else { ?>

<div class="card rounded-[28px] p-16 text-center">

<div class="text-7xl mb-5">
📭
</div>

<h2 class="text-3xl font-bold text-white">
No Complaints Found
</h2>

<p class="text-zinc-500 mt-3">
No complaint records available right now.
</p>

</div>

<?php } ?>

</div>

</div>

</div>

</body>
</html>