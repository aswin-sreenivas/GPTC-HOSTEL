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

/* ================= UPDATE PROFILE ================= */

if(isset($_POST['update_profile'])){

$phone=mysqli_real_escape_string($conn,$_POST['phone']);
$address=mysqli_real_escape_string($conn,$_POST['address']);

mysqli_query($conn,"
UPDATE students
SET
phone='$phone',
address='$address'
WHERE user_id='$user_id'
");

$success="Profile updated successfully";

}

/* ================= CHANGE PASSWORD ================= */

if(isset($_POST['change_password'])){

$current=$_POST['current_password'];
$new=$_POST['new_password'];
$confirm=$_POST['confirm_password'];

$user=mysqli_fetch_assoc(mysqli_query($conn,"
SELECT * FROM users
WHERE user_id='$user_id'
"));

/* VERIFY HASHED PASSWORD */

if(!password_verify($current,$user['password'])){

$error="Current password is incorrect";

}

/* CHECK PASSWORD MATCH */

elseif($new!=$confirm){

$error="New passwords do not match";

}

/* PASSWORD CRITERIA */

elseif(
!preg_match(
'/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&]).{8,30}$/',
$new
)
){

$error="Password must contain uppercase, lowercase, number and special character";

}

else{

/* HASH PASSWORD */

$new_password=password_hash($new,PASSWORD_DEFAULT);

/* UPDATE PASSWORD */

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

parents.father_name,
parents.mother_name,
parents.phone AS parent_phone,

rooms.room_number,
hostel_blocks.block_name,

users.username

FROM students

LEFT JOIN parents
ON students.parent_id = parents.parent_id

LEFT JOIN users
ON students.user_id = users.user_id

LEFT JOIN room_allocations
ON students.student_id = room_allocations.student_id
AND room_allocations.status='active'

LEFT JOIN rooms
ON room_allocations.room_id = rooms.room_id

LEFT JOIN hostel_blocks
ON rooms.block_id = hostel_blocks.block_id

WHERE students.user_id='$user_id'
");

$student=mysqli_fetch_assoc($query);

?>

<!DOCTYPE html>
<html>

<head>

<title>My Profile</title>

<script src="https://cdn.tailwindcss.com"></script>

<style>

body{
background:#020617;
font-family:system-ui;
}

.card{
background:#18181b;
border:1px solid #27272a;
backdrop-filter:blur(10px);
}

.input{
width:100%;
background:#27272a;
border:1px solid #3f3f46;
padding:14px;
border-radius:14px;
outline:none;
color:white;
transition:.3s;
}

.input:focus{
border-color:#10b981;
box-shadow:0 0 0 4px rgba(16,185,129,.1);
}

</style>

</head>

<body class="text-zinc-200">

<div class="flex">

<?php include("layout/sidebar.php"); ?>

<div class="flex-1 p-10">

<?php include("layout/header.php"); ?>

<!-- HEADER -->

<div class="mb-8">

<h1 class="text-4xl font-bold text-white">
My Profile
</h1>

<p class="text-zinc-500 mt-2">
Manage your hostel and personal information
</p>

</div>

<!-- ALERTS -->

<?php if($success!=""){ ?>

<div class="bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 p-4 rounded-2xl mb-6 flex justify-between items-center">

<div>
<?php echo $success; ?>
</div>

<button
onclick="this.parentElement.style.display='none'"
class="text-xl hover:text-white">

&times;

</button>

</div>

<?php } ?>

<?php if($error!=""){ ?>

<div class="bg-red-500/10 border border-red-500/20 text-red-400 p-4 rounded-2xl mb-6 flex justify-between items-center">

<div>
<?php echo $error; ?>
</div>

<button
onclick="this.parentElement.style.display='none'"
class="text-xl hover:text-white">

&times;

</button>

</div>

<?php } ?>

<!-- MAIN GRID -->

<div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

<!-- PROFILE CARD -->

<div class="card rounded-3xl p-8 xl:col-span-1 h-fit">

<div class="flex flex-col items-center text-center">

<div class="w-28 h-28 rounded-full bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-4xl font-bold text-emerald-400 mb-5">

<?php echo strtoupper(substr($student['full_name'],0,1)); ?>

</div>

<h2 class="text-2xl font-bold text-white">
<?php echo $student['full_name']; ?>
</h2>

<p class="text-zinc-500 mt-2">
<?php echo $student['course']; ?>
</p>

<div class="mt-5">

<span class="bg-emerald-500/20 text-emerald-400 px-4 py-2 rounded-full text-sm">
Active Student
</span>

</div>

</div>

<div class="border-t border-zinc-800 mt-8 pt-6 space-y-5">

<div class="flex justify-between">

<span class="text-zinc-500">
Admission No
</span>

<span class="text-white font-medium">
<?php echo $student['admission_no']; ?>
</span>

</div>

<div class="flex justify-between">

<span class="text-zinc-500">
Department
</span>

<span class="text-white font-medium">
<?php echo $student['department']; ?>
</span>

</div>

<div class="flex justify-between">

<span class="text-zinc-500">
Year
</span>

<span class="text-white font-medium">
Year <?php echo $student['year']; ?>
</span>

</div>

<div class="flex justify-between">

<span class="text-zinc-500">
Blood Group
</span>

<span class="text-red-400 font-medium">
<?php echo $student['blood_group']; ?>
</span>

</div>

<div class="flex justify-between">

<span class="text-zinc-500">
Room
</span>

<span class="text-emerald-400 font-medium">

<?php
echo $student['room_number']
? "Room ".$student['room_number']
: "Not Assigned";
?>

</span>

</div>

</div>

</div>

<!-- PERSONAL INFO -->

<div class="card rounded-3xl p-8 xl:col-span-2">

<h2 class="text-2xl font-bold text-white mb-2">
Personal Information
</h2>

<p class="text-zinc-500 text-sm mb-8">
Manage your personal details
</p>

<form method="POST">

<div class="grid grid-cols-2 gap-6">

<!-- EMAIL -->

<div>

<label class="text-sm text-zinc-400 block mb-2">
Email Address
</label>

<input
type="email"
value="<?php echo $student['email']; ?>"
disabled
class="input opacity-60 cursor-not-allowed"
>

</div>

<!-- PHONE -->

<div>

<label class="text-sm text-zinc-400 block mb-2">
Phone Number
</label>

<input
type="text"
name="phone"
value="<?php echo $student['phone']; ?>"
class="input"
required
maxlength="10"
pattern="[0-9]{10}"
title="Enter valid 10 digit phone number"
>

</div>

<!-- BLOCK -->

<div>

<label class="text-sm text-zinc-400 block mb-2">
Hostel Block
</label>

<input
type="text"
value="<?php echo $student['block_name'] ? $student['block_name'] : 'N/A'; ?>"
disabled
class="input opacity-60 cursor-not-allowed"
>

</div>

<!-- JOIN DATE -->

<div>

<label class="text-sm text-zinc-400 block mb-2">
Join Date
</label>

<input
type="text"
value="<?php echo $student['join_date']; ?>"
disabled
class="input opacity-60 cursor-not-allowed"
>

</div>

<!-- ADDRESS -->

<div class="col-span-2">

<label class="text-sm text-zinc-400 block mb-2">
Address
</label>

<textarea
name="address"
rows="5"
class="input"
required><?php echo $student['address']; ?></textarea>

</div>

</div>

<button
name="update_profile"
class="mt-7 bg-emerald-600 hover:bg-emerald-500 transition px-7 py-3 rounded-2xl font-semibold shadow-lg shadow-emerald-600/20">

Update Profile

</button>

</form>

</div>

<!-- PASSWORD -->

<div class="card rounded-3xl p-8 xl:col-span-2">

<h2 class="text-2xl font-bold text-white mb-2">
Security Settings
</h2>

<p class="text-zinc-500 text-sm mb-8">
Update your login password securely
</p>

<form method="POST">

<div class="grid grid-cols-3 gap-5">

<!-- CURRENT PASSWORD -->

<div>

<label class="text-sm text-zinc-400 block mb-2">
Current Password
</label>

<input
type="password"
name="current_password"
class="input"
required
placeholder="Enter Current Password"
>

</div>

<!-- NEW PASSWORD -->

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
maxlength="30"
pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&]).{8,30}$"
title="Password must contain uppercase, lowercase, number and special character"
placeholder="Enter New Password"
>


</div>

<!-- CONFIRM PASSWORD -->

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
maxlength="30"
pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&]).{8,30}$"
title="Password must contain uppercase, lowercase, number and special character"
placeholder="Confirm Password"
>

</div>

</div>

<button
name="change_password"
class="mt-7 bg-blue-600 hover:bg-blue-500 transition px-7 py-3 rounded-2xl font-semibold shadow-lg shadow-blue-600/20">

Change Password

</button>

</form>

</div>

<!-- PARENT DETAILS -->

<div class="card rounded-3xl p-8 xl:col-span-1 h-fit">

<h2 class="text-2xl font-bold text-white mb-6">
Parent Details
</h2>

<div class="space-y-6">

<div>

<div class="text-zinc-500 text-sm mb-1">
Father Name
</div>

<div class="text-white font-medium">
<?php echo $student['father_name'] ? $student['father_name'] : "Not Available"; ?>
</div>

</div>

<div>

<div class="text-zinc-500 text-sm mb-1">
Mother Name
</div>

<div class="text-white font-medium">
<?php echo $student['mother_name'] ? $student['mother_name'] : "Not Available"; ?>
</div>

</div>

<div>

<div class="text-zinc-500 text-sm mb-1">
Parent Phone
</div>

<div class="text-white font-medium">
<?php echo $student['parent_phone'] ? $student['parent_phone'] : "Not Available"; ?>
</div>

</div>

</div>

</div>

</div>

</div>

</div>

</body>
</html>