<?php 
session_start();
include('includes/config.php');

if (empty($_SESSION['id'])) {   
    header('location:login.php');
    exit();
} else {
    $uid = intval($_SESSION['id']);

	if (isset($_GET['id'])) {
        $delId = intval($_GET['id']);
		db_query("DELETE FROM orders WHERE userId=? AND (paymentMethod IS NULL OR paymentMethod='') AND id=?", [$uid, $delId], "ii");
	}
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
	    <title>Pending Orders | Storefront</title>
	    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
	    <link rel="stylesheet" href="assets/css/main.css">
	    <link rel="stylesheet" href="assets/css/blue.css">
	    <link rel="stylesheet" href="assets/css/font-awesome.min.css">
		<link rel="shortcut icon" href="assets/images/favicon.ico">
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
			<li style="color:var(--color-dark); font-weight:700;">Pending Orders</li>
		</ul>
	</div>
</div>

<div class="body-content">
	<div class="container">
		<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:24px;">
			<h2 style="margin:0; font-size:24px; font-weight:800; color:var(--color-dark);"><i class="fa fa-clock-o" style="color:var(--color-primary); margin-right:8px;"></i> Pending Orders (Awaiting Payment)</h2>
			<span style="font-size:13px; color:var(--color-text-muted);">Prices in <strong style="color:var(--color-primary);"><?php echo get_current_currency()['code']; ?> (<?php echo get_current_currency()['symbol']; ?>)</strong></span>
		</div>

		<div class="modern-cart-table table-responsive">
			<table class="table table-hover" style="margin-bottom:0;">
				<thead>
					<tr>
						<th style="width:40px;">#</th>
						<th style="width:90px;">Product</th>
						<th>Description</th>
						<th>Qty</th>
						<th>Unit Price</th>
						<th>Shipping</th>
						<th>Grand Total</th>
						<th>Status</th>
						<th>Order Date</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody>
				<?php 
				$pendingOrders = db_fetch_all("SELECT products.productImage1 as pimg1, products.productName as pname, products.id as proid, orders.productId as opid, orders.quantity as qty, products.productPrice as pprice, products.shippingCharge as shippingcharge, orders.paymentMethod as paym, orders.orderDate as odate, orders.id as oid FROM orders JOIN products ON orders.productId=products.id WHERE orders.userId=? AND (orders.paymentMethod IS NULL OR orders.paymentMethod='') ORDER BY orders.id DESC", [$uid], "i");

				$cnt = 1;
				if (!empty($pendingOrders)) {
				    foreach ($pendingOrders as $row) {
				        $qty = intval($row['qty']);
				        $price = floatval($row['pprice']);
				        $shippcharge = floatval($row['shippingcharge']);
				        $grandTotal = ($qty * $price) + $shippcharge;
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
							<span style="font-size:12px; color:var(--color-text-muted);">Pending Order #<?php echo e($row['oid']); ?></span>
						</td>
						<td style="font-weight:700;"><?php echo e($qty); ?></td>
						<td style="font-weight:700;"><?php echo format_price($price); ?></td>
						<td style="font-size:13px;"><?php echo ($shippcharge == 0) ? 'Free' : format_price($shippcharge); ?></td>
						<td style="font-weight:800; color:var(--color-primary); font-size:15px;"><?php echo format_price($grandTotal);?></td>
						<td><span class="badge" style="background:#fef3c7; color:#92400e; font-weight:700;">Awaiting Payment</span></td>
						<td style="font-size:13px; color:var(--color-text-muted);"><?php echo e($row['odate']); ?></td>
						<td>
							<a href="pending-orders.php?id=<?php echo e($row['oid']); ?>" class="btn btn-default btn-xs" onclick="return confirm('Cancel this pending order?');" style="color:#ef4444; border:1px solid #fee2e2; border-radius:6px; font-weight:700; padding:4px 10px;">
								<i class="fa fa-trash"></i> Cancel
							</a>
						</td>
					</tr>
				<?php 
				        $cnt++;
				    } 
				?>
				<tr>
					<td colspan="10" style="background:#f8fafc; padding:20px 16px;">
				        <div style="display:flex; justify-content:flex-end;">
				            <a href="payment-method.php" class="btn btn-primary" style="background:var(--color-primary-gradient); border:none; border-radius:8px; padding:12px 28px; font-weight:700; font-size:14px; box-shadow:var(--shadow-glow);">
				            	<i class="fa fa-credit-card"></i> Proceed to Payment
				            </a>
						</div>
				    </td>
				</tr>
				<?php } else { ?>
				<tr>
				    <td colspan="10" style="text-align:center; padding:40px 20px; color:var(--color-text-muted);">
				    	<i class="fa fa-check-circle" style="font-size:36px; color:#10b981; margin-bottom:10px; display:block;"></i>
				    	No pending orders. All orders are up to date!
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
<?php } ?>