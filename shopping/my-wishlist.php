<?php
session_start();
include('includes/config.php');

if (empty($_SESSION['id'])) {   
    header('location:login.php');
    exit();
} else {
    $uid = intval($_SESSION['id']);

    // Code for Product deletion from wishlist (with IDOR protection)
    if (isset($_GET['del'])) {
        $wid = intval($_GET['del']);
        db_query("DELETE FROM wishlist WHERE id=? AND userId=?", [$wid, $uid], "ii");
    }

    if (isset($_GET['action']) && $_GET['action'] == "add") {
        $id = intval($_GET['id'] ?? 0);
        db_query("DELETE FROM wishlist WHERE productId=? AND userId=?", [$id, $uid], "ii");
        if (isset($_SESSION['cart'][$id])) {
            $_SESSION['cart'][$id]['quantity']++;
        } else {
            $product = db_fetch_one("SELECT id, productPrice FROM products WHERE id=?", [$id], "i");
            if ($product) {
                $_SESSION['cart'][$product['id']] = array("quantity" => 1, "price" => $product['productPrice']);
                header('location:my-wishlist.php');
                exit();
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
	    <title>Saved Manifest | ZeyTech</title>
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
					[CUSTOMER.SAVED_MANIFEST]
				</div>
				<h2 style="font-family:'Fraunces', serif; font-size:24px; font-weight:700; color:#f2efe6; margin:4px 0 0 0; letter-spacing:-0.02em;">Saved Wishlist Manifest</h2>
			</div>
			<div style="font-family:'Space Mono'; font-size:11px; color:#8ea2bf;">
				RATES: <strong style="color:#d9b567;"><?php echo get_current_currency()['code']; ?></strong>
			</div>
		</div>

		<div class="manifest-panel" style="padding:0; overflow:hidden;">
			<div class="table-responsive">
				<table class="enterprise-table">
					<thead>
						<tr>
							<th style="width:70px;">Item</th>
							<th>Details</th>
							<th>Price</th>
							<th style="width:160px;">Action</th>
							<th style="width:40px;"></th>
						</tr>
					</thead>
					<tbody>
					<?php
					$wishlistItems = db_fetch_all("SELECT products.productName as pname, products.productImage1 as pimage, products.productPrice as pprice, products.productAvailability as pavail, wishlist.productId as pid, wishlist.id as wid FROM wishlist JOIN products ON products.id=wishlist.productId WHERE wishlist.userId=?", [$uid], "i");

					if (!empty($wishlistItems)) {
					    foreach ($wishlistItems as $row) {
					        $pd = $row['pid'];
					        $price = floatval($row['pprice']);
					?>
						<tr>
							<td>
								<a href="product-details.php?pid=<?php echo e($row['pid']);?>">
									<img src="<?php echo e(get_product_image_url(['id' => $row['pid'], 'productImage1' => $row['pimage']])); ?>" style="width:50px; height:50px; object-fit:contain; border-radius:2px; background:#080e1a; border:1px solid rgba(142,162,191,0.2);">
								</a>
							</td>
							<td>
								<div style="font-family:'IBM Plex Sans'; font-size:13px; font-weight:600; color:#f2efe6; margin-bottom:2px;">
									<a href="product-details.php?pid=<?php echo e($row['pid']);?>" style="color:#f2efe6; text-decoration:none;"><?php echo e($row['pname']);?></a>
								</div>
								<div>
									<?php if ($row['pavail'] == 'In Stock') { ?>
									<span class="tag-pill tag-success" style="font-size:10px;">[IN STOCK]</span>
									<?php } else { ?>
									<span class="tag-pill tag-danger" style="font-size:10px;">[OUT OF STOCK]</span>
									<?php } ?>
								</div>
							</td>
							<td style="font-family:'Space Mono'; font-weight:700; font-size:14px; color:#d9b567;">
								<?php echo format_price($price); ?>
							</td>
							<td>
								<?php if ($row['pavail'] == 'In Stock') { ?>
								<a href="my-wishlist.php?action=add&id=<?php echo e($row['pid']); ?>" class="btn-add-cart-modern">
									+ MOVE TO BAG
								</a>
								<?php } ?>
							</td>
							<td>
								<a href="my-wishlist.php?del=<?php echo e($row['wid']);?>" onClick="return confirm('Remove item?')" style="color:#ef4444; font-size:13px;" title="Remove"><i class="fa fa-trash-o"></i></a>
							</td>
						</tr>
					<?php 
					    } 
					} else { 
					?>
						<tr>
							<td colspan="5" style="text-align:center; padding:60px 20px; font-family:'Space Mono'; color:#8ea2bf;">
								No items currently saved on your wishlist manifest.
							</td>
						</tr>
					<?php } ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>

<?php include('includes/footer.php');?>
<script src="assets/js/jquery-1.11.1.min.js"></script>
<script src="assets/js/bootstrap.min.js"></script>
</body>
</html>
<?php } ?>