<?php
session_start();

if(!isset($_SESSION['user_id']) || $_SESSION['role']!="parent"){
header("Location: ../index.php");
exit();
}

include("../config/db.php");

$user_id=$_SESSION['user_id'];

/* get student via parent */

$result=mysqli_query($conn,"
SELECT students.student_id
FROM students
LEFT JOIN parents
ON students.parent_id = parents.parent_id
WHERE parents.user_id='$user_id'
");

if(!$result){
die("SQL Error: ".mysqli_error($conn));
}

$student=mysqli_fetch_assoc($result);

if(!$student){
die("No student linked to this parent.");
}

$student_id=$student['student_id'];

/* get complaints */

$complaints=mysqli_query($conn,"
SELECT * FROM complaints
WHERE student_id='$student_id'
ORDER BY created_at DESC
");
?>

<!DOCTYPE html>
<html>

<head>
<title>Complaints</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-black text-zinc-200">

<div class="flex">

<?php include("layout/sidebar.php"); ?>

<div class="flex-1 p-10">

<h1 class="text-3xl font-bold mb-6">Complaints</h1>

<?php if(mysqli_num_rows($complaints)>0){ ?>

<?php while($row=mysqli_fetch_assoc($complaints)){ ?>

<div class="bg-zinc-900 p-6 mb-4 rounded-xl">

<h2 class="text-white"><?php echo $row['title']; ?></h2>

<p class="text-zinc-400"><?php echo $row['description']; ?></p>

<span class="text-sm text-zinc-500"><?php echo $row['status']; ?></span>

</div>

<?php } ?>

<?php } else { ?>

<div class="text-zinc-500 p-6">
No complaints found
</div>

<?php } ?>

</div>

</div>

</body>
</html>