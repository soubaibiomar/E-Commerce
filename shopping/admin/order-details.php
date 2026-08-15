<?php
session_start();
include('include/config.php');

if (empty($_SESSION['alogin'])) {	
    header('location:index.php');
    exit();
} else {
    $orderid = intval($_GET['oid'] ?? 0);
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Admin | Order Details</title>
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
								<h3>Order Details #<?php echo e($orderid);?></h3>
							</div>
							<div class="module-body table">
								<br />

					<div class="table-responsive">		
			<table cellpadding="0" cellspacing="0" border="0" class="datatable-1 table table-bordered table-striped display table-responsive" >
<tbody>
<?php 
$orderDetails = db_fetch_all("SELECT orders.id as oid, users.name as username, users.email as useremail, users.contactno as usercontact, users.shippingAddress as shippingaddress, users.shippingCity as shippingcity, users.shippingState as shippingstate, users.shippingPincode as shippingpincode, products.productName as productname, products.shippingCharge as shippingcharge, orders.quantity as quantity, orders.orderDate as orderdate, products.productPrice as productprice, billingAddress, billingState, billingCity, billingPincode, products.id as pid, productImage1 FROM orders JOIN users ON orders.userId=users.id JOIN products ON products.id=orders.productId WHERE orders.id=?", [$orderid], "i");

foreach ($orderDetails as $row) {
?>										
										<tr>
											<th>Order Id</th>
											<td><?php echo e($row['oid']);?></td>
											<th>Order Date</th>
											<td><?php echo e($row['orderdate']);?></td>
										</tr>
										<tr>
											<th>Username</th>
											<td><?php echo e($row['username']);?></td>
											<th>User Contact Details</th>
											<td><?php echo e($row['useremail']);?> / <?php echo e($row['usercontact']);?></td>
										</tr>
										<tr>
										<th>User Shipping Details</th>
											<td><?php echo e($row['shippingaddress'] . ", " . $row['shippingcity'] . ", " . $row['shippingstate'] . " - " . $row['shippingpincode']);?></td>
										<th>User Billing Details</th>
											<td><?php echo e($row['billingAddress'] . ", " . $row['billingCity'] . ", " . $row['billingState'] . " - " . $row['billingPincode']);?></td>
										</tr>
										<tr>
											<th>Product Name</th>
											<td><?php echo e($row['productname']);?></td>
											<th>Product Image</th>
											<td><img src="productimages/<?php echo e($row['pid']);?>/<?php echo e($row['productImage1']);?>" width="100"></td>
										</tr>
										<tr>
											<th>Product Quantity</th>
											<td><?php echo e($row['quantity']);?></td>
											<th>Product Price</th>
											<td><?php echo e($row['productprice']);?></td>
										</tr>
										<tr>
											<th>Shipping Charge</th>
											<td><?php echo e($row['shippingcharge']);?></td>
											<th>Grand Total</th>
											<td><?php echo e($row['quantity'] * $row['productprice'] + $row['shippingcharge']);?></td>
										</tr>
<?php } ?>
										</tbody>
								</table>

<?php 
$tracks = db_fetch_all("SELECT * FROM ordertrackhistory WHERE orderId=? ORDER BY id ASC", [$orderid], "i");
if (!empty($tracks)) {
?>
			<table cellpadding="0" cellspacing="0" border="0" class="table table-bordered table-striped" style="margin-top:1%;" >
	<tr>
		<th colspan="3" style="color:blue; font-size:16px; text-align:center;">Order History</th>
	</tr>
	<tr>
		<th>Remark</th>
		<th>Status</th>
		<th>Date</th>
	</tr>
<?php foreach ($tracks as $track) { ?>
      <tr>
      <td><?php echo e($track['remark']);?></td>
      <td><?php echo e($track['status']);?></td>
      <td><?php echo e($track['postingDate']);?></td>
    </tr>
<?php } ?>
</table>
<?php } ?>

<table class="table" style="margin-top: 10px;">
	<tr>
		<td>
			<a href="updateorder.php?oid=<?php echo e($orderid);?>" title="Update order" target="_blank" class="btn btn-primary">Take Action</a>
		</td>
	</tr>
</table>

							</div>
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