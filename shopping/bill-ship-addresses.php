<?php
session_start();
include('includes/config.php');

if (empty($_SESSION['id'])) {   
    header('location:login.php');
    exit();
} else {
	$uid = intval($_SESSION['id']);

	// code for billing address update
	if (isset($_POST['update'])) {
		$baddress = trim($_POST['billingaddress'] ?? '');
		$bstate = trim($_POST['bilingstate'] ?? '');
		$bcity = trim($_POST['billingcity'] ?? '');
		$bpincode = intval($_POST['billingpincode'] ?? 0);
		db_query("UPDATE users SET billingAddress=?, billingState=?, billingCity=?, billingPincode=? WHERE id=?", [$baddress, $bstate, $bcity, $bpincode, $uid], "sssii");
		echo "<script>alert('Billing Address recorded on ledger.');</script>";
	}

	// code for shipping address update
	if (isset($_POST['shipupdate'])) {
		$saddress = trim($_POST['shippingaddress'] ?? '');
		$sstate = trim($_POST['shippingstate'] ?? '');
		$scity = trim($_POST['shippingcity'] ?? '');
		$spincode = intval($_POST['shippingpincode'] ?? 0);
		db_query("UPDATE users SET shippingAddress=?, shippingState=?, shippingCity=?, shippingPincode=? WHERE id=?", [$saddress, $sstate, $scity, $spincode, $uid], "sssii");
		echo "<script>alert('Shipping Address recorded on ledger.');</script>";
	}

    $user = db_fetch_one("SELECT * FROM users WHERE id=?", [$uid], "i");
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
	    <title>Shipping &amp; Billing Addresses | ZeyTech</title>
	    <link rel="preconnect" href="https://fonts.googleapis.com">
	    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	    <link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,400;0,9..144,600;0,9..144,700;0,9..144,800;1,9..144,400&family=IBM+Plex+Sans:ital,wght@0,400;0,500;0,600;0,700;1,400&family=Space+Mono:ital,wght@0,400;0,700;1,400&display=swap" rel="stylesheet">
	    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
	    <link rel="stylesheet" href="assets/css/font-awesome.min.css">
	    <link rel="stylesheet" href="assets/css/modern-storefront.css">
		<link rel="shortcut icon" href="assets/images/favicon.ico">
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
						[DESTINATION.BILLING_RECORD]
					</div>
					<h2 style="font-family:'Fraunces', serif; font-size:22px; font-weight:700; color:#f2efe6; margin:0 0 16px 0; letter-spacing:-0.02em;">Billing Address</h2>

					<form class="register-form" role="form" method="post">
						<div class="form-group" style="margin-bottom:14px;">
						    <label style="font-family:'Space Mono'; font-size:11px; font-weight:700; color:#8ea2bf; text-transform:uppercase;" for="billingaddress">STREET ADDRESS *</label>
						    <textarea class="form-control" id="billingaddress" name="billingaddress" required="required" rows="2" style="background:#080e1a; border:1px solid rgba(142,162,191,0.25); color:#f2efe6; font-size:13px; border-radius:2px;"><?php echo e($user['billingAddress'] ?? '');?></textarea>
						</div>
						<div class="form-group" style="margin-bottom:14px;">
						    <label style="font-family:'Space Mono'; font-size:11px; font-weight:700; color:#8ea2bf; text-transform:uppercase;" for="bilingstate">REGION / STATE *</label>
						    <input type="text" class="form-control" id="bilingstate" name="bilingstate" value="<?php echo e($user['billingState'] ?? '');?>" required style="height:42px; border-radius:2px; border:1px solid rgba(142,162,191,0.25); background:#080e1a; color:#f2efe6; font-size:13px;">
						</div>
						<div class="form-group" style="margin-bottom:14px;">
						    <label style="font-family:'Space Mono'; font-size:11px; font-weight:700; color:#8ea2bf; text-transform:uppercase;" for="billingcity">CITY *</label>
						    <input type="text" class="form-control" id="billingcity" name="billingcity" required="required" value="<?php echo e($user['billingCity'] ?? '');?>" style="height:42px; border-radius:2px; border:1px solid rgba(142,162,191,0.25); background:#080e1a; color:#f2efe6; font-size:13px;">
						</div>
						<div class="form-group" style="margin-bottom:20px;">
						    <label style="font-family:'Space Mono'; font-size:11px; font-weight:700; color:#8ea2bf; text-transform:uppercase;" for="billingpincode">POSTAL CODE *</label>
						    <input type="text" class="form-control" id="billingpincode" name="billingpincode" required="required" value="<?php echo e($user['billingPincode'] ?? '');?>" style="height:42px; border-radius:2px; border:1px solid rgba(142,162,191,0.25); background:#080e1a; color:#f2efe6; font-size:13px;">
						</div>
						<button type="submit" name="update" class="btn-primary" style="padding:10px 20px; font-family:'Space Mono'; font-size:11px;">SAVE BILLING ADDRESS</button>
					</form>
				</div>

				<div class="manifest-panel" style="padding:28px;">
					<div style="font-family:'Space Mono'; font-size:10px; font-weight:700; color:#c79a44; text-transform:uppercase; letter-spacing:0.08em; margin-bottom:8px;">
						[DESTINATION.SHIPPING_RECORD]
					</div>
					<h2 style="font-family:'Fraunces', serif; font-size:22px; font-weight:700; color:#f2efe6; margin:0 0 16px 0; letter-spacing:-0.02em;">Shipping Destination</h2>

					<form class="register-form" role="form" method="post">
						<div class="form-group" style="margin-bottom:14px;">
						    <label style="font-family:'Space Mono'; font-size:11px; font-weight:700; color:#8ea2bf; text-transform:uppercase;" for="shippingaddress">STREET ADDRESS *</label>
						    <textarea class="form-control" id="shippingaddress" name="shippingaddress" required="required" rows="2" style="background:#080e1a; border:1px solid rgba(142,162,191,0.25); color:#f2efe6; font-size:13px; border-radius:2px;"><?php echo e($user['shippingAddress'] ?? '');?></textarea>
						</div>
						<div class="form-group" style="margin-bottom:14px;">
						    <label style="font-family:'Space Mono'; font-size:11px; font-weight:700; color:#8ea2bf; text-transform:uppercase;" for="shippingstate">REGION / STATE *</label>
						    <input type="text" class="form-control" id="shippingstate" name="shippingstate" value="<?php echo e($user['shippingState'] ?? '');?>" required style="height:42px; border-radius:2px; border:1px solid rgba(142,162,191,0.25); background:#080e1a; color:#f2efe6; font-size:13px;">
						</div>
						<div class="form-group" style="margin-bottom:14px;">
						    <label style="font-family:'Space Mono'; font-size:11px; font-weight:700; color:#8ea2bf; text-transform:uppercase;" for="shippingcity">CITY *</label>
						    <input type="text" class="form-control" id="shippingcity" name="shippingcity" required="required" value="<?php echo e($user['shippingCity'] ?? '');?>" style="height:42px; border-radius:2px; border:1px solid rgba(142,162,191,0.25); background:#080e1a; color:#f2efe6; font-size:13px;">
						</div>
						<div class="form-group" style="margin-bottom:20px;">
						    <label style="font-family:'Space Mono'; font-size:11px; font-weight:700; color:#8ea2bf; text-transform:uppercase;" for="shippingpincode">POSTAL CODE *</label>
						    <input type="text" class="form-control" id="shippingpincode" name="shippingpincode" required="required" value="<?php echo e($user['shippingPincode'] ?? '');?>" style="height:42px; border-radius:2px; border:1px solid rgba(142,162,191,0.25); background:#080e1a; color:#f2efe6; font-size:13px;">
						</div>
						<button type="submit" name="shipupdate" class="btn-primary" style="padding:10px 20px; font-family:'Space Mono'; font-size:11px;">SAVE SHIPPING DESTINATION</button>
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