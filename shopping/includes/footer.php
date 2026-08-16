<footer class="modern-footer">
	<div class="container">
		<div class="row">
			<!-- Brand & Mission -->
			<div class="col-xs-12 col-sm-6 col-md-4" style="margin-bottom:24px;">
				<div class="footer-brand">
					<div class="logo" style="margin-bottom:14px;">
						<a href="index.php" class="logo-brand" style="display:flex; align-items:center; gap:10px; text-decoration:none;">
							<img src="assets/images/logo.jpg" alt="ZeyTech Logo" class="brand-logo-img" style="height:36px;">
							<span style="font-family:'Fraunces', serif; font-size:20px; font-weight:700; color:#ffffff;">ZeyTech</span>
						</a>
					</div>
					<p style="font-size:13px; color:#94a3b8; line-height:1.6; max-width:320px;">
						Casablanca Regional Central Hub. Enterprise hardware, M3 workstations, 3D WebGL inspection, and autonomous commerce settlement.
					</p>
				</div>
			</div>

			<!-- Quick Categories -->
			<div class="col-xs-6 col-sm-3 col-md-2" style="margin-bottom:24px;">
				<h4>Categories</h4>
				<ul>
					<?php 
					$footCats = db_fetch_all("SELECT id, categoryName FROM category ORDER BY id ASC LIMIT 5");
					foreach($footCats as $fc) { ?>
					<li><a href="category.php?cid=<?php echo e($fc['id']);?>"><?php echo e($fc['categoryName']);?></a></li>
					<?php } ?>
				</ul>
			</div>

			<!-- Customer Care -->
			<div class="col-xs-6 col-sm-3 col-md-3" style="margin-bottom:24px;">
				<h4>Operations & Support</h4>
				<ul>
					<li><a href="zeytech-ops-console.html" target="_blank"><i class="fa fa-sliders" style="margin-right:4px;"></i> Merchant Console</a></li>
					<li><a href="zeytech-platform.php" target="_blank"><i class="fa fa-line-chart" style="margin-right:4px;"></i> System Telemetry</a></li>
					<li><a href="track-orders.php"><i class="fa fa-truck" style="margin-right:4px;"></i> Track Delivery</a></li>
					<li><a href="order-history.php"><i class="fa fa-history" style="margin-right:4px;"></i> Order History</a></li>
					<li><a href="my-wishlist.php"><i class="fa fa-heart" style="margin-right:4px;"></i> Saved Items</a></li>
				</ul>
			</div>

			<!-- Newsletter & Guarantee -->
			<div class="col-xs-12 col-sm-12 col-md-3" style="margin-bottom:24px;">
				<h4>Newsletter</h4>
				<p style="font-size:12px; color:#94a3b8; margin-bottom:12px;">Subscribe for new hardware arrivals and Casablanca inventory updates.</p>
				<form onsubmit="event.preventDefault(); alert('Subscribed to Casablanca commercial updates.');" style="display:flex; gap:6px;">
					<input type="email" placeholder="Enter your email..." required style="background:#0b162c; border:1px solid rgba(226,232,240,0.15); border-radius:4px; padding:8px 12px; color:#f8fafc; font-size:12px; font-family:'IBM Plex Sans'; width:100%; outline:none;">
					<button type="submit" class="btn-primary" style="padding:8px 14px; font-size:12px; flex-shrink:0;">Join</button>
				</form>
			</div>
		</div>

		<!-- Bottom Bar -->
		<div class="footer-bottom">
			<div>
				&copy; <?php echo date('Y');?> ZeyTech. All rights reserved. Casablanca Central Fulfillment.
			</div>
			<div style="display:flex; gap:16px;">
				<a href="index.php">Terms of Service</a>
				<a href="index.php">Privacy Policy</a>
				<a href="track-orders.php">Logistics Policy</a>
			</div>
		</div>
	</div>
</footer>
<script>
function handleImageError(img) {
    if (!img || img.dataset.hasFailed) return;
    img.dataset.hasFailed = "true";
    var alt = img.alt || "ZeyTech Product";
    var encodedName = encodeURIComponent(alt);
    img.src = 'data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="400" height="300" viewBox="0 0 400 300"><rect width="400" height="300" fill="%230b162c"/><rect x="8" y="8" width="384" height="284" rx="6" fill="%23121e36" stroke="%23c59b43" stroke-width="1.5"/><text x="50%" y="45%" fill="%23d9b45d" font-family="sans-serif" font-size="28" text-anchor="middle">⚡</text><text x="50%" y="65%" fill="%23ffffff" font-family="sans-serif" font-weight="bold" font-size="13" text-anchor="middle">' + alt.replace(/</g, "&lt;").replace(/>/g, "&gt;") + '</text></svg>';
}
</script>
<?php include('includes/ai-chat-widget.php');?>