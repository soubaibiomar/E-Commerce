<?php 
session_start();
include('includes/config.php');

$pid = intval($_GET['pid'] ?? 0);

if (isset($_GET['action']) && $_GET['action'] == "add") {
	$id = intval($_GET['id'] ?? 0);
	if (isset($_SESSION['cart'][$id])) {
		$_SESSION['cart'][$id]['quantity']++;
	} else {
		$product = db_fetch_one("SELECT id, productPrice FROM products WHERE id=?", [$id], "i");
		if ($product) {
			$_SESSION['cart'][$product['id']] = array("quantity" => 1, "price" => $product['productPrice']);
			echo "<script>alert('Product added to your cart.'); window.location='my-cart.php';</script>";
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

if (isset($_POST['submit'])) {
	$qty = intval($_POST['quality'] ?? 5);
	$price = intval($_POST['price'] ?? 5);
	$value = intval($_POST['value'] ?? 5);
	$name = trim($_POST['name'] ?? '');
	$summary = trim($_POST['summary'] ?? '');
	$review = trim($_POST['review'] ?? '');
    if (!empty($name) && !empty($review)) {
	    db_query("INSERT INTO productreviews(productId, quality, price, value, name, summary, review) VALUES(?, ?, ?, ?, ?, ?, ?)", [$pid, $qty, $price, $value, $name, $summary, $review], "iiiisss");
		echo "<script>alert('Review recorded on ledger.');</script>";
    }
}

$product = db_fetch_one("SELECT products.*, category.categoryName, subcategory.subcategory as subcategoryName FROM products JOIN category ON category.id=products.category JOIN subcategory ON subcategory.id=products.subCategory WHERE products.id=?", [$pid], "i");
$row = $product;

$specs = [];
if (!empty($row['specifications'])) {
    $decoded = json_decode($row['specifications'], true);
    if (is_array($decoded)) {
        $specs = $decoded;
    }
}

// Infer 3D Model type from category / name
$pNameLower = strtolower($row['productName'] ?? '');
$modelType = 'smartphone';
if (strpos($pNameLower, 'macbook') !== false || strpos($pNameLower, 'xps') !== false || strpos($pNameLower, 'laptop') !== false) {
    $modelType = 'laptop';
} elseif (strpos($pNameLower, 'headphone') !== false || strpos($pNameLower, 'sony wh') !== false || strpos($pNameLower, 'earbud') !== false) {
    $modelType = 'headphone';
} elseif (strpos($pNameLower, 'watch') !== false) {
    $modelType = 'watch';
} elseif (strpos($pNameLower, 'playstation') !== false || strpos($pNameLower, 'ps5') !== false || strpos($pNameLower, 'switch') !== false) {
    $modelType = 'console';
} elseif (strpos($pNameLower, 'chair') !== false || strpos($pNameLower, 'aeron') !== false) {
    $modelType = 'chair';
} elseif (strpos($pNameLower, 'habit') !== false || strpos($pNameLower, 'book') !== false || strpos($pNameLower, 'money') !== false || strpos($pNameLower, 'deep work') !== false) {
    $modelType = 'book';
}
$revCount = db_fetch_one("SELECT COUNT(id) as cnt, AVG(quality) as avg_rating FROM productreviews WHERE productId=?", [$pid], "i");
$numReviews = intval($revCount['cnt'] ?? 0);
$avgRating = floatval($revCount['avg_rating'] ?? 4.9);
if ($avgRating <= 0) $avgRating = 4.9;
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
<?php 
if ($row) {
    $seoTitle = $row['productName'] . " | Official Specs & 3D WebGL Studio | ZeyTech";
    $seoDesc = "Explore " . $row['productName'] . " by " . $row['productCompany'] . ". Real-time Casablanca inventory, 3D WebGL inspection, and multi-currency checkout.";
    $ogImg = "admin/productimages/" . $row['id'] . "/" . $row['productImage1'];
    render_seo_meta($seoTitle, $seoDesc, "product-details.php?pid=" . $row['id'], $ogImg, "product");
    render_product_schema($row, $numReviews, $avgRating);
}
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

<!-- Breadcrumb -->
<div style="background:#0b162c; border-bottom:1px solid rgba(226,232,240,0.10); padding:10px 0; font-size:12px;">
	<div class="container">
		<?php if ($row) { ?>
		<div style="color:#94a3b8;">
			<a href="index.php" style="color:#94a3b8; text-decoration:none;">Home</a>
			<span style="margin:0 6px; color:#64748b;">/</span>
			<a href="category.php?cid=<?php echo e($row['category']); ?>" style="color:#94a3b8; text-decoration:none;"><?php echo e($row['categoryName']);?></a>
			<span style="margin:0 6px; color:#64748b;">/</span>
			<a href="sub-category.php?scid=<?php echo e($row['subCategory']); ?>" style="color:#94a3b8; text-decoration:none;"><?php echo e($row['subcategoryName']);?></a>
			<span style="margin:0 6px; color:#64748b;">/</span>
			<span style="color:#d9b45d; font-weight:600;"><?php echo e($row['productName']);?></span>
		</div>
		<?php } ?>
	</div>
</div>

<div class="body-content" style="padding-top:28px; padding-bottom:60px;">
	<div class='container'>
		<?php if ($row) { 
			$price = floatval($row['productPrice']);
			$oldPrice = floatval($row['productPriceBeforeDiscount']);
			$shipping = floatval($row['shippingCharge']);
			$discount = ($oldPrice > $price && $oldPrice > 0) ? round((($oldPrice - $price) / $oldPrice) * 100) : 0;
		?>
		<div class="manifest-panel" style="background:#121e36; border:1px solid rgba(226,232,240,0.12); border-radius:8px; padding:24px; margin-bottom:32px; box-shadow:var(--shadow-md);">
			<div class="row">
				<!-- Media Showcase Column (2D Gallery & 3D Interactive WebGL Model) -->
				<div class="col-xs-12 col-md-5">
					<!-- View Mode Switcher -->
					<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:14px;">
						<div style="display:flex; gap:6px;">
							<button id="viewBtn2D" type="button" class="btn-primary" onclick="switchMediaView('2d')" style="padding:6px 14px; font-size:12px; border-radius:4px;">
								<i class="fa fa-image"></i> Photos
							</button>
							<button id="viewBtn3D" type="button" class="btn-ghost" onclick="switchMediaView('3d')" style="padding:6px 14px; font-size:12px; border-radius:4px;">
								<i class="fa fa-cube"></i> 3D Studio
							</button>
						</div>
						<span style="font-size:11px; font-weight:600; color:#d9b45d; background:rgba(197,155,67,0.12); padding:3px 10px; border-radius:12px; border:1px solid rgba(197,155,67,0.25);">
							360&deg; Interactive
						</span>
					</div>

					<!-- 2D Photo Container -->
					<div id="container2D">
						<div style="background:#0b162c; border:1px solid rgba(226,232,240,0.10); border-radius:6px; padding:24px; text-align:center; position:relative; min-height:380px; display:flex; align-items:center; justify-content:center;">
							<?php if ($discount > 0) { ?>
							<span style="position:absolute; top:12px; left:12px; z-index:2; font-size:11px; font-weight:700; background:#c59b43; color:#0b162c; padding:3px 8px; border-radius:4px;">-<?php echo e($discount); ?>% OFF</span>
							<?php } ?>
							<img id="mainProductImage" src="<?php echo e(get_product_image_url($row, 1)); ?>" alt="<?php echo e($row['productName']);?>" style="max-height:340px; max-width:90%; object-fit:contain; transition:transform 0.25s ease;">
						</div>

						<!-- Thumbnails Strip -->
						<div style="display:flex; gap:10px; margin-top:12px; justify-content:center;">
							<div onclick="document.getElementById('mainProductImage').src='<?php echo e(get_product_image_url($row, 1)); ?>'" style="width:68px; height:68px; background:#0b162c; border:2px solid #c59b43; border-radius:4px; padding:4px; cursor:pointer; display:flex; align-items:center; justify-content:center;">
								<img src="<?php echo e(get_product_image_url($row, 1)); ?>" style="max-height:100%; max-width:100%; object-fit:contain;">
							</div>
							<?php if (!empty($row['productImage2'])) { ?>
							<div onclick="document.getElementById('mainProductImage').src='<?php echo e(get_product_image_url($row, 2)); ?>'" style="width:68px; height:68px; background:#0b162c; border:1px solid rgba(226,232,240,0.15); border-radius:4px; padding:4px; cursor:pointer; display:flex; align-items:center; justify-content:center;">
								<img src="<?php echo e(get_product_image_url($row, 2)); ?>" style="max-height:100%; max-width:100%; object-fit:contain;">
							</div>
							<?php } ?>
							<?php if (!empty($row['productImage3'])) { ?>
							<div onclick="document.getElementById('mainProductImage').src='<?php echo e(get_product_image_url($row, 3)); ?>'" style="width:68px; height:68px; background:#0b162c; border:1px solid rgba(226,232,240,0.15); border-radius:4px; padding:4px; cursor:pointer; display:flex; align-items:center; justify-content:center;">
								<img src="<?php echo e(get_product_image_url($row, 3)); ?>" style="max-height:100%; max-width:100%; object-fit:contain;">
							</div>
							<?php } ?>
						</div>
					</div>

					<!-- 3D Interactive WebGL Model Studio -->
					<div id="container3D" style="display:none;">
						<div style="background:#0b162c; border:1px solid rgba(226,232,240,0.10); border-radius:6px; overflow:hidden; position:relative;">
							<!-- 3D Canvas Target -->
							<div id="webgl3DCanvas" style="width:100%; height:380px; cursor:grab;"></div>

							<!-- Floating 3D Tool Overlay -->
							<div style="position:absolute; top:12px; left:12px; background:rgba(11,22,44,0.85); backdrop-filter:blur(4px); padding:4px 10px; border-radius:4px; font-size:11px; color:#94a3b8; border:1px solid rgba(226,232,240,0.15); pointer-events:none;">
								<i class="fa fa-arrows"></i> Drag to Rotate &bull; Scroll to Zoom
							</div>

							<!-- Studio Controls Toolbar -->
							<div style="padding:10px 14px; background:#182847; border-top:1px solid rgba(226,232,240,0.10); display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:8px;">
								<!-- Color Finishes -->
								<div style="display:flex; align-items:center; gap:6px;">
									<span style="font-size:11px; font-weight:600; color:#94a3b8; text-transform:uppercase;">Finish:</span>
									<button type="button" onclick="window.active3DViewer.setColor('#94a3b8')" title="Silver" style="width:18px; height:18px; border-radius:50%; background:#94a3b8; border:1px solid #ffffff; cursor:pointer; outline:none;"></button>
									<button type="button" onclick="window.active3DViewer.setColor('#0b162c')" title="Space Black" style="width:18px; height:18px; border-radius:50%; background:#0b162c; border:1px solid #ffffff; cursor:pointer; outline:none;"></button>
									<button type="button" onclick="window.active3DViewer.setColor('#1e3a8a')" title="Deep Navy" style="width:18px; height:18px; border-radius:50%; background:#1e3a8a; border:1px solid #ffffff; cursor:pointer; outline:none;"></button>
									<button type="button" onclick="window.active3DViewer.setColor('#c59b43')" title="ZeyTech Gold" style="width:18px; height:18px; border-radius:50%; background:#c59b43; border:1px solid #ffffff; cursor:pointer; outline:none;"></button>
								</div>

								<!-- Studio Action Buttons -->
								<div style="display:flex; gap:6px;">
									<button type="button" onclick="toggle3DRotation(this)" class="btn-ghost" style="padding:4px 10px; font-size:11px; border-radius:4px;">
										Auto-Spin
									</button>
									<button type="button" onclick="cycle3DLighting()" class="btn-ghost" style="padding:4px 10px; font-size:11px; border-radius:4px;">
										Lighting
									</button>
									<button type="button" onclick="toggle3DWireframe(this)" class="btn-ghost" style="padding:4px 10px; font-size:11px; border-radius:4px;">
										Wireframe
									</button>
								</div>
							</div>
						</div>
					</div>
				</div>

				<!-- Product Info Column -->
				<div class="col-xs-12 col-md-7" style="padding-left:28px;">
					<div style="font-size:12px; font-weight:600; text-transform:uppercase; color:#d9b45d; letter-spacing:0.05em; margin-bottom:6px;">
						<?php echo e($row['productCompany']); ?> &bull; <?php echo e($row['categoryName']); ?>
					</div>

					<h1 style="font-family:'Fraunces', serif; font-size:26px; font-weight:700; color:#ffffff; line-height:1.25; margin:0 0 12px 0;">
						<?php echo e($row['productName']);?>
					</h1>

					<!-- Rating and Reviews Count -->
					<div style="display:flex; align-items:center; gap:8px; margin-bottom:16px;">
						<div style="color:#d9b45d; font-size:13px;">
							<i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i>
						</div>
						<span style="font-size:12px; color:#94a3b8;">(<?php echo e($numReviews); ?> Customer Reviews)</span>
					</div>

					<!-- Price Container with Region Pricing -->
					<div style="background:#182847; border:1px solid rgba(226,232,240,0.10); border-radius:6px; padding:16px 20px; margin-bottom:20px; display:flex; align-items:baseline; gap:12px;">
						<span style="font-family:'Space Mono'; font-size:24px; font-weight:700; color:#d9b45d;"><?php echo format_price($price); ?></span>
						<?php if ($oldPrice > $price) { ?>
						<span style="font-family:'Space Mono'; font-size:14px; color:#64748b; text-decoration:line-through;"><?php echo format_price($oldPrice); ?></span>
						<span style="font-size:11px; font-weight:700; background:rgba(197,155,67,0.15); color:#d9b45d; border:1px solid rgba(197,155,67,0.3); padding:2px 8px; border-radius:4px;">
							Save <?php echo format_price($oldPrice - $price); ?>
						</span>
						<?php } ?>
					</div>

					<!-- Key Tech Highlights -->
					<?php if(!empty($specs)) { ?>
					<div style="background:#0b162c; border:1px solid rgba(226,232,240,0.10); border-radius:6px; padding:14px 18px; margin-bottom:20px;">
						<div style="font-size:11px; font-weight:600; text-transform:uppercase; color:#94a3b8; letter-spacing:0.05em; margin-bottom:8px;">
							Key Specifications
						</div>
						<div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(200px, 1fr)); gap:8px; font-size:12px;">
							<?php 
							$shownCount = 0;
							foreach($specs as $k => $v) {
								if($shownCount < 4) {
							?>
							<div>
								<strong style="color:#ffffff;"><?php echo e($k); ?>:</strong> 
								<span style="color:#94a3b8; font-size:12px;"><?php echo e(substr($v, 0, 45)); ?><?php echo strlen($v) > 45 ? '...' : ''; ?></span>
							</div>
							<?php 
									$shownCount++;
								}
							} 
							?>
						</div>
					</div>
					<?php } ?>

					<!-- Meta details -->
					<div style="display:grid; grid-template-columns:repeat(2, 1fr); gap:12px; margin-bottom:20px; font-size:13px;">
						<div>
							<span style="color:#94a3b8;">Availability:</span> 
							<?php if ($row['productAvailability'] == 'In Stock') { ?>
							<span style="color:#10b981; font-weight:600;">● In Stock</span>
							<?php } else { ?>
							<span style="color:#ef4444; font-weight:600;">Out of Stock</span>
							<?php } ?>
						</div>
						<div>
							<span style="color:#94a3b8;">Shipping:</span> 
							<span><?php echo ($shipping == 0) ? '<span style="color:#10b981; font-weight:600;">Free Delivery</span>' : format_price($shipping); ?></span>
						</div>
					</div>

					<!-- Quick Description -->
					<div style="font-size:13px; color:#94a3b8; line-height:1.6; margin-bottom:24px;">
						<?php echo nl2br(e(substr($row['productDescription'], 0, 220))); ?>...
					</div>

					<!-- Action Buttons -->
					<div style="display:flex; gap:12px; flex-wrap:wrap; align-items:center; margin-bottom:24px;">
						<?php if ($row['productAvailability'] == 'In Stock') { ?>
						<a href="product-details.php?action=add&id=<?php echo e($row['id']); ?>" class="btn-primary" style="padding:12px 28px; font-size:13px; border-radius:4px;">
							<i class="fa fa-shopping-cart"></i> Add to Cart
						</a>
						<?php } ?>
						<a href="product-details.php?pid=<?php echo e($row['id']);?>&action=wishlist" class="btn-ghost" style="padding:12px 20px; font-size:13px; border-radius:4px;">
							<i class="fa fa-heart-o"></i> Save to Wishlist
						</a>
					</div>

					<!-- Moroccan Domestic Shipping Rate Calculator -->
					<div style="background:#182847; border:1px solid rgba(226,232,240,0.10); border-radius:6px; padding:14px 18px; margin-bottom:20px;">
						<div style="font-size:11px; font-weight:600; text-transform:uppercase; color:#d9b45d; letter-spacing:0.05em; margin-bottom:8px;">
							<i class="fa fa-truck"></i> Domestic Delivery Calculator
						</div>
						<div style="display:flex; gap:10px; align-items:center; flex-wrap:wrap;">
							<select id="shippingRegionSelect" style="width:220px; font-size:12px; font-family:'IBM Plex Sans'; background:#0b162c; border:1px solid rgba(226,232,240,0.15); color:#f8fafc; padding:8px 10px; border-radius:4px; outline:none;" onchange="calculateProductShipping()">
								<option value="Casablanca-Settat">Casablanca-Settat (Hub)</option>
								<option value="Rabat-Salé-Kénitra">Rabat-Salé-Kénitra</option>
								<option value="Marrakech-Safi">Marrakech-Safi</option>
								<option value="Tanger-Tétouan-Al Hoceïma">Tanger-Tétouan</option>
								<option value="Fès-Meknès">Fès-Meknès</option>
								<option value="Souss-Massa">Souss-Massa (Agadir)</option>
								<option value="Oriental">Oriental (Oujda)</option>
								<option value="Laâyoune-Sakia El Hamra">Laâyoune</option>
							</select>
							<div id="shippingQuoteResult" style="font-family:'Space Mono'; font-size:12px; font-weight:700; color:#10b981;">
								CTM: 35.00 MAD &bull; 24h Transit
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<!-- Product Full Description & Fiche Technique Tabs -->
		<div class="manifest-panel" style="background:#121e36; border:1px solid rgba(226,232,240,0.12); border-radius:8px; padding:28px; margin-bottom:40px; box-shadow:var(--shadow-md);">
			<div style="margin-bottom:20px; border-bottom:1px solid rgba(226,232,240,0.10); padding-bottom:12px;">
				<h3 style="font-family:'Fraunces', serif; font-size:20px; font-weight:700; color:#ffffff; margin:0 0 4px 0;">
					Technical Specifications &amp; Details
				</h3>
				<p style="font-size:12px; color:#94a3b8; margin:0;">
					Verified hardware specifications &bull; Casablanca Central Warehouse
				</p>
			</div>

			<?php if(!empty($specs)) { ?>
			<div class="table-responsive">
				<table class="enterprise-table">
					<tbody>
						<?php 
						foreach($specs as $specKey => $specVal) { 
						?>
						<tr>
							<td style="width:240px; font-family:'Space Mono'; font-size:12px; font-weight:700; color:#d9b567; border-right:1px solid rgba(142,162,191,0.1);">
								<?php echo e($specKey); ?>
							</td>
							<td style="color:#f2efe6; font-size:13px; font-family:'IBM Plex Sans';">
								<?php echo e($specVal); ?>
							</td>
						</tr>
						<?php } ?>
					</tbody>
				</table>
			</div>
			<?php } else { ?>
			<p style="color:#8ea2bf; font-family:'Space Mono'; font-size:12px;">Specifications registered under general hardware profile.</p>
			<?php } ?>

			<!-- Detailed Product Overview -->
			<div style="margin-top:32px; border-top:1px solid rgba(142,162,191,0.18); padding-top:20px;">
				<h4 style="font-family:'Fraunces', serif; font-size:18px; font-weight:700; color:#f2efe6; margin-bottom:12px;">
					Commercial Overview
				</h4>
				<div style="font-size:14px; color:#8ea2bf; line-height:1.7;">
					<?php echo nl2br(e($row['productDescription']));?>
				</div>
			</div>
		</div>

		<?php } else { ?>
		<div style="text-align:center; padding:80px 20px; background:#0c1526; border:1px solid rgba(142,162,191,0.18); border-radius:2px;">
			<h2 style="font-family:'Fraunces', serif; color:#f2efe6;">Item Not Found</h2>
			<p style="color:#8ea2bf; font-family:'Space Mono'; font-size:12px; margin-bottom:20px;">The requested serial or item was not located in the Casablanca catalog.</p>
			<a href="index.php" class="btn-primary">RETURN TO CATALOG</a>
		</div>
		<?php } ?>
	</div>
</div>

<?php include('includes/footer.php');?>

<script src="assets/js/jquery-1.11.1.min.js"></script>
<script src="assets/js/bootstrap.min.js"></script>

<!-- Three.js and OrbitControls for Interactive 3D Model Studio -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/controls/OrbitControls.js"></script>
<script src="assets/js/product-3d-model.js"></script>

<script>
var is3DInitialized = false;
var lightingModes = ['studio', 'neon', 'sunset'];
var currentLightIndex = 0;

function switchMediaView(mode) {
	var c2D = document.getElementById('container2D');
	var c3D = document.getElementById('container3D');
	var b2D = document.getElementById('viewBtn2D');
	var b3D = document.getElementById('viewBtn3D');

	if (mode === '3d') {
		c2D.style.display = 'none';
		c3D.style.display = 'block';
		b2D.className = 'btn-ghost';
		b3D.className = 'btn-primary';

		if (!is3DInitialized) {
			window.active3DViewer = new Product3DViewer('webgl3DCanvas', {
				type: '<?php echo $modelType; ?>',
				name: '<?php echo addslashes($row['productName'] ?? ''); ?>',
				color: '#c79a44'
			});
			is3DInitialized = true;
		} else {
			window.active3DViewer.onWindowResize();
		}
	} else {
		c3D.style.display = 'none';
		c2D.style.display = 'block';
		b3D.className = 'btn-ghost';
		b2D.className = 'btn-primary';
	}
}

function toggle3DRotation(btn) {
	if (window.active3DViewer) {
		window.active3DViewer.toggleRotation();
	}
}

function toggle3DWireframe(btn) {
	if (window.active3DViewer) {
		window.active3DViewer.toggleWireframe();
	}
}

function cycle3DLighting() {
	if (window.active3DViewer) {
		currentLightIndex = (currentLightIndex + 1) % lightingModes.length;
		window.active3DViewer.setLighting(lightingModes[currentLightIndex]);
	}
}
</script>
</body>
</html>