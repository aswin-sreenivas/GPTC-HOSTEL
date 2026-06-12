<?php
session_start();

if(!isset($_SESSION['user_id']) || $_SESSION['role']!="student"){
header("Location: ../index.php");
exit();
}

include("../config/db.php");

$user_id=$_SESSION['user_id'];

/* get student id */

$student=mysqli_fetch_assoc(mysqli_query($conn,"
SELECT student_id FROM students
WHERE user_id='$user_id'
"));

$student_id=$student['student_id'];

/* handle rating */

if(isset($_POST['rate'])){

$menu_id=$_POST['menu_id'];
$rating=$_POST['rating'];
$comments=$_POST['comments'];

$check=mysqli_query($conn,"
SELECT * FROM food_ratings
WHERE student_id='$student_id'
AND menu_id='$menu_id'
");

if(mysqli_num_rows($check)>0){

mysqli_query($conn,"
UPDATE food_ratings
SET rating='$rating', comments='$comments'
WHERE student_id='$student_id'
AND menu_id='$menu_id'
");

}else{

mysqli_query($conn,"
INSERT INTO food_ratings
(student_id,menu_id,rating,comments)
VALUES
('$student_id','$menu_id','$rating','$comments')
");

}

}

/* get menus */

$menus=mysqli_query($conn,"SELECT * FROM mess_menu ORDER BY FIELD(day_of_week,
'Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday')");

?>

<!DOCTYPE html>
<html>

<head>

<title>Mess Menu</title>

<script src="https://cdn.tailwindcss.com"></script>

</head>

<body class="bg-black text-zinc-200">

<div class="flex">

<?php include("layout/sidebar.php"); ?>

<div class="flex-1 p-10">

<?php include("layout/header.php"); ?>

<h1 class="text-3xl font-bold text-white mb-2">
Mess Menu
</h1>

<p class="text-zinc-500 mb-8">
View the weekly hostel meal schedule.
</p>

<div class="grid grid-cols-3 gap-6">

<?php while($row=mysqli_fetch_assoc($menus)){ 

/* average rating */

$avg=mysqli_fetch_assoc(mysqli_query($conn,"
SELECT AVG(rating) as avg_rating
FROM food_ratings
WHERE menu_id='".$row['menu_id']."'
"));

$rating=round($avg['avg_rating'],1);

?>

<div class="bg-zinc-900 border border-zinc-800 rounded-xl p-6">

<h2 class="text-xl font-semibold text-white mb-2">
<?php echo $row['day_of_week']; ?>
</h2>

<div class="text-yellow-400 text-sm mb-4">

<?php
if($rating){
echo "⭐ ".$rating." / 5";
}else{
echo "No ratings yet";
}
?>

</div>

<div class="space-y-4 text-sm">

<div>
<div class="text-zinc-400">Breakfast</div>
<div class="text-white"><?php echo $row['breakfast']; ?></div>
</div>

<div>
<div class="text-zinc-400">Lunch</div>
<div class="text-white"><?php echo $row['lunch']; ?></div>
</div>

<div>
<div class="text-zinc-400">Dinner</div>
<div class="text-white"><?php echo $row['dinner']; ?></div>
</div>

<div>
<div class="text-zinc-400">Snacks</div>
<div class="text-white"><?php echo $row['snacks']; ?></div>
</div>

</div>

<!-- rating form -->

<form method="POST" class="mt-6 space-y-2">

<input type="hidden" name="menu_id" value="<?php echo $row['menu_id']; ?>">

<select name="rating" class="bg-zinc-800 p-2 rounded w-full">

<option value="5">⭐⭐⭐⭐⭐ Excellent</option>
<option value="4">⭐⭐⭐⭐ Good</option>
<option value="3">⭐⭐⭐ Average</option>
<option value="2">⭐⭐ Poor</option>
<option value="1">⭐ Bad</option>

</select>

<textarea
name="comments"
placeholder="Optional comment..."
class="bg-zinc-800 p-2 rounded w-full text-sm"
></textarea>

<button name="rate"
class="bg-emerald-600 px-4 py-2 rounded text-sm w-full">
Submit Rating
</button>

</form>

</div>

<?php } ?>

</div>

</div>

</div>

</body>
</html>