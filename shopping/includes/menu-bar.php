<div class="header-nav">
    <div class="container">
        <div class="navbar navbar-default" role="navigation" style="border:none; margin:0; background:transparent;">
            <div class="navbar-header">
                <button data-target="#mc-horizontal-menu-collapse" data-toggle="collapse" class="navbar-toggle collapsed" type="button" style="background:#182847; border:1px solid rgba(226,232,240,0.15); border-radius:4px;">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar" style="background:#c59b43;"></span>
                    <span class="icon-bar" style="background:#c59b43;"></span>
                    <span class="icon-bar" style="background:#c59b43;"></span>
                </button>
            </div>
            <div class="navbar-collapse collapse" id="mc-horizontal-menu-collapse" style="padding:0;">
				<ul class="nav navbar-nav" style="display:flex; align-items:center; flex-wrap:wrap; width:100%;">
					<li class="<?php echo (basename($_SERVER['PHP_SELF']) == 'index.php') ? 'active' : ''; ?>">
						<a href="index.php"><i class="fa fa-home" style="margin-right:4px;"></i> Home</a>
					</li>
					<?php 
					$activeCid = intval($_GET['cid'] ?? 0);
					$menuCats = db_fetch_all("SELECT id, categoryName FROM category ORDER BY id ASC LIMIT 6");
					foreach ($menuCats as $row) {
						$isCatActive = ($activeCid === intval($row['id']));
					?>
					<li class="<?php echo $isCatActive ? 'active' : ''; ?>">
						<a href="category.php?cid=<?php echo e($row['id']);?>"><?php echo e($row['categoryName']);?></a>
					</li>
					<?php } ?>
					
					<!-- Quick Links -->
					<li style="margin-left:auto; display:flex; align-items:center; gap:8px;">
						<a href="zeytech-ops-console.html" class="nav-action-pill" target="_blank">
							<i class="fa fa-sliders"></i> Operations
						</a>
						<a href="zeytech-platform.php" class="nav-action-pill" target="_blank" style="background:rgba(226,232,240,0.06); border-color:rgba(226,232,240,0.15); color:#94a3b8 !important;">
							<i class="fa fa-line-chart"></i> Telemetry
						</a>
					</li>
				</ul>
			</div>
        </div>
    </div>
</div>