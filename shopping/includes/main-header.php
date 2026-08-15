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
					<a href="index.php" class="logo-brand">
						<img src="assets/images/logo.jpg" alt="ZeyTech Logo" class="brand-logo-img">
						<span class="brand-name">ZeyTech</span>
						<span class="brand-badge">Casablanca Hub</span>
					</a>
				</div>		
			</div>

			<!-- Search Area -->
			<div class="col-xs-12 col-sm-8 col-md-6 top-search-holder">
				<div class="search-area">
				    <form name="search" method="post" action="search-result.php" style="display:flex; margin:0;">
				        <input class="search-field" placeholder="Search laptops, displays, audio, wearables..." name="product" required autocomplete="off" style="flex:1;">
				        <button class="search-button" type="submit" aria-label="Search Catalog">
				        	<i class="fa fa-search"></i>
				        </button>
				    </form>
				</div>
			</div>

			<!-- Cart Link & Summary -->
			<div class="col-xs-12 col-sm-12 col-md-3 animate-dropdown top-cart-row-holder" style="padding-right:0; display:flex; justify-content:flex-end;">
				<a href="my-cart.php" class="basket">
					<div class="cart-icon-wrap">
						<i class="fa fa-shopping-cart"></i>
						<span class="cart-count-badge"><?php echo $totalQty; ?></span>
					</div>
					<div class="total-price-basket" style="text-align:left;">
						<span class="lbl" style="font-size:11px; color:#94a3b8; display:block;">Cart Total:</span>
						<span class="value" style="font-family:'Space Mono', monospace; font-size:13px; font-weight:700; color:#d9b45d;">
							<?php echo format_price($totalPrice); ?>
						</span>
					</div>
				</a>
			</div>

		</div>
	</div>
</div>