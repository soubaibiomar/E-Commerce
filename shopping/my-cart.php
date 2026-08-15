<?php 
session_start();
include('includes/config.php');

// Code for Update Cart
if (isset($_POST['submit'])) {
    if (!empty($_SESSION['cart']) && isset($_POST['quantity']) && is_array($_POST['quantity'])) {
        foreach ($_POST['quantity'] as $key => $val) {
            $key = intval($key);
            $val = intval($val);
            if ($val <= 0) {
                unset($_SESSION['cart'][$key]);
            } else {
                $_SESSION['cart'][$key]['quantity'] = $val;
            }
        }
        echo "<script>alert('Quantities updated on ledger.');</script>";
    }
}

// Code for Remove a Product from Cart
if (isset($_POST['remove_code']) && is_array($_POST['remove_code'])) {
    if (!empty($_SESSION['cart'])) {
        foreach ($_POST['remove_code'] as $key) {
            unset($_SESSION['cart'][intval($key)]);
        }
        echo "<script>alert('Selected items removed from bag.');</script>";
    }
}

// Direct remove query param
if (isset($_GET['del'])) {
    $delId = intval($_GET['del']);
    unset($_SESSION['cart'][$delId]);
    header('location:my-cart.php');
    exit();
}

// Code for insert product in order table
if (isset($_POST['ordersubmit'])) {
    if (empty($_SESSION['login']) || empty($_SESSION['id'])) {   
        header('location:login.php');
        exit();
    } else {
        if (!empty($_SESSION['cart'])) {
            $userId = intval($_SESSION['id']);
            foreach ($_SESSION['cart'] as $productId => $cartItem) {
                $pid = intval($productId);
                $qty = max(1, intval($cartItem['quantity'] ?? 1));
                db_query("INSERT INTO orders(userId, productId, quantity) VALUES(?, ?, ?)", [$userId, $pid, $qty], "iis");
            }
            header('location:payment-method.php');
            exit();
        }
    }
}

// Code for billing address update
if (isset($_POST['update'])) {
    if (!empty($_SESSION['id'])) {
        $userId = intval($_SESSION['id']);
        $baddress = trim($_POST['billingaddress'] ?? '');
        $bstate = trim($_POST['bilingstate'] ?? '');
        $bcity = trim($_POST['billingcity'] ?? '');
        $bpincode = intval($_POST['billingpincode'] ?? 0);
        
        $query = db_query("UPDATE users SET billingAddress=?, billingState=?, billingCity=?, billingPincode=? WHERE id=?", [$baddress, $bstate, $bcity, $bpincode, $userId], "sssii");
        if ($query) {
            echo "<script>alert('Billing Address saved to record.');</script>";
        }
    }
}

