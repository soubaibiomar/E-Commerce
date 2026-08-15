<?php
session_start();
include('include/config.php');

if (empty($_SESSION['alogin'])) {	
    header('location:index.php');
    exit();
} else {
	$pid = intval($_GET['id'] ?? 0);

	if (isset($_POST['submit'])) {
		$category = intval($_POST['category'] ?? 0);
		$subcat = intval($_POST['subcategory'] ?? 0);
		$productname = trim($_POST['productName'] ?? '');
		$productcompany = trim($_POST['productCompany'] ?? '');
		$productprice = intval($_POST['productprice'] ?? 0);
		$productpricebd = intval($_POST['productpricebd'] ?? 0);
		$productdescription = trim($_POST['productDescription'] ?? '');
		$productscharge = intval($_POST['productShippingcharge'] ?? 0);
		$productavailability = trim($_POST['productAvailability'] ?? 'In Stock');
		$productmodel = trim($_POST['productModel'] ?? 'smartphone');
		$fichetechnique = trim($_POST['ficheTechnique'] ?? '');
		$currentTime = date('d-m-Y h:i:s A');
		
		db_query("UPDATE products SET category=?, subCategory=?, productName=?, productCompany=?, productPrice=?, productDescription=?, shippingCharge=?, productAvailability=?, productPriceBeforeDiscount=?, specifications=?, productModel=?, ficheTechnique=?, updationDate=? WHERE id=?", 
			[$category, $subcat, $productname, $productcompany, $productprice, $productdescription, $productscharge, $productavailability, $productpricebd, $fichetechnique, $productmodel, $fichetechnique, $currentTime, $pid],
			"iisssisisssssi"
		);
		$_SESSION['msg'] = "Product Updated Successfully !!";
	}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Admin | Edit Product</title>
	<link type="text/css" href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
	<link type="text/css" href="bootstrap/css/bootstrap-responsive.min.css" rel="stylesheet">
	<link type="text/css" href="css/theme.css" rel="stylesheet">
	<link type="text/css" href="images/icons/css/font-awesome.css" rel="stylesheet">
	<link type="text/css" href='https://fonts.googleapis.com/css?family=Open+Sans:400italic,600italic,400,600' rel='stylesheet'>
<script src="scripts/jquery-1.9.1.min.js" type="text/javascript"></script>
<script>
function getSubcat(val) {
	$.ajax({
	type: "POST",
	url: "get_subcat.php",
	data: 'cat_id=' + val,
	success: function(data){
		$("#subcategory").html(data);
	}
	});
}
</script>	
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
								<h3>Edit Product</h3>
							</div>
							<div class="module-body">

<?php if (isset($_SESSION['msg']) && !empty($_SESSION['msg'])) { ?>
									<div class="alert alert-success">
										<button type="button" class="close" data-dismiss="alert">×</button>
									<strong>Well done!</strong> <?php echo e($_SESSION['msg']); $_SESSION['msg'] = ""; ?>
									</div>
<?php } ?>

									<br />

			<form class="form-horizontal row-fluid" name="insertproduct" method="post" enctype="multipart/form-data">

<?php 
$product = db_fetch_one("SELECT products.*, category.categoryName as catname, category.id as cid, subcategory.subcategory as subcatname, subcategory.id as subcatid FROM products JOIN category ON category.id=products.category JOIN subcategory ON subcategory.id=products.subCategory WHERE products.id=?", [$pid], "i");
if ($product) {
?>

<div class="control-group">
<label class="control-label" for="category">Category</label>
<div class="controls">
<select name="category" id="category" class="span8 tip" onChange="getSubcat(this.value);" required>
<option value="<?php echo e($product['cid']);?>"><?php echo e($product['catname']);?></option> 
<?php 
$allCategories = db_fetch_all("SELECT id, categoryName FROM category WHERE id != ? ORDER BY categoryName ASC", [$product['cid']], "i");
foreach ($allCategories as $rw) {
?>
<option value="<?php echo e($rw['id']);?>"><?php echo e($rw['categoryName']);?></option>
<?php } ?>
</select>
</div>
</div>

<div class="control-group">
<label class="control-label" for="subcategory">Sub Category</label>
<div class="controls">
<select name="subcategory" id="subcategory" class="span8 tip" required>
<option value="<?php echo e($product['subcatid']);?>"><?php echo e($product['subcatname']);?></option>
</select>
</div>
</div>

<div class="control-group">
<label class="control-label" for="productName">Product Name</label>
<div class="controls">
<input type="text" name="productName" id="productName" placeholder="Enter Product Name" value="<?php echo e($product['productName']);?>" class="span8 tip" required>
</div>
</div>

<div class="control-group">
<label class="control-label" for="productCompany">Product Company</label>
<div class="controls">
<input type="text" name="productCompany" id="productCompany" placeholder="Enter Product Company Name" value="<?php echo e($product['productCompany']);?>" class="span8 tip" required>
</div>
</div>

<div class="control-group">
<label class="control-label" for="productpricebd">Product Price Before Discount</label>
<div class="controls">
<input type="number" name="productpricebd" id="productpricebd" placeholder="Enter Product Price" value="<?php echo e($product['productPriceBeforeDiscount']);?>" class="span8 tip" required>
</div>
</div>

<div class="control-group">
<label class="control-label" for="productprice">Product Price (Selling Price)</label>
<div class="controls">
<input type="number" name="productprice" id="productprice" placeholder="Enter Product Price" value="<?php echo e($product['productPrice']);?>" class="span8 tip" required>
</div>
</div>

<div class="control-group">
<label class="control-label" for="productDescription">Product Description</label>
<div class="controls">
<textarea name="productDescription" id="productDescription" placeholder="Enter Product Description" rows="4" class="span8 tip"><?php echo e($product['productDescription']);?></textarea>  
</div>
</div>

<div class="control-group">
<label class="control-label" for="productModel">3D Model Preset / GLB Path</label>
<div class="controls">
<select name="productModel" id="productModel" class="span8 tip">
<option value="<?php echo e($product['productModel'] ?? 'smartphone');?>"><?php echo e(ucfirst($product['productModel'] ?? 'smartphone'));?> (Current)</option>
<option value="smartphone">Smartphone 3D Studio</option>
<option value="laptop">Laptop / Notebook 3D Studio</option>
<option value="headphone">Headphones / Audio 3D Studio</option>
<option value="watch">Smartwatch 3D Studio</option>
<option value="console">Gaming Console 3D Studio</option>
<option value="chair">Ergonomic Furniture / Chair 3D Studio</option>
<option value="book">Hardcover Book 3D Studio</option>
</select>
</div>
</div>

<div class="control-group">
<label class="control-label" for="ficheTechnique">Fiche Technique (JSON Specs)</label>
<div class="controls">
<textarea name="ficheTechnique" id="ficheTechnique" placeholder='{"Brand": "Apple", "Display": "6.7 inch OLED", "Processor": "A17 Pro", "Battery": "4422 mAh"}' rows="5" class="span8 tip"><?php echo e($product['specifications'] ?? $product['ficheTechnique'] ?? '');?></textarea>  
</div>
</div>

<div class="control-group">
<label class="control-label" for="productShippingcharge">Product Shipping Charge</label>
<div class="controls">
<input type="number" name="productShippingcharge" id="productShippingcharge" placeholder="Enter Product Shipping Charge" value="<?php echo e($product['shippingCharge']);?>" class="span8 tip" required>
</div>
</div>

<div class="control-group">
<label class="control-label" for="productAvailability">Product Availability</label>
<div class="controls">
<select name="productAvailability" id="productAvailability" class="span8 tip" required>
<option value="<?php echo e($product['productAvailability']);?>"><?php echo e($product['productAvailability']);?></option>
<option value="In Stock">In Stock</option>
<option value="Out of Stock">Out of Stock</option>
</select>
</div>
</div>

<div class="control-group">
<label class="control-label">Product Image 1</label>
<div class="controls">
<img src="productimages/<?php echo e($pid);?>/<?php echo e($product['productImage1']);?>" width="200" height="100"> <a href="update-image1.php?id=<?php echo e($product['id']);?>">Change Image</a>
</div>
</div>

<div class="control-group">
<label class="control-label">Product Image 2</label>
<div class="controls">
<img src="productimages/<?php echo e($pid);?>/<?php echo e($product['productImage2']);?>" width="200" height="100"> <a href="update-image2.php?id=<?php echo e($product['id']);?>">Change Image</a>
</div>
</div>

<div class="control-group">
<label class="control-label">Product Image 3</label>
<div class="controls">
<img src="productimages/<?php echo e($pid);?>/<?php echo e($product['productImage3']);?>" width="200" height="100"> <a href="update-image3.php?id=<?php echo e($product['id']);?>">Change Image</a>
</div>
</div>
<?php } ?>

	<div class="control-group">
		<div class="controls">
			<button type="submit" name="submit" class="btn btn-primary">Update Product</button>
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