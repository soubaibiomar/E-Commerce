<?php 
if (isset($_GET['action'])) {
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
    }
}

// Calculate cart totals
$totalPrice = 0;
$totalQty = 0;
$cartRows = [];

if (!empty($_SESSION['cart'])) {
    $cart_ids = array_filter(array_map('intval', array_keys($_SESSION['cart'])));
    if (!empty($cart_ids)) {
        $placeholders = implode(',', array_fill(0, count($cart_ids), '?'));
        $types = str_repeat('i', count($cart_ids));
        $cartRows = db_fetch_all("SELECT * FROM products WHERE id IN ($placeholders)", $cart_ids, $types);
        foreach ($cartRows as $row) {
            $pid = intval($row['id']);
            $qty = intval($_SESSION['cart'][$pid]['quantity'] ?? 1);
            $itemTotal = ($qty * floatval($row['productPrice'])) + floatval($row['shippingCharge']);
            $totalPrice += $itemTotal;
            $totalQty += $qty;
        }
    }
}
$_SESSION['qnty'] = $totalQty;
$_SESSION['tp'] = $totalPrice;
?>
<div class="main-header">
	<div class="container">
		<div class="row" style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:16px;">
			
			<!-- Official App Logo & Brand Motif -->
			<div class="col-xs-12 col-sm-4 col-md-3 logo-holder" style="padding-left:0;">
				<div class="logo">
					<a href="index.php" class="logo-brand" style="display:inline-flex; align-items:center; gap:10px; text-decoration:none;">
						<img src="assets/images/logo.jpg" alt="ZeyTech Logo" style="height:38px; width:auto; object-fit:contain; border-radius:2px;">
						<span style="font-family:'Fraunces', serif; font-size:22px; font-weight:700; color:#f2efe6; letter-spacing:-0.02em;">ZeyTech</span>
						<span class="brand-badge">[HUB.A1]</span>
					</a>
				</div>		
			</div>

			<!-- Search Area -->
			<div class="col-xs-12 col-sm-8 col-md-6 top-search-holder">
				<div class="search-area">
				    <form name="search" method="post" action="search-result.php" style="display:flex; align-items:center; width:100%; margin:0;">
				        <input class="search-field" placeholder="Search hardware specifications, serials, models..." name="product" required="required" />
				        <button class="search-button" type="submit" name="search" aria-label="Search">
							<i class="fa fa-search" style="font-size:12px;"></i>
						</button>    
				    </form>
				</div>
			</div>

			<!-- Top Cart & Quick Links -->
			<div class="col-xs-12 col-sm-12 col-md-3 top-cart-row" style="display:flex; justify-content:flex-end; align-items:center; padding-right:0; gap:10px;">
				<a href="my-cart.php" class="cart-pill-btn">
					<i class="fa fa-shopping-bag" style="font-size:14px; color:#c79a44;"></i>
					<div style="display:flex; flex-direction:column; line-height:1.2;">
						<span style="font-family:'Space Mono'; font-size:10px; color:#8ea2bf; text-transform:uppercase; letter-spacing:0.06em;">CART [<?php echo $totalQty; ?>]</span>
						<span style="font-family:'Space Mono'; font-size:12px; font-weight:700; color:#d9b567;"><?php echo format_price($totalPrice); ?></span>
					</div>
					<?php if($totalQty > 0) { ?>
						<span class="cart-count-badge"><?php echo $totalQty; ?></span>
					<?php } ?>
				</a>
			</div>
		</div>
	</div>
</div>