<?php
session_start();

if(!isset($_SESSION['user_id']) || $_SESSION['role']!="admin"){
header("Location: ../index.php");
exit();
}

include("../config/db.php");

/* ================= SEARCH ================= */

$search = isset($_GET['search'])
? mysqli_real_escape_string($conn,$_GET['search'])
: '';

$where="";

if($search!=""){

$where="

WHERE

students.full_name LIKE '%$search%'
OR students.admission_no LIKE '%$search%'
OR students.phone LIKE '%$search%'
OR students.email LIKE '%$search%'
OR rooms.room_number LIKE '%$search%'

";

}

/* ================= STUDENTS ================= */

$students = mysqli_query($conn,"
SELECT 

students.*,
rooms.room_number

FROM students

LEFT JOIN room_allocations 
ON students.student_id = room_allocations.student_id 
AND room_allocations.status='active'

LEFT JOIN rooms
ON room_allocations.room_id = rooms.room_id

$where

ORDER BY students.full_name ASC
");

?>

<!DOCTYPE html>
<html>

<head>

<title>Student Management</title>

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

.search{
background:#27272a;
border:1px solid #3f3f46;
outline:none;
transition:.3s;
}

.search:focus{
border-color:#10b981;
box-shadow:0 0 0 4px rgba(16,185,129,.08);
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
Student Management
</h1>

<p class="text-zinc-500 mt-2">
Manage and track all hostel residents.
</p>

</div>

<a
href="add_student.php"
class="bg-emerald-600 hover:bg-emerald-500 transition px-6 py-3 rounded-2xl text-white font-semibold">

+ Add New Student

</a>

</div>

<!-- SEARCH -->

<div class="card rounded-[28px] p-6 mb-6">

<form method="GET">

<div class="flex gap-4">

<input
type="text"
name="search"
value="<?php echo $search; ?>"
placeholder="Search by name, admission no, phone, email or room..."
class="search text-white px-5 py-4 rounded-2xl w-full"
>

<button
type="submit"
class="bg-emerald-600 hover:bg-emerald-500 transition px-8 rounded-2xl font-semibold">

Search

</button>

<?php if($search!=""){ ?>

<a
href="students.php"
class="bg-zinc-800 hover:bg-zinc-700 transition px-6 py-4 rounded-2xl">

Clear

</a>

<?php } ?>

</div>

</form>

</div>

<!-- TABLE -->

<div class="card rounded-[28px] overflow-hidden">

<div class="overflow-x-auto">

<table class="w-full text-left">

<thead class="bg-zinc-950 border-b border-zinc-800 text-zinc-400 text-sm">

<tr>

<th class="p-5">Student</th>
<th>Admission No</th>
<th>Room</th>
<th>Contact</th>
<th>Status</th>
<th>Actions</th>

</tr>

</thead>

<tbody>

<?php if(mysqli_num_rows($students)>0){ ?>

<?php while($row=mysqli_fetch_assoc($students)){ ?>

<tr class="border-b border-zinc-800 transition">

<!-- STUDENT -->

<td class="p-5">

<div class="flex items-center gap-4">

<div class="w-12 h-12 rounded-full bg-zinc-700 flex items-center justify-center font-bold text-white">

<?php echo strtoupper(substr($row['full_name'],0,2)); ?>

</div>

<div>

<div class="text-white font-semibold">

<?php echo $row['full_name']; ?>

</div>

<div class="text-zinc-500 text-sm mt-1">

<?php echo $row['email']; ?>

</div>

</div>

</div>

</td>

<!-- REG -->

<td>

<div class="text-white">

<?php echo $row['admission_no']; ?>

</div>

</td>

<!-- ROOM -->

<td>

<?php

if($row['room_number']){

echo "
<span class='bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 px-4 py-2 rounded-full text-xs'>
Room ".$row['room_number']."
</span>
";

}else{

echo "
<span class='text-zinc-500 text-sm'>
Not Assigned
</span>
";

}

?>

</td>

<!-- CONTACT -->

<td>

<div class="text-sm text-white">

<?php echo $row['phone']; ?>

</div>

<div class="text-xs text-zinc-500 mt-1">

<?php echo $row['address']; ?>

</div>

</td>

<!-- STATUS -->

<td>

<span class="bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 px-4 py-2 rounded-full text-xs">

ACTIVE

</span>

</td>

<!-- ACTIONS -->

<td>

<div class="flex gap-5 text-sm">

<a
href="edit_student.php?id=<?php echo $row['student_id']; ?>"
class="text-blue-400 hover:text-blue-300 transition">

Edit

</a>

<a
href="delete_student.php?id=<?php echo $row['student_id']; ?>"
onclick="return confirm('Delete this student?')"
class="text-red-400 hover:text-red-300 transition">

Delete

</a>

</div>

</td>

</tr>

<?php } ?>

<?php } else { ?>

<tr>

<td colspan="6" class="p-16 text-center">

<div class="text-7xl mb-5">
📭
</div>

<h2 class="text-3xl font-bold text-white">
No Students Found
</h2>

<p class="text-zinc-500 mt-3">

No matching students for your search.

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