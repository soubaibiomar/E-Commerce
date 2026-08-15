<?php
session_start();
include('includes/config.php');

$cid = intval($_GET['cid'] ?? 0);

if (isset($_GET['action']) && $_GET['action'] == "add") {
	$id = intval($_GET['id'] ?? 0);
	if (isset($_SESSION['cart'][$id])) {
		$_SESSION['cart'][$id]['quantity']++;
	} else {
		$product = db_fetch_one("SELECT id, productPrice FROM products WHERE id=?", [$id], "i");
		if ($product) {
			$_SESSION['cart'][$product['id']] = array("quantity" => 1, "price" => $product['productPrice']);
			echo "<script>alert('Product added to cart'); window.location='my-cart.php';</script>";
            exit();
		}
	}
}

// Code for Wishlist
if (isset($_GET['pid']) && isset($_GET['action']) && $_GET['action'] == "wishlist") {
	if (empty($_SESSION['id'])) {   
        header('location:login.php');
        exit();
    } else {
        $uid = intval($_SESSION['id']);
        $pid = intval($_GET['pid']);
        $exists = db_fetch_one("SELECT id FROM wishlist WHERE userId=? AND productId=?", [$uid, $pid], "ii");
        if (!$exists) {
            db_query("INSERT INTO wishlist(userId, productId) VALUES(?, ?)", [$uid, $pid], "ii");
            echo "<script>alert('Product added to saved manifest.');</script>";
        } else {
            echo "<script>alert('Product already in saved manifest.');</script>";
        }
        echo "<script>window.location='my-wishlist.php';</script>";
        exit();
    }
}

$curCat = db_fetch_one("SELECT categoryName, categoryDescription FROM category WHERE id=?", [$cid], "i");
$catName = $curCat['categoryName'] ?? 'Category Products';
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
<?php 
$catDesc = !empty($curCat['categoryDescription']) ? $curCat['categoryDescription'] : "Explore " . $catName . " with live multi-currency pricing, authentic manufacturer specifications, and interactive 3D product previews.";
render_seo_meta($catName . " Catalog & Hardware | ZeyTech", $catDesc, "category.php?cid=" . $cid, "", "website");
?>
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

