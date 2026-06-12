<?php

include("../config/db.php");

$id=$_GET['id'];

$menu=mysqli_fetch_assoc(mysqli_query($conn,"
SELECT * FROM mess_menu
WHERE menu_id='$id'
"));

if(isset($_POST['update'])){

$breakfast=$_POST['breakfast'];
$lunch=$_POST['lunch'];
$dinner=$_POST['dinner'];
$snacks=$_POST['snacks'];

mysqli_query($conn,"
UPDATE mess_menu
SET breakfast='$breakfast',
lunch='$lunch',
dinner='$dinner',
snacks='$snacks'
WHERE menu_id='$id'
");

header("Location: mess_menu.php");

}
?>