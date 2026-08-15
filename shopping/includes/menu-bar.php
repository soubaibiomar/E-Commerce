<div class="header-nav">
    <div class="container">
        <div class="navbar navbar-default" role="navigation" style="border:none; margin:0;">
            <div class="navbar-header">
                <button data-target="#mc-horizontal-menu-collapse" data-toggle="collapse" class="navbar-toggle collapsed" type="button" style="background:#111d33; border:1px solid rgba(142,162,191,0.25); border-radius:2px;">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar" style="background:#c79a44;"></span>
                    <span class="icon-bar" style="background:#c79a44;"></span>
                    <span class="icon-bar" style="background:#c79a44;"></span>
                </button>
            </div>
            <div class="navbar-collapse collapse" id="mc-horizontal-menu-collapse" style="padding:0;">
				<ul class="nav navbar-nav" style="display:flex; align-items:center; flex-wrap:wrap; width:100%;">
					<li class="<?php echo (basename($_SERVER['PHP_SELF']) == 'index.php') ? 'active' : ''; ?>">
						<a href="index.php">Catalog Home</a>
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
					
					<!-- Enterprise Console & Telemetry Quick Links -->
					<li style="margin-left:auto;">
						<a href="zeytech-ops-console.html" class="admin-link-pill" target="_blank" style="display:inline-flex; align-items:center; gap:6px;">
							[OPS.CONSOLE]
						</a>
					</li>
					<li>
						<a href="zeytech-platform.php" target="_blank" style="color:#8ea2bf; font-family:'Space Mono'; font-size:11px; padding:12px 14px;">
							[TELEMETRY]
						</a>
					</li>
				</ul>
			</div>
        </div>
    </div>
</div>