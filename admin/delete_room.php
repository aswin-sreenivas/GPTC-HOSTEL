<?php
session_start();

if(!isset($_SESSION['user_id']) || $_SESSION['role']!="admin"){
header("Location: ../index.php");
exit();
}

include("../config/db.php");

$id=$_GET['id'];

/* ================= CHECK ACTIVE STUDENTS ================= */

$check=mysqli_query($conn,"
SELECT *
FROM room_allocations
WHERE room_id='$id'
AND status='active'
");

if(mysqli_num_rows($check)>0){

echo "
<script>
alert('Cannot delete room. Students are still allocated.');
window.location='rooms.php';
</script>
";

exit();

}

/* ================= DELETE ROOM ================= */

mysqli_query($conn,"
DELETE FROM rooms
WHERE room_id='$id'
");

header("Location: rooms.php");
exit();

?>