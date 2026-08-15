<?php 
session_start();
include('includes/config.php');

if (empty($_SESSION['id'])) {   
    header('location:login.php');
    exit();
} else {
    $uid = intval($_SESSION['id']);

	if (isset($_POST['submit'])) {
        $paymethod = trim($_POST['paymethod'] ?? 'COD');
		db_query("UPDATE orders SET paymentMethod=? WHERE userId=? AND (paymentMethod IS NULL OR paymentMethod='')", [$paymethod, $uid], "si");
		unset($_SESSION['cart']);
		header('location:order-history.php');
        exit();
	}
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
	    <title>Settlement Channel | ZeyTech</title>
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

<div class="body-content" style="padding-top:36px; padding-bottom:60px;">
	<div class="container">
		<div class="row">
			<div class="col-md-8 col-md-offset-2">
				<div class="manifest-panel" style="padding:32px;">
					<div style="font-family:'Space Mono'; font-size:10px; font-weight:700; color:#c79a44; text-transform:uppercase; letter-spacing:0.08em; margin-bottom:8px;">
						[SETTLEMENT.GATEWAY_ROUTING]
					</div>
					<h2 style="font-family:'Fraunces', serif; font-size:24px; font-weight:700; color:#f2efe6; margin:0 0 8px 0; letter-spacing:-0.02em;">Select Settlement Channel</h2>
					<p style="font-size:13px; color:#8ea2bf; margin-bottom:24px;">Choose your payment gateway for Casablanca regional fulfillment.</p>

					<form name="payment" method="post">
						<div style="display:flex; flex-direction:column; gap:12px; margin-bottom:24px;">
							<label style="background:#080e1a; border:1px solid rgba(142,162,191,0.25); border-radius:2px; padding:16px; display:flex; align-items:center; gap:12px; cursor:pointer;">
								<input type="radio" name="paymethod" value="COD" checked="checked">
								<div>
									<div style="font-family:'Space Mono'; font-size:12px; font-weight:700; color:#f2efe6;">[COD] CASH ON DELIVERY</div>
									<div style="font-size:12px; color:#8ea2bf; margin-top:2px;">Settlement in MAD upon parcel delivery anywhere across Morocco.</div>
								</div>
							</label>

							<label style="background:#080e1a; border:1px solid rgba(142,162,191,0.25); border-radius:2px; padding:16px; display:flex; align-items:center; gap:12px; cursor:pointer;">
								<input type="radio" name="paymethod" value="CMI Gateway">
								<div>
									<div style="font-family:'Space Mono'; font-size:12px; font-weight:700; color:#f2efe6;">[CMI] MOROCCAN BANKING GATEWAY</div>
									<div style="font-size:12px; color:#8ea2bf; margin-top:2px;">Instant 3D-Secure clearing via Moroccan bank cards (Attijari, BCP, BMCE, CIH).</div>
								</div>
							</label>

							<label style="background:#080e1a; border:1px solid rgba(142,162,191,0.25); border-radius:2px; padding:16px; display:flex; align-items:center; gap:12px; cursor:pointer;">
								<input type="radio" name="paymethod" value="International Card / Crypto">
								<div>
									<div style="font-family:'Space Mono'; font-size:12px; font-weight:700; color:#f2efe6;">[VISA / MASTERCARD / USDT] GLOBAL SETTLEMENT</div>
									<div style="font-size:12px; color:#8ea2bf; margin-top:2px;">Multi-currency settlement in USD, EUR, or USDT with real-time conversion.</div>
								</div>
							</label>
						</div>

						<button type="submit" name="submit" class="btn-primary" style="width:100%; padding:12px; font-family:'Space Mono'; font-size:12px;">
							CONFIRM SETTLEMENT &amp; RECORD ORDER &rarr;
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
<?php } ?>