<?php
session_start();
include('include/config.php');

if (empty($_SESSION['alogin'])) {	
    header('location:index.php');
    exit();
} else {

if (isset($_GET['del'])) {
    $delId = intval($_GET['id'] ?? 0);
    if ($delId > 0) {
        db_query("DELETE FROM products WHERE id = ?", [$delId], "i");
        $_SESSION['delmsg'] = "Product deleted !!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Admin | Manage Products</title>
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
								<h3>Manage Products</h3>
							</div>
							<div class="module-body table">
<?php if (isset($_SESSION['delmsg']) && !empty($_SESSION['delmsg'])) { ?>
									<div class="alert alert-error">
										<button type="button" class="close" data-dismiss="alert">×</button>
									<strong>Done!</strong> <?php echo e($_SESSION['delmsg']); $_SESSION['delmsg'] = ""; ?>
									</div>
<?php } ?>

									<br />

								<table cellpadding="0" cellspacing="0" border="0" class="datatable-1 table table-bordered table-striped display" width="100%">
									<thead>
										<tr>
											<th>#</th>
											<th>Product Name</th>
											<th>Category</th>
											<th>Subcategory</th>
											<th>Company Name</th>
											<th>Product Creation Date</th>
											<th>Action</th>
										</tr>
									</thead>
									<tbody>

<?php 
$products = db_fetch_all("SELECT products.*, category.categoryName, subcategory.subcategory FROM products JOIN category ON category.id=products.category JOIN subcategory ON subcategory.id=products.subCategory ORDER BY products.id DESC");
$cnt = 1;
foreach ($products as $row) {
?>									
										<tr>
											<td><?php echo e($cnt);?></td>
											<td><?php echo e($row['productName']);?></td>
											<td><?php echo e($row['categoryName']);?></td>
											<td><?php echo e($row['subcategory']);?></td>
											<td><?php echo e($row['productCompany']);?></td>
											<td><?php echo e($row['postingDate']);?></td>
											<td>
											<a href="edit-products.php?id=<?php echo e($row['id']);?>" ><i class="icon-edit"></i></a>
											<a href="manage-products.php?id=<?php echo e($row['id']);?>&del=delete" onClick="return confirm('Are you sure you want to delete?')"><i class="icon-remove-sign"></i></a></td>
										</tr>
<?php 
    $cnt++; 
} 
?>
								</table>
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
	<script src="scripts/flot/jquery.flot.js" type="text/javascript"></script>
	<script src="scripts/datatables/jquery.dataTables.js"></script>
	<script>
		$(document).ready(function() {
			$('.datatable-1').dataTable();
			$('.dataTables_paginate').addClass("btn-group datatable-pagination");
			$('.dataTables_paginate > a').wrapInner('<span />');
			$('.dataTables_paginate > a:first-child').append('<i class="icon-chevron-left shaded"></i>');
			$('.dataTables_paginate > a:last-child').append('<i class="icon-chevron-right shaded"></i>');
		} );
	</script>
</body>
</html>
<?php } ?>