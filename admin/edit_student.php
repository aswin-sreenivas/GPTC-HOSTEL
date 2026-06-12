<?php
session_start();

if(!isset($_SESSION['user_id']) || $_SESSION['role']!="admin"){
header("Location: ../index.php");
exit();
}

include("../config/db.php");

$id = $_GET['id'];

/* FETCH STUDENT */

$student = mysqli_fetch_assoc(mysqli_query($conn,"
SELECT * FROM students WHERE student_id='$id'
"));

if(!$student){
die("Student not found");
}

/* UPDATE STUDENT */

if(isset($_POST['update'])){

$name = trim($_POST['name']);
$email = trim($_POST['email']);
$phone = trim($_POST['phone']);
$address = trim($_POST['address']);

/* VALIDATIONS */

if(!preg_match("/^[a-zA-Z ]+$/",$name)){
$error = "Name should contain only letters";
}

elseif(!filter_var($email,FILTER_VALIDATE_EMAIL)){
$error = "Invalid email format";
}

elseif(!preg_match("/^[0-9]{10}$/",$phone)){
$error = "Phone number must be 10 digits";
}

else{

mysqli_query($conn,"
UPDATE students
SET
full_name='$name',
email='$email',
phone='$phone',
address='$address'
WHERE student_id='$id'
");

header("Location: students.php");
exit();

}

}

?>

<!DOCTYPE html>
<html>

<head>

<title>Edit Student</title>

<script src="https://cdn.tailwindcss.com"></script>

</head>

<body class="bg-black text-zinc-200">

<div class="flex">

<?php include("layout/sidebar.php"); ?>

<div class="flex-1 p-10">

<h1 class="text-3xl font-bold mb-6">
Edit Student
</h1>

<div class="bg-zinc-900 p-6 rounded-xl max-w-2xl">

<?php if(isset($error)){ ?>

<div class="bg-red-600 text-white p-3 rounded mb-4">
<?php echo $error; ?>
</div>

<?php } ?>

<form method="POST" class="space-y-4">

<!-- NAME -->

<input
type="text"
name="name"
required
value="<?php echo $student['full_name']; ?>"
placeholder="Full Name"
pattern="[A-Za-z ]+"
title="Only letters allowed"
class="w-full bg-zinc-800 p-3 rounded outline-none"
/>

<!-- EMAIL -->

<input
type="email"
name="email"
required
value="<?php echo $student['email']; ?>"
placeholder="Email Address"
class="w-full bg-zinc-800 p-3 rounded outline-none"
/>

<!-- PHONE -->

<input
type="tel"
name="phone"
required
value="<?php echo $student['phone']; ?>"
placeholder="9876543210"
pattern="[0-9]{10}"
maxlength="10"
inputmode="numeric"
title="Enter valid 10 digit phone number"
class="w-full bg-zinc-800 p-3 rounded outline-none"
oninput="this.value=this.value.replace(/[^0-9]/g,'')"
/>

<!-- ADDRESS -->

<textarea
name="address"
required
placeholder="Address"
class="w-full bg-zinc-800 p-3 rounded outline-none h-28"
><?php echo $student['address']; ?></textarea>

<!-- BUTTON -->

<button
type="submit"
name="update"
class="bg-emerald-600 hover:bg-emerald-700 px-6 py-3 rounded text-white font-semibold"
>
Update Student
</button>

</form>

</div>

</div>

</div>

</body>
</html>