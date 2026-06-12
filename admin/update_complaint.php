<?php
session_start();

if(!isset($_SESSION['user_id']) || $_SESSION['role']!="admin"){
header("Location: ../index.php");
}

include("../config/db.php");

$id=$_GET['id'];

$complaint=mysqli_fetch_assoc(mysqli_query($conn,"
SELECT complaints.*,students.full_name
FROM complaints
LEFT JOIN students
ON complaints.student_id=students.student_id
WHERE complaint_id='$id'
"));

if(isset($_POST['update'])){

$status=$_POST['status'];
$message=$_POST['message'];
$admin=$_SESSION['user_id'];

mysqli_query($conn,"
INSERT INTO complaint_updates
(complaint_id,updated_by,update_message,status)
VALUES
('$id','$admin','$message','$status')
");

mysqli_query($conn,"
UPDATE complaints
SET status='$status'
WHERE complaint_id='$id'
");

$msg="Complaint Updated Successfully";

}

?>

<!DOCTYPE html>
<html>

<head>

<title>Update Complaint</title>

<script src="https://cdn.tailwindcss.com"></script>

</head>

<body class="bg-black text-zinc-200">

<div class="flex">

<?php include("layout/sidebar.php"); ?>

<div class="flex-1 p-10">

<?php include("layout/header.php"); ?>

<h1 class="text-3xl font-bold mb-6">
Update Complaint
</h1>

<?php if(isset($msg)){ ?>

<div class="bg-emerald-600/20 text-emerald-400 p-3 rounded mb-6">
<?php echo $msg; ?>
</div>

<?php } ?>

<div class="bg-zinc-900 border border-zinc-800 p-8 rounded-xl mb-6">

<h2 class="text-xl font-semibold mb-2">
<?php echo $complaint['title']; ?>
</h2>

<p class="text-zinc-400 mb-2">
Student: <?php echo $complaint['full_name']; ?>
</p>

<p class="text-zinc-400">
<?php echo $complaint['description']; ?>
</p>

</div>

<form method="POST" class="bg-zinc-900 border border-zinc-800 p-8 rounded-xl space-y-6">

<div>

<label class="text-sm text-zinc-400">
Update Status
</label>

<select name="status" class="w-full bg-zinc-800 p-3 rounded mt-1">

<option value="pending">Pending</option>
<option value="in_progress">In Progress</option>
<option value="resolved">Resolved</option>

</select>

</div>

<div>

<label class="text-sm text-zinc-400">
Update Message
</label>

<textarea name="message" required class="w-full bg-zinc-800 p-3 rounded mt-1"></textarea>

</div>

<button name="update" class="bg-emerald-600 hover:bg-emerald-500 px-6 py-3 rounded-lg text-white">

Update Complaint

</button>

</form>

</div>

</div>

</body>
</html>