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

<!-- Breadcrumb (Space Mono Editorial Line) -->
<div style="background:#0c1526; border-bottom:1px solid rgba(142,162,191,0.18); padding:10px 0; font-family:'Space Mono', monospace; font-size:11px;">
	<div class="container">
		<?php if ($row) { ?>
		<div style="color:#8ea2bf;">
			<a href="index.php" style="color:#8ea2bf; text-decoration:none;">CATALOG</a>
			<span style="margin:0 6px; color:#5e7391;">/</span>
			<a href="category.php?cid=<?php echo e($row['category']); ?>" style="color:#8ea2bf; text-decoration:none;"><?php echo strtoupper(e($row['categoryName']));?></a>
			<span style="margin:0 6px; color:#5e7391;">/</span>
			<a href="sub-category.php?scid=<?php echo e($row['subCategory']); ?>" style="color:#8ea2bf; text-decoration:none;"><?php echo strtoupper(e($row['subcategoryName']));?></a>
			<span style="margin:0 6px; color:#5e7391;">/</span>
			<span style="color:#d9b567; font-weight:700;">#<?php echo str_pad($row['id'], 4, '0', STR_PAD_LEFT); ?></span>
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
		<div class="manifest-panel" style="margin-bottom:32px;">
			<div class="row">
				<!-- Media Showcase Column (2D Gallery & 3D Interactive WebGL Model) -->
				<div class="col-xs-12 col-md-5">
					<!-- View Mode Switcher -->
					<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
						<div style="display:flex; gap:4px;">
							<button id="viewBtn2D" type="button" class="btn-primary" onclick="switchMediaView('2d')" style="padding:4px 12px; font-size:11px; font-family:'Space Mono';">
								2D PHOTOS
							</button>
							<button id="viewBtn3D" type="button" class="btn-ghost" onclick="switchMediaView('3d')" style="padding:4px 12px; font-size:11px; font-family:'Space Mono';">
								3D WEBGL STUDIO
							</button>
						</div>
						<span class="tag-pill tag-gold" style="font-size:10px;">
							[360&deg; ROTATION]
						</span>
					</div>

					<!-- 2D Photo Container -->
					<div id="container2D">
						<div style="background:#080e1a; border:1px solid rgba(142,162,191,0.18); border-radius:2px; padding:24px; text-align:center; position:relative; min-height:380px; display:flex; align-items:center; justify-content:center;">
							<?php if ($discount > 0) { ?>
							<span class="tag-pill tag-gold" style="position:absolute; top:12px; left:12px; z-index:2; font-size:11px;">-<?php echo e($discount); ?>% OFF</span>
							<?php } ?>
							<img id="mainProductImage" src="<?php echo e(get_product_image_url($row, 1)); ?>" alt="<?php echo e($row['productName']);?>" style="max-height:340px; max-width:90%; object-fit:contain; transition:transform 0.25s ease;">
						</div>

						<!-- Thumbnails Strip -->
						<div style="display:flex; gap:10px; margin-top:12px; justify-content:center;">
							<div onclick="document.getElementById('mainProductImage').src='<?php echo e(get_product_image_url($row, 1)); ?>'" style="width:68px; height:68px; background:#080e1a; border:1px solid #c79a44; border-radius:2px; padding:4px; cursor:pointer; display:flex; align-items:center; justify-content:center;">
								<img src="<?php echo e(get_product_image_url($row, 1)); ?>" style="max-height:100%; max-width:100%; object-fit:contain;">
							</div>
							<?php if (!empty($row['productImage2'])) { ?>
							<div onclick="document.getElementById('mainProductImage').src='<?php echo e(get_product_image_url($row, 2)); ?>'" style="width:68px; height:68px; background:#080e1a; border:1px solid rgba(142,162,191,0.2); border-radius:2px; padding:4px; cursor:pointer; display:flex; align-items:center; justify-content:center;">
								<img src="<?php echo e(get_product_image_url($row, 2)); ?>" style="max-height:100%; max-width:100%; object-fit:contain;">
							</div>
							<?php } ?>
							<?php if (!empty($row['productImage3'])) { ?>
							<div onclick="document.getElementById('mainProductImage').src='<?php echo e(get_product_image_url($row, 3)); ?>'" style="width:68px; height:68px; background:#080e1a; border:1px solid rgba(142,162,191,0.2); border-radius:2px; padding:4px; cursor:pointer; display:flex; align-items:center; justify-content:center;">
								<img src="<?php echo e(get_product_image_url($row, 3)); ?>" style="max-height:100%; max-width:100%; object-fit:contain;">
							</div>
							<?php } ?>
						</div>
					</div>

					<!-- 3D Interactive WebGL Model Studio -->
					<div id="container3D" style="display:none;">
						<div style="background:#080e1a; border:1px solid rgba(142,162,191,0.18); border-radius:2px; overflow:hidden; position:relative;">
							<!-- 3D Canvas Target -->
							<div id="webgl3DCanvas" style="width:100%; height:380px; cursor:grab;"></div>

							<!-- Floating 3D Tool Overlay -->
							<div style="position:absolute; top:12px; left:12px; background:rgba(8,14,26,0.85); backdrop-filter:blur(4px); padding:4px 10px; border-radius:2px; font-family:'Space Mono'; font-size:10px; color:#8ea2bf; border:1px solid rgba(142,162,191,0.2); pointer-events:none;">
								[DRAG: ROTATE &bull; SCROLL: ZOOM]
							</div>

							<!-- Studio Controls Toolbar -->
							<div style="padding:10px 14px; background:#0c1526; border-top:1px solid rgba(142,162,191,0.18); display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:8px;">
								<!-- Color Finishes -->
								<div style="display:flex; align-items:center; gap:6px;">
									<span style="font-family:'Space Mono'; font-size:10px; font-weight:700; color:#8ea2bf; text-transform:uppercase;">FINISH:</span>
									<button type="button" onclick="window.active3DViewer.setColor('#94a3b8')" title="Natural Titanium / Silver" style="width:16px; height:16px; border-radius:2px; background:#94a3b8; border:1px solid #f2efe6; cursor:pointer; outline:none;"></button>
									<button type="button" onclick="window.active3DViewer.setColor('#080e1a')" title="Space Black / Graphite" style="width:16px; height:16px; border-radius:2px; background:#080e1a; border:1px solid #f2efe6; cursor:pointer; outline:none;"></button>
									<button type="button" onclick="window.active3DViewer.setColor('#1e3a8a')" title="Deep Navy" style="width:16px; height:16px; border-radius:2px; background:#1e3a8a; border:1px solid #f2efe6; cursor:pointer; outline:none;"></button>
									<button type="button" onclick="window.active3DViewer.setColor('#c79a44')" title="ZeyTech Gold" style="width:16px; height:16px; border-radius:2px; background:#c79a44; border:1px solid #f2efe6; cursor:pointer; outline:none;"></button>
								</div>

								<!-- Studio Action Buttons -->
								<div style="display:flex; gap:4px;">
									<button type="button" onclick="toggle3DRotation(this)" class="btn-ghost" style="padding:2px 8px; font-size:10px; font-family:'Space Mono';">
										AUTO-SPIN
									</button>
									<button type="button" onclick="cycle3DLighting()" class="btn-ghost" style="padding:2px 8px; font-size:10px; font-family:'Space Mono';">
										LIGHTING
									</button>
									<button type="button" onclick="toggle3DWireframe(this)" class="btn-ghost" style="padding:2px 8px; font-size:10px; font-family:'Space Mono';">
										WIREFRAME
									</button>
								</div>
							</div>
						</div>
					</div>
				</div>

				<!-- Product Info Column -->
				<div class="col-xs-12 col-md-7" style="padding-left:28px;">
					<div style="font-family:'Space Mono', monospace; font-size:11px; font-weight:700; text-transform:uppercase; color:#c79a44; letter-spacing:0.08em; margin-bottom:6px;">
						[<?php echo strtoupper(e($row['productCompany'])); ?> &bull; <?php echo strtoupper(e($row['categoryName'])); ?>]
					</div>

					<h1 style="font-family:'Fraunces', serif; font-size:28px; font-weight:700; color:#f2efe6; line-height:1.2; margin:0 0 12px 0; letter-spacing:-0.02em;">
						<?php echo e($row['productName']);?>
					</h1>

					<!-- Rating and Reviews Count -->
					<div style="display:flex; align-items:center; gap:8px; margin-bottom:16px;">
						<div style="color:#d9b567; font-size:13px;">
							<i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i>
						</div>
						<span style="font-family:'Space Mono'; font-size:11px; color:#8ea2bf;">(<?php echo e($numReviews); ?> LEDGER REVIEWS)</span>
					</div>

					<!-- Price Container with Region Pricing -->
					<div style="background:#111d33; border:1px solid rgba(142,162,191,0.18); border-radius:2px; padding:14px 18px; margin-bottom:20px; display:flex; align-items:baseline; gap:12px;">
						<span style="font-family:'Space Mono'; font-size:24px; font-weight:700; color:#d9b567;"><?php echo format_price($price); ?></span>
						<?php if ($oldPrice > $price) { ?>
						<span style="font-family:'Space Mono'; font-size:14px; color:#5e7391; text-decoration:line-through;"><?php echo format_price($oldPrice); ?></span>
						<span class="tag-pill tag-gold" style="font-size:11px;">SAVE <?php echo format_price($oldPrice - $price); ?></span>
						<?php } ?>
					</div>

					<!-- Key Tech Highlights Pill Box -->
					<?php if(!empty($specs)) { ?>
					<div style="background:#080e1a; border:1px solid rgba(142,162,191,0.18); border-radius:2px; padding:14px 18px; margin-bottom:20px;">
						<div style="font-family:'Space Mono'; font-size:10px; font-weight:700; text-transform:uppercase; color:#8ea2bf; letter-spacing:0.08em; margin-bottom:8px;">
							[FICHE TECHNIQUE &bull; HIGHLIGHTS]
						</div>
						<div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(200px, 1fr)); gap:8px; font-size:12px;">
							<?php 
							$shownCount = 0;
							foreach($specs as $k => $v) {
								if($shownCount < 4) {
							?>
							<div>
								<strong style="color:#f2efe6; font-family:'IBM Plex Sans';"><?php echo e($k); ?>:</strong> 
								<span style="color:#8ea2bf; font-family:'Space Mono'; font-size:11px;"><?php echo e(substr($v, 0, 45)); ?><?php echo strlen($v) > 45 ? '...' : ''; ?></span>
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
					<div style="display:grid; grid-template-columns:repeat(2, 1fr); gap:12px; margin-bottom:20px; font-size:13px; font-family:'Space Mono';">
						<div>
							<span style="color:#8ea2bf;">AVAILABILITY:</span> 
							<?php if ($row['productAvailability'] == 'In Stock') { ?>
							<span style="color:#22c55e; font-weight:700;">[IN STOCK]</span>
							<?php } else { ?>
							<span style="color:#ef4444; font-weight:700;">[OUT OF STOCK]</span>
							<?php } ?>
						</div>
						<div>
							<span style="color:#8ea2bf;">DELIVERY:</span> 
							<span><?php echo ($shipping == 0) ? '<span style="color:#22c55e; font-weight:700;">FREE EXPRESS</span>' : format_price($shipping); ?></span>
						</div>
					</div>

					<!-- Quick Description -->
					<div style="font-size:13px; color:#8ea2bf; line-height:1.6; margin-bottom:24px;">
						<?php echo nl2br(e(substr($row['productDescription'], 0, 220))); ?>...
					</div>

					<!-- Action Buttons -->
					<div style="display:flex; gap:12px; flex-wrap:wrap; align-items:center; margin-bottom:24px;">
						<?php if ($row['productAvailability'] == 'In Stock') { ?>
						<a href="product-details.php?action=add&id=<?php echo e($row['id']); ?>" class="btn-primary" style="padding:12px 28px; font-family:'Space Mono'; font-size:12px;">
							+ ADD TO BAG
						</a>
						<?php } ?>
						<a href="product-details.php?pid=<?php echo e($row['id']);?>&action=wishlist" class="btn-ghost" style="padding:12px 20px; font-family:'Space Mono'; font-size:12px;">
							SAVE TO MANIFEST
						</a>
					</div>

					<!-- Moroccan Domestic Shipping Rate Calculator -->
					<div style="background:#111d33; border:1px solid rgba(142,162,191,0.18); border-radius:2px; padding:14px 18px; margin-bottom:20px;">
						<div style="font-family:'Space Mono'; font-size:10px; font-weight:700; text-transform:uppercase; color:#c79a44; letter-spacing:0.08em; margin-bottom:8px;">
							[CASABLANCA HUB-A1 DOMESTIC DISPATCH TARIFF]
						</div>
						<div style="display:flex; gap:10px; align-items:center; flex-wrap:wrap;">
							<select id="shippingRegionSelect" style="width:220px; font-size:12px; font-family:'Space Mono'; background:#080e1a; border:1px solid rgba(142,162,191,0.25); color:#f2efe6; padding:6px 10px; border-radius:2px; outline:none;" onchange="calculateProductShipping()">
								<option value="Casablanca-Settat">Casablanca-Settat (Hub-A1)</option>
								<option value="Rabat-Salé-Kénitra">Rabat-Salé-Kénitra</option>
								<option value="Marrakech-Safi">Marrakech-Safi</option>
								<option value="Tanger-Tétouan-Al Hoceïma">Tanger-Tétouan</option>
								<option value="Fès-Meknès">Fès-Meknès</option>
								<option value="Souss-Massa">Souss-Massa (Agadir)</option>
								<option value="Oriental">Oriental (Oujda)</option>
								<option value="Laâyoune-Sakia El Hamra">Laâyoune</option>
							</select>
							<div id="shippingQuoteResult" style="font-family:'Space Mono'; font-size:12px; font-weight:700; color:#22c55e;">
								CTM: 35.00 MAD &bull; 24h Transit
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<!-- Product Full Description & Fiche Technique Tabs -->
		<div class="manifest-panel" style="padding:28px; margin-bottom:40px;">
			<div style="margin-bottom:20px; border-bottom:1px solid rgba(142,162,191,0.18); padding-bottom:12px;">
				<h3 style="font-family:'Fraunces', serif; font-size:22px; font-weight:700; color:#f2efe6; margin:0 0 4px 0; letter-spacing:-0.02em;">
					Fiche Technique &amp; Specifications
				</h3>
				<p style="font-family:'Space Mono'; font-size:11px; color:#8ea2bf; margin:0;">
					[VERIFIED HARDWARE LEDGER DATA &bull; CASABLANCA REPO]
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