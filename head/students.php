<?php
session_start();

if(!isset($_SESSION['user_id']) || $_SESSION['role']!="head"){
header("Location: ../index.php");
exit();
}

include("../config/db.php");

/* blocks */

$blocks=mysqli_query($conn,"
SELECT * FROM hostel_blocks
ORDER BY block_name ASC
");

/* filter */

$block_filter="";

if(isset($_GET['block']) && $_GET['block']!=""){

$block=$_GET['block'];

$block_filter=" AND hostel_blocks.block_id='$block' ";

}

/* students */

$students=mysqli_query($conn,"
SELECT students.*, rooms.room_number, hostel_blocks.block_name

FROM students

LEFT JOIN room_allocations
ON students.student_id = room_allocations.student_id
AND room_allocations.status='active'

LEFT JOIN rooms
ON room_allocations.room_id = rooms.room_id

LEFT JOIN hostel_blocks
ON rooms.block_id = hostel_blocks.block_id

WHERE 1=1 $block_filter

ORDER BY students.student_id DESC
");

$total_students=mysqli_num_rows($students);

?>

<!DOCTYPE html>
<html>

<head>

<title>Students Monitoring</title>

<script src="https://cdn.tailwindcss.com"></script>

<style>

body{
background:#020617;
font-family:system-ui;
}

.card{
background:#18181b;
border:1px solid #27272a;
}

</style>

</head>

<body class="text-zinc-200">

<div class="flex">

<!-- SIDEBAR -->

<?php include("layout/sidebar.php"); ?>

<!-- MAIN -->

<div class="flex-1 p-10">

<!-- HEADER -->

<div class="flex justify-between items-center mb-8">

<div>

<h1 class="text-4xl font-bold text-white">
Students Monitoring
</h1>

<p class="text-zinc-500 mt-2">
Monitor hostel students and allocations
</p>

</div>

<div class="bg-zinc-900 border border-zinc-800 px-5 py-3 rounded-xl">

<div class="text-sm text-zinc-400">
Filtered Students
</div>

<div class="font-semibold text-lg">
<?php echo $total_students; ?>
</div>

</div>

</div>

<!-- FILTER + SEARCH -->

<div class="card rounded-2xl p-5 mb-8">

<div class="flex justify-between items-center gap-4">

<!-- SEARCH -->

<input
type="text"
id="searchInput"
placeholder="Search student..."
class="bg-zinc-800 border border-zinc-700 px-4 py-3 rounded-xl w-full outline-none"
/>

<!-- BLOCK FILTER -->

<form method="GET">

<select
name="block"
onchange="this.form.submit()"
class="bg-zinc-800 border border-zinc-700 px-4 py-3 rounded-xl outline-none">

<option value="">
All Blocks
</option>

<?php while($b=mysqli_fetch_assoc($blocks)){ ?>

<option
value="<?php echo $b['block_id']; ?>"
<?php if(isset($_GET['block']) && $_GET['block']==$b['block_id']) echo "selected"; ?>>

<?php echo $b['block_name']; ?>

</option>

<?php } ?>

</select>

</form>

<div class="text-zinc-500 text-sm whitespace-nowrap">
Read Only
</div>

</div>

</div>

<!-- TABLE -->

<div class="card rounded-2xl overflow-hidden">

<table class="w-full text-left">

<thead class="bg-zinc-900 border-b border-zinc-800 text-zinc-400 text-sm">

<tr>

<th class="p-5">Student</th>
<th>Admission</th>
<th>Phone</th>
<th>Room</th>
<th>Block</th>
<th>Status</th>
<th class="text-center">Action</th>

</tr>

</thead>

<tbody id="studentTable">

<?php if(mysqli_num_rows($students)>0){ ?>

<?php while($row=mysqli_fetch_assoc($students)){ ?>

<tr class="border-b border-zinc-800 hover:bg-zinc-900 transition">

<td class="p-5">

<div class="font-semibold text-white">
<?php echo $row['full_name']; ?>
</div>

<div class="text-sm text-zinc-500">
<?php echo $row['email']; ?>
</div>

</td>

<td>
<?php echo $row['admission_no']; ?>
</td>

<td>
<?php echo $row['phone']; ?>
</td>

<td>

<?php
echo $row['room_number']
? "Room ".$row['room_number']
: "Not Assigned";
?>

</td>

<td>

<?php
echo $row['block_name']
? $row['block_name']
: "N/A";
?>

</td>

<td>

<span class="bg-emerald-500/20 text-emerald-400 px-3 py-1 rounded-full text-xs">
Active
</span>

</td>

<td class="text-center">

<a
href="student_details.php?id=<?php echo $row['student_id']; ?>"
class="bg-emerald-600 hover:bg-emerald-500 px-4 py-2 rounded-lg text-sm transition">

View Details

</a>

</td>

</tr>

<?php } ?>

<?php } else { ?>

<tr>

<td colspan="7" class="p-10 text-center text-zinc-500">

No students found

</td>

</tr>

<?php } ?>

</tbody>

</table>

</div>

</div>

</div>

<!-- SEARCH SCRIPT -->

<script>

const searchInput=document.getElementById("searchInput");

searchInput.addEventListener("keyup",function(){

let filter=searchInput.value.toLowerCase();

let rows=document.querySelectorAll("#studentTable tr");

rows.forEach(row=>{

let text=row.innerText.toLowerCase();

if(text.includes(filter)){

row.style.display="";

}else{

row.style.display="none";

}

});

});

</script>

</body>
</html>