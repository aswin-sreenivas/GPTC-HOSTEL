<?php
session_start();
include("config/db.php");

$error="";

if(isset($_POST['login'])){

$username=mysqli_real_escape_string($conn,$_POST['username']);
$password=$_POST['password'];

$query="SELECT * FROM users WHERE username='$username'";

$result=mysqli_query($conn,$query);

if(mysqli_num_rows($result)==1){

$user=mysqli_fetch_assoc($result);

$db_password=$user['password'];
$role=$user['role'];

$login_success=false;

/* ADMIN + HEAD NORMAL PASSWORD */

if($role=="admin" || $role=="head"){

if($password==$db_password){

$login_success=true;

}

}

/* HASHED PASSWORD LOGIN */

else{

if(password_verify($password,$db_password)){

$login_success=true;

}

}

if($login_success){

$_SESSION['user_id']=$user['user_id'];
$_SESSION['role']=$user['role'];
$_SESSION['username']=$user['username'];

if($role=="admin"){

header("Location: admin/admin_dashboard.php");
exit();

}

elseif($role=="student"){

header("Location: student/student_dashboard.php");
exit();

}

elseif($role=="parent"){

header("Location: parent/parent_dashboard.php");
exit();

}

elseif($role=="head"){

header("Location: head/dashboard.php");
exit();

}

}else{

$error="Invalid Username or Password";

}

}else{

$error="Invalid Username or Password";

}

}
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>GPTC HOSTEL</title>

<script src="https://cdn.tailwindcss.com"></script>

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>

<style>

*{
margin:0;
padding:0;
box-sizing:border-box;
scroll-behavior:smooth;
}

body{
font-family:Inter,system-ui;
background:#020617;
overflow-x:hidden;
color:white;
}

.grid-bg{
position:absolute;
inset:0;
background-image:
linear-gradient(rgba(255,255,255,.03) 1px, transparent 1px),
linear-gradient(90deg, rgba(255,255,255,.03) 1px, transparent 1px);
background-size:40px 40px;
mask-image:linear-gradient(to bottom,rgba(0,0,0,1),transparent);
}

.glow{
position:absolute;
border-radius:999px;
filter:blur(100px);
opacity:.18;
animation:float 8s ease-in-out infinite;
}

.glow1{
width:350px;
height:350px;
background:#10b981;
top:-100px;
left:-100px;
}

.glow2{
width:300px;
height:300px;
background:#2563eb;
bottom:-100px;
right:-100px;
animation-delay:2s;
}

.glow3{
width:250px;
height:250px;
background:#9333ea;
top:40%;
left:45%;
animation-delay:4s;
}

@keyframes float{

0%{
transform:translateY(0px);
}

50%{
transform:translateY(20px);
}

100%{
transform:translateY(0px);
}

}

.glass{
background:rgba(15,23,42,0.72);
backdrop-filter:blur(25px);
border:1px solid rgba(255,255,255,0.08);
}

