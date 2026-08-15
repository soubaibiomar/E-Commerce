<?php
$currentCurr = get_current_currency();
$currencies = get_currency_list();
?>
<div class="top-bar">
	<div class="container">
		<div class="header-top-inner" style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">
			<div class="cnt-account">
				<ul class="list-unstyled list-inline" style="margin-bottom:0; display:flex; align-items:center; gap:16px; font-size:12px;">
					<?php if(!empty($_SESSION['username'])) { ?>
					<li>
						<span style="display:inline-flex; align-items:center; gap:6px; color:#d9b45d; font-weight:600; padding:2px 10px; background:rgba(197,155,67,0.12); border:1px solid rgba(197,155,67,0.25); border-radius:4px;">
							<i class="fa fa-user-circle"></i> <?php echo htmlspecialchars(e($_SESSION['username']));?>
						</span>
					</li>
					<?php } ?>
					<li><a href="my-account.php"><i class="fa fa-user-o" style="margin-right:4px;"></i> My Account</a></li>
					<li><a href="my-wishlist.php"><i class="fa fa-heart-o" style="margin-right:4px;"></i> Wishlist</a></li>
					<li><a href="my-cart.php"><i class="fa fa-shopping-bag" style="margin-right:4px;"></i> Cart</a></li>
					<li><a href="track-orders.php"><i class="fa fa-truck" style="margin-right:4px;"></i> Track Order</a></li>
					<?php if(empty($_SESSION['id'])) { ?>
					<li><a href="login.php" style="color:#d9b45d; font-weight:600;">Sign In</a></li>
					<li><a href="signup.php">Register</a></li>
					<?php } else { ?>
					<li><a href="logout.php"><i class="fa fa-sign-out" style="margin-right:4px;"></i> Logout</a></li>
					<?php } ?>
				</ul>
			</div>

			<div class="cnt-block">
				<ul class="list-unstyled list-inline" style="margin-bottom:0; display:flex; align-items:center; gap:12px;">
					<!-- Currency Selector Dropdown -->
					<li class="dropdown currency-selector-dropdown">
						<a href="#" class="dropdown-toggle currency-badge-btn" data-toggle="dropdown">
							<span><?php echo $currentCurr['flag']; ?> <?php echo $currentCurr['code']; ?> (<?php echo $currentCurr['symbol']; ?>)</span>
							<i class="fa fa-chevron-down" style="font-size:10px; margin-left:4px; opacity:0.7;"></i>
						</a>
						<div class="dropdown-menu dropdown-menu-right currency-dropdown-menu" style="width:300px; padding:0; overflow:hidden; border-radius:6px; background:#121e36; border:1px solid rgba(226,232,240,0.15); box-shadow:0 12px 32px rgba(0,0,0,0.5);">
							<div style="padding:12px 14px; background:#182847; border-bottom:1px solid rgba(226,232,240,0.10);">
								<div style="font-size:11px; font-weight:600; text-transform:uppercase; color:#94a3b8; letter-spacing:0.05em; margin-bottom:8px; display:flex; justify-content:space-between;">
									<span>Supported Currencies</span>
									<span style="color:#d9b45d;">Live Exchange</span>
								</div>
								<div style="position:relative;">
									<input type="text" id="currencyQuickSearch" placeholder="Search currency or country..." onkeyup="filterCurrencies(this.value)" style="width:100%; padding:6px 10px 6px 28px; font-size:12px; font-family:'IBM Plex Sans'; border-radius:4px; border:1px solid rgba(226,232,240,0.15); background:#0b162c; color:#f8fafc; outline:none;" onclick="event.stopPropagation();">
									<i class="fa fa-search" style="position:absolute; left:9px; top:8px; font-size:11px; color:#94a3b8;"></i>
								</div>
							</div>

							<ul id="currencyListUl" class="list-unstyled" style="max-height:260px; overflow-y:auto; margin:0; padding:4px 0;">
								<?php foreach($currencies as $c) { 
									$isActive = ($c['code'] === $currentCurr['code']);
									$query = $_GET;
									$query['currency'] = $c['code'];
									$currUrl = '?' . http_build_query($query);
									$searchKey = strtolower($c['code'] . ' ' . $c['name'] . ' ' . ($c['country'] ?? ''));
								?>
								<li class="currency-item" data-search="<?php echo e($searchKey); ?>">
									<a href="<?php echo e($currUrl); ?>" class="<?php echo $isActive ? 'active' : ''; ?>" style="display:flex; justify-content:space-between; align-items:center; padding:8px 14px; font-size:12px; text-decoration:none; color:<?php echo $isActive ? '#d9b45d' : '#94a3b8'; ?>; background:<?php echo $isActive ? 'rgba(197,155,67,0.12)' : 'transparent'; ?>; transition:background 0.15s ease;">
										<span style="display:flex; align-items:center; gap:8px;">
											<span style="font-size:14px;"><?php echo $c['flag']; ?></span>
											<strong style="color:<?php echo $isActive ? '#ffffff' : '#f8fafc'; ?>;"><?php echo $c['code']; ?></strong>
											<span style="color:#64748b; font-size:11px;">(<?php echo $c['symbol']; ?>)</span>
										</span>
										<span style="font-size:11px; color:<?php echo $isActive ? '#d9b45d' : '#64748b'; ?>;">
											<?php if($c['code'] === 'MAD') { echo 'Base (1.00)'; } else { echo '~' . round($c['rate_to_mad'], 2) . ' MAD'; } ?>
										</span>
									</a>
								</li>
								<?php } ?>
							</ul>
						</div>
					</li>
				</ul>
			</div>
		</div>
	</div>
</div>
<script>
function filterCurrencies(query) {
	query = (query || '').toLowerCase().trim();
	var items = document.querySelectorAll('#currencyListUl .currency-item');
	items.forEach(function(el) {
		var search = el.getAttribute('data-search') || '';
		if(!query || search.indexOf(query) !== -1) {
			el.style.display = 'block';
		} else {
			el.style.display = 'none';
		}
	});
}
</script>