<div class="span3">
					<div class="sidebar">

<ul class="widget widget-menu unstyled">
							<li>
								<a class="collapsed" data-toggle="collapse" href="#togglePages">
									<i class="menu-icon icon-cog"></i>
									<i class="icon-chevron-down pull-right"></i><i class="icon-chevron-up pull-right"></i>
									Order Management
								</a>
								<ul id="togglePages" class="collapse unstyled">
									<li>
										<a href="todays-orders.php">
											<i class="icon-tasks"></i>
											Today's Orders
<?php
$from = date('Y-m-d') . " 00:00:00";
$to = date('Y-m-d') . " 23:59:59";
$resToday = db_fetch_one("SELECT COUNT(id) as cnt FROM orders WHERE orderDate BETWEEN ? AND ?", [$from, $to], "ss");
$num_rows1 = $resToday['cnt'] ?? 0;
?>
											<b class="label orange pull-right"><?php echo e($num_rows1); ?></b>
										</a>
									</li>
									<li>
										<a href="pending-orders.php">
											<i class="icon-tasks"></i>
											Pending Orders
<?php	
$status = 'Delivered';									 
$resPending = db_fetch_one("SELECT COUNT(id) as cnt FROM orders WHERE orderStatus != ? OR orderStatus IS NULL", [$status], "s");
$num = $resPending['cnt'] ?? 0;
?>
											<b class="label orange pull-right"><?php echo e($num); ?></b>
										</a>
									</li>
									<li>
										<a href="delivered-orders.php">
											<i class="icon-inbox"></i>
											Delivered Orders
<?php	
$resDelivered = db_fetch_one("SELECT COUNT(id) as cnt FROM orders WHERE orderStatus = ?", [$status], "s");
$num1 = $resDelivered['cnt'] ?? 0;
?>
											<b class="label green pull-right"><?php echo e($num1); ?></b>
										</a>
									</li>
								</ul>
							</li>
							
							<li>
								<a href="escalation-queue.php" style="color:#f59e0b; font-weight:700;">
									<i class="menu-icon icon-warning-sign" style="color:#f59e0b;"></i>
									AI Escalation Queue
									<b class="label orange pull-right">3</b>
								</a>
							</li>
							<li>
								<a href="manage-users.php">
									<i class="menu-icon icon-group"></i>
									Manage users
								</a>
							</li>
						</ul>

						<ul class="widget widget-menu unstyled">
                                <li><a href="category.php"><i class="menu-icon icon-tasks"></i> Create Category </a></li>
                                <li><a href="subcategory.php"><i class="menu-icon icon-tasks"></i>Sub Category </a></li>
                                <li><a href="insert-product.php"><i class="menu-icon icon-paste"></i>Insert Product </a></li>
                                <li><a href="manage-products.php"><i class="menu-icon icon-table"></i>Manage Products </a></li>
                        
                            </ul>

						<ul class="widget widget-menu unstyled">
							<li><a href="user-logs.php"><i class="menu-icon icon-tasks"></i>User Login Log </a></li>
							
							<li>
								<a href="logout.php">
									<i class="menu-icon icon-signout"></i>
									Logout
								</a>
							</li>
						</ul>

					</div>
				</div>
