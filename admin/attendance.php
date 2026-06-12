<?php
session_start();

if(!isset($_SESSION['user_id']) || $_SESSION['role']!="admin"){
header("Location: ../index.php");
exit();
}

include("../config/db.php");

/* DATE */

$date = isset($_GET['date']) ? $_GET['date'] : date("Y-m-d");

/* PREVENT FUTURE DATE */

if($date > date("Y-m-d")){
$date = date("Y-m-d");
}

/* SEARCH + FILTER */

$search = isset($_GET['search']) ? $_GET['search'] : "";
$block_filter = isset($_GET['block']) ? $_GET['block'] : "";
$room_filter = isset($_GET['room']) ? $_GET['room'] : "";

$attendance_filter = isset($_GET['attendance_filter'])
? $_GET['attendance_filter']
: "";

/* MARK ATTENDANCE */

if(isset($_GET['mark'])){

$student=$_GET['student'];
$status=$_GET['status'];
$admin=$_SESSION['user_id'];

$check=mysqli_query($conn,"
SELECT * FROM attendance
WHERE student_id='$student'
AND date='$date'
");

if(mysqli_num_rows($check)==0){

mysqli_query($conn,"
INSERT INTO attendance
(student_id,date,status,marked_by)
VALUES
('$student','$date','$status','$admin')
");

}else{

mysqli_query($conn,"
UPDATE attendance
SET status='$status'
WHERE student_id='$student'
AND date='$date'
");

}

header("Location: attendance.php?date=".$date.
"&search=".$search.
"&block=".$block_filter.
"&room=".$room_filter.
"&attendance_filter=".$attendance_filter);

exit();

}

/* BLOCKS */

$blocks=mysqli_query($conn,"
SELECT * FROM hostel_blocks
ORDER BY block_name ASC
");

/* ROOMS */

$rooms=mysqli_query($conn,"
SELECT * FROM rooms
ORDER BY room_number ASC
");

/* STUDENTS QUERY */

$query="
SELECT 
students.*,
rooms.room_number,
hostel_blocks.block_name

FROM students

LEFT JOIN room_allocations
ON students.student_id=room_allocations.student_id
AND room_allocations.status='active'

LEFT JOIN rooms
ON room_allocations.room_id=rooms.room_id

LEFT JOIN hostel_blocks
ON rooms.block_id=hostel_blocks.block_id

WHERE 1
";

/* SEARCH */

if($search!=""){

$query.=" AND (
students.full_name LIKE '%$search%'
OR students.admission_no LIKE '%$search%'
OR students.email LIKE '%$search%'
)";

}

/* BLOCK FILTER */

if($block_filter!=""){

$query.=" AND hostel_blocks.block_id='$block_filter'";

}

/* ROOM FILTER */

if($room_filter!=""){

$query.=" AND rooms.room_id='$room_filter'";

}

/* ATTENDANCE FILTER */

if($attendance_filter=="present"){

$query.=" AND students.student_id IN (
SELECT student_id
FROM attendance
WHERE date='$date'
AND status='present'
)";

}

elseif($attendance_filter=="absent"){

$query.=" AND students.student_id IN (
SELECT student_id
FROM attendance
WHERE date='$date'
AND status='absent'
)";

}

elseif($attendance_filter=="unmarked"){

$query.=" AND students.student_id NOT IN (
SELECT student_id
FROM attendance
WHERE date='$date'
)";

}

$query.=" ORDER BY students.full_name ASC";

$students=mysqli_query($conn,$query);

?>

<!DOCTYPE html>
<html>

<head>

<title>Attendance</title>

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

<div class="flex justify-between items-center mb-8">

<div>

<h1 class="text-4xl font-bold text-white">
Attendance Management
</h1>

<p class="text-zinc-500 mt-2">
Search, filter and mark student attendance
</p>

</div>

<div class="bg-zinc-900 border border-zinc-800 px-5 py-3 rounded-xl">

<div class="text-sm text-zinc-400">
Selected Date
</div>

<div class="font-semibold">
<?php echo $date; ?>
</div>

</div>

</div>

<!-- FILTERS -->

<div class="card rounded-2xl p-6 mb-8">

<form method="GET">

<div class="grid grid-cols-5 gap-4">

<!-- DATE -->

<div>

<label class="text-sm text-zinc-400 mb-2 block">
Date
</label>

<input
type="date"
name="date"
value="<?php echo isset($date) ? $date : date('Y-m-d'); ?>"
max="<?php echo date('Y-m-d'); ?>"
required
class="w-full bg-zinc-800 border border-zinc-700 px-4 py-3 rounded-xl outline-none"
/>

</div>

<!-- SEARCH -->

<div>

<label class="text-sm text-zinc-400 mb-2 block">
Search Student
</label>

<input
type="text"
name="search"
value="<?php echo $search; ?>"
placeholder="Name / Admission"
class="w-full bg-zinc-800 border border-zinc-700 px-4 py-3 rounded-xl outline-none"
/>

</div>

<!-- BLOCK -->

<div>

<label class="text-sm text-zinc-400 mb-2 block">
Filter Block
</label>

<select
name="block"
class="w-full bg-zinc-800 border border-zinc-700 px-4 py-3 rounded-xl outline-none">

<option value="">
All Blocks
</option>

<?php while($block=mysqli_fetch_assoc($blocks)){ ?>

<option
value="<?php echo $block['block_id']; ?>"
<?php if($block_filter==$block['block_id']) echo "selected"; ?>>

<?php echo $block['block_name']; ?>

</option>

<?php } ?>

</select>

</div>

<!-- ROOM -->

<div>

<label class="text-sm text-zinc-400 mb-2 block">
Filter Room
</label>

<select
name="room"
class="w-full bg-zinc-800 border border-zinc-700 px-4 py-3 rounded-xl outline-none">

<option value="">
All Rooms
</option>

<?php while($room=mysqli_fetch_assoc($rooms)){ ?>

<option
value="<?php echo $room['room_id']; ?>"
<?php if($room_filter==$room['room_id']) echo "selected"; ?>>

Room <?php echo $room['room_number']; ?>

</option>

<?php } ?>

</select>

</div>

<!-- ATTENDANCE FILTER -->

<div>

<label class="text-sm text-zinc-400 mb-2 block">
Attendance Filter
</label>

<select
name="attendance_filter"
class="w-full bg-zinc-800 border border-zinc-700 px-4 py-3 rounded-xl outline-none">

<option value="">
All Students
</option>

<option
value="unmarked"
<?php if($attendance_filter=="unmarked") echo "selected"; ?>>
Unmarked
</option>

<option
value="present"
<?php if($attendance_filter=="present") echo "selected"; ?>>
Present
</option>

<option
value="absent"
<?php if($attendance_filter=="absent") echo "selected"; ?>>
Absent
</option>

</select>

</div>

</div>

<!-- BUTTONS -->

<div class="flex gap-4 mt-6">

<button
class="bg-emerald-600 hover:bg-emerald-500 transition px-6 py-3 rounded-xl">

Apply Filters

</button>

<a
href="attendance.php"
class="bg-zinc-800 hover:bg-zinc-700 transition px-6 py-3 rounded-xl">

Reset

</a>

</div>

</form>

</div>

<!-- TABLE -->

<div class="card rounded-2xl overflow-hidden">

<table class="w-full text-left">

<thead class="bg-zinc-900 border-b border-zinc-800 text-zinc-400 text-sm">

<tr>

<th class="p-5">Student</th>
<th>Room</th>
<th>Block</th>
<th>Status</th>
<th class="text-center">Action</th>

</tr>

</thead>

<tbody>

<?php while($row=mysqli_fetch_assoc($students)){ 

$student_id=$row['student_id'];

$att=mysqli_fetch_assoc(mysqli_query($conn,"
SELECT status FROM attendance
WHERE student_id='$student_id'
AND date='$date'
"));

?>

<tr class="border-b border-zinc-800 hover:bg-zinc-900 transition">

<!-- STUDENT -->

<td class="p-5">

<div class="font-semibold text-white">
<?php echo $row['full_name']; ?>
</div>

<div class="text-zinc-500 text-sm">
<?php echo $row['email']; ?>
</div>

</td>

<!-- ROOM -->

<td>

<?php
echo $row['room_number']
? "Room ".$row['room_number']
: "Not Assigned";
?>

</td>

<!-- BLOCK -->

<td>

<?php
echo $row['block_name']
? $row['block_name']
: "N/A";
?>

</td>

<!-- STATUS -->

<td>

<?php

if($att){

if($att['status']=="present"){

echo "<span class='bg-emerald-500/20 text-emerald-400 px-3 py-1 rounded-full text-xs'>Present</span>";

}elseif($att['status']=="absent"){

echo "<span class='bg-red-500/20 text-red-400 px-3 py-1 rounded-full text-xs'>Absent</span>";

}

}else{

echo "<span class='bg-yellow-500/20 text-yellow-400 px-3 py-1 rounded-full text-xs'>Unmarked</span>";

}

?>

</td>

<!-- ACTION -->

<td class="text-center">

<div class="flex justify-center gap-3">

<a
href="?date=<?php echo $date ?>&search=<?php echo $search ?>&block=<?php echo $block_filter ?>&room=<?php echo $room_filter ?>&attendance_filter=<?php echo $attendance_filter ?>&mark=1&student=<?php echo $student_id ?>&status=present"
class="bg-emerald-600 hover:bg-emerald-500 px-4 py-2 rounded-lg text-xs transition">

Present

</a>

<a
href="?date=<?php echo $date ?>&search=<?php echo $search ?>&block=<?php echo $block_filter ?>&room=<?php echo $room_filter ?>&attendance_filter=<?php echo $attendance_filter ?>&mark=1&student=<?php echo $student_id ?>&status=absent"
class="bg-red-600 hover:bg-red-500 px-4 py-2 rounded-lg text-xs transition">

Absent

</a>

</div>

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