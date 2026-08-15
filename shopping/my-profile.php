<?php 
session_start();
include_once('includes/config.php');

if (empty($_SESSION['id'])) {   
    header('location:login.php');
    exit();
} else {
    $uid = intval($_SESSION['id']);

    if (isset($_POST['update'])) {
        $name = trim($_POST['fullname'] ?? '');
        $contactno = trim($_POST['contactnumber'] ?? '');
        $query = db_query("UPDATE users SET name=?, contactno=? WHERE id=?", [$name, $contactno, $uid], "ssi");
        if ($query) {
            echo "<script>alert('Profile Updated successfully'); window.location='my-profile.php';</script>";
            exit();
        } else {
            echo "<script>alert('Something went wrong. Please try again.'); window.location='my-profile.php';</script>";
            exit();
        }
    }

    $result = db_fetch_one("SELECT * FROM users WHERE id=?", [$uid], "i");
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <title>Shopping | User Profile</title>
        <link rel="icon" type="image/x-icon" href="assets/favicon.ico" />
        <link rel="stylesheet" href="assets/css/bootstrap.min.css">
	    <link rel="stylesheet" href="assets/css/main.css">
	    <link rel="stylesheet" href="assets/css/red.css">
	    <link rel="stylesheet" href="assets/css/font-awesome.min.css">
		<link href='https://fonts.googleapis.com/css?family=Roboto:300,400,500,700' rel='stylesheet' type='text/css'>
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
				<li class='active'>My Profile</li>
			</ul>
		</div>
	</div>
</div>

<div class="body-content outer-top-bd">
    <div class="container">
        <div class="checkout-box inner-bottom-sm">
            <div class="row">
                <div class="col-md-8">
                    <h4><?php echo e($result['name'] ?? '');?>'s Profile</h4>
                    <form method="post" name="profile" class="register-form outer-top-xs">
                        <div class="form-group">
                            <label class="info-title" for="fullname">Full Name <span>*</span></label>
                            <input type="text" name="fullname" id="fullname" value="<?php echo e($result['name'] ?? '');?>" class="form-control unicase-form-control text-input" required>
                        </div>
                        <div class="form-group">
                            <label class="info-title" for="emailid">Email ID <span>*</span></label>
                            <input type="email" name="emailid" id="emailid" class="form-control unicase-form-control text-input" value="<?php echo e($result['email'] ?? '');?>" readonly>
                        </div>
                        <div class="form-group">
                            <label class="info-title" for="contactnumber">Contact Number <span>*</span></label>
                            <input type="text" name="contactnumber" id="contactnumber" value="<?php echo e($result['contactno'] ?? '');?>" class="form-control unicase-form-control text-input" maxlength="10" required>
                        </div>
                        <button type="submit" name="update" id="update" class="btn-upper btn btn-primary checkout-page-button">Update Profile</button>
                    </form>
                </div>
                <?php include('includes/myaccount-sidebar.php');?>
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
