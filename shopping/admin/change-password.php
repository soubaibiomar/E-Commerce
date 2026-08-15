<?php
session_start();
include('include/config.php');

if (empty($_SESSION['alogin'])) {	
    header('location:index.php');
    exit();
} else {
    $username = $_SESSION['alogin'];

    if (isset($_POST['submit'])) {
        $oldpwd = $_POST['password'] ?? '';
        $newpwd = $_POST['newpassword'] ?? '';
        $currentTime = date('d-m-Y h:i:s A');

        $admin = db_fetch_one("SELECT id, password FROM admin WHERE username=?", [$username], "s");
        if ($admin && verify_and_rehash_password($oldpwd, $admin['password'])) {
            $newHashed = hash_password($newpwd);
            db_query("UPDATE admin SET password=?, updationDate=? WHERE username=?", [$newHashed, $currentTime, $username], "sss");
            $_SESSION['msg'] = "Password Changed Successfully !!";
        } else {
            $_SESSION['msg'] = "Old Password does not match !!";
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Admin | Change Password</title>
	<link type="text/css" href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
	<link type="text/css" href="bootstrap/css/bootstrap-responsive.min.css" rel="stylesheet">
	<link type="text/css" href="css/theme.css" rel="stylesheet">
	<link type="text/css" href="images/icons/css/font-awesome.css" rel="stylesheet">
	<link type="text/css" href='https://fonts.googleapis.com/css?family=Open+Sans:400italic,600italic,400,600' rel='stylesheet'>
	<script type="text/javascript">
function valid() {
    if (document.chngpwd.password.value == "") {
        alert("Current Password Field is Empty !!");
        document.chngpwd.password.focus();
        return false;
    } else if (document.chngpwd.newpassword.value == "") {
        alert("New Password Field is Empty !!");
        document.chngpwd.newpassword.focus();
        return false;
    } else if (document.chngpwd.confirmpassword.value == "") {
        alert("Confirm Password Field is Empty !!");
        document.chngpwd.confirmpassword.focus();
        return false;
    } else if (document.chngpwd.newpassword.value != document.chngpwd.confirmpassword.value) {
        alert("Password and Confirm Password Field do not match !!");
        document.chngpwd.confirmpassword.focus();
        return false;
    }
    return true;
}
</script>
</head>
<body>
<?php include('include/header.php');?>

	<div class="wrapper">
		<div class="container">
			<div class="row">
<?php include('include/sidebar.php');?>				
			<div class="span9">
					<div class="content">

						<div class="module">
							<div class="module-head">
								<h3>Admin Change Password</h3>
							</div>
							<div class="module-body">

<?php if (isset($_SESSION['msg']) && !empty($_SESSION['msg'])) { ?>
									<div class="alert alert-info">
										<button type="button" class="close" data-dismiss="alert">×</button>
										<?php echo e($_SESSION['msg']); $_SESSION['msg'] = ""; ?>
									</div>
<?php } ?>
									<br />

			<form class="form-horizontal row-fluid" name="chngpwd" method="post" onSubmit="return valid();">
									
<div class="control-group">
<label class="control-label" for="password">Current Password</label>
<div class="controls">
<input type="password" placeholder="Enter your current Password" id="password" name="password" class="span8 tip" required>
</div>
</div>

<div class="control-group">
<label class="control-label" for="newpassword">New Password</label>
<div class="controls">
<input type="password" placeholder="Enter your new Password" id="newpassword" name="newpassword" class="span8 tip" required>
</div>
</div>

<div class="control-group">
<label class="control-label" for="confirmpassword">Confirm Password</label>
<div class="controls">
<input type="password" placeholder="Enter your new Password again" id="confirmpassword" name="confirmpassword" class="span8 tip" required>
</div>
</div>

										<div class="control-group">
											<div class="controls">
												<button type="submit" name="submit" class="btn btn-primary">Submit</button>
											</div>
										</div>
									</form>
							</div>
						</div>
					</div><!--/.content-->
				</div><!--/.span9-->
			</div>
		</div><!--/.container-->
	</div><!--/.wrapper-->

<?php include('include/footer.php');?>

	<script src="scripts/jquery-1.9.1.min.js" type="text/javascript"></script>
	<script src="scripts/jquery-ui-1.10.1.custom.min.js" type="text/javascript"></script>
	<script src="bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
</body>
</html>
<?php } ?>