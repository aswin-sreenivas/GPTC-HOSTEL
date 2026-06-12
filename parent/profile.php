<?php
session_start();

if(!isset($_SESSION['user_id']) || $_SESSION['role']!="parent"){
header("Location: ../index.php");
exit();
}

include("../config/db.php");

$user_id=$_SESSION['user_id'];

$success="";
$error="";

/* ================= UPDATE PARENT PROFILE ================= */

if(isset($_POST['update_parent'])){

$father_name=mysqli_real_escape_string($conn,$_POST['father_name']);
$mother_name=mysqli_real_escape_string($conn,$_POST['mother_name']);
$phone=mysqli_real_escape_string($conn,$_POST['phone']);
$address=mysqli_real_escape_string($conn,$_POST['address']);

mysqli_query($conn,"
UPDATE parents
SET
father_name='$father_name',
mother_name='$mother_name',
phone='$phone',
address='$address'
WHERE user_id='$user_id'
");

$success="Parent profile updated successfully";

}

/* ================= CHANGE PASSWORD ================= */

if(isset($_POST['change_password'])){

$current=$_POST['current_password'];
$new=$_POST['new_password'];
$confirm=$_POST['confirm_password'];

$user=mysqli_fetch_assoc(mysqli_query($conn,"
SELECT *
FROM users
WHERE user_id='$user_id'
"));

/* VERIFY HASHED PASSWORD */

if(!password_verify($current,$user['password'])){

$error="Current password is incorrect";

}elseif($new!=$confirm){

$error="New passwords do not match";

}else{

/* HASH NEW PASSWORD */

$new_password=password_hash($new,PASSWORD_DEFAULT);

mysqli_query($conn,"
UPDATE users
SET password='$new_password'
WHERE user_id='$user_id'
");

$success="Password changed successfully";

}

}

/* ================= PROFILE QUERY ================= */

$query=mysqli_query($conn,"
SELECT 

students.*,

parents.parent_id,
parents.father_name,
parents.mother_name,
parents.phone AS parent_phone,
parents.address AS parent_address,

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

WHERE parents.user_id='$user_id'
");

if(!$query){
die("SQL Error: ".mysqli_error($conn));
}

$data=mysqli_fetch_assoc($query);

if(!$data){
die("No student linked to this parent.");
}

?>

<!DOCTYPE html>
<html>

<head>

<title>Parent Profile</title>

<script src="https://cdn.tailwindcss.com"></script>

<style>

body{
background:#020617;
font-family:system-ui;
}

.card{
background:#18181b;
border:1px solid #27272a;
backdrop-filter:blur(12px);
transition:.3s;
}

.card:hover{
border-color:rgba(16,185,129,.2);
transform:translateY(-2px);
}

.input{
width:100%;
background:#27272a;
border:1px solid #3f3f46;
padding:14px;
border-radius:16px;
outline:none;
color:white;
transition:.3s;
}

.input:focus{
border-color:#10b981;
box-shadow:0 0 0 4px rgba(16,185,129,.08);
}

</style>

</head>

<body class="text-zinc-200">

<div class="flex min-h-screen">

<?php include("layout/sidebar.php"); ?>

<div class="flex-1 p-10">

<!-- HEADER -->

<div class="flex justify-between items-center mb-10">

<div>

<h1 class="text-4xl font-black text-white">
Parent Profile
</h1>

<p class="text-zinc-500 mt-2 text-lg">
Manage parent account and student information.
</p>

</div>

<div class="card px-5 py-4 rounded-2xl">

<div class="text-zinc-400 text-sm">
Linked Student
</div>

<div class="font-semibold text-white mt-1">
<?php echo $data['full_name']; ?>
</div>

</div>

</div>

<!-- ALERTS -->

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

<!-- MAIN GRID -->

<div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

<!-- STUDENT CARD -->

<div class="card rounded-[32px] p-8">

<div class="flex flex-col items-center text-center">

<div class="w-28 h-28 rounded-full bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-4xl font-black text-emerald-400 mb-5">

<?php echo strtoupper(substr($data['full_name'],0,1)); ?>

</div>

<h2 class="text-2xl font-bold text-white">
<?php echo $data['full_name']; ?>
</h2>

<p class="text-zinc-500 mt-2">
<?php echo $data['course']; ?>
</p>

</div>

<div class="border-t border-zinc-800 mt-8 pt-6 space-y-5 text-sm">

<div class="flex justify-between">

<span class="text-zinc-500">
Admission No
</span>

<span class="text-white">
<?php echo $data['admission_no']; ?>
</span>

</div>

<div class="flex justify-between">

<span class="text-zinc-500">
Department
</span>

<span class="text-white">
<?php echo $data['department']; ?>
</span>

</div>

<div class="flex justify-between">

<span class="text-zinc-500">
Phone
</span>

<span class="text-white">
<?php echo $data['phone']; ?>
</span>

</div>

<div class="flex justify-between">

<span class="text-zinc-500">
Room
</span>

<span class="text-emerald-400">

<?php
echo $data['room_number']
? "Room ".$data['room_number']
: "Not Assigned";
?>

</span>

</div>

<div class="flex justify-between">

<span class="text-zinc-500">
Block
</span>

<span class="text-white">

<?php
echo $data['block_name']
? $data['block_name']
: "N/A";
?>

</span>

</div>

</div>

</div>

<!-- PARENT PROFILE -->

<div class="card rounded-[32px] p-8 xl:col-span-2">

<h2 class="text-2xl font-bold text-white mb-2">
Parent Information
</h2>

<p class="text-zinc-500 text-sm mb-8">
Update parent contact information
</p>

<form method="POST">

<div class="grid grid-cols-2 gap-6">

<div>

<label class="text-sm text-zinc-400 block mb-2">
Father Name
</label>

<input
type="text"
name="father_name"
value="<?php echo $data['father_name']; ?>"
class="input"
required
>

</div>

<div>

<label class="text-sm text-zinc-400 block mb-2">
Mother Name
</label>

<input
type="text"
name="mother_name"
value="<?php echo $data['mother_name']; ?>"
class="input"
required
>

</div>

<div>

<label class="text-sm text-zinc-400 block mb-2">
Phone Number
</label>

<input
type="text"
name="phone"
value="<?php echo $data['parent_phone']; ?>"
class="input"
required
>

</div>

<div>

<label class="text-sm text-zinc-400 block mb-2">
Email
</label>

<input
type="text"
value="<?php echo $_SESSION['username']; ?>"
disabled
class="input opacity-60 cursor-not-allowed"
>

</div>

<div class="col-span-2">

<label class="text-sm text-zinc-400 block mb-2">
Address
</label>

<textarea
name="address"
rows="5"
class="input"
required><?php echo $data['parent_address']; ?></textarea>

</div>

</div>

<button
name="update_parent"
class="mt-7 bg-emerald-600 hover:bg-emerald-500 transition px-8 py-3 rounded-2xl font-semibold shadow-lg shadow-emerald-600/20">

Update Parent Profile

</button>

</form>

</div>

<!-- PASSWORD -->

<div class="card rounded-[32px] p-8 xl:col-span-2">

<h2 class="text-2xl font-bold text-white mb-2">
Security Settings
</h2>

<p class="text-zinc-500 text-sm mb-8">
Change your parent account password
</p>

<form method="POST">

<div class="grid grid-cols-3 gap-5">

<div>

<label class="text-sm text-zinc-400 block mb-2">
Current Password
</label>

<input
type="password"
name="current_password"
class="input"
required
>

</div>

<div>

<label class="text-sm text-zinc-400 block mb-2">
New Password
</label>

<input
type="password"
name="new_password"
class="input"
required
minlength="8"
pattern="^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[@$!%*?&]).{8,}$"
title="Password must contain uppercase, lowercase, number and special character"
placeholder="Enter New Password"
>

</div>

<div>

<label class="text-sm text-zinc-400 block mb-2">
Confirm Password
</label>

<input
type="password"
name="confirm_password"
class="input"
required
minlength="8"
pattern="^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[@$!%*?&]).{8,}$"
title="Password must contain uppercase, lowercase, number and special character"
placeholder="Confirm Password"
>

</div>

</div>

<button
name="change_password"
class="mt-7 bg-blue-600 hover:bg-blue-500 transition px-8 py-3 rounded-2xl font-semibold shadow-lg shadow-blue-600/20">

Change Password

</button>

</form>

</div>

</div>

</div>

</div>

</body>
</html>