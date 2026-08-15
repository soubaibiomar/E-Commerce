<?php
session_start();
include('include/config.php');

if (empty($_SESSION['alogin'])) {	
    header('location:index.php');
    exit();
} else {
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Admin | Users log</title>
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
								<h3>User Login Logs</h3>
							</div>
							<div class="module-body table">
								<table cellpadding="0" cellspacing="0" border="0" class="datatable-1 table table-bordered table-striped display" width="100%">
									<thead>
										<tr>
											<th>#</th>
											<th>User Email</th>
											<th>User IP</th>
											<th>Login Time</th>
											<th>Logout Time</th>
											<th>Status</th>
										</tr>
									</thead>
									<tbody>

<?php 
$logs = db_fetch_all("SELECT * FROM userlog ORDER BY id DESC");
$cnt = 1;
foreach ($logs as $row) {
    $ip = trim($row['userip'] ?? '');
?>									
										<tr>
											<td><?php echo e($cnt);?></td>
											<td><?php echo e($row['userEmail']);?></td>
											<td><?php echo e($ip);?></td>
											<td><?php echo e($row['loginTime']);?></td>
											<td><?php echo e($row['logout']); ?></td>
											<td><?php echo ($row['status'] == 1) ? '<span class="label label-success">Successful</span>' : '<span class="label label-important">Failed</span>'; ?></td>
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