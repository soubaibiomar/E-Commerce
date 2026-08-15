<div class="side-menu outer-bottom-xs" style="background:#121e36; border:1px solid rgba(226,232,240,0.12); border-radius:6px; overflow:hidden;">
    <div class="head" style="background:#182847; color:#ffffff; font-size:13px; font-weight:600; padding:14px 18px; border-bottom:1px solid rgba(226,232,240,0.10); display:flex; align-items:center; gap:8px;">
        <i class="fa fa-th-large" style="color:#d9b45d;"></i>
        <span>Categories</span>
    </div>        
    <nav class="yamm megamenu-horizontal" role="navigation">
        <ul class="nav nav-tabs" style="border:none; margin:0;">
            <?php 
            $sideCats = db_fetch_all("SELECT id, categoryName FROM category ORDER BY categoryName ASC");
            foreach ($sideCats as $row) {
            ?>
            <li class="menu-item" style="width:100%;">
                <a href="category.php?cid=<?php echo e($row['id']);?>" style="display:flex; align-items:center; justify-content:space-between; padding:12px 18px; color:#94a3b8; font-size:13px; font-weight:500; border-bottom:1px solid rgba(226,232,240,0.06); text-decoration:none; transition:all 0.15s ease;">
                    <span style="color:#f8fafc;"><?php echo e($row['categoryName']);?></span>
                    <i class="fa fa-angle-right" style="color:#64748b; font-size:12px;"></i>
                </a>
            </li>
            <?php } ?>
        </ul>
    </nav>
</div>