<div class="body-content outer-top-xs" style="padding-top:24px; padding-bottom:60px;">
	<div class='container'>
		<!-- Category Header Banner -->
		<div class="modern-hero-banner" style="margin-bottom:28px; padding:32px 36px;">
			<div class="hero-eyebrow">
				<span class="hexagram-mark" style="width:16px; height:16px;">
					<svg class="hexagram-svg" viewBox="0 0 24 24">
						<polygon points="12,2 22,18 2,18" stroke="#c79a44" fill="none" stroke-width="1.5"/>
						<polygon points="12,22 22,6 2,6" stroke="#d9b567" fill="none" stroke-width="1.5"/>
					</svg>
				</span>
				[CATALOG.DOMAIN] &bull; CASABLANCA CENTRAL STOCK
			</div>
			<h1 class="hero-headline" style="font-size:28px; margin-bottom:8px;"><?php echo e($catName); ?></h1>
			<p class="hero-subtext" style="margin-bottom:0; font-size:13px;"><?php echo e($curCat['categoryDescription'] ?? 'Verified enterprise and commercial hardware with real-time regional settlement.'); ?></p>
		</div>

		<div class='row outer-bottom-sm'>
			<!-- Sidebar -->
			<div class='col-md-3 sidebar'>
				<!-- Subcategories -->
				<div class="manifest-panel" style="padding:16px; margin-bottom:20px;">
					<div style="font-family:'Space Mono'; font-size:11px; font-weight:700; color:#c79a44; text-transform:uppercase; letter-spacing:0.06em; margin-bottom:12px; border-bottom:1px solid rgba(142,162,191,0.18); padding-bottom:8px;">
						[SUB-DOMAINS]
					</div>
					<ul class="list-unstyled" style="margin:0;">
						<?php 
						$subcats = db_fetch_all("SELECT id, subcategory FROM subcategory WHERE categoryid=?", [$cid], "i");
						if (!empty($subcats)) {
							foreach ($subcats as $sc) {
						?>
						<li style="margin-bottom:6px;">
							<a href="sub-category.php?scid=<?php echo e($sc['id']);?>" style="display:flex; justify-content:space-between; align-items:center; padding:6px 8px; color:#8ea2bf; font-size:13px; text-decoration:none; border-radius:2px; transition:all 0.15s ease;">
								<span><?php echo e($sc['subcategory']);?></span>
								<i class="fa fa-angle-right" style="font-size:11px; color:#5e7391;"></i>
							</a>
						</li>
						<?php } } else { ?>
						<li style="padding:8px; font-family:'Space Mono'; font-size:11px; color:#5e7391;">None registered</li>
						<?php } ?>
					</ul>
				</div>

				<!-- All Categories Filter -->
				<div class="manifest-panel" style="padding:16px; margin-bottom:20px;">
					<div style="font-family:'Space Mono'; font-size:11px; font-weight:700; color:#c79a44; text-transform:uppercase; letter-spacing:0.06em; margin-bottom:12px; border-bottom:1px solid rgba(142,162,191,0.18); padding-bottom:8px;">
						[ALL DOMAINS]
					</div>
					<ul class="list-unstyled" style="margin:0;">
						<?php 
						$allCats = db_fetch_all("SELECT id, categoryName FROM category ORDER BY categoryName ASC");
						foreach ($allCats as $ac) {
							$isActive = ($cid === intval($ac['id']));
						?>
						<li style="margin-bottom:4px;">
							<a href="category.php?cid=<?php echo e($ac['id']);?>" style="display:flex; justify-content:space-between; align-items:center; padding:6px 8px; color:<?php echo $isActive ? '#d9b567' : '#8ea2bf'; ?>; background:<?php echo $isActive ? 'rgba(199,154,68,0.12)' : 'transparent'; ?>; font-family:'IBM Plex Sans'; font-size:13px; font-weight:<?php echo $isActive ? '700' : '400'; ?>; text-decoration:none; border-radius:2px;">
								<span><?php echo e($ac['categoryName']);?></span>
								<?php if($isActive) { ?><span style="font-family:'Space Mono'; font-size:10px; color:#d9b567;">[ACTIVE]</span><?php } ?>
							</a>
						</li>
						<?php } ?>
					</ul>
				</div>
            </div>

			<!-- Product Grid -->
			<div class='col-md-9'>
				<div class="row">
					<?php
					$catProducts = db_fetch_all("SELECT * FROM products WHERE category=? ORDER BY id DESC", [$cid], "i");
					if (!empty($catProducts)) {
						foreach ($catProducts as $row) {
							$price = floatval($row['productPrice']);
							$oldPrice = floatval($row['productPriceBeforeDiscount']);
							$discount = ($oldPrice > $price && $oldPrice > 0) ? round((($oldPrice - $price) / $oldPrice) * 100) : 0;
					?>
					<div class="col-xs-12 col-sm-6 col-md-4">
						<div class="modern-product-card">
							<div class="product-img-wrapper">
								<?php if ($discount > 0) { ?>
								<span class="product-badge-stock">-<?php echo e($discount); ?>%</span>
								<?php } ?>
								<a href="product-details.php?pid=<?php echo e($row['id']);?>" style="display:flex; align-items:center; justify-content:center; width:100%; height:100%;">
									<img src="<?php echo e(get_product_image_url($row)); ?>" alt="<?php echo e($row['productName']);?>">
								</a>
							</div>

							<div class="product-category-label"><?php echo e($catName); ?></div>
							<a href="product-details.php?pid=<?php echo e($row['id']);?>" class="product-name-link">
								<?php echo e($row['productName']);?>
							</a>

							<div class="product-price-row">
								<div>
									<div class="price-main"><?php echo format_price($price); ?></div>
									<?php if ($oldPrice > $price) { ?>
									<div class="price-strike"><?php echo format_price($oldPrice); ?></div>
									<?php } ?>
								</div>

								<?php if ($row['productAvailability'] == 'In Stock') { ?>
								<a href="category.php?cid=<?php echo e($cid);?>&action=add&id=<?php echo e($row['id']); ?>" class="btn-add-cart-modern">
									+ ADD
								</a>
								<?php } else { ?>
								<span class="tag-pill tag-danger">[OUT OF STOCK]</span>
								<?php } ?>
							</div>
						</div>
					</div>
					<?php 
						} 
					} else { 
					?>
					<div class="col-xs-12" style="text-align:center; padding:60px 20px; background:#0c1526; border-radius:2px; border:1px solid rgba(142,162,191,0.18);">
						<h3 style="font-family:'Fraunces', serif; color:#f2efe6; font-weight:700;">No Items In This Domain</h3>
						<p style="color:#8ea2bf; font-family:'Space Mono'; font-size:12px; margin-bottom:20px;">Inventory is currently being reallocated at Casablanca Hub-A1.</p>
						<a href="index.php" class="btn-primary">RETURN TO CATALOG</a>
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