<?php
session_start();

if(!isset($_SESSION['user_id']) || $_SESSION['role']!="parent"){
header("Location: ../index.php");
exit();
}

include("../config/db.php");

$user_id=$_SESSION['user_id'];

$success="";
$error="";

/* ================= GET STUDENT ================= */

$result=mysqli_query($conn,"
SELECT 

students.*,
parents.father_name

FROM students

LEFT JOIN parents
ON students.parent_id = parents.parent_id

WHERE parents.user_id='$user_id'
");

if(!$result){
die("SQL Error : ".mysqli_error($conn));
}

$student=mysqli_fetch_assoc($result);

if(!$student){
die("No student linked to this parent.");
}

$student_id=$student['student_id'];

/* ================= PAYMENT SUBMIT ================= */

if(isset($_POST['submit_payment'])){

$fee_id = isset($_POST['fee_id'])
? mysqli_real_escape_string($conn,trim($_POST['fee_id']))
: '';

$transaction_id = isset($_POST['transaction_id'])
? mysqli_real_escape_string($conn,trim($_POST['transaction_id']))
: '';

$proof="";

/* ================= VALIDATION ================= */

if($fee_id==""){

$error="Fee ID missing.";

}

elseif($transaction_id==""){

$error="Transaction ID is required.";

}

elseif(strlen($transaction_id)<14){

$error="Transaction ID must contain minimum 14 characters.";

}

/* ================= GET FEE ================= */

if($error==""){

$fee_query=mysqli_query($conn,"
SELECT *
FROM fees
WHERE fee_id='$fee_id'
AND student_id='$student_id'
");

if(!$fee_query){

$error="Fee fetch failed.";

}else{

$fee_data=mysqli_fetch_assoc($fee_query);

if(!$fee_data){

$error="Invalid fee selected.";

}else{

$amount_paid=$fee_data['amount'];

}

}

}

/* ================= CHECK EXISTING ================= */

if($error==""){

$check=mysqli_query($conn,"
SELECT *
FROM fee_payments
WHERE fee_id='$fee_id'
AND student_id='$student_id'
AND verification_status='pending'
");

if($check && mysqli_num_rows($check)>0){

$error="Payment proof already submitted and waiting for admin approval.";

}

}

/* ================= FILE UPLOAD ================= */

if($error==""){

if(isset($_FILES['payment_proof']) && $_FILES['payment_proof']['name']!=""){

$folder="../uploads/payments/";

if(!is_dir($folder)){
mkdir($folder,0777,true);
}

/* FILE EXTENSION */

$extension=strtolower(pathinfo(
$_FILES['payment_proof']['name'],
PATHINFO_EXTENSION
));

/* ALLOWED */

$allowed=array("jpg","jpeg","png","pdf");

if(!in_array($extension,$allowed)){

$error="Only JPG, JPEG, PNG, PDF files allowed.";

}else{

$file=time()."_".rand(1000,9999).".".$extension;

$tmp=$_FILES['payment_proof']['tmp_name'];

if(move_uploaded_file($tmp,$folder.$file)){

$proof=$file;

}else{

$error="Failed to upload payment screenshot.";

}

}

}else{

$error="Please upload payment screenshot.";

}

}

/* ================= INSERT PAYMENT ================= */

if($error==""){

$insert=mysqli_query($conn,"
INSERT INTO fee_payments
(
fee_id,
student_id,
amount_paid,
payment_method,
transaction_id,
payment_date,
payment_screenshot,
verification_status,
status,
proof_image,
created_at
)

VALUES
(
'$fee_id',
'$student_id',
'$amount_paid',
'upi',
'$transaction_id',
NOW(),
'$proof',
'pending',
'Active',
'$proof',
NOW()
)
");

if($insert){

$success="Payment proof submitted successfully.";

}else{

$error="Payment submission failed : ".mysqli_error($conn);

}

}

}

/* ================= FEES ================= */

$fees=mysqli_query($conn,"
SELECT *
FROM fees
WHERE student_id='$student_id'
ORDER BY due_date DESC
");

/* ================= STATS ================= */

$total_pending=mysqli_fetch_assoc(mysqli_query($conn,"
SELECT SUM(amount) as total
FROM fees
WHERE student_id='$student_id'
AND status='pending'
"))['total'];

$total_paid=mysqli_fetch_assoc(mysqli_query($conn,"
SELECT SUM(amount) as total
FROM fees
WHERE student_id='$student_id'
AND status='paid'
"))['total'];

$pending_count=mysqli_fetch_assoc(mysqli_query($conn,"
SELECT COUNT(*) as total
FROM fees
WHERE student_id='$student_id'
AND status='pending'
"))['total'];

?>

<!DOCTYPE html>
<html>

<head>

<title>Fee Management</title>

<script src="https://cdn.tailwindcss.com"></script>

<style>

body{
background:#020617;
font-family:system-ui;
}

.card{
background:rgba(24,24,27,.92);
border:1px solid #27272a;
backdrop-filter:blur(14px);
transition:.3s;
}

.card:hover{
border-color:rgba(16,185,129,.25);
transform:translateY(-2px);
}

.input{
width:100%;
background:#27272a;
border:1px solid #3f3f46;
padding:13px 15px;
border-radius:16px;
outline:none;
color:white;
transition:.3s;
}

.input:focus{
border-color:#10b981;
box-shadow:0 0 0 4px rgba(16,185,129,.1);
}

</style>

</head>

<body class="text-zinc-200">

<div class="flex min-h-screen">

<?php include("layout/sidebar.php"); ?>

<div class="flex-1 p-10">

<!-- HEADER -->

<div class="flex justify-between items-center mb-10">

<div>

<h1 class="text-4xl font-black text-white">
Fee Management
</h1>

<p class="text-zinc-500 mt-2 text-lg">
Pay hostel fees and upload proof securely.
</p>

</div>

<div class="card px-5 py-4 rounded-2xl">

<div class="text-zinc-400 text-sm">
Student
</div>

<div class="text-white font-semibold mt-1">
<?php echo $student['full_name']; ?>
</div>

</div>

</div>

<!-- ALERTS -->

<?php if($success!=""){ ?>

<div class="bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 p-4 rounded-2xl mb-6">

<?php echo $success; ?>

</div>

<?php } ?>

<?php if($error!=""){ ?>

<div class="bg-red-500/10 border border-red-500/20 text-red-400 p-4 rounded-2xl mb-6">

<?php echo $error; ?>

</div>

<?php } ?>

<!-- STATS -->

<div class="grid grid-cols-3 gap-6 mb-8">

<div class="card rounded-3xl p-6">

<div class="text-zinc-400 text-sm">
TOTAL PAID
</div>

<h2 class="text-4xl font-black text-emerald-400 mt-4">
₹<?php echo $total_paid ? $total_paid : 0; ?>
</h2>

</div>

<div class="card rounded-3xl p-6">

<div class="text-zinc-400 text-sm">
PENDING FEES
</div>

<h2 class="text-4xl font-black text-yellow-400 mt-4">
₹<?php echo $total_pending ? $total_pending : 0; ?>
</h2>

</div>

<div class="card rounded-3xl p-6">

<div class="text-zinc-400 text-sm">
PENDING COUNT
</div>

<h2 class="text-4xl font-black text-red-400 mt-4">
<?php echo $pending_count ? $pending_count : 0; ?>
</h2>

</div>

</div>

<!-- FEES -->

<div class="grid lg:grid-cols-2 gap-6">

<?php while($row=mysqli_fetch_assoc($fees)){ ?>

<?php

$fee_id=$row['fee_id'];

$payment=mysqli_query($conn,"
SELECT *
FROM fee_payments
WHERE fee_id='$fee_id'
AND student_id='$student_id'
ORDER BY payment_id DESC
LIMIT 1
");

$payment_data=mysqli_fetch_assoc($payment);

?>

<div class="card rounded-[30px] p-7">

<div class="flex justify-between items-start mb-6">

<div>

<h2 class="text-2xl font-bold text-white">
<?php echo $row['fee_type']; ?>
</h2>

<p class="text-zinc-500 mt-2 text-sm">

Due:
<?php echo date("d M Y",strtotime($row['due_date'])); ?>

</p>

</div>

<?php

$status = isset($payment_data['verification_status'])
? $payment_data['verification_status']
: '';

if($status=="pending"){

echo "
<span class='bg-blue-500/10 text-blue-400 border border-blue-500/20 px-4 py-2 rounded-full text-sm'>
Under Review
</span>
";

}

elseif($status=="approved"){

echo "
<span class='bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 px-4 py-2 rounded-full text-sm'>
Verified
</span>
";

}

elseif($status=="rejected"){

echo "
<span class='bg-red-500/10 text-red-400 border border-red-500/20 px-4 py-2 rounded-full text-sm'>
Rejected
</span>
";

}

else{

echo "
<span class='bg-zinc-800 text-zinc-400 border border-zinc-700 px-4 py-2 rounded-full text-sm'>
Not Submitted
</span>
";

}

?>

</div>

<div class="text-5xl font-black text-white mb-7">

₹<?php echo $row['amount']; ?>

</div>

<?php

$current_status = isset($payment_data['verification_status'])
? $payment_data['verification_status']
: '';

if(!$payment_data || $current_status=="rejected"){

?>

<!-- QR -->

<div class="bg-zinc-900 border border-zinc-800 rounded-2xl p-5 mb-6 text-center">

<img
src="../assets/qr.png"
class="w-44 mx-auto rounded-xl mb-4"
>

<div class="text-zinc-400 text-sm">
UPI ID
</div>

<div class="text-white font-semibold text-lg mt-1">
hostel@okicic
</div>

</div>

<!-- FORM -->

<form method="POST" enctype="multipart/form-data">

<input
type="hidden"
name="fee_id"
value="<?php echo $row['fee_id']; ?>"
>

<div class="space-y-4">

<input
type="text"
name="transaction_id"
placeholder="Enter Transaction ID"
required
minlength="14"
maxlength="30"
pattern="[A-Za-z0-9]{14,30}"
title="Transaction ID must contain minimum 14 characters and only letters & numbers"
class="input"
>

<div>

<label class="block text-sm text-zinc-400 mb-2">

Upload Payment Screenshot

</label>

<input
type="file"
name="payment_proof"
required
accept=".jpg,.jpeg,.png,.pdf"
class="input"
>

</div>

<button
type="submit"
name="submit_payment"
class="w-full bg-emerald-600 hover:bg-emerald-500 py-4 rounded-2xl font-semibold transition">

Submit Payment Proof

</button>

</div>

</form>

<?php } else { ?>

<div class="bg-zinc-900 border border-zinc-800 rounded-2xl p-5">

<div class="flex justify-between items-center">

<div>

<div class="text-zinc-400 text-sm">
Transaction ID
</div>

<div class="text-white font-semibold mt-1">
<?php echo $payment_data['transaction_id']; ?>
</div>

</div>

<div>

<?php

if($current_status=="pending"){

echo "
<div class='bg-blue-500/10 text-blue-400 px-4 py-2 rounded-xl text-sm'>
Waiting Approval
</div>
";

}

elseif($current_status=="approved"){

echo "
<div class='bg-emerald-500/10 text-emerald-400 px-4 py-2 rounded-xl text-sm'>
Payment Verified
</div>
";

}

?>

</div>

</div>

</div>

<?php } ?>

</div>

<?php } ?>

</div>

</div>

</div>

</body>
</html>