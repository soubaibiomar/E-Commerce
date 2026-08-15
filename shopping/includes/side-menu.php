<div class="side-menu outer-bottom-xs" style="background:#0c1526; border:1px solid rgba(142,162,191,0.18); border-radius:2px; overflow:hidden;">
    <div class="head" style="background:#111d33; color:#f2efe6; font-family:'Space Mono', monospace; font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.08em; padding:12px 16px; border-bottom:1px solid rgba(142,162,191,0.18);">
        <span>[CATALOG.DOMAINS]</span>
    </div>        
    <nav class="yamm megamenu-horizontal" role="navigation">
        <ul class="nav nav-tabs" style="border:none; margin:0;">
            <?php 
            $sideCats = db_fetch_all("SELECT id, categoryName FROM category ORDER BY categoryName ASC");
            foreach ($sideCats as $row) {
            ?>
            <li class="menu-item" style="width:100%;">
                <a href="category.php?cid=<?php echo e($row['id']);?>" style="display:flex; align-items:center; justify-content:space-between; padding:11px 16px; color:#8ea2bf; font-family:'IBM Plex Sans'; font-size:13px; font-weight:500; border-bottom:1px solid rgba(142,162,191,0.08); text-decoration:none; transition:all 0.15s ease;">
                    <span style="color:#f2efe6;"><?php echo e($row['categoryName']);?></span>
                    <i class="fa fa-angle-right" style="color:#5e7391; font-size:12px;"></i>
                </a>
            </li>
            <?php } ?>
        </ul>
    </nav>
</div>