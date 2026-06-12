<?php

include("../config/db.php");

$id=$_GET['id'];

$checkout=date("Y-m-d H:i:s");

mysqli_query($conn,"
UPDATE visitors
SET check_out='$checkout'
WHERE visitor_id='$id'
");

header("Location: visitors.php");

?>