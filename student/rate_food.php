<?php
session_start();

include("../config/db.php");

if(!isset($_SESSION['user_id'])){
header("Location: ../index.php");
exit();
}

$user_id=$_SESSION['user_id'];

$student=mysqli_fetch_assoc(mysqli_query($conn,"
SELECT student_id FROM students
WHERE user_id='$user_id'
"));

$student_id=$student['student_id'];

$menu=$_POST['menu_id'];
$rating=$_POST['rating'];
$comments=$_POST['comments'];

mysqli_query($conn,"
INSERT INTO food_ratings
(student_id,menu_id,rating,comments)
VALUES
('$student_id','$menu','$rating','$comments')
");

header("Location: mess_menu.php");
exit();
?>