<?php
session_start();

if(!isset($_SESSION['user_id']) || $_SESSION['role']!="admin"){
header("Location: ../index.php");
exit();
}

include("../config/db.php");

$student_id=$_GET['id'];

/* ================= GET ACTIVE ROOM ================= */

$allocation=mysqli_fetch_assoc(mysqli_query($conn,"
SELECT *
FROM room_allocations
WHERE student_id='$student_id'
AND status='active'
"));

if($allocation){

$room_id=$allocation['room_id'];

/* remove allocation */

mysqli_query($conn,"
UPDATE room_allocations
SET status='removed'
WHERE allocation_id='".$allocation['allocation_id']."'
");

/* reduce occupancy */

mysqli_query($conn,"
UPDATE rooms
SET current_occupancy=current_occupancy-1
WHERE room_id='$room_id'
AND current_occupancy > 0
");

}

header("Location: students.php");
exit();

?>