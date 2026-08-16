<?php
session_start();
error_reporting(0);
include('includes/config.php');

if(isset($_GET['action']) && $_GET['action']=="add"){
	$id=intval($_GET['id']);
	if(isset($_SESSION['cart'][$id])){
		$_SESSION['cart'][$id]['quantity']++;
	}else{
		$product = db_fetch_one("SELECT * FROM products WHERE id=?", [$id], "i");
		if($product){
			$_SESSION['cart'][$product['id']]=array("quantity" => 1, "price" => $product['productPrice']);
			echo "<script>alert('Product added to your cart.');</script>";
			echo "<script type='text/javascript'> document.location ='my-cart.php'; </script>";
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
		<meta name="description" content="ZeyTech - Premium Commercial Hardware, Workstations, Displays & AI Multi-Agent Commerce">
		<meta name="author" content="ZeyTech">
	    <meta name="robots" content="all">

	    <title>ZeyTech | Premium Electronics & Modern Commerce</title>

	    <!-- Bootstrap Core CSS -->
	    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
	    <link rel="stylesheet" href="assets/css/font-awesome.min.css">
	    
	    <!-- Harmonized ZeyTech Brand Design System -->
	    <link rel="stylesheet" href="assets/css/modern-storefront.css">
		<link rel="shortcut icon" href="assets/images/favicon.ico">
	</head>
    <body class="cnt-home">
	
<header class="header-style-1">
<?php include('includes/top-header.php');?>
<?php include('includes/main-header.php');?>
<?php include('includes/menu-bar.php');?>
</header>

<div class="body-content" id="top-banner-and-menu" style="padding-top: 28px; padding-bottom: 60px;">
	<div class="container">
		<div class="row">
		
			<!-- Left Categories Sidebar -->
			<div class="col-xs-12 col-sm-12 col-md-3 sidebar">
				<?php include('includes/side-menu.php');?>

				<!-- Hub Security Badge -->
				<div style="background:#121e36; border:1px solid rgba(226,232,240,0.12); border-radius:6px; padding:18px; margin-top:20px; box-shadow:var(--shadow-sm);">
					<div style="font-size:12px; font-weight:600; color:#d9b45d; margin-bottom:6px; display:flex; align-items:center; gap:6px;">
						<i class="fa fa-shield"></i>
						<span>Authentic Moroccan Stock</span>
					</div>
					<div style="font-size:12px; color:#94a3b8; line-height:1.5;">
						All hardware is backed by 1-year official manufacturer warranty and stored in Casablanca Central Hub.
					</div>
				</div>
			</div>
			
			<!-- Right Main Workspace / Hero & Products -->
			<div class="col-xs-12 col-sm-12 col-md-9 homebanner-holder">
				
				<!-- Refined Hero Banner -->
				<div class="modern-hero-banner">
					<div class="hero-eyebrow">
						<i class="fa fa-star" style="color:#d9b45d; font-size:11px;"></i>
						Official 2026 Commercial Hardware &bull; Casablanca
					</div>
					<h1 class="hero-headline">
						Enterprise-Grade Technology.<br>Delivered <em>Across Morocco</em>.
					</h1>
					<p class="hero-subtext">
						Explore high-performance M3 workstations, 4K OLED displays, and studio acoustics with instant AI support and verified domestic delivery.
					</p>
					<div style="display:flex; gap:12px; flex-wrap:wrap;">
						<a href="category.php?cid=1" class="btn-primary">
							<i class="fa fa-shopping-bag"></i> Browse Products
						</a>
						<a href="track-orders.php" class="btn-ghost">
							<i class="fa fa-truck"></i> Track Delivery
						</a>
					</div>
				</div>

				<!-- 4 Value Proposition Cards -->
				<div class="value-props-grid">
					<div class="value-prop-card">
						<span class="value-prop-num">01</span>
						<div>
							<div class="value-prop-title">Express Delivery</div>
							<div class="value-prop-desc">12 Moroccan Regions via CTM &amp; Amana</div>
						</div>
					</div>

					<div class="value-prop-card">
						<span class="value-prop-num">02</span>
						<div>
							<div class="value-prop-title">Official Warranty</div>
							<div class="value-prop-desc">Direct 1-Year Manufacturer Guarantee</div>
						</div>
					</div>

					<div class="value-prop-card">
						<span class="value-prop-num">03</span>
						<div>
							<div class="value-prop-title">AI Sales Engineer</div>
							<div class="value-prop-desc">Real-time Darija, French &amp; English</div>
						</div>
					</div>

					<div class="value-prop-card">
						<span class="value-prop-num">04</span>
						<div>
							<div class="value-prop-title">Multi-Currency</div>
							<div class="value-prop-desc">MAD, USD, EUR Live Settlement</div>
						</div>
					</div>
				</div>

				<!-- Featured Hardware Showcase -->
				<div style="display:flex; justify-content:space-between; align-items:baseline; margin: 32px 0 20px 0; border-bottom:1px solid rgba(226,232,240,0.12); padding-bottom:12px;">
					<div>
						<h2 style="font-size:20px; font-weight:600; color:#ffffff; margin:0;">
							Featured Products
						</h2>
						<div style="font-size:12px; color:#94a3b8; margin-top:2px;">
							Hand-picked workstations, displays, and audio hardware
						</div>
					</div>
					<a href="category.php?cid=1" style="font-size:12px; font-weight:600; color:#d9b45d; text-decoration:none;">
						View All &rarr;
					</a>
				</div>

				<!-- Product Cards Grid -->
				<div class="row">
					<?php
					$products = db_fetch_all("SELECT p.*, c.categoryName FROM products p LEFT JOIN category c ON p.category = c.id ORDER BY p.id ASC LIMIT 9");
					foreach($products as $row) {
						$pid = intval($row['id']);
						$img = get_product_image_url($row, 1);
						$stockQty = intval($row['in_stock_units'] ?? 10);
						$isLowStock = ($stockQty > 0 && $stockQty <= 5);
						$isOutOfStock = ($stockQty <= 0);
					?>
					<div class="col-xs-12 col-sm-6 col-md-4">
						<div class="modern-product-card">
							<div class="product-img-wrapper">
								<?php if($isOutOfStock) { ?>
									<span class="product-badge-stock" style="background:rgba(239,68,68,0.12); color:#ef4444; border-color:rgba(239,68,68,0.3);">
										Out of Stock
									</span>
								<?php } elseif($isLowStock) { ?>
									<span class="product-badge-stock" style="background:rgba(245,158,11,0.12); color:#f59e0b; border-color:rgba(245,158,11,0.3);">
										Low Stock (<?php echo $stockQty; ?> left)
									</span>
								<?php } else { ?>
									<span class="product-badge-stock">
										● In Stock
									</span>
								<?php } ?>
								<a href="product-details.php?pid=<?php echo $pid; ?>" style="display:flex; align-items:center; justify-content:center; width:100%; height:100%;">
									<img src="<?php echo e($img); ?>" alt="<?php echo e($row['productName']); ?>" onerror="handleImageError(this)">
								</a>
							</div>

							<div class="product-category-label">
								<?php echo e($row['categoryName'] ?? 'Electronics'); ?>
							</div>

							<a href="product-details.php?pid=<?php echo $pid; ?>" class="product-name-link" title="<?php echo e($row['productName']); ?>">
								<?php echo e($row['productName']); ?>
							</a>

							<div class="product-price-row">
								<div>
									<div class="price-main"><?php echo format_price($row['productPrice']); ?></div>
									<?php if(!empty($row['productPriceBeforeDiscount']) && $row['productPriceBeforeDiscount'] > $row['productPrice']) { ?>
										<div class="price-strike"><?php echo format_price($row['productPriceBeforeDiscount']); ?></div>
									<?php } ?>
								</div>

								<a href="index.php?page=product&action=add&id=<?php echo $pid; ?>" class="btn-add-cart-modern">
									<i class="fa fa-shopping-cart"></i> Add
								</a>
							</div>
						</div>
					</div>
					<?php } ?>
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