.primary-btn{
background:linear-gradient(135deg,#10b981,#059669);
transition:.35s;
box-shadow:0 15px 35px rgba(16,185,129,.25);
}

.primary-btn:hover{
transform:translateY(-3px);
box-shadow:0 20px 40px rgba(16,185,129,.35);
}

.input-box{
background:rgba(24,24,27,.7);
border:1px solid rgba(255,255,255,.06);
transition:.3s;
}

.input-box:focus-within{
border-color:#10b981;
box-shadow:0 0 0 4px rgba(16,185,129,.12);
}

.text-gradient{
background:linear-gradient(to right,#fff,#94a3b8);
-webkit-background-clip:text;
-webkit-text-fill-color:transparent;
}

::-webkit-scrollbar{
width:6px;
}

::-webkit-scrollbar-thumb{
background:#27272a;
border-radius:999px;
}

</style>

</head>

<body>

<div class="grid-bg"></div>

<div class="glow glow1"></div>
<div class="glow glow2"></div>
<div class="glow glow3"></div>

<nav class="fixed top-0 left-0 w-full z-50 border-b border-white/5 backdrop-blur-xl bg-black/20">

<div class="max-w-7xl mx-auto px-8 h-20 flex items-center justify-between">

<div class="flex items-center gap-4">

<div class="w-12 h-12 rounded-2xl bg-emerald-500 flex items-center justify-center text-black font-black text-xl shadow-lg shadow-emerald-500/30">

H

</div>

<div>

<h1 class="text-xl font-black tracking-wide">
GPTC HOSTEL
</h1>

<p class="text-zinc-500 text-xs">
GPTC HOSTEL
</p>

</div>

</div>

</div>

</nav>

<section class="relative min-h-screen flex items-center">

<div class="max-w-7xl mx-auto px-8 grid lg:grid-cols-2 gap-20 items-center relative z-10">

<div>

<h1 class="text-7xl font-black leading-[1.05] tracking-tight text-gradient">

Smart Hostel
Management Platform

</h1>

<p class="text-zinc-400 text-lg mt-8 leading-relaxed max-w-2xl">

GPTC HOSTEL is a premium SaaS-level hostel management platform designed for colleges and institutions with advanced dashboards, room allocation, attendance tracking, fee payments, complaint monitoring and parent access.

</p>

<div class="flex flex-wrap gap-5 mt-10">

<a href="#login"
class="primary-btn px-8 py-4 rounded-2xl text-lg font-semibold">

Get Started

</a>

</div>

</div>

<!-- LOGIN -->

<div id="login" class="glass rounded-[36px] p-10 relative overflow-hidden">

<div class="absolute top-0 right-0 w-52 h-52 bg-emerald-500/10 rounded-full blur-3xl"></div>

<div class="relative z-10">

<div class="text-center mb-8">

<div class="w-20 h-20 rounded-[28px] bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center mx-auto mb-5">

<i class="fa-solid fa-building-shield text-emerald-400 text-3xl"></i>

</div>

<h1 class="text-4xl font-black text-white">
Welcome
</h1>

<p class="text-zinc-500 mt-3">
Sign in to continue to your dashboard
</p>

</div>

<?php if($error!=""){ ?>

<div class="bg-red-500/10 border border-red-500/20 text-red-400 p-4 rounded-2xl mb-6 text-sm flex justify-between items-center">

<div>
<i class="fa-solid fa-circle-exclamation mr-2"></i>
<?php echo $error; ?>
</div>

<button
type="button"
onclick="this.parentElement.style.display='none'"
class="text-lg hover:text-white">

&times;

</button>

</div>

<?php } ?>

<form method="POST">

<!-- USERNAME -->

<div class="mb-5">

<label class="text-zinc-400 text-sm block mb-3">
Username
</label>

<div class="input-box flex items-center rounded-2xl px-5">

<i class="fa-regular fa-user text-zinc-500"></i>

<input
type="text"
name="username"
placeholder="Enter your username"
class="bg-transparent w-full p-4 text-white outline-none placeholder:text-zinc-500"
required>

</div>

</div>

<!-- PASSWORD -->

<div class="mb-7">

<label class="text-zinc-400 text-sm block mb-3">
Password
</label>

<div class="input-box flex items-center rounded-2xl px-5">

<i class="fa-solid fa-lock text-zinc-500"></i>

<input
type="password"
id="password"
name="password"
placeholder="••••••••"
class="bg-transparent w-full p-4 text-white outline-none placeholder:text-zinc-500"
required>

<button
type="button"
onclick="togglePassword()"
class="text-zinc-500 hover:text-white transition">

<i id="eyeIcon" class="fa-solid fa-eye"></i>

</button>

</div>

</div>

<!-- BUTTON -->

<button
name="login"
class="primary-btn w-full py-4 rounded-2xl text-lg font-bold">

<i class="fa-solid fa-arrow-right-to-bracket mr-2"></i>

Login

</button>

</form>

</div>

</div>

</div>

</section>

<script>

function togglePassword(){

let password=document.getElementById("password");
let eyeIcon=document.getElementById("eyeIcon");

if(password.type==="password"){

password.type="text";

eyeIcon.classList.remove("fa-eye");
eyeIcon.classList.add("fa-eye-slash");

}else{

password.type="password";

eyeIcon.classList.remove("fa-eye-slash");
eyeIcon.classList.add("fa-eye");

}

}

</script>

</body>
</html>