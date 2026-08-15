<?php 
require_once("includes/config.php");

if (!empty($_POST["email"])) {
	$email = trim($_POST["email"]);
	$user = db_fetch_one("SELECT email FROM users WHERE email=?", [$email], "s");
	if ($user) {
		echo "<span style='color:red'> Email already exists.</span>";
		echo "<script>$('#submit').prop('disabled',true);</script>";
	} else {
		echo "<span style='color:green'> Email available for Registration.</span>";
		echo "<script>$('#submit').prop('disabled',false);</script>";
	}
}
?>
