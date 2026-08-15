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
		<meta name="description" content="ZeyTech - Premium Commercial Hardware, Laptops & AI Multi-Agent Commerce">
		<meta name="author" content="ZeyTech">
	    <meta name="robots" content="all">

	    <title>ZeyTech | Premium Electronics & Autonomous Commerce</title>

	    <!-- Bootstrap Core CSS -->
	    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
	    <link rel="stylesheet" href="assets/css/font-awesome.min.css">
	    
	    <!-- Authentic ZeyTech Brand Design System -->
	    <link rel="stylesheet" href="assets/css/modern-storefront.css">
		<link rel="shortcut icon" href="assets/images/favicon.ico">
	</head>
    <body class="cnt-home">
	
<header class="header-style-1">
<?php include('includes/top-header.php');?>
<?php include('includes/main-header.php');?>
<?php include('includes/menu-bar.php');?>
</header>

<div class="body-content" id="top-banner-and-menu" style="padding-top: 24px; padding-bottom: 60px;">
	<div class="container">
		<div class="row">
		
			<!-- Left Categories Sidebar -->
			<div class="col-xs-12 col-sm-12 col-md-3 sidebar">
				<?php include('includes/side-menu.php');?>

				<!-- Hub Security Badge (Editorial Ledger Style) -->
				<div style="background:#0c1526; border:1px solid rgba(142,162,191,0.18); border-radius:2px; padding:18px; margin-top:20px;">
					<div style="font-family:'Space Mono', monospace; font-size:11px; font-weight:700; color:#c79a44; text-transform:uppercase; letter-spacing:0.06em; margin-bottom:6px;">
						[CASABLANCA.HUB_A1]
					</div>
					<div style="font-size:12px; color:#8ea2bf; line-height:1.5;">
						All inventory is real-time reserved and verified under 3-state warehouse locks before cryptographic settlement.
					</div>
				</div>
			</div>
			
			<!-- Right Main Workspace / Hero & Products -->
			<div class="col-xs-12 col-sm-12 col-md-9 homebanner-holder">
				
				<!-- Editorial Hero Banner (Fraunces & Hexagram Mark) -->
				<div class="modern-hero-banner">
					<div class="hero-eyebrow">
						<span class="hexagram-mark" style="width:16px; height:16px;">
							<svg class="hexagram-svg" viewBox="0 0 24 24">
								<polygon points="12,2 22,18 2,18" stroke="#c79a44" fill="none" stroke-width="1.5"/>
								<polygon points="12,22 22,6 2,6" stroke="#d9b567" fill="none" stroke-width="1.5"/>
							</svg>
						</span>
						Casablanca Regional Fulfillment &bull; Official 2026 Commercial Hardware
					</div>
					<h1 class="hero-headline">
						Enterprise-Grade Tech.<br>Delivered <em>Across Morocco</em>.
					</h1>
					<p class="hero-subtext">
						Explore M3 Max workstations, 4K displays, and precision audio gear with instant Darija AI assistance and cryptographic multi-currency checkout.
					</p>
					<div style="display:flex; gap:12px; flex-wrap:wrap;">
						<a href="category.php?cid=1" class="btn-primary">
							EXPLORE CATALOG &rarr;
						</a>
						<a href="zeytech-platform.php" target="_blank" class="btn-ghost">
							[TELEMETRY.DASHBOARD]
						</a>
					</div>
				</div>

				<!-- 4 Value Proposition Manifest Cards -->
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
							<div class="value-prop-desc">MAD, USD, EUR Real-Time Settlement</div>
						</div>
					</div>
				</div>

				<!-- Featured Hardware Showcase -->
				<div style="display:flex; justify-content:space-between; align-items:baseline; margin: 32px 0 20px 0; border-bottom:1px solid rgba(142,162,191,0.18); padding-bottom:10px;">
					<div>
						<h2 style="font-family:'Fraunces', serif; font-size:22px; font-weight:700; color:#f2efe6; margin:0; letter-spacing:-0.02em;">
							Featured Hardware
						</h2>
						<div style="font-family:'Space Mono', monospace; font-size:11px; color:#8ea2bf; margin-top:2px;">
							[STOCK: REAL-TIME VERIFIED AT CENTRAL HUB-A1]
						</div>
					</div>
					<a href="category.php?cid=1" style="font-family:'Space Mono', monospace; font-size:11px; font-weight:700; color:#d9b567; text-decoration:none;">
						ALL CATEGORIES &rarr;
					</a>
				</div>

				<!-- Product Cards Grid -->
				<div class="row">
					<?php
					$products = db_fetch_all("SELECT p.*, c.categoryName FROM products p LEFT JOIN category c ON p.category = c.id ORDER BY p.id ASC LIMIT 9");
					foreach($products as $row) {
						$pid = intval($row['id']);
						$img = !empty($row['productImage1']) ? 'admin/productimages/'.$pid.'/'.$row['productImage1'] : 'assets/images/blank.gif';
					?>
					<div class="col-xs-12 col-sm-6 col-md-4">
						<div class="modern-product-card">
							<div class="product-img-wrapper">
								<span class="product-badge-stock">
									[IN STOCK]
								</span>
								<a href="product-details.php?pid=<?php echo $pid; ?>" style="display:flex; align-items:center; justify-content:center; width:100%; height:100%;">
									<img src="<?php echo e($img); ?>" alt="<?php echo e($row['productName']); ?>" onerror="this.src='assets/images/blank.gif';">
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
									+ ADD
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