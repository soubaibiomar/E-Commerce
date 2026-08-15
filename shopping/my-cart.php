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
		<div style="display:flex; justify-content:space-between; align-items:baseline; margin-bottom:20px; border-bottom:1px solid rgba(142,162,191,0.18); padding-bottom:12px;">
			<div>
				<div style="font-family:'Space Mono'; font-size:10px; font-weight:700; color:#c79a44; text-transform:uppercase; letter-spacing:0.08em;">
					[CHECKOUT.MANIFEST_ACTIVE]
				</div>
				<h2 style="font-family:'Fraunces', serif; font-size:24px; font-weight:700; color:#f2efe6; margin:4px 0 0 0; letter-spacing:-0.02em;">Shopping Bag &amp; Manifest</h2>
			</div>
			<a href="index.php" style="font-family:'Space Mono'; font-size:11px; font-weight:700; color:#d9b567; text-decoration:none;">&larr; CATALOG</a>
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
				<div class="manifest-panel" style="padding:0; overflow:hidden; margin-bottom:16px;">
					<div class="table-responsive">
						<table class="enterprise-table">
							<thead>
								<tr>
									<th style="width:30px;"><input type="checkbox" id="selectAll" onclick="toggleSelectAll(this)"></th>
									<th style="width:70px;">Item</th>
									<th>Description</th>
									<th style="width:90px;">Quantity</th>
									<th>Unit Price</th>
									<th>Total</th>
									<th style="width:40px;"></th>
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
								<tr>
									<td style="text-align:center;"><input type="checkbox" name="remove_code[]" value="<?php echo e($pid);?>"></td>
									<td>
										<a href="product-details.php?pid=<?php echo e($pid);?>">
											<img src="admin/productimages/<?php echo e($pid);?>/<?php echo e($row['productImage1']);?>" style="width:54px; height:54px; object-fit:contain; border-radius:2px; background:#080e1a; border:1px solid rgba(142,162,191,0.2);">
										</a>
									</td>
									<td>
										<div style="font-family:'IBM Plex Sans'; font-size:13px; font-weight:600; color:#f2efe6; margin-bottom:2px;">
											<a href="product-details.php?pid=<?php echo e($pid);?>" style="color:#f2efe6; text-decoration:none;"><?php echo e($row['productName']);?></a>
										</div>
										<span style="font-family:'Space Mono'; font-size:11px; color:#8ea2bf;">[BRAND: <?php echo strtoupper(e($row['productCompany']));?>]</span>
									</td>
									<td>
										<input type="number" min="1" max="99" name="quantity[<?php echo e($pid);?>]" value="<?php echo e($qty); ?>" style="width:65px; border-radius:2px; text-align:center; font-family:'Space Mono'; font-weight:700; background:#080e1a; border:1px solid rgba(142,162,191,0.25); color:#f2efe6; padding:4px;">
									</td>
									<td style="font-family:'Space Mono'; font-size:12px; color:#8ea2bf;"><?php echo format_price($unitPrice); ?></td>
									<td style="font-family:'Space Mono'; font-weight:700; color:#d9b567; font-size:13px;"><?php echo format_price($lineTotal); ?></td>
									<td>
										<a href="my-cart.php?del=<?php echo e($pid);?>" onclick="return confirm('Remove item?');" style="color:#ef4444; font-size:13px;" title="Remove"><i class="fa fa-trash-o"></i></a>
									</td>
								</tr>
							<?php } ?>
							</tbody>
						</table>
					</div>
				</div>

				<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:28px;">
					<button type="submit" name="submit" class="btn-ghost" style="font-family:'Space Mono'; font-size:11px;">UPDATE QUANTITIES</button>
					<button type="submit" name="remove_code_btn" class="btn-ghost" style="font-family:'Space Mono'; font-size:11px; color:#ef4444; border-color:rgba(239,68,68,0.3);">REMOVE SELECTED</button>
				</div>

				<!-- Addresses Manifest -->
				<?php if (!empty($_SESSION['id'])) { 
					$userId = intval($_SESSION['id']);
					$userAddr = db_fetch_one("SELECT * FROM users WHERE id=?", [$userId], "i");
				?>
				<div class="manifest-panel" style="padding:24px;">
					<div style="font-family:'Space Mono'; font-size:10px; font-weight:700; color:#c79a44; text-transform:uppercase; letter-spacing:0.08em; margin-bottom:12px;">
						[LOGISTICS.DESTINATION_CONFIRMATION]
					</div>
					
					<div class="row">
						<div class="col-md-6">
							<div style="background:#080e1a; border:1px solid rgba(142,162,191,0.18); border-radius:2px; padding:16px; height:100%;">
								<div style="font-family:'Space Mono'; font-size:11px; font-weight:700; color:#f2efe6; margin-bottom:10px;">BILLING ADDRESS</div>
								<div class="form-group" style="margin-bottom:8px;">
									<textarea name="billingaddress" class="form-control" rows="2" style="background:#0c1526; border:1px solid rgba(142,162,191,0.2); border-radius:2px; color:#f2efe6; font-size:12px;"><?php echo e($userAddr['billingAddress'] ?? '');?></textarea>
								</div>
								<div class="form-group" style="margin-bottom:8px;">
									<input type="text" name="billingcity" class="form-control" placeholder="City" value="<?php echo e($userAddr['billingCity'] ?? '');?>" style="background:#0c1526; border:1px solid rgba(142,162,191,0.2); border-radius:2px; color:#f2efe6; font-size:12px;">
								</div>
								<button type="submit" name="update" class="btn-ghost" style="padding:4px 10px; font-size:10px; font-family:'Space Mono';">SAVE BILLING</button>
							</div>
						</div>

						<div class="col-md-6">
							<div style="background:#080e1a; border:1px solid rgba(142,162,191,0.18); border-radius:2px; padding:16px; height:100%;">
								<div style="font-family:'Space Mono'; font-size:11px; font-weight:700; color:#f2efe6; margin-bottom:10px;">SHIPPING ADDRESS</div>
								<div class="form-group" style="margin-bottom:8px;">
									<textarea name="shippingaddress" class="form-control" rows="2" style="background:#0c1526; border:1px solid rgba(142,162,191,0.2); border-radius:2px; color:#f2efe6; font-size:12px;"><?php echo e($userAddr['shippingAddress'] ?? '');?></textarea>
								</div>
								<div class="form-group" style="margin-bottom:8px;">
									<input type="text" name="shippingcity" class="form-control" placeholder="City" value="<?php echo e($userAddr['shippingCity'] ?? '');?>" style="background:#0c1526; border:1px solid rgba(142,162,191,0.2); border-radius:2px; color:#f2efe6; font-size:12px;">
								</div>
								<button type="submit" name="shipupdate" class="btn-ghost" style="padding:4px 10px; font-size:10px; font-family:'Space Mono';">SAVE SHIPPING</button>
							</div>
						</div>
					</div>
				</div>
				<?php } ?>
			</div>

			<!-- Order Summary Column -->
			<div class="col-md-4 col-sm-12">
				<div class="manifest-panel" style="padding:24px;">
					<div style="font-family:'Space Mono'; font-size:10px; font-weight:700; color:#c79a44; text-transform:uppercase; letter-spacing:0.08em; margin-bottom:12px;">
						[SETTLEMENT.SUMMARY]
					</div>
					
					<div style="display:flex; justify-content:space-between; margin-bottom:10px; font-size:13px;">
						<span style="color:#8ea2bf;">Items Subtotal:</span>
						<strong style="font-family:'Space Mono'; color:#f2efe6;"><?php echo format_price($cartTotal); ?></strong>
					</div>

					<div style="display:flex; justify-content:space-between; margin-bottom:10px; font-size:13px;">
						<span style="color:#8ea2bf;">Domestic Transit:</span>
						<span style="font-family:'Space Mono'; color:#22c55e;"><?php echo ($shippingTotal == 0) ? 'FREE' : format_price($shippingTotal); ?></span>
					</div>

					<div style="display:flex; justify-content:space-between; margin-bottom:16px; font-size:13px;">
						<span style="color:#8ea2bf;">Settlement Rate:</span>
						<span style="font-family:'Space Mono'; font-weight:700; color:#d9b567;"><?php echo get_current_currency()['code']; ?></span>
					</div>

					<!-- Promotional Coupon Code Box -->
					<div style="margin-bottom:18px; padding:12px; background:#080e1a; border:1px solid rgba(142,162,191,0.2); border-radius:2px;">
						<div style="font-family:'Space Mono'; font-size:10px; font-weight:700; color:#c79a44; margin-bottom:6px;">[PROMO.VOUCHER_CODE]</div>
						<div style="display:flex; gap:6px;">
							<input type="text" id="couponInput" placeholder="e.g. ZEYTECH10VIP" style="flex:1; padding:6px 10px; font-size:12px; font-family:'Space Mono'; background:#0c1526; border:1px solid rgba(142,162,191,0.25); color:#f2efe6; text-transform:uppercase; border-radius:2px; outline:none;">
							<button type="button" onclick="applyCoupon()" class="btn-ghost" style="padding:4px 10px; font-family:'Space Mono'; font-size:10px; flex-shrink:0;">APPLY</button>
						</div>
						<div id="couponFeedback" style="font-family:'Space Mono'; font-size:10px; margin-top:6px; display:none;"></div>
					</div>

					<div id="discountRow" style="display:none; justify-content:space-between; margin-bottom:12px; font-size:13px; color:#22c55e;">
						<span>VIP Voucher Discount:</span>
						<strong style="font-family:'Space Mono';" id="discountDisplay">-0.00 MAD</strong>
					</div>

					<div style="border-top:1px solid rgba(142,162,191,0.18); padding-top:14px; margin-bottom:20px; display:flex; justify-content:space-between; align-items:baseline;">
						<span style="font-family:'Fraunces', serif; font-size:18px; font-weight:700; color:#f2efe6;">Grand Total:</span>
						<span style="font-family:'Space Mono'; font-size:22px; font-weight:700; color:#d9b567;" id="grandTotalDisplay"><?php echo format_price($cartTotal + $shippingTotal); ?></span>
					</div>

					<div>
						<?php if (!empty($_SESSION['id'])) { ?>
						<button type="submit" name="ordersubmit" class="btn-primary" style="width:100%; padding:12px; font-family:'Space Mono'; font-size:12px;">
							PROCEED TO PAYMENT &rarr;
						</button>
						<?php } else { ?>
						<a href="login.php" class="btn-primary" style="width:100%; padding:12px; font-family:'Space Mono'; font-size:12px; text-decoration:none; display:inline-block; text-align:center;">
							SIGN IN TO CHECKOUT &rarr;
						</a>
						<?php } ?>
					</div>

					<div style="display:flex; justify-content:center; gap:8px; margin-top:16px; font-family:'Space Mono'; font-size:10px; color:#5e7391;">
						[256-BIT HMAC CRYPTOGRAPHIC VERIFICATION]
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