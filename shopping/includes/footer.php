<footer class="modern-footer">
	<div class="container">
		<div class="row">
			<!-- Brand & Mission -->
			<div class="col-xs-12 col-sm-6 col-md-4" style="margin-bottom:24px;">
				<div class="footer-brand">
					<div class="logo" style="margin-bottom:14px;">
						<a href="index.php" class="logo-brand" style="display:flex; align-items:center; gap:10px; text-decoration:none;">
							<span class="hexagram-mark" style="width:24px; height:24px;">
								<svg class="hexagram-svg" viewBox="0 0 24 24">
									<polygon points="12,2 22,18 2,18" stroke="#c79a44" fill="none" stroke-width="1.5"/>
									<polygon points="12,22 22,6 2,6" stroke="#d9b567" fill="none" stroke-width="1.5"/>
								</svg>
							</span>
							<span style="font-family:'Fraunces', serif; font-size:20px; font-weight:700; color:#f2efe6;">ZeyTech</span>
						</a>
					</div>
					<p style="font-size:13px; color:#8ea2bf; line-height:1.6; max-width:320px;">
						Casablanca Regional Central Hub-A1. Enterprise hardware, M3 workstations, 3D WebGL inspection, and autonomous multi-agent settlement.
					</p>
				</div>
			</div>

			<!-- Quick Categories -->
			<div class="col-xs-6 col-sm-3 col-md-2" style="margin-bottom:24px;">
				<h4>CATALOG</h4>
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
				<h4>MANAGEMENT</h4>
				<ul>
					<li><a href="zeytech-ops-console.html" target="_blank">[OPS.CONSOLE]</a></li>
					<li><a href="zeytech-platform.php" target="_blank">[TELEMETRY]</a></li>
					<li><a href="track-orders.php">Waybill Tracking</a></li>
					<li><a href="order-history.php">Settled Orders</a></li>
					<li><a href="my-wishlist.php">Saved Manifest</a></li>
				</ul>
			</div>

			<!-- Newsletter & Guarantee -->
			<div class="col-xs-12 col-sm-12 col-md-3" style="margin-bottom:24px;">
				<h4>COMMERCIAL DISPATCH</h4>
				<p style="font-size:12px; color:#8ea2bf; margin-bottom:12px;">Subscribe for Casablanca inventory restock announcements.</p>
				<form onsubmit="event.preventDefault(); alert('Subscribed to Casablanca commercial dispatch.');" style="display:flex; gap:6px;">
					<input type="email" placeholder="work.email@domain.com" required style="background:#080e1a; border:1px solid rgba(142,162,191,0.25); border-radius:2px; padding:8px 12px; color:#f2efe6; font-size:12px; font-family:'Space Mono'; width:100%; outline:none;">
					<button type="submit" class="btn-primary" style="padding:8px 14px; font-size:11px; flex-shrink:0;">JOIN</button>
				</form>
			</div>
		</div>

		<!-- Bottom Bar -->
		<div class="footer-bottom">
			<div>
				&copy; <?php echo date('Y'); ?> ZEYTECH COMMERCIAL OS &bull; CASABLANCA HUB-A1
			</div>
			<div style="display:flex; gap:8px; font-family:'Space Mono'; font-size:10px; color:#8ea2bf;">
				<span style="border:1px solid rgba(142,162,191,0.2); padding:2px 6px; border-radius:2px;">[CMI.GATEWAY]</span>
				<span style="border:1px solid rgba(142,162,191,0.2); padding:2px 6px; border-radius:2px;">[CTM.LOGISTICS]</span>
				<span style="border:1px solid rgba(142,162,191,0.2); padding:2px 6px; border-radius:2px;">[AMANA.EXPRESS]</span>
				<span style="border:1px solid rgba(142,162,191,0.2); padding:2px 6px; border-radius:2px;">[HMAC.SHA256]</span>
			</div>
		</div>
	</div>
</footer>

<!-- Include AI Shopping Advisor Chatbot Widget -->
<?php include(__DIR__ . '/ai-chat-widget.php'); ?>