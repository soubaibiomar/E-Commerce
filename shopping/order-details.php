<?php 
session_start();
include('includes/config.php');
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
	    <title>Order Details | Storefront</title>
	    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
	    <link rel="stylesheet" href="assets/css/main.css">
	    <link rel="stylesheet" href="assets/css/blue.css">
	    <link rel="stylesheet" href="assets/css/font-awesome.min.css">
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
    <body class="cnt-home">
	
<header class="header-style-1">
<?php include('includes/top-header.php');?>
<?php include('includes/main-header.php');?>
<?php include('includes/menu-bar.php');?>
</header>

<div style="background:#ffffff; border-bottom:1px solid var(--color-border); padding:12px 0; margin-bottom:28px;">
	<div class="container">
		<ul class="list-inline list-unstyled" style="margin-bottom:0; font-size:13px; color:var(--color-text-muted);">
			<li><a href="index.php"><i class="fa fa-home"></i> Home</a></li>
			<li>&rsaquo;</li>
			<li style="color:var(--color-dark); font-weight:700;">Order Details</li>
		</ul>
	</div>
</div>

<div class="body-content">
	<div class="container">
		<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:24px;">
			<h2 style="margin:0; font-size:24px; font-weight:800; color:var(--color-dark);"><i class="fa fa-file-text-o" style="color:var(--color-primary); margin-right:8px;"></i> Order Breakdown</h2>
			<span style="font-size:13px; color:var(--color-text-muted);">Prices in <strong style="color:var(--color-primary);"><?php echo get_current_currency()['code']; ?> (<?php echo get_current_currency()['symbol']; ?>)</strong></span>
		</div>

		<div class="modern-cart-table table-responsive">
			<table class="table table-hover" style="margin-bottom:0;">
				<thead>
					<tr>
						<th style="width:40px;">#</th>
						<th style="width:90px;">Product</th>
						<th>Details</th>
						<th>Qty</th>
						<th>Unit Price</th>
						<th>Total</th>
						<th>Payment</th>
						<th>Order Date</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody>
				<?php 
				$orderid = intval($_POST['orderid'] ?? $_GET['orderid'] ?? 0);
				$email = trim($_POST['email'] ?? $_SESSION['login'] ?? '');

				$rows = [];
				if ($orderid > 0 && !empty($email)) {
				    $authCheck = db_fetch_one("SELECT odrs.id FROM orders AS odrs JOIN users AS usr ON usr.id = odrs.userId WHERE usr.email = ? AND odrs.id = ?", [$email, $orderid], "si");
				    if ($authCheck) {
				        $rows = db_fetch_all("SELECT products.productImage1 AS pimg1, products.productName AS pname, products.id as proid, orders.productId AS opid, orders.quantity AS qty, products.productPrice AS pprice, orders.paymentMethod AS paym, orders.orderDate AS odate, orders.id AS orderid FROM orders JOIN products ON orders.productId=products.id WHERE orders.id=? AND orders.paymentMethod IS NOT NULL", [$orderid], "i");
				    }
				}

				if (!empty($rows)) {
				    $cnt = 1;
				    foreach ($rows as $row) {
				        $qty = intval($row['qty']);
				        $price = floatval($row['pprice']);
				?>
					<tr>
						<td style="font-weight:700; color:#94a3b8;"><?php echo e($cnt);?></td>
						<td>
							<a href="product-details.php?pid=<?php echo e($row['opid']);?>">
							    <img src="admin/productimages/<?php echo e($row['proid']);?>/<?php echo e($row['pimg1']);?>" style="width:65px; height:65px; object-fit:contain; border-radius:8px; background:#f8fafc; border:1px solid #e2e8f0;">
							</a>
						</td>
						<td>
							<h4 style="font-size:14px; font-weight:700; margin-bottom:4px;">
								<a href="product-details.php?pid=<?php echo e($row['opid']);?>"><?php echo e($row['pname']);?></a>
							</h4>
							<span style="font-size:12px; color:var(--color-text-muted);">Order #<?php echo e($row['orderid']); ?></span>
						</td>
						<td style="font-weight:700;"><?php echo e($qty); ?></td>
						<td style="font-weight:700;"><?php echo format_price($price); ?></td>
						<td style="font-weight:800; color:var(--color-primary); font-size:15px;"><?php echo format_price($qty * $price);?></td>
						<td><span class="badge" style="background:#e0e7ff; color:#4338ca; font-weight:700;"><?php echo e($row['paym']); ?></span></td>
						<td style="font-size:13px; color:var(--color-text-muted);"><?php echo e($row['odate']); ?></td>
						<td>
							<a href="javascript:void(0);" onClick="popUpWindow('track-order.php?oid=<?php echo e($row['orderid']);?>', 50, 50, 650, 650);" class="btn btn-default btn-xs" style="border-radius:6px; font-weight:700; color:var(--color-primary); border:1px solid rgba(79,70,229,0.3); padding:4px 10px;">
								<i class="fa fa-map-marker"></i> Track
							</a>
						</td>
					</tr>
				<?php 
				        $cnt++;
				    } 
				} else { ?>
					<tr>
						<td colspan="9" style="text-align:center; padding:40px 20px; color:var(--color-text-muted);">
							<i class="fa fa-search" style="font-size:36px; color:#cbd5e1; margin-bottom:10px; display:block;"></i>
							Either the Order ID or registered email is incorrect, or no matching order was found.
						</td>
					</tr>
				<?php } ?>
				</tbody>
			</table>
		</div>

		<?php include('includes/brands-slider.php');?>
	</div>
</div>

<?php include('includes/footer.php');?>
	<script src="assets/js/jquery-1.11.1.min.js"></script>
	<script src="assets/js/bootstrap.min.js"></script>
</body>
</html>