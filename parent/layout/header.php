<div class="flex justify-between items-center mb-10">

<div>

<h2 class="text-2xl font-bold text-white">
Parent Panel
</h2>

<p class="text-zinc-500 text-sm mt-1">
HostelHub Pro
</p>

</div>

<div class="flex items-center gap-4">

<div class="text-right">

<div class="text-sm text-zinc-400">
Logged in as
</div>

<div class="font-semibold text-white">
<?php echo $_SESSION['username']; ?>
</div>

</div>

<a
href="../auth/logout.php"
class="bg-red-600 hover:bg-red-500 transition px-5 py-2 rounded-xl text-sm">

Logout

</a>

</div>

</div>