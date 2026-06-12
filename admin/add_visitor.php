<?php
session_start();

if(!isset($_SESSION['user_id']) || $_SESSION['role']!="admin"){
header("Location: ../index.php");
exit();
}

include("../config/db.php");

$msg="";

/* ================= STUDENTS ================= */

$students=mysqli_query($conn,"
SELECT * FROM students
ORDER BY full_name ASC
");

/* ================= SAVE VISITOR ================= */

if(isset($_POST['save'])){

$name=$_POST['name'];
$phone=$_POST['phone'];
$student=$_POST['student'];
$relation=$_POST['relation'];

$checkin=date("Y-m-d H:i:s");

/* ================= CHECK DUPLICATE ACTIVE VISITOR ================= */

$check=mysqli_query($conn,"
SELECT *
FROM visitors

WHERE phone='$phone'
AND check_out IS NULL
");

if(mysqli_num_rows($check)>0){

$msg="Visitor already checked in!";

}

/* ================= INSERT ================= */

else{

mysqli_query($conn,"
INSERT INTO visitors
(visitor_name,phone,student_id,relation,check_in)

VALUES

(
'$name',
'$phone',
'$student',
'$relation',
'$checkin'
)
");

$msg="Visitor Checked In Successfully";

}

}
?>

<!DOCTYPE html>
<html>

<head>

<title>Add Visitor</title>

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
width:100%;
background:#27272a;
border:1px solid #3f3f46;
padding:14px;
border-radius:14px;
outline:none;
color:white;
}

.input:focus{
border-color:#10b981;
}

</style>

</head>

<body class="text-zinc-200">

<div class="flex min-h-screen">

<?php include("layout/sidebar.php"); ?>

<div class="flex-1 p-10">

<?php include("layout/header.php"); ?>

<!-- HEADER -->

<div class="mb-8">

<h1 class="text-4xl font-bold text-white">
Add Visitor
</h1>

<p class="text-zinc-500 mt-2">
Register hostel visitors and student meetings.
</p>

</div>

<!-- FORM -->

<form method="POST">

<div class="card rounded-3xl p-8 grid grid-cols-2 gap-6">

<!-- VISITOR NAME -->

<div>

<label class="block text-sm text-zinc-400 mb-2">
Visitor Name
</label>

<input
type="text"
name="name"
required
placeholder="Visitor Name"
class="input"
>

</div>

<!-- PHONE -->

<div>

<label class="block text-sm text-zinc-400 mb-2">
Phone
</label>

<input
type="tel"
name="phone"
required
placeholder="9876543210"
pattern="[0-9]{10}"
maxlength="10"
inputmode="numeric"
title="Enter valid 10 digit phone number"
class="input"
>

</div>

<!-- STUDENT -->

<div>

<label class="block text-sm text-zinc-400 mb-2">
Student
</label>

<select
name="student"
required
class="input">

<option value="">
Select Student
</option>

<?php while($s=mysqli_fetch_assoc($students)){ ?>

<option value="<?php echo $s['student_id']; ?>">

<?php echo $s['full_name']; ?>

</option>

<?php } ?>

</select>

</div>

<!-- RELATION -->

<div>

<label class="block text-sm text-zinc-400 mb-2">
Relation
</label>

<select
name="relation"
required
class="input">

<option value="">
Select Relation
</option>

<option value="Father">Father</option>
<option value="Mother">Mother</option>
<option value="Brother">Brother</option>
<option value="Sister">Sister</option>
<option value="Guardian">Guardian</option>
<option value="Relative">Relative</option>
<option value="Friend">Friend</option>

</select>

</div>

<!-- BUTTON -->

<div class="col-span-2 mt-2">

<button
name="save"
class="bg-emerald-600 hover:bg-emerald-500 transition px-8 py-4 rounded-2xl text-white font-semibold">

Check In Visitor

</button>

</div>

</div>

</form>

</div>

</div>

<!-- TOAST -->

<?php if($msg!=""){ ?>

<div id="toast"
class="fixed top-5 right-5 px-6 py-4 rounded-2xl shadow-2xl text-white z-50 transition-all duration-500

<?php echo (strpos($msg,'Successfully')!==false)
? 'bg-emerald-600'
: 'bg-red-600'; ?>">

<?php echo $msg; ?>

</div>

<script>

setTimeout(() => {
document.getElementById("toast").style.opacity="0";
document.getElementById("toast").style.transform="translateY(-20px)";
},3000);

setTimeout(() => {
document.getElementById("toast").remove();
},3500);

</script>

<?php } ?>

</body>
</html>