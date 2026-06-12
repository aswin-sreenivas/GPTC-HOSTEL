<?php
session_start();

if(!isset($_SESSION['user_id']) || $_SESSION['role']!="head"){
header("Location: ../index.php");
exit();
}

include("../config/db.php");

/* ================= FEES ================= */

$fees=mysqli_query($conn,"
SELECT 

fees.*,
students.full_name,

fee_payments.verification_status,
fee_payments.amount_paid,
fee_payments.transaction_id

FROM fees

LEFT JOIN students
ON fees.student_id = students.student_id

LEFT JOIN fee_payments
ON fees.fee_id = fee_payments.fee_id

ORDER BY fees.due_date DESC
");

/* ================= TOTAL FEES ================= */

$total_query=mysqli_query($conn,"
SELECT SUM(amount) AS total
FROM fees
");

$total=mysqli_fetch_assoc($total_query);

/* ================= COLLECTED ================= */

$paid_query=mysqli_query($conn,"
SELECT SUM(amount_paid) AS total
FROM fee_payments
WHERE verification_status='approved'
");

$paid=mysqli_fetch_assoc($paid_query);

/* ================= PENDING ================= */

$pending_query=mysqli_query($conn,"
SELECT SUM(amount) AS total
FROM fees
WHERE fee_id NOT IN (

SELECT fee_id
FROM fee_payments
WHERE verification_status='approved'

)
");

$pending=mysqli_fetch_assoc($pending_query);

?>

<!DOCTYPE html>
<html>

<head>

<title>Fee Monitoring</title>

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
Fee Monitoring
</h1>

<p class="text-zinc-500 mt-2">
Monitor hostel fee collections and pending dues
</p>

</div>

<div class="text-sm text-zinc-500">
Read Only Access
</div>

</div>

<!-- STATS -->

<div class="grid grid-cols-3 gap-6 mb-8">

<div class="card rounded-2xl p-6">

<div class="text-zinc-400 text-sm">
Total Fees
</div>

<div class="text-4xl font-bold text-white mt-3">
₹<?php echo $total['total'] ? $total['total'] : 0; ?>
</div>

</div>

<div class="card rounded-2xl p-6">

<div class="text-zinc-400 text-sm">
Collected
</div>

<div class="text-4xl font-bold text-emerald-400 mt-3">
₹<?php echo $paid['total'] ? $paid['total'] : 0; ?>
</div>

</div>

<div class="card rounded-2xl p-6">

<div class="text-zinc-400 text-sm">
Pending
</div>

<div class="text-4xl font-bold text-red-400 mt-3">
₹<?php echo $pending['total'] ? $pending['total'] : 0; ?>
</div>

</div>

</div>

<!-- TABLE -->

<div class="card rounded-2xl overflow-hidden">

<table class="w-full text-left">

<thead class="bg-zinc-900 border-b border-zinc-800 text-zinc-400 text-sm">

<tr>

<th class="p-5">Student</th>
<th>Amount</th>
<th>Due Date</th>
<th>Transaction ID</th>
<th>Status</th>

</tr>

</thead>

<tbody>

<?php if(mysqli_num_rows($fees)>0){ ?>

<?php while($row=mysqli_fetch_assoc($fees)){ ?>

<tr class="border-b border-zinc-800 hover:bg-zinc-900 transition">

<td class="p-5">

<div class="font-semibold text-white">
<?php echo $row['full_name']; ?>
</div>

</td>

<td>

₹<?php echo $row['amount']; ?>

</td>

<td>

<?php echo date("d M Y",strtotime($row['due_date'])); ?>

</td>

<td>

<?php

echo $row['transaction_id']
? $row['transaction_id']
: "N/A";

?>

</td>

<td>

<?php

$status=strtolower($row['verification_status']);

if($status=="approved"){

echo "
<span class='bg-emerald-500/20 text-emerald-400 px-3 py-1 rounded-full text-xs'>
Paid
</span>
";

}elseif($status=="pending"){

echo "
<span class='bg-yellow-500/20 text-yellow-400 px-3 py-1 rounded-full text-xs'>
Under Review
</span>
";

}elseif($status=="rejected"){

echo "
<span class='bg-red-500/20 text-red-400 px-3 py-1 rounded-full text-xs'>
Rejected
</span>
";

}else{

echo "
<span class='bg-red-500/20 text-red-400 px-3 py-1 rounded-full text-xs'>
Pending
</span>
";

}

?>

</td>

</tr>

<?php } ?>

<?php } else { ?>

<tr>

<td colspan="5" class="p-10 text-center text-zinc-500">
No fee records found
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