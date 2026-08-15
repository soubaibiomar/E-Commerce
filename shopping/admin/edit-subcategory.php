<?php
session_start();
include('include/config.php');

if (empty($_SESSION['alogin'])) {	
    header('location:index.php');
    exit();
} else {
	$id = intval($_GET['id'] ?? 0);
	$currentTime = date('d-m-Y h:i:s A');

	if (isset($_POST['submit'])) {
		$category = intval($_POST['category'] ?? 0);
		$subcat = trim($_POST['subcategory'] ?? '');
		db_query("UPDATE subcategory SET categoryid=?, subcategory=?, updationDate=? WHERE id=?", [$category, $subcat, $currentTime, $id], "issi");
		$_SESSION['msg'] = "Sub-Category Updated !!";
	}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Admin | Edit SubCategory</title>
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
								<h3>Edit SubCategory</h3>
							</div>
							<div class="module-body">

<?php if (isset($_SESSION['msg']) && !empty($_SESSION['msg'])) { ?>
									<div class="alert alert-success">
										<button type="button" class="close" data-dismiss="alert">×</button>
									<strong>Well done!</strong> <?php echo e($_SESSION['msg']); $_SESSION['msg'] = ""; ?>
									</div>
<?php } ?>

									<br />

			<form class="form-horizontal row-fluid" name="Category" method="post" >
<?php
$sub = db_fetch_one("SELECT subcategory.id, category.id as catid, category.categoryName, subcategory.subcategory FROM subcategory JOIN category ON category.id=subcategory.categoryid WHERE subcategory.id=?", [$id], "i");
if ($sub) {
?>		

<div class="control-group">
<label class="control-label" for="category">Category</label>
<div class="controls">
<select name="category" id="category" class="span8 tip" required>
<option value="<?php echo e($sub['catid']);?>"><?php echo e($sub['categoryName']);?></option>
<?php 
$allCategories = db_fetch_all("SELECT id, categoryName FROM category WHERE id != ? ORDER BY categoryName ASC", [$sub['catid']], "i");
foreach ($allCategories as $catRow) {
?>
<option value="<?php echo e($catRow['id']);?>"><?php echo e($catRow['categoryName']);?></option>
<?php } ?>
</select>
</div>
</div>

<div class="control-group">
<label class="control-label" for="subcategory">SubCategory Name</label>
<div class="controls">
<input type="text" placeholder="Enter SubCategory Name" id="subcategory" name="subcategory" value="<?php echo e($sub['subcategory']);?>" class="span8 tip" required>
</div>
</div>

<?php } ?>	

	<div class="control-group">
		<div class="controls">
			<button type="submit" name="submit" class="btn btn-primary">Update SubCategory</button>
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