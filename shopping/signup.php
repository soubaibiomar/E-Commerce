<?php 
session_start();
include_once('includes/config.php');

if (isset($_POST['submit'])) {
    $name = trim($_POST['fullname'] ?? '');
    $email = trim($_POST['emailid'] ?? '');
    $contactno = trim($_POST['contactnumber'] ?? '');
    $password = $_POST['inputuserpwd'] ?? '';

    $existing = db_fetch_one("SELECT id FROM users WHERE email=?", [$email], "s");
    if (!$existing) {
        $hashed = hash_password($password);
        $insert = db_query("INSERT INTO users(name, email, contactno, password) VALUES(?, ?, ?, ?)", [$name, $email, $contactno, $hashed], "ssss");
        if ($insert) {
            echo "<script>alert('You are successfully registered. Please login.'); window.location='login.php';</script>";
            exit();
        } else {
            echo "<script>alert('Registration failed. Something went wrong.'); window.location='signup.php';</script>";
            exit();
        }
    } else {
        echo "<script>alert('Email is already registered. Please login.'); window.location='login.php';</script>";
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <title>Shopping | User Sign Up</title>
        <link rel="icon" type="image/x-icon" href="assets/favicon.ico" />
        <link rel="stylesheet" href="assets/css/bootstrap.min.css">
	    <link rel="stylesheet" href="assets/css/main.css">
	    <link rel="stylesheet" href="assets/css/red.css">
	    <link rel="stylesheet" href="assets/css/font-awesome.min.css">
		<link href='https://fonts.googleapis.com/css?family=Roboto:300,400,500,700' rel='stylesheet' type='text/css'>
        <script src="assets/js/jquery-1.11.1.min.js"></script>
        <script>
function emailAvailability() {
    $("#loaderIcon").show();
    jQuery.ajax({
        url: "check_availability.php",
        data: 'email=' + $("#emailid").val(),
        type: "POST",
        success: function(data){
            $("#user-email-status").html(data);
            $("#loaderIcon").hide();
        },
        error: function (){}
    });
}
        </script>
    </head>
    <body class="cnt-home">
<header class="header-style-1">
<?php include('includes/top-header.php');?>
<?php include('includes/main-header.php');?>
<?php include('includes/menu-bar.php');?>
</header>

<div class="breadcrumb">
	<div class="container">
		<div class="breadcrumb-inner">
			<ul class="list-inline list-unstyled">
				<li><a href="index.php">Home</a></li>
				<li class='active'>Sign Up</li>
			</ul>
		</div>
	</div>
</div>

<div class="body-content outer-top-bd">
    <div class="container">
        <div class="sign-in-page inner-bottom-sm">
            <div class="row">
                <div class="col-md-6 col-sm-6 sign-in">
                    <h4 class="">Create a new account</h4>
                    <form method="post" name="signup" class="register-form outer-top-xs">
                        <div class="form-group">
                            <label class="info-title" for="fullname">Full Name <span>*</span></label>
                            <input type="text" class="form-control unicase-form-control text-input" id="fullname" name="fullname" required="required">
                        </div>
                        <div class="form-group">
                            <label class="info-title" for="emailid">Email Address <span>*</span></label>
                            <input type="email" class="form-control unicase-form-control text-input" id="emailid" name="emailid" onBlur="emailAvailability()" required="required">
                            <span id="user-email-status" style="font-size:12px;"></span>
                        </div>
                        <div class="form-group">
                            <label class="info-title" for="contactnumber">Contact No <span>*</span></label>
                            <input type="text" class="form-control unicase-form-control text-input" id="contactnumber" name="contactnumber" maxlength="10" required="required">
                        </div>
                        <div class="form-group">
                            <label class="info-title" for="inputuserpwd">Password <span>*</span></label>
                            <input type="password" class="form-control unicase-form-control text-input" id="inputuserpwd" name="inputuserpwd" required="required">
                        </div>
                        <button type="submit" name="submit" class="btn-upper btn btn-primary checkout-page-button">Sign Up</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('includes/footer.php'); ?>
<script src="assets/js/bootstrap.min.js"></script>
</body>
</html>
