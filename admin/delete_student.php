<?php
session_start();

if(!isset($_SESSION['user_id']) || $_SESSION['role']!="admin"){
header("Location: ../index.php");
exit();
}

include("../config/db.php");

if(isset($_GET['id'])){

$student_id=$_GET['id'];

/* ================= GET USER ================= */

$get=mysqli_query($conn,"
SELECT user_id
FROM students
WHERE student_id='$student_id'
");

$data=mysqli_fetch_assoc($get);

$user_id=$data['user_id'];

/* ================= REMOVE ROOM ================= */

$allocation=mysqli_query($conn,"
SELECT *
FROM room_allocations
WHERE student_id='$student_id'
AND status='active'
");

if(mysqli_num_rows($allocation)>0){

$alloc=mysqli_fetch_assoc($allocation);

$room_id=$alloc['room_id'];

/* remove allocation */

mysqli_query($conn,"
UPDATE room_allocations
SET status='removed'
WHERE allocation_id='".$alloc['allocation_id']."'
");

/* update occupancy */

mysqli_query($conn,"
UPDATE rooms
SET current_occupancy=current_occupancy-1
WHERE room_id='$room_id'
AND current_occupancy > 0
");

}

/* ================= DELETE RELATED DATA ================= */

mysqli_query($conn,"
DELETE FROM attendance
WHERE student_id='$student_id'
");

mysqli_query($conn,"
DELETE FROM complaints
WHERE student_id='$student_id'
");

mysqli_query($conn,"
DELETE FROM fees
WHERE student_id='$student_id'
");

mysqli_query($conn,"
DELETE FROM leave_requests
WHERE student_id='$student_id'
");

mysqli_query($conn,"
DELETE FROM room_allocations
WHERE student_id='$student_id'
");

/* ================= DELETE STUDENT ================= */

mysqli_query($conn,"
DELETE FROM students
WHERE student_id='$student_id'
");

/* ================= DELETE USER ================= */

mysqli_query($conn,"
DELETE FROM users
WHERE user_id='$user_id'
");

/* ================= REDIRECT ================= */

header("Location: students.php");
exit();

}else{

header("Location: students.php");
exit();

}
?>