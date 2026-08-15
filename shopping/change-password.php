<?php 
session_start();
include_once('includes/config.php');

if (empty($_SESSION['id'])) {   
    header('location:login.php');
    exit();
} else {
    $uid = intval($_SESSION['id']);

    if (isset($_POST['update'])) {
        $cpass = $_POST['cpass'] ?? '';
        $newpass = $_POST['newpass'] ?? '';
        
        $user = db_fetch_one("SELECT password FROM users WHERE id=?", [$uid], "i");
        if ($user && verify_and_rehash_password($cpass, $user['password'])) {
            $hashedPassword = hash_password($newpass);
            db_query("UPDATE users SET password=? WHERE id=?", [$hashedPassword, $uid], "si");
            echo "<script>alert('Password Changed Successfully !!'); window.location='change-password.php';</script>";
            exit();
        } else {
            echo "<script>alert('Current Password does not match !!'); window.location='change-password.php';</script>";
            exit();
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <title>Shopping | Change Password</title>
        <link rel="icon" type="image/x-icon" href="assets/favicon.ico" />
        <link rel="stylesheet" href="assets/css/bootstrap.min.css">
	    <link rel="stylesheet" href="assets/css/main.css">
	    <link rel="stylesheet" href="assets/css/red.css">
		<link rel="stylesheet" href="assets/css/font-awesome.min.css">
		<link href='https://fonts.googleapis.com/css?family=Roboto:300,400,500,700' rel='stylesheet' type='text/css'>

<script type="text/javascript">
function valid() {
    if (document.chngpwd.cpass.value == "") {
        alert("Current Password Field is Empty !!");
        document.chngpwd.cpass.focus();
        return false;
    } else if (document.chngpwd.newpass.value == "") {
        alert("New Password Field is Empty !!");
        document.chngpwd.newpass.focus();
        return false;
    } else if (document.chngpwd.cnfpass.value == "") {
        alert("Confirm Password Field is Empty !!");
        document.chngpwd.cnfpass.focus();
        return false;
    } else if (document.chngpwd.newpass.value != document.chngpwd.cnfpass.value) {
        alert("Password and Confirm Password Field do not match !!");
        document.chngpwd.cnfpass.focus();
        return false;
    }
    return true;
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
				<li class='active'>Change Password</li>
			</ul>
		</div>
	</div>
</div>

<div class="body-content outer-top-bd">
    <div class="container">
        <div class="sign-in-page inner-bottom-sm">
            <div class="row">
                <div class="col-md-6 col-sm-6 sign-in">
                    <h4 class="">Change Password</h4>
                    <form method="post" name="chngpwd" class="register-form outer-top-xs" onSubmit="return valid();">
                        <div class="form-group">
                            <label class="info-title" for="cpass">Current Password <span>*</span></label>
                            <input type="password" class="form-control unicase-form-control text-input" id="cpass" name="cpass" required="required">
                        </div>
                        <div class="form-group">
                            <label class="info-title" for="newpass">New Password <span>*</span></label>
                            <input type="password" class="form-control unicase-form-control text-input" id="newpass" name="newpass" required="required">
                        </div>
                        <div class="form-group">
                            <label class="info-title" for="cnfpass">Confirm Password <span>*</span></label>
                            <input type="password" class="form-control unicase-form-control text-input" id="cnfpass" name="cnfpass" required="required">
                        </div>
                        <button type="submit" name="update" class="btn-upper btn btn-primary checkout-page-button">Update Password</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('includes/footer.php'); ?>
<script src="assets/js/jquery-1.11.1.min.js"></script>
<script src="assets/js/bootstrap.min.js"></script>
</body>
</html>
<?php } ?>
