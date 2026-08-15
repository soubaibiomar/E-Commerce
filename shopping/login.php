<?php
session_start();
include('includes/config.php');

// Code user Registration
if (isset($_POST['submit'])) {
    $name = trim($_POST['fullname'] ?? '');
    $email = trim($_POST['emailid'] ?? '');
    $contactno = trim($_POST['contactno'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($name) || empty($email) || empty($password)) {
        echo "<script>alert('Please fill in all required fields.');</script>";
    } else {
        // Check if email already exists
        $existing = db_fetch_one("SELECT id FROM users WHERE email=?", [$email], "s");
        if ($existing) {
            echo "<script>alert('Email is already registered. Please login or use a different email.');</script>";
        } else {
            $hashedPassword = hash_password($password);
            $query = db_query("INSERT INTO users(name, email, contactno, password) VALUES(?, ?, ?, ?)", [$name, $email, $contactno, $hashedPassword], "ssss");
            if ($query) {
                echo "<script>alert('Account created successfully. Please sign in.');</script>";
            } else {
                echo "<script>alert('Registration failed. Please try again.');</script>";
            }
        }
    }
}

// Code for User login
if (isset($_POST['login'])) {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $uip = $_SERVER['REMOTE_ADDR'] ?? '';

    $user = db_fetch_one("SELECT * FROM users WHERE email=?", [$email], "s");
    
    $loginSuccess = false;
    if ($user) {
        $loginSuccess = verify_and_rehash_password($password, $user['password'], function($newHash) use ($user) {
            db_query("UPDATE users SET password=? WHERE id=?", [$newHash, $user['id']], "si");
        });
    }

    if ($loginSuccess) {
        session_regenerate_id(true);
        $_SESSION['login'] = $user['email'];
        $_SESSION['id'] = $user['id'];
        $_SESSION['username'] = $user['name'];
        $status = 1;
        db_query("INSERT INTO userlog(userEmail, userip, status) VALUES(?, ?, ?)", [$user['email'], $uip, $status], "ssi");
        header("location:my-cart.php");
        exit();
    } else {
        $status = 0;
        db_query("INSERT INTO userlog(userEmail, userip, status) VALUES(?, ?, ?)", [$email, $uip, $status], "ssi");
        $_SESSION['errmsg'] = "Invalid email id or Password";
        header("location:login.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
	    <title>ZeyTech | Commercial Authentication &amp; Identity</title>

	    <link rel="preconnect" href="https://fonts.googleapis.com">
	    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	    <link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,400;0,9..144,600;0,9..144,700;0,9..144,800;1,9..144,400&family=IBM+Plex+Sans:ital,wght@0,400;0,500;0,600;0,700;1,400&family=Space+Mono:ital,wght@0,400;0,700;1,400&display=swap" rel="stylesheet">
	    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
	    <link rel="stylesheet" href="assets/css/font-awesome.min.css">
	    <link rel="stylesheet" href="assets/css/modern-storefront.css">
		<link rel="shortcut icon" href="assets/images/favicon.ico">

<script type="text/javascript">
function valid() {
    if (document.register.password.value != document.register.confirmpassword.value) {
        alert("Password and Confirm Password Field do not match!");
        document.register.confirmpassword.focus();
        return false;
    }
    return true;
}
function userAvailability() {
    $("#loaderIcon").show();
    jQuery.ajax({
        url: "check_availability.php",
        data: 'email=' + $("#email").val(),
        type: "POST",
        success: function(data) {
            $("#user-availability-status1").html(data);
            $("#loaderIcon").hide();
        },
        error: function (){}
    });
}
</script>
	</head>
    <body class="cnt-home" style="background:#080e1a; color:#f2efe6;">
	
<header class="header-style-1">
<?php include('includes/top-header.php');?>
<?php include('includes/main-header.php');?>
<?php include('includes/menu-bar.php');?>
</header>

<div class="body-content" style="padding-top:36px; padding-bottom:60px;">
	<div class="container">
		<div class="row">
			<!-- Sign-in -->			
			<div class="col-md-6 col-sm-6" style="margin-bottom:24px;">
				<div class="manifest-panel" style="padding:32px; height:100%;">
					<div style="font-family:'Space Mono'; font-size:11px; font-weight:700; color:#c79a44; text-transform:uppercase; letter-spacing:0.08em; margin-bottom:8px;">
						[AUTH.CUSTOMER_SIGNIN]
					</div>
					<h2 style="font-family:'Fraunces', serif; font-size:24px; font-weight:700; color:#f2efe6; margin:0 0 8px 0; letter-spacing:-0.02em;">Welcome Back</h2>
					<p style="font-size:13px; color:#8ea2bf; margin-bottom:24px;">Access your saved ledger, order tracking, and custom quotes.</p>
					
					<form class="register-form" method="post">
						<?php if (!empty($_SESSION['errmsg'])) { ?>
						<div style="background:rgba(239,68,68,0.12); border:1px solid #ef4444; color:#fca5a5; padding:10px 14px; border-radius:2px; font-family:'Space Mono'; font-size:12px; margin-bottom:18px;">
							[ERROR: <?php echo e($_SESSION['errmsg']); $_SESSION['errmsg'] = ""; ?>]
						</div>
						<?php } ?>
						
						<div class="form-group" style="margin-bottom:16px;">
							<label style="font-family:'Space Mono'; font-size:11px; font-weight:700; color:#8ea2bf; text-transform:uppercase;" for="exampleInputEmail1">EMAIL ADDRESS *</label>
							<input type="email" name="email" class="form-control" id="exampleInputEmail1" required style="height:44px; border-radius:2px; border:1px solid rgba(142,162,191,0.25); background:#080e1a; color:#f2efe6; font-family:'IBM Plex Sans'; font-size:13px;">
						</div>
						<div class="form-group" style="margin-bottom:16px;">
							<label style="font-family:'Space Mono'; font-size:11px; font-weight:700; color:#8ea2bf; text-transform:uppercase;" for="exampleInputPassword1">PASSWORD *</label>
							<input type="password" name="password" class="form-control" id="exampleInputPassword1" required style="height:44px; border-radius:2px; border:1px solid rgba(142,162,191,0.25); background:#080e1a; color:#f2efe6; font-family:'IBM Plex Sans'; font-size:13px;">
						</div>
						<div style="display:flex; justify-content:flex-end; margin-bottom:20px;">
							<a href="forgot-password.php" style="font-family:'Space Mono'; font-size:11px; color:#d9b567; text-decoration:none;">Forgot credentials?</a>
						</div>
						<button type="submit" class="btn-primary" name="login" style="width:100%; height:44px; font-family:'Space Mono'; font-size:12px;">
							SIGN IN TO ACCOUNT &rarr;
						</button>
					</form>					
				</div>
			</div>

			<!-- Create a new account -->
			<div class="col-md-6 col-sm-6" style="margin-bottom:24px;">
				<div class="manifest-panel" style="padding:32px; height:100%;">
					<div style="font-family:'Space Mono'; font-size:11px; font-weight:700; color:#c79a44; text-transform:uppercase; letter-spacing:0.08em; margin-bottom:8px;">
						[AUTH.NEW_REGISTRATION]
					</div>
					<h2 style="font-family:'Fraunces', serif; font-size:24px; font-weight:700; color:#f2efe6; margin:0 0 8px 0; letter-spacing:-0.02em;">Create Account</h2>
					<p style="font-size:13px; color:#8ea2bf; margin-bottom:24px;">Join ZeyTech for instant checkout and multi-currency commercial settlement.</p>
					
					<form class="register-form" role="form" method="post" name="register" onSubmit="return valid();">
						<div class="form-group" style="margin-bottom:14px;">
							<label style="font-family:'Space Mono'; font-size:11px; font-weight:700; color:#8ea2bf; text-transform:uppercase;" for="fullname">FULL NAME *</label>
							<input type="text" class="form-control" id="fullname" name="fullname" required="required" style="height:44px; border-radius:2px; border:1px solid rgba(142,162,191,0.25); background:#080e1a; color:#f2efe6; font-family:'IBM Plex Sans'; font-size:13px;">
						</div>

						<div class="form-group" style="margin-bottom:14px;">
							<label style="font-family:'Space Mono'; font-size:11px; font-weight:700; color:#8ea2bf; text-transform:uppercase;" for="email">EMAIL ADDRESS *</label>
							<input type="email" class="form-control" id="email" onBlur="userAvailability()" name="emailid" required style="height:44px; border-radius:2px; border:1px solid rgba(142,162,191,0.25); background:#080e1a; color:#f2efe6; font-family:'IBM Plex Sans'; font-size:13px;">
							<span id="user-availability-status1" style="font-size:11px; font-family:'Space Mono'; color:#d9b567;"></span>
						</div>

						<div class="form-group" style="margin-bottom:14px;">
							<label style="font-family:'Space Mono'; font-size:11px; font-weight:700; color:#8ea2bf; text-transform:uppercase;" for="contactno">PHONE NUMBER *</label>
							<input type="text" class="form-control" id="contactno" name="contactno" maxlength="10" required style="height:44px; border-radius:2px; border:1px solid rgba(142,162,191,0.25); background:#080e1a; color:#f2efe6; font-family:'IBM Plex Sans'; font-size:13px;">
						</div>

						<div class="form-group" style="margin-bottom:14px;">
							<label style="font-family:'Space Mono'; font-size:11px; font-weight:700; color:#8ea2bf; text-transform:uppercase;" for="password">PASSWORD *</label>
							<input type="password" class="form-control" id="password" name="password" required style="height:44px; border-radius:2px; border:1px solid rgba(142,162,191,0.25); background:#080e1a; color:#f2efe6; font-family:'IBM Plex Sans'; font-size:13px;">
						</div>

						<div class="form-group" style="margin-bottom:20px;">
							<label style="font-family:'Space Mono'; font-size:11px; font-weight:700; color:#8ea2bf; text-transform:uppercase;" for="confirmpassword">CONFIRM PASSWORD *</label>
							<input type="password" class="form-control" id="confirmpassword" name="confirmpassword" required style="height:44px; border-radius:2px; border:1px solid rgba(142,162,191,0.25); background:#080e1a; color:#f2efe6; font-family:'IBM Plex Sans'; font-size:13px;">
						</div>

						<button type="submit" name="submit" class="btn-primary" id="submit" style="width:100%; height:44px; font-family:'Space Mono'; font-size:12px;">
							REGISTER ACCOUNT &rarr;
						</button>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>

<?php include('includes/footer.php');?>
<script src="assets/js/jquery-1.11.1.min.js"></script>
<script src="assets/js/bootstrap.min.js"></script>
</body>
</html>