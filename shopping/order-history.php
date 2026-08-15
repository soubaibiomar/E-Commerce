<?php 
session_start();
include('includes/config.php');

if (empty($_SESSION['login']) || empty($_SESSION['id'])) {   
    header('location:login.php');
    exit();
}
$userId = intval($_SESSION['id']);
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
	    <title>Settled Orders Ledger | ZeyTech</title>
	    <link rel="preconnect" href="https://fonts.googleapis.com">
	    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	    <link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,400;0,9..144,600;0,9..144,700;0,9..144,800;1,9..144,400&family=IBM+Plex+Sans:ital,wght@0,400;0,500;0,600;0,700;1,400&family=Space+Mono:ital,wght@0,400;0,700;1,400&display=swap" rel="stylesheet">
	    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
	    <link rel="stylesheet" href="assets/css/font-awesome.min.css">
	    <link rel="stylesheet" href="assets/css/modern-storefront.css">
		<link rel="shortcut icon" href="assets/images/favicon.ico">
		<script language="javascript" type="text/javascript">
		var popUpWin=0;
		function popUpWindow(URLStr, left, top, width, height) {
			if(popUpWin) {
				if(!popUpWin.closed) popUpWin.close();
			}
			popUpWin = open(URLStr,'popUpWin', 'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,copyhistory=yes,width='+650+',height='+650+',left='+left+', top='+top+',screenX='+left+',screenY='+top+'');
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
		<div style="display:flex; justify-content:space-between; align-items:baseline; margin-bottom:20px; border-bottom:1px solid rgba(142,162,191,0.18); padding-bottom:12px;">
			<div>
				<div style="font-family:'Space Mono'; font-size:10px; font-weight:700; color:#c79a44; text-transform:uppercase; letter-spacing:0.08em;">
					[SETTLEMENT.ORDER_LEDGER]
				</div>
				<h2 style="font-family:'Fraunces', serif; font-size:24px; font-weight:700; color:#f2efe6; margin:4px 0 0 0; letter-spacing:-0.02em;">Settled Orders &amp; Waybills</h2>
			</div>
			<div style="font-family:'Space Mono'; font-size:11px; color:#8ea2bf;">
				SETTLEMENT CURRENCY: <strong style="color:#d9b567;"><?php echo get_current_currency()['code']; ?></strong>
			</div>
		</div>

		<div class="manifest-panel" style="padding:0; overflow:hidden;">
			<div class="table-responsive">
				<table class="enterprise-table">
					<thead>
						<tr>
							<th style="width:40px;">#</th>
							<th style="width:70px;">Item</th>
							<th>Details</th>
							<th>Qty</th>
							<th>Unit Price</th>
							<th>Total</th>
							<th>Payment</th>
							<th>Timestamp</th>
							<th>Action</th>
						</tr>
					</thead>
					<tbody>
					<?php 
					$orders = db_fetch_all("SELECT products.productImage1 AS pimg1, products.productName AS pname, products.id AS proid, orders.productId AS opid, orders.quantity AS qty, products.productPrice AS pprice, products.shippingCharge AS shippingcharge, orders.paymentMethod AS paym, orders.orderDate AS odate, orders.orderStatus AS ostatus, orders.id AS orderid FROM orders JOIN products ON orders.productId=products.id WHERE orders.userId=? AND orders.paymentMethod IS NOT NULL ORDER BY orders.id DESC", [$userId], "i");
					$cnt = 1;
					if (!empty($orders)) {
					    foreach ($orders as $row) {
					        $qty = intval($row['qty']);
					        $price = floatval($row['pprice']);
					        $shippcharge = floatval($row['shippingcharge']);
					        $grandTotal = ($qty * $price) + $shippcharge;
					?>
						<tr>
							<td style="font-family:'Space Mono'; font-size:11px; color:#8ea2bf;">#<?php echo str_pad($cnt, 2, '0', STR_PAD_LEFT);?></td>
							<td>
								<a href="product-details.php?pid=<?php echo e($row['opid']);?>">
								    <img src="admin/productimages/<?php echo e($row['proid']);?>/<?php echo e($row['pimg1']);?>" style="width:50px; height:50px; object-fit:contain; border-radius:2px; background:#080e1a; border:1px solid rgba(142,162,191,0.2);">
								</a>
							</td>
							<td>
								<div style="font-family:'IBM Plex Sans'; font-size:13px; font-weight:600; color:#f2efe6;">
									<a href="product-details.php?pid=<?php echo e($row['opid']);?>" style="color:#f2efe6; text-decoration:none;"><?php echo e($row['pname']);?></a>
								</div>
								<span style="font-family:'Space Mono'; font-size:10px; color:#8ea2bf;">[ORDER #<?php echo e($row['orderid']); ?>]</span>
							</td>
							<td style="font-family:'Space Mono'; font-weight:700; color:#f2efe6;"><?php echo e($qty); ?></td>
							<td style="font-family:'Space Mono'; font-size:12px; color:#8ea2bf;"><?php echo format_price($price); ?></td>
							<td style="font-family:'Space Mono'; font-weight:700; color:#d9b567; font-size:13px;"><?php echo format_price($grandTotal);?></td>
							<td><span class="tag-pill tag-gold" style="font-size:10px;"><?php echo e($row['paym']); ?></span></td>
							<td style="font-family:'Space Mono'; font-size:11px; color:#8ea2bf;"><?php echo e($row['odate']); ?></td>
							<td>
								<a href="track-orders.php?tr=<?php echo e($row['orderid']);?>" class="btn-ghost" style="padding:2px 8px; font-size:10px; font-family:'Space Mono';">
									TRACK &rarr;
								</a>
							</td>
						</tr>
					<?php 
					        $cnt++;
					    } 
					} else { ?>
					    <tr>
					    	<td colspan="9" style="text-align:center; padding:50px 20px; font-family:'Space Mono'; color:#8ea2bf;">
					    		No settled order transactions recorded on this profile ledger.
					    	</td>
					    </tr>
					<?php } ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>

<?php include('includes/footer.php');?>
<script src="assets/js/jquery-1.11.1.min.js"></script>
<script src="assets/js/bootstrap.min.js"></script>
</body>
</html>