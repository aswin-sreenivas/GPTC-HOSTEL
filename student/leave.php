<?php
session_start();

if(!isset($_SESSION['user_id']) || $_SESSION['role']!="student"){
header("Location: ../index.php");
exit();
}

include("../config/db.php");

$user_id=$_SESSION['user_id'];

$success="";
$error="";

/* ================= GET STUDENT ================= */

$student_query=mysqli_query($conn,"
SELECT student_id,full_name
FROM students
WHERE user_id='$user_id'
");

$student=mysqli_fetch_assoc($student_query);

if(!$student){
die("Student record not found. Please contact admin.");
}

$student_id=$student['student_id'];

/* ================= APPLY LEAVE ================= */

if(isset($_POST['apply'])){

$type=$_POST['leave_type'];

$from=$_POST['from'];
$to=$_POST['to'];

$reason=mysqli_real_escape_string($conn,$_POST['reason']);

$medical_file="";

/* upload medical proof */

if($type=="Sick Leave"){

if(isset($_FILES['medical_proof']) && $_FILES['medical_proof']['name']!=""){

$folder="../uploads/medical/";

if(!is_dir($folder)){
mkdir($folder,0777,true);
}

$file=time()."_".$_FILES['medical_proof']['name'];

$tmp=$_FILES['medical_proof']['tmp_name'];

move_uploaded_file($tmp,$folder.$file);

$medical_file=$file;

}else{

$error="Medical proof required for sick leave.";

}

}

/* insert leave */

if($from!="" && $to!="" && $reason!="" && $error==""){

mysqli_query($conn,"
INSERT INTO leave_requests
(
student_id,
leave_type,
leave_from,
leave_to,
reason,
medical_proof,
status
)

VALUES
(
'$student_id',
'$type',
'$from',
'$to',
'$reason',
'$medical_file',
'Pending'
)
");

$success="Leave request submitted successfully.";

}

}

/* ================= LEAVE HISTORY ================= */

$leaves=mysqli_query($conn,"
SELECT *
FROM leave_requests
WHERE student_id='$student_id'
ORDER BY created_at DESC
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
transition:.3s;
}

.card:hover{
border-color:#10b98130;
}

.input{
width:100%;
background:#27272a;
border:1px solid #3f3f46;
padding:14px;
border-radius:16px;
outline:none;
color:white;
}

.input:focus{
border-color:#10b981;
box-shadow:0 0 0 4px rgba(16,185,129,.08);
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
Leave Management
</h1>

<p class="text-zinc-500 mt-2">
Apply for hostel and sick leave requests.
</p>

</div>

<button
onclick="document.getElementById('leaveform').classList.toggle('hidden')"
class="bg-emerald-600 hover:bg-emerald-500 transition px-6 py-3 rounded-2xl font-semibold">

+ Apply Leave

</button>

</div>

<!-- ALERT -->

<?php if($success!=""){ ?>

<div class="bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 p-4 rounded-2xl mb-6">

<?php echo $success; ?>

</div>

<?php } ?>

<?php if($error!=""){ ?>

<div class="bg-red-500/10 border border-red-500/20 text-red-400 p-4 rounded-2xl mb-6">

<?php echo $error; ?>

</div>

<?php } ?>

<!-- FORM -->

<div id="leaveform" class="hidden card rounded-[30px] p-8 mb-8">

<form method="POST" enctype="multipart/form-data">

<div class="grid grid-cols-2 gap-6">

<!-- TYPE -->

<div>

<label class="block text-sm text-zinc-400 mb-2">
Leave Type
</label>

<select
name="leave_type"
id="leave_type"
onchange="toggleMedical()"
class="input"
required>

<option value="Normal Leave">
Normal Leave
</option>

<option value="Sick Leave">
Sick Leave
</option>

</select>

</div>

<!-- FROM -->

<div>

<label class="block text-sm text-zinc-400 mb-2">
From Date
</label>

<input
type="date"
name="from"
required
class="input"
>

</div>

<!-- TO -->

<div>

<label class="block text-sm text-zinc-400 mb-2">
To Date
</label>

<input
type="date"
name="to"
required
class="input"
>

</div>

<!-- REASON -->

<div>

<label class="block text-sm text-zinc-400 mb-2">
Reason
</label>

<input
type="text"
name="reason"
placeholder="Enter leave reason"
required
class="input"
>

</div>

<!-- MEDICAL -->

<div id="medicalbox" class="hidden col-span-2">

<label class="block text-sm text-zinc-400 mb-2">
Upload Medical Proof
</label>

<input
type="file"
name="medical_proof"
class="input"
>

</div>

</div>

<button
type="submit"
name="apply"
class="mt-7 bg-emerald-600 hover:bg-emerald-500 transition px-8 py-3 rounded-2xl font-semibold">

Submit Leave Request

</button>

</form>

</div>

<!-- HISTORY -->

<div class="space-y-5">

<?php if(mysqli_num_rows($leaves)>0){ ?>

<?php while($row=mysqli_fetch_assoc($leaves)){ ?>

<div class="card rounded-[28px] p-6">

<div class="flex justify-between items-start">

<!-- LEFT -->

<div>

<div class="flex items-center gap-3 mb-3">

<?php if($row['leave_type']=="Sick Leave"){ ?>

<span class="bg-red-500/10 text-red-400 border border-red-500/20 text-xs px-3 py-1 rounded-full">

🤒 Sick Leave

</span>

<?php } else { ?>

<span class="bg-blue-500/10 text-blue-400 border border-blue-500/20 text-xs px-3 py-1 rounded-full">

📄 Normal Leave

</span>

<?php } ?>

<?php

if($row['status']=="Pending"){

echo "
<span class='bg-yellow-500/10 text-yellow-400 border border-yellow-500/20 text-xs px-3 py-1 rounded-full'>
Pending
</span>
";

}

elseif($row['status']=="Approved"){

echo "
<span class='bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 text-xs px-3 py-1 rounded-full'>
Approved
</span>
";

}

else{

echo "
<span class='bg-red-500/10 text-red-400 border border-red-500/20 text-xs px-3 py-1 rounded-full'>
Rejected
</span>
";

}

?>

</div>

<h2 class="text-2xl font-bold text-white">

<?php echo $row['leave_from']; ?>

→

<?php echo $row['leave_to']; ?>

</h2>

<p class="text-zinc-400 mt-3">

<?php echo $row['reason']; ?>

</p>

<?php if($row['medical_proof']!=""){ ?>

<a
href="../uploads/medical/<?php echo $row['medical_proof']; ?>"
target="_blank"
class="inline-block mt-4 text-emerald-400 hover:text-emerald-300 text-sm">

📎 View Medical Proof

</a>

<?php } ?>

</div>

<!-- RIGHT -->

<div class="text-zinc-500 text-sm">

<?php echo date("d M Y",strtotime($row['created_at'])); ?>

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
No Leave Requests
</h2>

<p class="text-zinc-500 mt-3">
No leave applications submitted yet.
</p>

</div>

<?php } ?>

</div>

</div>

</div>

<script>

function toggleMedical(){

let type=document.getElementById("leave_type").value;

let box=document.getElementById("medicalbox");

if(type=="Sick Leave"){

box.classList.remove("hidden");

}else{

box.classList.add("hidden");

}

}

</script>

</body>
</html>