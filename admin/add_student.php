<?php
session_start();

if(!isset($_SESSION['user_id']) || $_SESSION['role']!="admin"){
header("Location: ../index.php");
exit();
}

include("../config/db.php");

$msg="";

if(isset($_POST['save'])){

/* ================= STUDENT ================= */

$name=mysqli_real_escape_string($conn,$_POST['name']);
$email=mysqli_real_escape_string($conn,$_POST['email']);
$phone=mysqli_real_escape_string($conn,$_POST['phone']);
$reg=mysqli_real_escape_string($conn,$_POST['reg']);

$gender=mysqli_real_escape_string($conn,$_POST['gender']);
$dob=mysqli_real_escape_string($conn,$_POST['dob']);

$course=mysqli_real_escape_string($conn,$_POST['course']);
$department=mysqli_real_escape_string($conn,$_POST['department']);
$year=mysqli_real_escape_string($conn,$_POST['year']);

$blood_group=mysqli_real_escape_string($conn,$_POST['blood_group']);

$address=mysqli_real_escape_string($conn,$_POST['address']);

$join_date=mysqli_real_escape_string($conn,$_POST['join_date']);

/* ================= PARENT ================= */

$parent_name=mysqli_real_escape_string($conn,$_POST['parent_name']);
$parent_phone=mysqli_real_escape_string($conn,$_POST['parent_phone']);

/* ================= LOGIN ================= */

$stu_username=mysqli_real_escape_string($conn,$_POST['stu_username']);
$par_username=mysqli_real_escape_string($conn,$_POST['par_username']);

/* PASSWORD HASHING */

$stu_password=password_hash($_POST['stu_password'], PASSWORD_DEFAULT);
$par_password=password_hash($_POST['par_password'], PASSWORD_DEFAULT);

/* ================= CHECK DUPLICATE USER ================= */

$check_user=mysqli_query($conn,"
SELECT * FROM users
WHERE username='$stu_username'
OR username='$par_username'
");

if(mysqli_num_rows($check_user)>0){

$msg="Username already exists!";

}

/* ================= CHECK ADMISSION ================= */

$check_reg=mysqli_query($conn,"
SELECT * FROM students
WHERE admission_no='$reg'
");

if(mysqli_num_rows($check_reg)>0){

$msg="Admission number already exists!";

}

/* ================= CHECK PHONE ================= */

$check_phone=mysqli_query($conn,"
SELECT * FROM students
WHERE phone='$phone'
");

if(mysqli_num_rows($check_phone)>0){

$msg="Phone number already exists!";

}

/* ================= CHECK EMAIL ================= */

$check_email=mysqli_query($conn,"
SELECT * FROM students
WHERE email='$email'
");

if(mysqli_num_rows($check_email)>0){

$msg="Email already exists!";

}

/* ================= INSERT ================= */

if($msg==""){

/* ================= PARENT LOGIN ================= */

mysqli_query($conn,"
INSERT INTO users
(username,password,role,phone,status)

VALUES

(
'$par_username',
'$par_password',
'parent',
'$parent_phone',
'active'
)
");

$parent_user_id=mysqli_insert_id($conn);

/* ================= PARENT TABLE ================= */

mysqli_query($conn,"
INSERT INTO parents
(user_id,father_name,phone)

VALUES

(
'$parent_user_id',
'$parent_name',
'$parent_phone'
)
");

$parent_id=mysqli_insert_id($conn);

/* ================= STUDENT LOGIN ================= */

mysqli_query($conn,"
INSERT INTO users
(username,password,role,email,phone,status)

VALUES

(
'$stu_username',
'$stu_password',
'student',
'$email',
'$phone',
'active'
)
");

$student_user_id=mysqli_insert_id($conn);

/* ================= STUDENT TABLE ================= */

mysqli_query($conn,"
INSERT INTO students
(
user_id,
parent_id,
admission_no,
full_name,
gender,
dob,
course,
department,
year,
phone,
email,
address,
blood_group,
join_date,
status
)

VALUES

(
'$student_user_id',
'$parent_id',
'$reg',
'$name',
'$gender',
'$dob',
'$course',
'$department',
'$year',
'$phone',
'$email',
'$address',
'$blood_group',
'$join_date',
'active'
)
");

$student_id=mysqli_insert_id($conn);

/* ================= DEFAULT FEE ================= */

mysqli_query($conn,"
INSERT INTO fees
(student_id,fee_type,amount,due_date,status)

VALUES

(
'$student_id',
'Hostel Fee',
'5000',
CURDATE(),
'pending'
)
");

$msg="Student & Parent Created Successfully";

}

}
?>

<!DOCTYPE html>
<html>

<head>

<title>Add Student</title>

<script src="https://cdn.tailwindcss.com"></script>

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>

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

.password-box{
position:relative;
}

.eye-btn{
position:absolute;
right:15px;
top:50%;
transform:translateY(-50%);
background:none;
border:none;
color:#a1a1aa;
cursor:pointer;
font-size:18px;
}

.eye-btn:hover{
color:white;
}

</style>

</head>

<body class="text-zinc-200">

<div class="flex">

<?php include("layout/sidebar.php"); ?>

<div class="flex-1 p-10">

<?php include("layout/header.php"); ?>

<div class="mb-8">

<h1 class="text-4xl font-bold text-white">
Add New Student
</h1>

<p class="text-zinc-500 mt-2">
Create student and parent accounts
</p>

</div>

<form method="POST">

<div class="grid grid-cols-2 gap-8">

<!-- STUDENT DETAILS -->

<div class="card rounded-3xl p-8">

<h2 class="text-2xl font-bold text-white mb-6">
Student Details
</h2>

<div class="space-y-5">

<div>
<label class="block text-sm text-zinc-400 mb-2">
Full Name
</label>

<input
type="text"
name="name"
required
placeholder="Student Name"
class="input">
</div>

<div>
<label class="block text-sm text-zinc-400 mb-2">
Admission Number
</label>

<input
type="text"
name="reg"
required
placeholder="HH2026001"
class="input">
</div>

<div>
<label class="block text-sm text-zinc-400 mb-2">
Gender
</label>

<select
name="gender"
class="input">

<option value="male">Male</option>
<option value="female">Female</option>
<option value="other">Other</option>

</select>
</div>

<div>
<label class="block text-sm text-zinc-400 mb-2">
Date of Birth
</label>

<input
type="date"
name="dob"
required
max="<?php echo date('Y-m-d', strtotime('-15 years')); ?>"
min="1990-01-01"
title="Student age must be at least 15 years"
class="input">
</div>

<div>
<label class="block text-sm text-zinc-400 mb-2">
Course
</label>

<input
type="text"
name="course"
placeholder="BCA"
class="input">
</div>

<div>
<label class="block text-sm text-zinc-400 mb-2">
Department
</label>

<input
type="text"
name="department"
placeholder="Computer Department"
class="input">
</div>

<div>
<label class="block text-sm text-zinc-400 mb-2">
Year
</label>

<select
name="year"
required
class="input">

<option value="">Select Year</option>
<option value="1">1</option>
<option value="2">2</option>
<option value="3">3</option>

</select>
</div>

<div>
<label class="block text-sm text-zinc-400 mb-2">
Blood Group
</label>

<input
type="text"
name="blood_group"
placeholder="O+"
class="input">
</div>

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
class="input">
</div>

<div>
<label class="block text-sm text-zinc-400 mb-2">
Email
</label>

<input
type="email"
name="email"
required
placeholder="student@mail.com"
class="input">
</div>

<div>
<label class="block text-sm text-zinc-400 mb-2">
Address
</label>

<textarea
name="address"
rows="4"
class="input"
placeholder="Student Address"></textarea>
</div>

<div>
<label class="block text-sm text-zinc-400 mb-2">
Join Date
</label>

<input
type="date"
name="join_date"
value="<?php echo date('Y-m-d'); ?>"
class="input">
</div>

</div>

</div>

<!-- RIGHT SIDE -->

<div class="space-y-8">

<!-- PARENT -->

<div class="card rounded-3xl p-8">

<h2 class="text-2xl font-bold text-white mb-6">
Parent Details
</h2>

<div class="space-y-5">

<div>
<label class="block text-sm text-zinc-400 mb-2">
Parent Name
</label>

<input
type="text"
name="parent_name"
required
placeholder="Parent Name"
class="input">
</div>

<div>
<label class="block text-sm text-zinc-400 mb-2">
Phone
</label>

<input
type="tel"
name="parent_phone"
required
placeholder="9876543210"
pattern="[0-9]{10}"
maxlength="10"
inputmode="numeric"
title="Enter valid 10 digit phone number"
class="input">
</div>

</div>

</div>

<!-- STUDENT LOGIN -->

<div class="card rounded-3xl p-8">

<h2 class="text-2xl font-bold text-white mb-6">
Student Login 
</h2>

<div class="space-y-5">

<div>
<label class="block text-sm text-zinc-400 mb-2">
Username
</label>

<input
type="text"
name="stu_username"
required
placeholder="student"
class="input">
</div>

<div>
<label class="block text-sm text-zinc-400 mb-2">
Password
</label>

<div class="password-box">

<input
type="password"
id="stu_password"
name="stu_password"
required
placeholder="Enter Password"
minlength="8"
pattern="^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[@$!%*?&]).{8,}$"
title="Password must contain uppercase, lowercase, number and special character"
class="input">

<button
type="button"
class="eye-btn"
onclick="togglePassword('stu_password','eye1')">

<i id="eye1" class="fa-solid fa-eye"></i>

</button>

</div>

</div>

</div>

</div>

<!-- PARENT LOGIN -->

<div class="card rounded-3xl p-8">

<h2 class="text-2xl font-bold text-white mb-6">
Parent Login Creation
</h2>

<div class="space-y-5">

<div>
<label class="block text-sm text-zinc-400 mb-2">
Username
</label>

<input
type="text"
name="par_username"
required
placeholder="parent"
class="input">
</div>

<div>
<label class="block text-sm text-zinc-400 mb-2">
Password
</label>

<div class="password-box">

<input
type="password"
id="par_password"
name="par_password"
required
placeholder="Enter Password"
minlength="8"
pattern="^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[@$!%*?&]).{8,}$"
title="Password must contain uppercase, lowercase, number and special character"
class="input">

<button
type="button"
class="eye-btn"
onclick="togglePassword('par_password','eye2')">

<i id="eye2" class="fa-solid fa-eye"></i>

</button>

</div>

</div>

</div>

</div>

</div>

</div>

<div class="mt-8">

<button
name="save"
class="bg-emerald-600 hover:bg-emerald-500 transition px-10 py-4 rounded-2xl text-lg font-semibold">

Create Student

</button>

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

<script>

function togglePassword(passwordId, eyeId){

let password=document.getElementById(passwordId);
let eye=document.getElementById(eyeId);

if(password.type==="password"){

password.type="text";

eye.classList.remove("fa-eye");
eye.classList.add("fa-eye-slash");

}else{

password.type="password";

eye.classList.remove("fa-eye-slash");
eye.classList.add("fa-eye");

}

}

</script>

</body>
</html>