<?php
session_start();

if(!isset($_SESSION['user_id']) || $_SESSION['role']!="student"){
header("Location: ../index.php");
exit();
}

include("../config/db.php");

$user_id=$_SESSION['user_id'];

/* ================= GET STUDENT ================= */

$student_query=mysqli_query($conn,"
SELECT student_id 
FROM students
WHERE user_id='$user_id'
");

$student=mysqli_fetch_assoc($student_query);

if(!$student){
die("Student record not found. Please contact admin.");
}

$student_id=$student['student_id'];

/* ================= INSERT COMPLAINT ================= */

if(isset($_POST['submit'])){

$title=mysqli_real_escape_string($conn,$_POST['title']);
$desc=mysqli_real_escape_string($conn,$_POST['description']);

if($title!="" && $desc!=""){

mysqli_query($conn,"
INSERT INTO complaints
(student_id,title,description,status,created_at)
VALUES
('$student_id','$title','$desc','pending',NOW())
");

header("Location: complaints.php");
exit();

}

}

/* ================= GET COMPLAINTS ================= */

$complaints=mysqli_query($conn,"
SELECT *
FROM complaints
WHERE student_id='$student_id'
ORDER BY created_at DESC
");

?>

<!DOCTYPE html>
<html>

<head>

<title>Complaints</title>

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

.input{
background:#27272a;
border:1px solid #3f3f46;
outline:none;
transition:.3s;
}

.input:focus{
border-color:#10b981;
box-shadow:0 0 0 4px rgba(16,185,129,.08);
}

.complaint-card{
background:#18181b;
border:1px solid #27272a;
transition:.3s;
}

.complaint-card:hover{
border-color:#10b98130;
transform:translateY(-2px);
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
Complaints
</h1>

<p class="text-zinc-500 mt-2">
Track and report your hostel issues.
</p>

</div>

<button
onclick="document.getElementById('form').classList.toggle('hidden')"
class="bg-emerald-600 hover:bg-emerald-500 transition px-6 py-3 rounded-2xl text-white font-semibold">

+ New Complaint

</button>

</div>

<!-- FORM -->

<div id="form" class="hidden mb-8 card rounded-[28px] p-6">

<form method="POST">

<div class="grid grid-cols-2 gap-4">

<input
type="text"
name="title"
placeholder="Complaint Title"
class="input p-4 rounded-2xl text-white"
required
/>

<input
type="text"
name="description"
placeholder="Describe issue"
class="input p-4 rounded-2xl text-white"
required
/>

</div>

<button
name="submit"
class="mt-5 bg-emerald-600 hover:bg-emerald-500 transition px-7 py-3 rounded-2xl text-white font-semibold">

Submit Complaint

</button>

</form>

</div>

<!-- COMPLAINT LIST -->

<div class="space-y-6">

<?php if(mysqli_num_rows($complaints)>0){ ?>

<?php while($row=mysqli_fetch_assoc($complaints)){ ?>

<div class="complaint-card rounded-[28px] p-6 flex justify-between items-center">

<div>

<!-- TITLE -->

<div class="text-xl font-bold text-white mb-2">

<?php echo $row['title']; ?>

</div>

<!-- DESCRIPTION -->

<div class="text-zinc-400 text-sm max-w-2xl leading-relaxed">

<?php echo $row['description']; ?>

</div>

<!-- DATE -->

<div class="text-xs text-zinc-600 mt-3">

Submitted:
<?php echo date("d M Y - h:i A",strtotime($row['created_at'])); ?>

</div>

</div>

<!-- STATUS -->

<div>

<?php

$status=strtolower(trim($row['status']));

if($status=="pending"){

?>

<span class="bg-yellow-500/10 text-yellow-400 border border-yellow-500/20 px-4 py-2 rounded-full text-xs">

🟡 Pending

</span>

<?php } elseif($status=="in_progress" || $status=="in progress"){ ?>

<span class="bg-blue-500/10 text-blue-400 border border-blue-500/20 px-4 py-2 rounded-full text-xs">

🔵 In Progress

</span>

<?php } elseif($status=="resolved"){ ?>

<span class="bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 px-4 py-2 rounded-full text-xs">

🟢 Resolved

</span>

<?php } else { ?>

<span class="bg-zinc-700 text-zinc-300 px-4 py-2 rounded-full text-xs">

Unknown

</span>

<?php } ?>

</div>

</div>

<?php } ?>

<?php } else { ?>

<div class="card rounded-[28px] p-16 text-center">

<div class="text-7xl mb-6">
📭
</div>

<h2 class="text-3xl font-black text-white">
No Complaints Yet
</h2>

<p class="text-zinc-500 mt-3">

You haven't submitted any complaints.

</p>

</div>

<?php } ?>

</div>

</div>

</div>

</body>
</html>