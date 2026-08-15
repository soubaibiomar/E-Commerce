<?php
session_start();
include('includes/config.php');

if (empty($_SESSION['id'])) {   
    header('location:login.php');
    exit();
} else {
    $uid = intval($_SESSION['id']);

	if (isset($_POST['update'])) {
		$name = trim($_POST['name'] ?? '');
		$contactno = trim($_POST['contactno'] ?? '');
		db_query("UPDATE users SET name=?, contactno=? WHERE id=?", [$name, $contactno, $uid], "ssi");
		echo "<script>alert('Profile data updated on record.');</script>";
	}

	if (isset($_POST['submit'])) {
        $cpass = $_POST['cpass'] ?? '';
        $newpass = $_POST['newpass'] ?? '';
        $currentTime = date('d-m-Y h:i:s A');

        $user = db_fetch_one("SELECT password FROM users WHERE id=?", [$uid], "i");
        if ($user && verify_and_rehash_password($cpass, $user['password'])) {
            $newHashed = hash_password($newpass);
            db_query("UPDATE users SET password=?, updationDate=? WHERE id=?", [$newHashed, $currentTime, $uid], "ssi");
            echo "<script>alert('Password security keys updated successfully.');</script>";
        } else {
            echo "<script>alert('Current Password does not match record.');</script>";
        }
	}

    $userData = db_fetch_one("SELECT * FROM users WHERE id=?", [$uid], "i");
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
	    <title>Customer Profile | ZeyTech</title>
	    <link rel="preconnect" href="https://fonts.googleapis.com">
	    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	    <link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,400;0,9..144,600;0,9..144,700;0,9..144,800;1,9..144,400&family=IBM+Plex+Sans:ital,wght@0,400;0,500;0,600;0,700;1,400&family=Space+Mono:ital,wght@0,400;0,700;1,400&display=swap" rel="stylesheet">
	    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
	    <link rel="stylesheet" href="assets/css/font-awesome.min.css">
	    <link rel="stylesheet" href="assets/css/modern-storefront.css">
		<link rel="shortcut icon" href="assets/images/favicon.ico">
<script type="text/javascript">
function valid() {
    if (document.chngpwd.cpass.value == "") {
        alert("Current Password Field is Empty !");
        document.chngpwd.cpass.focus();
        return false;
    } else if (document.chngpwd.newpass.value == "") {
        alert("New Password Field is Empty !");
        document.chngpwd.newpass.focus();
        return false;
    } else if (document.chngpwd.cnfpass.value == "") {
        alert("Confirm Password Field is Empty !");
        document.chngpwd.cnfpass.focus();
        return false;
    } else if (document.chngpwd.newpass.value != document.chngpwd.cnfpass.value) {
        alert("Password and Confirm Password Field do not match !");
        document.chngpwd.cnfpass.focus();
        return false;
    }
    return true;
}
</script>
	</head>
    <body class="cnt-home" style="background:#080e1a; color:#f2efe6;">
<header class="header-style-1">
<?php include('includes/top-header.php');?>
<?php include('includes/main-header.php');?>
<?php include('includes/menu-bar.php');?>
</header>

<div class="body-content" style="padding-top:28px; padding-bottom:60px;">
	<div class="container">
		<div class="row">
			<div class="col-md-8">
				<div class="manifest-panel" style="padding:28px; margin-bottom:24px;">
					<div style="font-family:'Space Mono'; font-size:10px; font-weight:700; color:#c79a44; text-transform:uppercase; letter-spacing:0.08em; margin-bottom:8px;">
						[PROFILE.CREDENTIALS]
					</div>
					<h2 style="font-family:'Fraunces', serif; font-size:22px; font-weight:700; color:#f2efe6; margin:0 0 16px 0; letter-spacing:-0.02em;">Personal Information</h2>
					
					<form class="register-form" role="form" method="post">
						<div class="form-group" style="margin-bottom:14px;">
						    <label style="font-family:'Space Mono'; font-size:11px; font-weight:700; color:#8ea2bf; text-transform:uppercase;" for="name">FULL NAME *</label>
						    <input type="text" class="form-control" value="<?php echo e($userData['name'] ?? '');?>" id="name" name="name" required="required" style="height:42px; border-radius:2px; border:1px solid rgba(142,162,191,0.25); background:#080e1a; color:#f2efe6; font-size:13px;">
						</div>
						<div class="form-group" style="margin-bottom:14px;">
						    <label style="font-family:'Space Mono'; font-size:11px; font-weight:700; color:#8ea2bf; text-transform:uppercase;" for="email">EMAIL ADDRESS (READ-ONLY)</label>
						    <input type="email" class="form-control" id="email" value="<?php echo e($userData['email'] ?? '');?>" readonly style="height:42px; border-radius:2px; border:1px solid rgba(142,162,191,0.15); background:#050912; color:#8ea2bf; font-family:'Space Mono'; font-size:12px;">
						</div>
						<div class="form-group" style="margin-bottom:20px;">
						    <label style="font-family:'Space Mono'; font-size:11px; font-weight:700; color:#8ea2bf; text-transform:uppercase;" for="contactno">PHONE NUMBER *</label>
						    <input type="text" class="form-control" id="contactno" name="contactno" required="required" value="<?php echo e($userData['contactno'] ?? '');?>" maxlength="10" style="height:42px; border-radius:2px; border:1px solid rgba(142,162,191,0.25); background:#080e1a; color:#f2efe6; font-size:13px;">
						</div>
						<button type="submit" name="update" class="btn-primary" style="padding:10px 20px; font-family:'Space Mono'; font-size:11px;">UPDATE PROFILE DATA</button>
					</form>
				</div>

				<div class="manifest-panel" style="padding:28px;">
					<div style="font-family:'Space Mono'; font-size:10px; font-weight:700; color:#c79a44; text-transform:uppercase; letter-spacing:0.08em; margin-bottom:8px;">
						[SECURITY.PASSWORD_KEY]
					</div>
					<h2 style="font-family:'Fraunces', serif; font-size:22px; font-weight:700; color:#f2efe6; margin:0 0 16px 0; letter-spacing:-0.02em;">Update Password</h2>

					<form class="register-form" role="form" method="post" name="chngpwd" onSubmit="return valid();">
						<div class="form-group" style="margin-bottom:14px;">
						    <label style="font-family:'Space Mono'; font-size:11px; font-weight:700; color:#8ea2bf; text-transform:uppercase;" for="cpass">CURRENT PASSWORD *</label>
						    <input type="password" class="form-control" id="cpass" name="cpass" required="required" style="height:42px; border-radius:2px; border:1px solid rgba(142,162,191,0.25); background:#080e1a; color:#f2efe6; font-size:13px;">
						</div>
						<div class="form-group" style="margin-bottom:14px;">
						    <label style="font-family:'Space Mono'; font-size:11px; font-weight:700; color:#8ea2bf; text-transform:uppercase;" for="newpass">NEW PASSWORD *</label>
						    <input type="password" class="form-control" id="newpass" name="newpass" required="required" style="height:42px; border-radius:2px; border:1px solid rgba(142,162,191,0.25); background:#080e1a; color:#f2efe6; font-size:13px;">
						</div>
						<div class="form-group" style="margin-bottom:20px;">
						    <label style="font-family:'Space Mono'; font-size:11px; font-weight:700; color:#8ea2bf; text-transform:uppercase;" for="cnfpass">CONFIRM NEW PASSWORD *</label>
						    <input type="password" class="form-control" id="cnfpass" name="cnfpass" required="required" style="height:42px; border-radius:2px; border:1px solid rgba(142,162,191,0.25); background:#080e1a; color:#f2efe6; font-size:13px;">
						</div>
						<button type="submit" name="submit" class="btn-ghost" style="padding:10px 20px; font-family:'Space Mono'; font-size:11px;">UPDATE PASSWORD KEY</button>
					</form> 
				</div>
			</div>
			<?php include('includes/myaccount-sidebar.php');?>
		</div>
	</div>
</div>

<?php include('includes/footer.php');?>
<script src="assets/js/jquery-1.11.1.min.js"></script>
<script src="assets/js/bootstrap.min.js"></script>
</body>
</html>
<?php } ?>