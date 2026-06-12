<?php
session_start();
if(!isset($_SESSION['user_id']) || $_SESSION['role']!="admin"){
header("Location: ../index.php");
}

include("../config/db.php");

$id=$_GET['id'];

$fee=mysqli_fetch_assoc(mysqli_query($conn,"
SELECT fees.*,students.full_name
FROM fees
LEFT JOIN students
ON fees.student_id=students.student_id
WHERE fee_id='$id'
"));

if(isset($_POST['pay'])){

$amount=$_POST['amount'];
$method=$_POST['method'];
$admin=$_SESSION['user_id'];
$date=date("Y-m-d H:i:s");

mysqli_query($conn,"INSERT INTO fee_payments
(fee_id,student_id,amount_paid,payment_method,payment_date,received_by)
VALUES
('$id','".$fee['student_id']."','$amount','$method','$date','$admin')
");

mysqli_query($conn,"
UPDATE fees
SET status='paid'
WHERE fee_id='$id'
");

$msg="Payment recorded successfully";

}
?>

<!DOCTYPE html>
<html>
<head>
<title>Pay Fee</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-black text-zinc-200">

<div class="flex">

<?php include("layout/sidebar.php"); ?>

<div class="flex-1 p-10">

<?php include("layout/header.php"); ?>

<h1 class="text-3xl font-bold mb-6">Fee Payment</h1>

<div class="bg-zinc-900 border border-zinc-800 p-8 rounded-xl mb-6">

<p class="text-white font-semibold">
Student: <?php echo $fee['full_name']; ?>
</p>

<p class="text-zinc-400">
Amount: ₹<?php echo $fee['amount']; ?>
</p>

</div>

<?php if(isset($msg)){ ?>

<div class="bg-emerald-600/20 text-emerald-400 p-3 rounded mb-6">
<?php echo $msg; ?>
</div>

<?php } ?>

<form method="POST" class="bg-zinc-900 border border-zinc-800 p-8 rounded-xl space-y-6">

<div>
<label class="text-sm text-zinc-400">Amount Paid</label>
<input type="number" name="amount" class="w-full bg-zinc-800 p-3 rounded mt-1">
</div>

<div>
<label class="text-sm text-zinc-400">Payment Method</label>

<select name="method" class="w-full bg-zinc-800 p-3 rounded mt-1">

<option value="cash">Cash</option>
<option value="upi">UPI</option>
<option value="bank">Bank Transfer</option>

</select>

</div>

<button name="pay" class="bg-emerald-600 px-6 py-3 rounded-lg text-white">

Confirm Payment

</button>

</form>

</div>
</div>
</body>
</html>