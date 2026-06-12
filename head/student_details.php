<?php
session_start();

if(!isset($_SESSION['user_id']) || $_SESSION['role']!="head"){
header("Location: ../index.php");
exit();
}

include("../config/db.php");

if(!isset($_GET['id'])){
header("Location: students.php");
exit();
}

$id=$_GET['id'];

/* student details */

$query=mysqli_query($conn,"
SELECT students.*, 

parents.father_name,
parents.phone AS parent_phone,
parents.email AS parent_email,

rooms.room_number,
hostel_blocks.block_name

FROM students

LEFT JOIN parents
ON students.parent_id = parents.parent_id

LEFT JOIN room_allocations
ON students.student_id = room_allocations.student_id
AND room_allocations.status='active'

LEFT JOIN rooms
ON room_allocations.room_id = rooms.room_id

LEFT JOIN hostel_blocks
ON rooms.block_id = hostel_blocks.block_id

WHERE students.student_id='$id'
");

$student=mysqli_fetch_assoc($query);

if(!$student){
die("Student not found");
}

?>

<!DOCTYPE html>
<html>

<head>

<title>Student Details</title>

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

<!-- TOP -->

<div class="flex justify-between items-center mb-8">

<div>

<h1 class="text-4xl font-bold text-white">
Student Details
</h1>

<p class="text-zinc-500 mt-2">
Complete student information overview
</p>

</div>

<a href="students.php"
class="bg-zinc-800 hover:bg-zinc-700 px-5 py-3 rounded-xl">
← Back
</a>

</div>

<!-- GRID -->

<div class="grid grid-cols-2 gap-8">

<!-- STUDENT -->

<div class="card rounded-2xl p-8">

<h2 class="text-2xl font-semibold mb-6">
Student Information
</h2>

<div class="space-y-5">

<div>
<div class="text-zinc-400 text-sm">Full Name</div>
<div class="text-lg"><?php echo $student['full_name']; ?></div>
</div>

<div>
<div class="text-zinc-400 text-sm">Admission Number</div>
<div class="text-lg"><?php echo $student['admission_no']; ?></div>
</div>

<div>
<div class="text-zinc-400 text-sm">Email</div>
<div class="text-lg"><?php echo $student['email']; ?></div>
</div>

<div>
<div class="text-zinc-400 text-sm">Phone</div>
<div class="text-lg"><?php echo $student['phone']; ?></div>
</div>

<div>
<div class="text-zinc-400 text-sm">Course</div>
<div class="text-lg"><?php echo $student['course']; ?></div>
</div>

<div>
<div class="text-zinc-400 text-sm">Year</div>
<div class="text-lg"><?php echo $student['year']; ?></div>
</div>

<div>
<div class="text-zinc-400 text-sm">Address</div>
<div class="text-lg"><?php echo $student['address']; ?></div>
</div>

</div>

</div>

<!-- HOSTEL -->

<div class="card rounded-2xl p-8">

<h2 class="text-2xl font-semibold mb-6">
Hostel Information
</h2>

<div class="space-y-5">

<div>
<div class="text-zinc-400 text-sm">Room Number</div>
<div class="text-lg">

<?php
echo $student['room_number']
? "Room ".$student['room_number']
: "Not Assigned";
?>

</div>
</div>

<div>
<div class="text-zinc-400 text-sm">Hostel Block</div>
<div class="text-lg">

<?php
echo $student['block_name']
? $student['block_name']
: "N/A";
?>

</div>
</div>

<div>
<div class="text-zinc-400 text-sm">Parent Name</div>
<div class="text-lg">
<?php echo $student['father_name']; ?>
</div>
</div>

<div>
<div class="text-zinc-400 text-sm">Parent Phone</div>
<div class="text-lg">
<?php echo $student['parent_phone']; ?>
</div>
</div>

<div>
<div class="text-zinc-400 text-sm">Parent Email</div>
<div class="text-lg">
<?php echo $student['parent_email']; ?>
</div>
</div>

</div>

</div>

</div>

</div>

</div>

</body>
</html>