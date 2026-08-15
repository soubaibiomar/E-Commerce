<?php
session_start();
include('includes/config.php');

$searchTerm = trim($_POST['product'] ?? $_GET['product'] ?? '');
$find = "%" . $searchTerm . "%";

if (isset($_GET['action']) && $_GET['action'] == "add") {
	$id = intval($_GET['id'] ?? 0);
	if (isset($_SESSION['cart'][$id])) {
		$_SESSION['cart'][$id]['quantity']++;
	} else {
		$product = db_fetch_one("SELECT id, productPrice FROM products WHERE id=?", [$id], "i");
		if ($product) {
			$_SESSION['cart'][$product['id']] = array("quantity" => 1, "price" => $product['productPrice']);
			echo "<script>alert('Product added to your bag.'); window.location='my-cart.php';</script>";
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
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
	    <title>Search: "<?php echo e($searchTerm); ?>" | ZeyTech</title>
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
		<!-- Search Banner Header -->
		<div class="modern-hero-banner" style="padding:28px 36px; margin-bottom:28px;">
			<div class="hero-eyebrow">
				<span class="hexagram-mark" style="width:16px; height:16px;">
					<svg class="hexagram-svg" viewBox="0 0 24 24">
						<polygon points="12,2 22,18 2,18" stroke="#c79a44" fill="none" stroke-width="1.5"/>
						<polygon points="12,22 22,6 2,6" stroke="#d9b567" fill="none" stroke-width="1.5"/>
					</svg>
				</span>
				[SEARCH.CATALOG_QUERY]
			</div>
			<h1 class="hero-headline" style="font-size:26px; margin-bottom:6px;">Search Results for "<?php echo e($searchTerm); ?>"</h1>
			<p class="hero-subtext" style="margin-bottom:0; font-size:13px;">Real-time settlement enabled: <strong style="color:#d9b567; font-family:'Space Mono';"><?php echo get_current_currency()['code']; ?> (<?php echo get_current_currency()['symbol']; ?>)</strong></p>
		</div>

		<div class='row outer-bottom-sm'>
			<!-- Sidebar Categories -->
			<div class='col-md-3 sidebar'>
				<div class="manifest-panel" style="padding:16px; margin-bottom:20px;">
					<div style="font-family:'Space Mono'; font-size:11px; font-weight:700; color:#c79a44; text-transform:uppercase; letter-spacing:0.06em; margin-bottom:12px; border-bottom:1px solid rgba(142,162,191,0.18); padding-bottom:8px;">
						[CATALOG.DOMAINS]
					</div>
					<ul class="list-unstyled" style="margin:0;">
						<?php 
						$allCats = db_fetch_all("SELECT id, categoryName FROM category ORDER BY categoryName ASC");
						foreach ($allCats as $row) {
						?>
						<li style="margin-bottom:6px;">
							<a href="category.php?cid=<?php echo e($row['id']);?>" style="display:flex; justify-content:space-between; align-items:center; padding:6px 8px; color:#8ea2bf; font-size:13px; text-decoration:none; border-radius:2px;">
								<span><?php echo e($row['categoryName']);?></span>
								<i class="fa fa-angle-right" style="font-size:11px; color:#5e7391;"></i>
							</a>
						</li>
						<?php } ?>
					</ul>
				</div>
            </div>

			<!-- Search Results Grid -->
			<div class='col-md-9'>
				<div class="row">
					<?php
					$results = db_fetch_all("SELECT products.*, category.categoryName FROM products LEFT JOIN category ON category.id=products.category WHERE products.productName LIKE ? OR products.productCompany LIKE ? OR products.productDescription LIKE ? ORDER BY products.id DESC", [$find, $find, $find], "sss");
					if (!empty($results)) {
						foreach ($results as $row) {
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

							<div class="product-category-label"><?php echo e($row['categoryName'] ?? 'Hardware'); ?></div>
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
								<a href="search-result.php?product=<?php echo urlencode($searchTerm); ?>&action=add&id=<?php echo e($row['id']); ?>" class="btn-add-cart-modern">
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
						<h3 style="font-family:'Fraunces', serif; color:#f2efe6; font-weight:700;">No Matching Hardware Located</h3>
						<p style="color:#8ea2bf; font-family:'Space Mono'; font-size:12px; margin-bottom:20px;">Try searching for generic specifications, brands, or part numbers.</p>
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