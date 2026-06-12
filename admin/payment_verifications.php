<?php
session_start();

if(!isset($_SESSION['user_id']) || $_SESSION['role']!="admin"){
header("Location: ../index.php");
exit();
}

include("../config/db.php");

$admin_id=$_SESSION['user_id'];

$success="";

/* ================= APPROVE ================= */

if(isset($_GET['approve'])){

$id=$_GET['approve'];

/* payment details */

$payment=mysqli_fetch_assoc(mysqli_query($conn,"
SELECT * FROM fee_payments
WHERE payment_id='$id'
"));

if($payment){

$fee_id=$payment['fee_id'];

mysqli_query($conn,"
UPDATE fee_payments
SET verification_status='approved',
verified_by='$admin_id',
verified_at=NOW()
WHERE payment_id='$id'
");

/* update fee */

mysqli_query($conn,"
UPDATE fees
SET status='paid'
WHERE fee_id='$fee_id'
");

$success="Payment approved successfully.";

}

}

/* ================= REJECT ================= */

if(isset($_GET['reject'])){

$id=$_GET['reject'];

mysqli_query($conn,"
UPDATE fee_payments
SET verification_status='rejected',
verified_by='$admin_id',
verified_at=NOW()
WHERE payment_id='$id'
");

$success="Payment rejected.";

}

/* ================= PAYMENTS ================= */

$payments=mysqli_query($conn,"
SELECT fee_payments.*, students.full_name,
fees.fee_type

FROM fee_payments

LEFT JOIN students
ON fee_payments.student_id = students.student_id

LEFT JOIN fees
ON fee_payments.fee_id = fees.fee_id

ORDER BY fee_payments.payment_id DESC
");

?>

<!DOCTYPE html>
<html>

<head>

<title>Payment Verifications</title>

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

<?php include("layout/sidebar.php"); ?>

<div class="flex-1 p-10">

<?php include("layout/header.php"); ?>

<!-- TITLE -->

<div class="flex justify-between items-center mb-8">

<div>

<h1 class="text-4xl font-bold text-white">
Payment Verifications
</h1>

<p class="text-zinc-500 mt-2">
Verify student fee payment proofs
</p>

</div>

</div>

<!-- SUCCESS -->

<?php if($success!=""){ ?>

<div class="bg-emerald-500/20 border border-emerald-500/30 text-emerald-400 p-4 rounded-xl mb-6">

<?php echo $success; ?>

</div>

<?php } ?>

<!-- TABLE -->

<div class="card rounded-2xl overflow-hidden">

<table class="w-full text-left">

<thead class="bg-zinc-900 border-b border-zinc-800 text-zinc-400 text-sm">

<tr>

<th class="p-5">Student</th>
<th>Fee</th>
<th>Amount</th>
<th>Transaction ID</th>
<th>Screenshot</th>
<th>Status</th>
<th class="text-center">Action</th>

</tr>

</thead>

<tbody>

<?php if(mysqli_num_rows($payments)>0){ ?>

<?php while($row=mysqli_fetch_assoc($payments)){ ?>

<tr class="border-b border-zinc-800 hover:bg-zinc-900 transition">

<!-- STUDENT -->

<td class="p-5">

<div class="font-semibold text-white">
<?php echo $row['full_name']; ?>
</div>

</td>

<!-- FEE -->

<td>

<?php echo $row['fee_type']; ?>

</td>

<!-- AMOUNT -->

<td>

₹<?php echo $row['amount_paid']; ?>

</td>

<!-- TXN -->

<td>

<div class="font-mono text-sm text-emerald-400">

<?php echo $row['transaction_id']; ?>

</div>

</td>

<!-- SCREENSHOT -->

<td>

<?php if($row['payment_screenshot']!=""){ ?>

<a
href="../uploads/payments/<?php echo $row['payment_screenshot']; ?>"
target="_blank"
class="bg-zinc-800 hover:bg-zinc-700 px-4 py-2 rounded-lg text-sm inline-block">

View Proof

</a>

<?php } else { ?>

<span class="text-zinc-500">
No File
</span>

<?php } ?>

</td>

<!-- STATUS -->

<td>

<?php if($row['verification_status']=="pending"){ ?>

<span class="bg-yellow-500/20 text-yellow-400 px-3 py-1 rounded-full text-xs">
Pending
</span>

<?php } ?>

<?php if($row['verification_status']=="approved"){ ?>

<span class="bg-emerald-500/20 text-emerald-400 px-3 py-1 rounded-full text-xs">
Approved
</span>

<?php } ?>

<?php if($row['verification_status']=="rejected"){ ?>

<span class="bg-red-500/20 text-red-400 px-3 py-1 rounded-full text-xs">
Rejected
</span>

<?php } ?>

</td>

<!-- ACTION -->

<td class="text-center">

<?php if($row['verification_status']=="pending"){ ?>

<div class="flex justify-center gap-2">

<a
href="?approve=<?php echo $row['payment_id']; ?>"
class="bg-emerald-600 hover:bg-emerald-500 px-4 py-2 rounded-lg text-sm transition">

Approve

</a>

<a
href="?reject=<?php echo $row['payment_id']; ?>"
class="bg-red-600 hover:bg-red-500 px-4 py-2 rounded-lg text-sm transition">

Reject

</a>

</div>

<?php } else { ?>

<span class="text-zinc-500 text-sm">
Completed
</span>

<?php } ?>

</td>

</tr>

<?php } ?>

<?php } else { ?>

<tr>

<td colspan="7" class="p-10 text-center text-zinc-500">

No payment records found

</td>

</tr>

<?php } ?>

</tbody>

</table>

</div>

</div>

</div>

</body>
</html>