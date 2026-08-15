<?php
session_start();
include('include/config.php');

if (empty($_SESSION['alogin'])) {	
    header('location:index.php');
    exit();
} else {
	
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

    $sql = db_query("INSERT INTO products(category, subCategory, productName, productCompany, productPrice, productDescription, shippingCharge, productAvailability, productImage1, productImage2, productImage3, productPriceBeforeDiscount, specifications, productModel, ficheTechnique) VALUES(?, ?, ?, ?, ?, ?, ?, ?, '', '', '', ?, ?, ?, ?)", 
        [$category, $subcat, $productname, $productcompany, $productprice, $productdescription, $productscharge, $productavailability, $productpricebd, $fichetechnique, $productmodel, $fichetechnique],
        "iisssisisisss"
    );

    if ($sql) {
        $productid = db_insert_id();
        $targetDir = __DIR__ . "/productimages/$productid";

        $img1 = "";
        $img2 = "";
        $img3 = "";

        if (isset($_FILES["productimage1"]) && $_FILES["productimage1"]['error'] === UPLOAD_ERR_OK) {
            $res1 = validate_and_upload_image($_FILES["productimage1"], $targetDir);
            if ($res1['success']) {
                $img1 = $res1['filename'];
            }
        }

        if (isset($_FILES["productimage2"]) && $_FILES["productimage2"]['error'] === UPLOAD_ERR_OK) {
            $res2 = validate_and_upload_image($_FILES["productimage2"], $targetDir);
            if ($res2['success']) {
                $img2 = $res2['filename'];
            }
        }

        if (isset($_FILES["productimage3"]) && $_FILES["productimage3"]['error'] === UPLOAD_ERR_OK) {
            $res3 = validate_and_upload_image($_FILES["productimage3"], $targetDir);
            if ($res3['success']) {
                $img3 = $res3['filename'];
            }
        }

        db_query("UPDATE products SET productImage1=?, productImage2=?, productImage3=? WHERE id=?", [$img1, $img2, $img3, $productid], "sssi");
        $_SESSION['msg'] = "Product Inserted Successfully !!";
    } else {
        $_SESSION['delmsg'] = "Error inserting product. Please check input values.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Admin | Insert Product</title>
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
								<h3>Insert Product</h3>
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
									<strong>Oh snap!</strong> <?php echo e($_SESSION['delmsg']); $_SESSION['delmsg'] = ""; ?>
									</div>
<?php } ?>

									<br />

			<form class="form-horizontal row-fluid" name="insertproduct" method="post" enctype="multipart/form-data">

<div class="control-group">
<label class="control-label" for="category">Category</label>
<div class="controls">
<select name="category" id="category" class="span8 tip" onChange="getSubcat(this.value);" required>
<option value="">Select Category</option> 
<?php 
$categories = db_fetch_all("SELECT * FROM category ORDER BY categoryName ASC");
foreach ($categories as $row) {
?>
<option value="<?php echo e($row['id']);?>"><?php echo e($row['categoryName']);?></option>
<?php } ?>
</select>
</div>
</div>

<div class="control-group">
<label class="control-label" for="subcategory">Sub Category</label>
<div class="controls">
<select name="subcategory" id="subcategory" class="span8 tip" required>
<option value="">Select Subcategory</option>
</select>
</div>
</div>

<div class="control-group">
<label class="control-label" for="productName">Product Name</label>
<div class="controls">
<input type="text" name="productName" id="productName" placeholder="Enter Product Name" class="span8 tip" required>
</div>
</div>

<div class="control-group">
<label class="control-label" for="productCompany">Product Company</label>
<div class="controls">
<input type="text" name="productCompany" id="productCompany" placeholder="Enter Product Company Name" class="span8 tip" required>
</div>
</div>

<div class="control-group">
<label class="control-label" for="productpricebd">Product Price Before Discount</label>
<div class="controls">
<input type="number" name="productpricebd" id="productpricebd" placeholder="Enter Product Price" class="span8 tip" required>
</div>
</div>

<div class="control-group">
<label class="control-label" for="productprice">Product Price After Discount (Selling Price)</label>
<div class="controls">
<input type="number" name="productprice" id="productprice" placeholder="Enter Product Selling Price" class="span8 tip" required>
</div>
</div>

<div class="control-group">
<label class="control-label" for="productDescription">Product Description</label>
<div class="controls">
<textarea name="productDescription" id="productDescription" placeholder="Enter Product Description" rows="4" class="span8 tip"></textarea>  
</div>
</div>

<div class="control-group">
<label class="control-label" for="productModel">3D Model Preset / GLB Path</label>
<div class="controls">
<select name="productModel" id="productModel" class="span8 tip">
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
<textarea name="ficheTechnique" id="ficheTechnique" placeholder='{"Brand": "Apple", "Display": "6.7 inch OLED", "Processor": "A17 Pro", "Battery": "4422 mAh"}' rows="5" class="span8 tip"></textarea>  
</div>
</div>

<div class="control-group">
<label class="control-label" for="productShippingcharge">Product Shipping Charge</label>
<div class="controls">
<input type="number" name="productShippingcharge" id="productShippingcharge" placeholder="Enter Product Shipping Charge" class="span8 tip" required>
</div>
</div>

<div class="control-group">
<label class="control-label" for="productAvailability">Product Availability</label>
<div class="controls">
<select name="productAvailability" id="productAvailability" class="span8 tip" required>
<option value="In Stock">In Stock</option>
<option value="Out of Stock">Out of Stock</option>
</select>
</div>
</div>

<div class="control-group">
<label class="control-label" for="productimage1">Product Image 1</label>
<div class="controls">
<input type="file" name="productimage1" id="productimage1" class="span8 tip" required accept="image/*">
</div>
</div>

<div class="control-group">
<label class="control-label" for="productimage2">Product Image 2</label>
<div class="controls">
<input type="file" name="productimage2" id="productimage2" class="span8 tip" required accept="image/*">
</div>
</div>

<div class="control-group">
<label class="control-label" for="productimage3">Product Image 3</label>
<div class="controls">
<input type="file" name="productimage3" id="productimage3" class="span8 tip" accept="image/*">
</div>
</div>

	<div class="control-group">
		<div class="controls">
			<button type="submit" name="submit" class="btn btn-primary">Insert Product</button>
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