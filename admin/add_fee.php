<?php
session_start();
if(!isset($_SESSION['user_id']) || $_SESSION['role']!="admin"){
header("Location: ../index.php");
}

include("../config/db.php");

$students=mysqli_query($conn,"SELECT * FROM students");

if(isset($_POST['save'])){

$student=$_POST['student'];
$type=$_POST['type'];
$amount=$_POST['amount'];
$due=$_POST['due'];

mysqli_query($conn,"INSERT INTO fees
(student_id,fee_type,amount,due_date,status)
VALUES
('$student','$type','$amount','$due','pending')");

$msg="Fee record added successfully";

}
?>

<!DOCTYPE html>
<html>
<head>
<title>Add Fee</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-black text-zinc-200">

<div class="flex">

<?php include("layout/sidebar.php"); ?>

<div class="flex-1 p-10">

<?php include("layout/header.php"); ?>

<h1 class="text-3xl font-bold mb-6">Add Fee Record</h1>

<?php if(isset($msg)){ ?>
<div class="bg-emerald-600/20 text-emerald-400 p-3 rounded mb-6">
<?php echo $msg; ?>
</div>
<?php } ?>

<form method="POST" class="bg-zinc-900 border border-zinc-800 p-8 rounded-xl grid grid-cols-2 gap-6">

<div>
<label class="text-sm text-zinc-400">Student</label>
<select name="student" class="w-full bg-zinc-800 p-3 rounded mt-1">

<?php while($s=mysqli_fetch_assoc($students)){ ?>

<option value="<?php echo $s['student_id']; ?>">
<?php echo $s['full_name']; ?>
</option>

<?php } ?>

</select>
</div>

<div>
<label class="text-sm text-zinc-400">Fee Type</label>
<input type="text" name="type" class="w-full bg-zinc-800 p-3 rounded mt-1">
</div>

<div>
<label class="text-sm text-zinc-400">Amount</label>
<input type="number" name="amount" class="w-full bg-zinc-800 p-3 rounded mt-1">
</div>

<div>
<label class="text-sm text-zinc-400">Due Date</label>
<input type="date" name="due" class="w-full bg-zinc-800 p-3 rounded mt-1">
</div>

<div class="col-span-2">

<button name="save" class="bg-emerald-600 px-6 py-3 rounded-lg text-white">
Add Fee
</button>

</div>

</form>

</div>
</div>
</body>
</html>