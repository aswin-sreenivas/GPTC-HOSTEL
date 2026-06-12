<?php
session_start();

if(!isset($_SESSION['user_id']) || $_SESSION['role']!="student"){
header("Location: ../index.php");
exit();
}

include("../config/db.php");

$user_id=$_SESSION['user_id'];

/* ================= GET STUDENT ================= */

$student=mysqli_fetch_assoc(mysqli_query($conn,"
SELECT student_id FROM students
WHERE user_id='$user_id'
"));

if(!$student){
die("Student record not found.");
}

$student_id=$student['student_id'];

$success="";
$error="";

/* ================= SUBMIT PAYMENT ================= */

if(isset($_POST['submit_payment'])){

$fee_id=$_POST['fee_id'];
$amount=$_POST['amount'];
$transaction_id=trim($_POST['transaction_id']);

$screenshot="";

/* CHECK EXISTING PENDING */

$existing=mysqli_fetch_assoc(mysqli_query($conn,"
SELECT * FROM fee_payments
WHERE fee_id='$fee_id'
AND student_id='$student_id'
AND verification_status='pending'
"));

if($existing){

$error="Payment proof already submitted. Wait for admin verification.";

}else{

/* upload screenshot */

if(isset($_FILES['screenshot']) && $_FILES['screenshot']['name']!=""){

$filename=time()."_".$_FILES['screenshot']['name'];

$tmp=$_FILES['screenshot']['tmp_name'];

move_uploaded_file($tmp,"../uploads/payments/".$filename);

$screenshot=$filename;

}

/* insert payment */

mysqli_query($conn,"
INSERT INTO fee_payments
(
fee_id,
student_id,
amount_paid,
payment_method,
transaction_id,
payment_date,
payment_screenshot,
verification_status
)

VALUES
(
'$fee_id',
'$student_id',
'$amount',
'UPI',
'$transaction_id',
NOW(),
'$screenshot',
'pending'
)
");

$success="Payment proof submitted successfully.";

}

}

/* ================= FEES ================= */

$fees=mysqli_query($conn,"
SELECT * FROM fees
WHERE student_id='$student_id'
ORDER BY due_date DESC
");

/* ================= TOTALS ================= */

$paid=mysqli_fetch_assoc(mysqli_query($conn,"
SELECT SUM(amount) AS total
FROM fees
WHERE student_id='$student_id'
AND status='paid'
"));

$pending=mysqli_fetch_assoc(mysqli_query($conn,"
SELECT SUM(amount) AS total
FROM fees
WHERE student_id='$student_id'
AND status='pending'
"));

?>

<!DOCTYPE html>
<html>

<head>

<title>Fee Payment Center</title>

<script src="https://cdn.tailwindcss.com"></script>

<style>

body{
background:#020617;
font-family:system-ui;
}

/* CARD */

.fee-card{
background:linear-gradient(145deg,#18181b,#0f0f0f);
border:1px solid #27272a;
transition:0.3s;
position:relative;
overflow:hidden;
}

.fee-card:hover{
border-color:rgba(16,185,129,0.3);
transform:translateY(-3px);
}

.fee-card::before{
content:'';
position:absolute;
width:180px;
height:180px;
background:rgba(16,185,129,0.04);
border-radius:999px;
top:-60px;
right:-60px;
filter:blur(30px);
}

/* SCROLL */

::-webkit-scrollbar{
width:6px;
}

::-webkit-scrollbar-thumb{
background:#27272a;
border-radius:999px;
}

</style>

</head>

<body class="text-zinc-200">

<div class="flex min-h-screen">

<!-- SIDEBAR -->

<?php include("layout/sidebar.php"); ?>

<!-- MAIN -->

<div class="flex-1 p-8 overflow-y-auto">

<?php include("layout/header.php"); ?>

<!-- HEADER -->

<div class="mb-10">

<h1 class="text-5xl font-black text-white tracking-tight">
Fee Payment Center
</h1>

<p class="text-zinc-500 mt-3 text-lg">
Manage hostel fee payments and verification.
</p>

</div>

<!-- ALERTS -->

<?php if($success!=""){ ?>

<div class="bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 p-4 rounded-2xl mb-8">

<?php echo $success; ?>

</div>

<?php } ?>

<?php if($error!=""){ ?>

<div class="bg-red-500/10 border border-red-500/20 text-red-400 p-4 rounded-2xl mb-8">

<?php echo $error; ?>

</div>

<?php } ?>

<!-- STATS -->

<div class="grid lg:grid-cols-2 grid-cols-1 gap-6 mb-10">

<!-- PAID -->

<div class="fee-card rounded-3xl p-7">

<div class="flex justify-between items-center">

<div>

<p class="text-zinc-500 text-xs uppercase tracking-[3px]">
Total Paid
</p>

<h2 class="text-5xl font-black text-emerald-400 mt-4">

₹<?php echo $paid['total'] ? $paid['total'] : 0; ?>

</h2>

<p class="text-zinc-500 mt-3 text-sm">
Verified payments
</p>

</div>

<div class="w-20 h-20 rounded-3xl bg-emerald-500/10 flex items-center justify-center text-4xl">

✅

</div>

</div>

</div>

<!-- PENDING -->

<div class="fee-card rounded-3xl p-7">

<div class="flex justify-between items-center">

<div>

<p class="text-zinc-500 text-xs uppercase tracking-[3px]">
Pending Fees
</p>

<h2 class="text-5xl font-black text-yellow-400 mt-4">

₹<?php echo $pending['total'] ? $pending['total'] : 0; ?>

</h2>

<p class="text-zinc-500 mt-3 text-sm">
Awaiting payment
</p>

</div>

<div class="w-20 h-20 rounded-3xl bg-yellow-500/10 flex items-center justify-center text-4xl">

💳

</div>

</div>

</div>

</div>

<!-- GRID -->

<div class="grid 2xl:grid-cols-3 xl:grid-cols-2 grid-cols-1 gap-6">

<?php while($row=mysqli_fetch_assoc($fees)){ ?>

<?php

/* CHECK PAYMENT */

$check_payment=mysqli_fetch_assoc(mysqli_query($conn,"
SELECT * FROM fee_payments
WHERE fee_id='".$row['fee_id']."'
AND student_id='$student_id'
ORDER BY payment_id DESC
LIMIT 1
"));

?>

<!-- CARD -->

<div class="fee-card rounded-3xl p-6">

<!-- TOP -->

<div class="flex justify-between items-start mb-5 relative z-10">

<div>

<p class="text-zinc-500 text-xs uppercase tracking-[3px] mb-2">
Hostel Fee
</p>

<h2 class="text-2xl font-black text-white leading-tight">

<?php echo $row['fee_type']; ?>

</h2>

<p class="text-zinc-500 text-sm mt-2">

Due:
<?php echo date("d M Y",strtotime($row['due_date'])); ?>

</p>

</div>

<!-- STATUS -->

<?php if($row['status']=="paid"){ ?>

<div class="bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 px-4 py-2 rounded-2xl text-xs font-semibold">
Approved
</div>

<?php } elseif($check_payment && $check_payment['verification_status']=="pending"){ ?>

<div class="bg-yellow-500/10 text-yellow-400 border border-yellow-500/20 px-4 py-2 rounded-2xl text-xs font-semibold">
Pending
</div>

<?php } elseif($check_payment && $check_payment['verification_status']=="rejected"){ ?>

<div class="bg-red-500/10 text-red-400 border border-red-500/20 px-4 py-2 rounded-2xl text-xs font-semibold">
Rejected
</div>

<?php } else { ?>

<div class="bg-zinc-800 text-zinc-400 border border-zinc-700 px-4 py-2 rounded-2xl text-xs font-semibold">
Unpaid
</div>

<?php } ?>

</div>

<!-- AMOUNT -->

<div class="mb-6 relative z-10">

<h1 class="text-5xl font-black text-white">

₹<?php echo $row['amount']; ?>

</h1>

</div>

<!-- APPROVED -->

<?php if($row['status']=="paid"){ ?>

<div class="bg-emerald-500/10 border border-emerald-500/20 rounded-3xl p-5 flex items-center gap-4 relative z-10">

<div class="w-14 h-14 rounded-2xl bg-emerald-500/10 flex items-center justify-center text-2xl">

✅

</div>

<div>

<h3 class="text-emerald-400 font-bold">
Payment Approved
</h3>

<p class="text-zinc-500 text-sm mt-1">
Verified successfully
</p>

</div>

</div>

<?php } ?>

<!-- PENDING -->

<?php if($check_payment && $check_payment['verification_status']=="pending"){ ?>

<div class="bg-yellow-500/10 border border-yellow-500/20 rounded-3xl p-5 relative z-10">

<div class="flex items-center gap-4 mb-4">

<div class="w-14 h-14 rounded-2xl bg-yellow-500/10 flex items-center justify-center text-2xl">

⏳

</div>

<div>

<h3 class="text-yellow-400 font-bold">
Verification Pending
</h3>

<p class="text-zinc-500 text-sm mt-1">
Awaiting admin approval
</p>

</div>

</div>

<div class="bg-black/30 border border-zinc-800 rounded-2xl p-4">

<p class="text-zinc-500 text-xs uppercase tracking-[2px] mb-2">
Transaction ID
</p>

<p class="text-white font-mono text-sm">
<?php echo $check_payment['transaction_id']; ?>
</p>

</div>

</div>

<?php } ?>

<!-- REJECTED -->

<?php if($check_payment && $check_payment['verification_status']=="rejected"){ ?>

<div class="bg-red-500/10 border border-red-500/20 rounded-3xl p-5 mb-5 relative z-10">

<div class="flex items-center gap-4">

<div class="w-14 h-14 rounded-2xl bg-red-500/10 flex items-center justify-center text-2xl">

❌

</div>

<div>

<h3 class="text-red-400 font-bold">
Payment Rejected
</h3>

<p class="text-zinc-500 text-sm mt-1">
Upload valid proof again
</p>

</div>

</div>

</div>

<?php } ?>

<!-- PAYMENT FORM -->

<?php if(
$row['status']!="paid"
&&
(
!$check_payment
||
$check_payment['verification_status']=="rejected"
)
){ ?>

<!-- QR -->

<div class="bg-black/30 border border-zinc-800 rounded-3xl p-5 mb-5 relative z-10">

<div class="flex items-center gap-5">

<img
src="../assets/qr.png"
class="w-24 h-24 rounded-2xl border border-zinc-700 shadow-xl"
>

<div>

<p class="text-zinc-500 text-xs uppercase tracking-[3px] mb-2">
UPI PAYMENT
</p>

<h3 class="text-white font-bold text-lg">
hostelhub@upi
</h3>

<p class="text-zinc-500 text-sm mt-2">
Scan QR and pay using any UPI app.
</p>

</div>

</div>

</div>

<!-- FORM -->

<form method="POST" enctype="multipart/form-data" class="space-y-4 relative z-10">

<input
type="hidden"
name="fee_id"
value="<?php echo $row['fee_id']; ?>"
>

<input
type="hidden"
name="amount"
value="<?php echo $row['amount']; ?>"
>

<!-- TXN -->

<div>

<label class="block text-sm text-zinc-400 mb-2">
Transaction ID
</label>

<input
type="text"
name="transaction_id"
placeholder="Enter UPI Transaction ID"
required
class="w-full bg-black/30 border border-zinc-700 px-5 py-4 rounded-2xl outline-none text-white placeholder:text-zinc-500 focus:border-emerald-500"
>

</div>

<!-- FILE -->

<div>

<label class="block text-sm text-zinc-400 mb-2">
Upload Screenshot
</label>

<input
type="file"
name="screenshot"
required
class="w-full bg-black/30 border border-zinc-700 p-4 rounded-2xl text-sm file:mr-4 file:border-0 file:bg-emerald-500 file:px-4 file:py-2 file:text-white file:rounded-xl"
>

</div>

<!-- BUTTON -->

<button
type="submit"
name="submit_payment"
class="w-full bg-emerald-600 hover:bg-emerald-500 py-4 rounded-2xl font-bold text-white transition-all duration-300 hover:scale-[1.01] shadow-lg shadow-emerald-500/20">

Submit Payment Proof

</button>

</form>

<?php } ?>

</div>

<?php } ?>

</div>

</div>

</div>

</body>
</html>