<?php
session_start();
include('include/config.php');

if (empty($_SESSION['alogin'])) {	
    header('location:index.php');
    exit();
} else {
	$pid = intval($_GET['id'] ?? 0);
	if (isset($_POST['submit'])) {
        $targetDir = __DIR__ . "/productimages/$pid";
        if (isset($_FILES["productimage2"]) && $_FILES["productimage2"]['error'] === UPLOAD_ERR_OK) {
            $uploadRes = validate_and_upload_image($_FILES["productimage2"], $targetDir);
            if ($uploadRes['success']) {
                $imgName = $uploadRes['filename'];
                db_query("UPDATE products SET productImage2=? WHERE id=?", [$imgName, $pid], "si");
                $_SESSION['msg'] = "Product Image 2 Updated Successfully !!";
            } else {
                $_SESSION['delmsg'] = "Image Upload Error: " . $uploadRes['error'];
            }
        }
	}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Admin | Update Product Image 2</title>
	<link type="text/css" href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
	<link type="text/css" href="bootstrap/css/bootstrap-responsive.min.css" rel="stylesheet">
	<link type="text/css" href="css/theme.css" rel="stylesheet">
	<link type="text/css" href="images/icons/css/font-awesome.css" rel="stylesheet">
	<link type="text/css" href='https://fonts.googleapis.com/css?family=Open+Sans:400italic,600italic,400,600' rel='stylesheet'>
</head>
<body>
<?php include('include/header.php');?>

	<div class="wrapper">
		<div class="container">
			<div class="row">
<?php include('include/sidebar.php');?>				
			<div class="span9">
					<div class="content">

						<div class="module">
							<div class="module-head">
								<h3>Update Product Image 2</h3>
							</div>
							<div class="module-body">

<?php if (isset($_SESSION['msg']) && !empty($_SESSION['msg'])) { ?>
									<div class="alert alert-success">
										<button type="button" class="close" data-dismiss="alert">×</button>
									<strong>Well done!</strong> <?php echo e($_SESSION['msg']); $_SESSION['msg'] = ""; ?>
									</div>
<?php } ?>

<?php if (isset($_SESSION['delmsg']) && !empty($_SESSION['delmsg'])) { ?>
									<div class="alert alert-error">
										<button type="button" class="close" data-dismiss="alert">×</button>
									<strong>Error!</strong> <?php echo e($_SESSION['delmsg']); $_SESSION['delmsg'] = ""; ?>
									</div>
<?php } ?>

									<br />

			<form class="form-horizontal row-fluid" name="insertproduct" method="post" enctype="multipart/form-data">

<?php 
$product = db_fetch_one("SELECT productName, productImage2 FROM products WHERE id=?", [$pid], "i");
if ($product) {
?>

<div class="control-group">
<label class="control-label" for="productName">Product Name</label>
<div class="controls">
<input type="text" name="productName" id="productName" readonly value="<?php echo e($product['productName']);?>" class="span8 tip">
</div>
</div>

<div class="control-group">
<label class="control-label">Current Product Image 2</label>
<div class="controls">
<img src="productimages/<?php echo e($pid);?>/<?php echo e($product['productImage2']);?>" width="200" height="100"> 
</div>
</div>

<div class="control-group">
<label class="control-label" for="productimage2">New Product Image 2</label>
<div class="controls">
<input type="file" name="productimage2" id="productimage2" class="span8 tip" required accept="image/*">
</div>
</div>

<?php } ?>

	<div class="control-group">
		<div class="controls">
			<button type="submit" name="submit" class="btn btn-primary">Update Image</button>
		</div>
	</div>
</form>
							</div>
						</div>
					</div><!--/.content-->
				</div><!--/.span9-->
			</div>
		</div><!--/.container-->
	</div><!--/.wrapper-->

<?php include('include/footer.php');?>

	<script src="scripts/jquery-1.9.1.min.js" type="text/javascript"></script>
	<script src="scripts/jquery-ui-1.10.1.custom.min.js" type="text/javascript"></script>
	<script src="bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
</body>
</html>
<?php } ?>