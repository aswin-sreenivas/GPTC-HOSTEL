<?php
session_start();

if(!isset($_SESSION['user_id']) || $_SESSION['role']!="admin"){
header("Location: ../index.php");
exit();
}

include("../config/db.php");

$allocation_id=$_GET['allocation_id'];
$room_id=$_GET['room_id'];

/* ================= REMOVE STUDENT ================= */

mysqli_query($conn,"
UPDATE room_allocations
SET status='removed'
WHERE allocation_id='$allocation_id'
");

/* ================= UPDATE ROOM ================= */

mysqli_query($conn,"
UPDATE rooms
SET current_occupancy=current_occupancy-1
WHERE room_id='$room_id'
AND current_occupancy > 0
");

/* ================= REDIRECT ================= */

header("Location: rooms.php");
exit();

?>