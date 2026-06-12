<?php

include("../config/db.php");

$id=$_GET['id'];

mysqli_query($conn,"
DELETE FROM mess_menu
WHERE menu_id='$id'
");

header("Location: mess_menu.php");

?>