// Code for shipping address update
if (isset($_POST['shipupdate'])) {
    if (!empty($_SESSION['id'])) {
        $userId = intval($_SESSION['id']);
        $saddress = trim($_POST['shippingaddress'] ?? '');
        $sstate = trim($_POST['shippingstate'] ?? '');
        $scity = trim($_POST['shippingcity'] ?? '');
        $spincode = intval($_POST['shippingpincode'] ?? 0);
        
        $query = db_query("UPDATE users SET shippingAddress=?, shippingState=?, shippingCity=?, shippingPincode=? WHERE id=?", [$saddress, $sstate, $scity, $spincode, $userId], "sssii");
        if ($query) {
            echo "<script>alert('Shipping Address saved to record.');</script>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
	    <title>Shopping Bag | ZeyTech Commerce</title>
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
		<div style="display:flex; justify-content:space-between; align-items:baseline; margin-bottom:20px; border-bottom:1px solid rgba(226,232,240,0.12); padding-bottom:12px;">
			<div>
				<div style="font-size:11px; font-weight:600; color:#d9b45d; text-transform:uppercase; letter-spacing:0.05em;">
					Order Review &amp; Checkout
				</div>
				<h2 style="font-family:'Fraunces', serif; font-size:24px; font-weight:700; color:#ffffff; margin:4px 0 0 0;">Shopping Cart</h2>
			</div>
			<a href="index.php" style="font-size:12px; font-weight:600; color:#d9b45d; text-decoration:none;">&larr; Continue Shopping</a>
		</div>

		<form name="cart" method="post">
		<?php
		$cartTotal = 0;
		$shippingTotal = 0;
		$hasItems = false;
		
		if (!empty($_SESSION['cart'])) {
			$pdtid = array_filter(array_map('intval', array_keys($_SESSION['cart'])));
			if (!empty($pdtid)) {
				$hasItems = true;
				$placeholders = implode(',', array_fill(0, count($pdtid), '?'));
				$types = str_repeat('i', count($pdtid));
				$productsInCart = db_fetch_all("SELECT * FROM products WHERE id IN ($placeholders)", $pdtid, $types);
		?>
		<div class="row">
			<!-- Cart Items Table Column -->
			<div class="col-md-8 col-sm-12">
				<div class="manifest-panel" style="background:#121e36; border:1px solid rgba(226,232,240,0.12); border-radius:8px; padding:0; overflow:hidden; margin-bottom:16px; box-shadow:var(--shadow-sm);">
					<div class="table-responsive">
						<table class="enterprise-table" style="width:100%; border-collapse:collapse;">
							<thead>
								<tr style="background:#182847; color:#94a3b8; font-size:12px;">
									<th style="width:30px; padding:12px 14px;"><input type="checkbox" id="selectAll" onclick="toggleSelectAll(this)"></th>
									<th style="width:70px; padding:12px 14px;">Item</th>
									<th style="padding:12px 14px;">Product</th>
									<th style="width:90px; padding:12px 14px;">Qty</th>
									<th style="padding:12px 14px;">Unit Price</th>
									<th style="padding:12px 14px;">Total</th>
									<th style="width:40px; padding:12px 14px;"></th>
								</tr>
							</thead>
							<tbody>
							<?php 
							foreach ($productsInCart as $row) {
								$pid = intval($row['id']);
								$qty = intval($_SESSION['cart'][$pid]['quantity'] ?? 1);
								$unitPrice = floatval($row['productPrice']);
								$shipCharge = floatval($row['shippingCharge']);
								$lineTotal = ($qty * $unitPrice) + $shipCharge;
								$cartTotal += ($qty * $unitPrice);
								$shippingTotal += $shipCharge;
							?>
								<tr style="border-bottom:1px solid rgba(226,232,240,0.06);">
									<td style="text-align:center; padding:14px;"><input type="checkbox" name="remove_code[]" value="<?php echo e($pid);?>"></td>
									<td style="padding:14px;">
										<a href="product-details.php?pid=<?php echo e($pid);?>">
											<img src="admin/productimages/<?php echo e($pid);?>/<?php echo e($row['productImage1']);?>" style="width:54px; height:54px; object-fit:contain; border-radius:4px; background:#0b162c; border:1px solid rgba(226,232,240,0.15);">
										</a>
									</td>
									<td style="padding:14px;">
										<div style="font-size:13px; font-weight:600; color:#ffffff; margin-bottom:2px;">
											<a href="product-details.php?pid=<?php echo e($pid);?>" style="color:#ffffff; text-decoration:none;"><?php echo e($row['productName']);?></a>
										</div>
										<span style="font-size:11px; color:#94a3b8;"><?php echo e($row['productCompany']);?></span>
									</td>
									<td style="padding:14px;">
										<input type="number" min="1" max="99" name="quantity[<?php echo e($pid);?>]" value="<?php echo e($qty); ?>" style="width:65px; border-radius:4px; text-align:center; font-family:'Space Mono'; font-weight:700; background:#0b162c; border:1px solid rgba(226,232,240,0.15); color:#ffffff; padding:6px;">
									</td>
									<td style="font-family:'Space Mono'; font-size:12px; color:#94a3b8; padding:14px;"><?php echo format_price($unitPrice); ?></td>
									<td style="font-family:'Space Mono'; font-weight:700; color:#d9b45d; font-size:13px; padding:14px;"><?php echo format_price($lineTotal); ?></td>
									<td style="padding:14px;">
										<a href="my-cart.php?del=<?php echo e($pid);?>" onclick="return confirm('Remove item?');" style="color:#ef4444; font-size:14px;" title="Remove"><i class="fa fa-trash-o"></i></a>
									</td>
								</tr>
							<?php } ?>
							</tbody>
						</table>
					</div>
				</div>

				<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:28px;">
					<button type="submit" name="submit" class="btn-ghost" style="font-size:12px; border-radius:4px;">Update Quantities</button>
					<button type="submit" name="remove_code_btn" class="btn-ghost" style="font-size:12px; border-radius:4px; color:#ef4444; border-color:rgba(239,68,68,0.3);">Remove Selected</button>
				</div>

				<!-- Delivery & Invoicing Details -->
				<?php if (!empty($_SESSION['id'])) { 
					$userId = intval($_SESSION['id']);
					$userAddr = db_fetch_one("SELECT * FROM users WHERE id=?", [$userId], "i");
				?>
				<div class="manifest-panel" style="background:#121e36; border:1px solid rgba(226,232,240,0.12); border-radius:8px; padding:24px; box-shadow:var(--shadow-sm);">
					<div style="font-size:11px; font-weight:600; color:#d9b45d; text-transform:uppercase; letter-spacing:0.05em; margin-bottom:14px;">
						<i class="fa fa-map-marker"></i> Delivery &amp; Invoicing Details
					</div>
					
					<div class="row">
						<div class="col-md-6">
							<div style="background:#0b162c; border:1px solid rgba(226,232,240,0.10); border-radius:6px; padding:16px; height:100%;">
								<div style="font-size:12px; font-weight:600; color:#ffffff; margin-bottom:10px;">Billing Address</div>
								<div class="form-group" style="margin-bottom:8px;">
									<textarea name="billingaddress" class="form-control" rows="2" style="background:#121e36; border:1px solid rgba(226,232,240,0.15); border-radius:4px; color:#ffffff; font-size:12px;"><?php echo e($userAddr['billingAddress'] ?? '');?></textarea>
								</div>
								<div class="form-group" style="margin-bottom:8px;">
									<input type="text" name="billingcity" class="form-control" placeholder="City" value="<?php echo e($userAddr['billingCity'] ?? '');?>" style="background:#121e36; border:1px solid rgba(226,232,240,0.15); border-radius:4px; color:#ffffff; font-size:12px;">
								</div>
								<button type="submit" name="update" class="btn-ghost" style="padding:4px 10px; font-size:11px; border-radius:4px;">Save Billing</button>
							</div>
						</div>

						<div class="col-md-6">
							<div style="background:#0b162c; border:1px solid rgba(226,232,240,0.10); border-radius:6px; padding:16px; height:100%;">
								<div style="font-size:12px; font-weight:600; color:#ffffff; margin-bottom:10px;">Shipping Address</div>
								<div class="form-group" style="margin-bottom:8px;">
									<textarea name="shippingaddress" class="form-control" rows="2" style="background:#121e36; border:1px solid rgba(226,232,240,0.15); border-radius:4px; color:#ffffff; font-size:12px;"><?php echo e($userAddr['shippingAddress'] ?? '');?></textarea>
								</div>
								<div class="form-group" style="margin-bottom:8px;">
									<input type="text" name="shippingcity" class="form-control" placeholder="City" value="<?php echo e($userAddr['shippingCity'] ?? '');?>" style="background:#121e36; border:1px solid rgba(226,232,240,0.15); border-radius:4px; color:#ffffff; font-size:12px;">
								</div>
								<button type="submit" name="shipupdate" class="btn-ghost" style="padding:4px 10px; font-size:11px; border-radius:4px;">Save Shipping</button>
							</div>
						</div>
					</div>
				</div>
				<?php } ?>
			</div>

			<!-- Order Summary Column -->
			<div class="col-md-4 col-sm-12">
				<div class="manifest-panel" style="background:#121e36; border:1px solid rgba(226,232,240,0.12); border-radius:8px; padding:24px; box-shadow:var(--shadow-sm);">
					<div style="font-size:11px; font-weight:600; color:#d9b45d; text-transform:uppercase; letter-spacing:0.05em; margin-bottom:14px;">
						Order Summary
					</div>
					
					<div style="display:flex; justify-content:space-between; margin-bottom:10px; font-size:13px;">
						<span style="color:#94a3b8;">Items Subtotal:</span>
						<strong style="font-family:'Space Mono'; color:#ffffff;"><?php echo format_price($cartTotal); ?></strong>
					</div>

					<div style="display:flex; justify-content:space-between; margin-bottom:10px; font-size:13px;">
						<span style="color:#94a3b8;">Domestic Transit:</span>
						<span style="font-family:'Space Mono'; color:#10b981; font-weight:600;"><?php echo ($shippingTotal == 0) ? 'FREE' : format_price($shippingTotal); ?></span>
					</div>

					<div style="display:flex; justify-content:space-between; margin-bottom:16px; font-size:13px;">
						<span style="color:#94a3b8;">Settlement Currency:</span>
						<span style="font-family:'Space Mono'; font-weight:700; color:#d9b45d;"><?php echo get_current_currency()['code']; ?></span>
					</div>

					<!-- Promotional Coupon Code Box -->
					<div style="margin-bottom:18px; padding:14px; background:#0b162c; border:1px solid rgba(226,232,240,0.10); border-radius:6px;">
						<div style="font-size:11px; font-weight:600; color:#d9b45d; margin-bottom:8px;">
							<i class="fa fa-tag"></i> Promotional Voucher
						</div>
						<div style="display:flex; gap:6px;">
							<input type="text" id="couponInput" placeholder="e.g. ZEYTECH10VIP" style="flex:1; padding:7px 10px; font-size:12px; font-family:'Space Mono'; background:#121e36; border:1px solid rgba(226,232,240,0.15); color:#ffffff; text-transform:uppercase; border-radius:4px; outline:none;">
							<button type="button" onclick="applyCoupon()" class="btn-ghost" style="padding:6px 12px; font-size:11px; border-radius:4px; flex-shrink:0;">Apply</button>
						</div>
						<div id="couponFeedback" style="font-family:'Space Mono'; font-size:11px; margin-top:6px; display:none;"></div>
					</div>

					<div id="discountRow" style="display:none; justify-content:space-between; margin-bottom:12px; font-size:13px; color:#10b981;">
						<span>Voucher Discount:</span>
						<strong style="font-family:'Space Mono';" id="discountDisplay">-0.00 MAD</strong>
					</div>

					<div style="border-top:1px solid rgba(226,232,240,0.10); padding-top:14px; margin-bottom:20px; display:flex; justify-content:space-between; align-items:baseline;">
						<span style="font-family:'Fraunces', serif; font-size:18px; font-weight:700; color:#ffffff;">Grand Total:</span>
						<span style="font-family:'Space Mono'; font-size:22px; font-weight:700; color:#d9b45d;" id="grandTotalDisplay"><?php echo format_price($cartTotal + $shippingTotal); ?></span>
					</div>

					<div>
						<?php if (!empty($_SESSION['id'])) { ?>
						<button type="submit" name="ordersubmit" class="btn-primary" style="width:100%; padding:12px; font-size:13px; border-radius:4px; justify-content:center;">
							Proceed to Checkout &rarr;
						</button>
						<?php } else { ?>
						<a href="login.php" class="btn-primary" style="width:100%; padding:12px; font-size:13px; border-radius:4px; text-decoration:none; display:flex; justify-content:center; text-align:center;">
							Sign In to Checkout &rarr;
						</a>
						<?php } ?>
					</div>

					<div style="display:flex; justify-content:center; align-items:center; gap:6px; margin-top:16px; font-size:11px; color:#64748b;">
						<i class="fa fa-lock"></i> 256-bit Encrypted Secure Checkout
					</div>
				</div>
			</div>
		</div>
		<?php 
			} 
		} 
		
		if (!$hasItems) { 
		?>
		<div style="text-align:center; padding:80px 20px; background:#0c1526; border-radius:2px; border:1px solid rgba(142,162,191,0.18);">
			<h2 style="font-family:'Fraunces', serif; font-weight:700; color:#f2efe6; margin-bottom:8px;">Your Bag is Empty</h2>
			<p style="color:#8ea2bf; font-family:'Space Mono'; font-size:12px; margin-bottom:24px;">
				No items currently reserved in your session manifest.
			</p>
			<a href="index.php" class="btn-primary">
				EXPLORE HARDWARE CATALOG &rarr;
			</a>
		</div>
		<?php } ?>
		</form>
	</div>
</div>

<?php include('includes/footer.php');?>
<script src="assets/js/jquery-1.11.1.min.js"></script>
<script src="assets/js/bootstrap.min.js"></script>
<script>
var currentSubtotal = <?php echo floatval($cartTotal); ?>;
var currentShipping = <?php echo floatval($shippingTotal); ?>;

function toggleSelectAll(master) {
	var checkboxes = document.getElementsByName('remove_code[]');
	for (var i = 0; i < checkboxes.length; i++) {
		checkboxes[i].checked = master.checked;
	}
}

function applyCoupon() {
	var code = document.getElementById('couponInput').value.trim().toUpperCase();
	var feedback = document.getElementById('couponFeedback');
	var discRow = document.getElementById('discountRow');
	var discDisp = document.getElementById('discountDisplay');
	var totalDisp = document.getElementById('grandTotalDisplay');

	if (!code) {
		feedback.style.display = 'block';
		feedback.style.color = '#ef4444';
		feedback.textContent = 'Please enter a code';
		return;
	}

	fetch('api-coupon-apply.php', {
		method: 'POST',
		headers: { 'Content-Type': 'application/json' },
		body: JSON.stringify({
			couponCode: code,
			subtotal: currentSubtotal,
			shipping: currentShipping
		})
	})
	.then(res => res.json())
	.then(data => {
		feedback.style.display = 'block';
		if (data.success) {
			feedback.style.color = '#22c55e';
			feedback.textContent = '✓ ' + data.savingsSummary;
			discRow.style.display = 'flex';
			discDisp.textContent = '- ' + Number(data.discountAmountMAD).toLocaleString() + ' MAD';
			totalDisp.textContent = Number(data.finalTotalMAD).toLocaleString() + ' MAD';
		} else {
			feedback.style.color = '#ef4444';
			feedback.textContent = '✕ ' + (data.message || data.error);
			discRow.style.display = 'none';
			totalDisp.textContent = Number(currentSubtotal + currentShipping).toLocaleString() + ' MAD';
		}
	})
	.catch(() => {
		feedback.style.display = 'block';
		feedback.style.color = '#ef4444';
		feedback.textContent = 'Failed to validate voucher.';
	});
}
</script>
</body>
</html>