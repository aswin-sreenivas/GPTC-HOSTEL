<?php
session_start();

if(!isset($_SESSION['user_id']) || $_SESSION['role']!="parent"){
header("Location: ../index.php");
exit();
}

include("../config/db.php");

$user_id=$_SESSION['user_id'];

/* ================= STUDENT INFO ================= */

$query=mysqli_query($conn,"
SELECT 

students.*,
rooms.room_number,
hostel_blocks.block_name,

parents.father_name,
parents.mother_name,
parents.phone AS parent_phone

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

WHERE parents.user_id='$user_id'
");

if(!$query){
die("SQL Error: ".mysqli_error($conn));
}

$student=mysqli_fetch_assoc($query);

if(!$student){
die("No student linked to this parent. Contact admin.");
}

$student_id=$student['student_id'];

/* ================= FEES ================= */

$pending_fees=mysqli_fetch_assoc(mysqli_query($conn,"
SELECT COUNT(*) as total
FROM fees
WHERE student_id='$student_id'
AND status='pending'
"))['total'];

$total_paid=mysqli_fetch_assoc(mysqli_query($conn,"
SELECT SUM(amount) as total
FROM fees
WHERE student_id='$student_id'
AND status='paid'
"))['total'];

/* ================= ATTENDANCE ================= */

$total_att=mysqli_fetch_assoc(mysqli_query($conn,"
SELECT COUNT(*) as total
FROM attendance
WHERE student_id='$student_id'
"))['total'];

$present_att=mysqli_fetch_assoc(mysqli_query($conn,"
SELECT COUNT(*) as total
FROM attendance
WHERE student_id='$student_id'
AND status='present'
"))['total'];

$attendance_percent=0;

if($total_att>0){
$attendance_percent=round(($present_att/$total_att)*100);
}

/* ================= COMPLAINTS ================= */

$total_complaints=mysqli_fetch_assoc(mysqli_query($conn,"
SELECT COUNT(*) as total
FROM complaints
WHERE student_id='$student_id'
"))['total'];

$pending_complaints=mysqli_fetch_assoc(mysqli_query($conn,"
SELECT COUNT(*) as total
FROM complaints
WHERE student_id='$student_id'
AND status!='resolved'
"))['total'];

/* ================= NOTICES ================= */

$notices=mysqli_query($conn,"
SELECT *
FROM notices
ORDER BY notice_id DESC
LIMIT 3
");

/* ================= RECENT FEES ================= */

$recent_fees=mysqli_query($conn,"
SELECT *
FROM fees
WHERE student_id='$student_id'
ORDER BY fee_id DESC
LIMIT 3
");

?>

<!DOCTYPE html>
<html>

<head>

<title>Parent Dashboard</title>

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
border-color:#10b98120;
transform:translateY(-2px);
}

</style>

</head>

<body class="text-zinc-200">

<div class="flex">

<?php include("layout/sidebar.php"); ?>

<div class="flex-1 p-10">

<!-- HEADER -->

<div class="flex justify-between items-center mb-10">

<div>

<h1 class="text-4xl font-bold text-white">
Welcome back,
<?php echo explode(" ",$student['father_name'])[0]; ?> 👋
</h1>

<p class="text-zinc-500 mt-2">
Monitor your child’s hostel activities and status.
</p>

</div>

<div class="bg-zinc-900 border border-zinc-800 px-5 py-3 rounded-2xl">

<div class="text-sm text-zinc-400">
Student
</div>

<div class="font-semibold text-white">
<?php echo $student['full_name']; ?>
</div>

</div>

</div>

<!-- TOP STATS -->

<div class="grid grid-cols-4 gap-6 mb-8">

<!-- ROOM -->

<div class="card rounded-3xl p-6">

<div class="flex justify-between items-start">

<div>

<div class="text-zinc-400 text-sm">
ROOM
</div>

<h2 class="text-3xl font-bold mt-3 text-white">

<?php
echo $student['room_number']
? "Room ".$student['room_number']
: "N/A";
?>

</h2>

<p class="text-zinc-500 text-sm mt-2">

<?php
echo $student['block_name']
? $student['block_name']
: "No Block";
?>

</p>

</div>

<div class="text-4xl">
🛏
</div>

</div>

</div>

<!-- FEES -->

<div class="card rounded-3xl p-6">

<div class="flex justify-between items-start">

<div>

<div class="text-zinc-400 text-sm">
PENDING FEES
</div>

<h2 class="text-3xl font-bold mt-3 text-yellow-400">
<?php echo $pending_fees; ?>
</h2>

<p class="text-zinc-500 text-sm mt-2">
Paid:
₹<?php echo $total_paid ? $total_paid : 0; ?>
</p>

</div>

<div class="text-4xl">
💳
</div>

</div>

</div>

<!-- ATTENDANCE -->

<div class="card rounded-3xl p-6">

<div class="flex justify-between items-start">

<div>

<div class="text-zinc-400 text-sm">
ATTENDANCE
</div>

<h2 class="text-3xl font-bold mt-3 text-emerald-400">
<?php echo $attendance_percent; ?>%
</h2>

<p class="text-zinc-500 text-sm mt-2">
Present: <?php echo $present_att; ?> Days
</p>

</div>

<div class="text-4xl">
📅
</div>

</div>

</div>

<!-- COMPLAINTS -->

<div class="card rounded-3xl p-6">

<div class="flex justify-between items-start">

<div>

<div class="text-zinc-400 text-sm">
COMPLAINTS
</div>

<h2 class="text-3xl font-bold mt-3 text-red-400">
<?php echo $pending_complaints; ?>
</h2>

<p class="text-zinc-500 text-sm mt-2">
Total: <?php echo $total_complaints; ?>
</p>

</div>

<div class="text-4xl">
⚠
</div>

</div>

</div>

</div>

<!-- MAIN GRID -->

<div class="grid grid-cols-3 gap-6">

<!-- STUDENT PROFILE -->

<div class="card rounded-3xl p-7">

<div class="flex flex-col items-center text-center">

<div class="w-24 h-24 rounded-full bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-3xl font-bold text-emerald-400 mb-5">

<?php echo strtoupper(substr($student['full_name'],0,1)); ?>

</div>

<h2 class="text-2xl font-bold text-white">
<?php echo $student['full_name']; ?>
</h2>

<p class="text-zinc-500 mt-2">
<?php echo $student['course']; ?>
</p>

</div>

<div class="border-t border-zinc-800 mt-6 pt-6 space-y-4 text-sm">

<div class="flex justify-between">

<span class="text-zinc-500">
Admission No
</span>

<span class="text-white">
<?php echo $student['admission_no']; ?>
</span>

</div>

<div class="flex justify-between">

<span class="text-zinc-500">
Department
</span>

<span class="text-white">
<?php echo $student['department']; ?>
</span>

</div>

<div class="flex justify-between">

<span class="text-zinc-500">
Year
</span>

<span class="text-white">
Year <?php echo $student['year']; ?>
</span>

</div>

<div class="flex justify-between">

<span class="text-zinc-500">
Blood Group
</span>

<span class="text-red-400">
<?php echo $student['blood_group']; ?>
</span>

</div>

<div class="flex justify-between">

<span class="text-zinc-500">
Phone
</span>

<span class="text-white">
<?php echo $student['phone']; ?>
</span>

</div>

</div>

</div>

<!-- RECENT FEES -->

<div class="card rounded-3xl p-7">

<div class="flex justify-between items-center mb-6">

<h2 class="text-2xl font-bold text-white">
Recent Fees
</h2>

<a href="fees.php"
class="text-emerald-400 text-sm hover:underline">

View All

</a>

</div>

<div class="space-y-5">

<?php while($fee=mysqli_fetch_assoc($recent_fees)){ ?>

<div class="border border-zinc-800 rounded-2xl p-4 bg-zinc-950/40">

<div class="flex justify-between items-center">

<div>

<div class="text-white font-medium">
<?php echo $fee['fee_type']; ?>
</div>

<div class="text-zinc-500 text-sm mt-1">
₹<?php echo $fee['amount']; ?>
</div>

</div>

<div>

<?php if($fee['status']=="paid"){ ?>

<span class="bg-emerald-500/20 text-emerald-400 px-3 py-1 rounded-full text-xs">
Paid
</span>

<?php } else { ?>

<span class="bg-yellow-500/20 text-yellow-400 px-3 py-1 rounded-full text-xs">
Pending
</span>

<?php } ?>

</div>

</div>

</div>

<?php } ?>

</div>

</div>

<!-- NOTICES -->

<div class="card rounded-3xl p-7">

<div class="flex justify-between items-center mb-6">

<h2 class="text-2xl font-bold text-white">
Latest Notices
</h2>

</div>

<div class="space-y-5">

<?php while($notice=mysqli_fetch_assoc($notices)){ ?>

<div class="border border-zinc-800 rounded-2xl p-5 bg-zinc-950/40">

<div class="flex justify-between items-center mb-2">

<h3 class="font-semibold text-white">
<?php echo $notice['title']; ?>
</h3>

<div class="text-xs text-zinc-500">
<?php echo date("d M Y",strtotime($notice['created_at'])); ?>
</div>

</div>

<p class="text-zinc-400 text-sm">

<?php
echo isset($notice['description'])
? $notice['description']
: "No description available.";
?>

</p>

</div>

<?php } ?>

</div>

</div>

</div>

</div>

</div>

</body>
</html>