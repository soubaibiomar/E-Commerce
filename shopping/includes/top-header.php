<?php
$currentCurr = get_current_currency();
$currencies = get_currency_list();
?>
<div class="top-bar">
	<div class="container">
		<div class="header-top-inner" style="display:flex; justify-content:space-between; align-items:center;">
			<div class="cnt-account">
				<ul class="list-unstyled list-inline" style="margin-bottom:0; display:flex; align-items:center; gap:12px; font-family:'Space Mono', monospace; font-size:11px;">
					<?php if(!empty($_SESSION['username'])) { ?>
					<li>
						<span style="display:inline-flex; align-items:center; gap:6px; color:#d9b567; font-weight:700; padding:2px 8px; background:rgba(199,154,68,0.12); border:1px solid rgba(199,154,68,0.3); border-radius:2px;">
							[USER: <?php echo strtoupper(e($_SESSION['username']));?>]
						</span>
					</li>
					<?php } ?>
					<li><a href="my-account.php">MY ACCOUNT</a></li>
					<li><a href="my-wishlist.php">WISHLIST</a></li>
					<li><a href="my-cart.php">CART</a></li>
					<li><a href="track-orders.php">TRACK ORDER</a></li>
					<?php if(empty($_SESSION['id'])) { ?>
					<li><a href="login.php">SIGN IN</a></li>
					<li><a href="signup.php">REGISTER</a></li>
					<?php } else { ?>
					<li><a href="logout.php">LOGOUT</a></li>
					<?php } ?>
				</ul>
			</div>

			<div class="cnt-block">
				<ul class="list-unstyled list-inline" style="margin-bottom:0; display:flex; align-items:center; gap:12px;">
					<!-- Global Region & Currency Selector -->
					<li class="dropdown currency-selector-dropdown">
						<a href="#" class="dropdown-toggle currency-badge-btn" data-toggle="dropdown">
							<span><?php echo $currentCurr['flag']; ?> <?php echo $currentCurr['code']; ?> (<?php echo $currentCurr['symbol']; ?>)</span>
							<i class="fa fa-chevron-down" style="font-size:9px; margin-left:4px;"></i>
						</a>
						<div class="dropdown-menu dropdown-menu-right currency-dropdown-menu" style="width:310px; padding:0; overflow:hidden; border-radius:2px; background:#0c1526; border:1px solid rgba(142,162,191,0.25); box-shadow:0 12px 24px rgba(0,0,0,0.5);">
							<div style="padding:12px 14px; background:#111d33; border-bottom:1px solid rgba(142,162,191,0.18);">
								<div style="font-family:'Space Mono', monospace; font-size:10px; font-weight:700; text-transform:uppercase; color:#8ea2bf; letter-spacing:0.08em; margin-bottom:8px; display:flex; justify-content:space-between;">
									<span>CURRENCIES (<?php echo count($currencies); ?>)</span>
									<span style="color:#d9b567;">LIVE SETTLEMENT</span>
								</div>
								<div style="position:relative;">
									<input type="text" id="currencyQuickSearch" placeholder="Search code or currency..." onkeyup="filterCurrencies(this.value)" style="width:100%; padding:6px 10px 6px 28px; font-size:12px; font-family:'IBM Plex Sans'; border-radius:2px; border:1px solid rgba(142,162,191,0.25); background:#080e1a; color:#f2efe6; outline:none;" onclick="event.stopPropagation();">
									<i class="fa fa-search" style="position:absolute; left:9px; top:8px; font-size:11px; color:#8ea2bf;"></i>
								</div>
							</div>

							<ul id="currencyListUl" class="list-unstyled" style="max-height:280px; overflow-y:auto; margin:0; padding:4px 0;">
								<?php foreach($currencies as $c) { 
									$isActive = ($c['code'] === $currentCurr['code']);
									$query = $_GET;
									$query['currency'] = $c['code'];
									$currUrl = '?' . http_build_query($query);
									$searchKey = strtolower($c['code'] . ' ' . $c['name'] . ' ' . ($c['country'] ?? ''));
								?>
								<li class="currency-item" data-search="<?php echo e($searchKey); ?>">
									<a href="<?php echo e($currUrl); ?>" class="<?php echo $isActive ? 'active' : ''; ?>" style="display:flex; justify-content:space-between; align-items:center; padding:8px 14px; font-size:12px; font-family:'Space Mono'; text-decoration:none; color:<?php echo $isActive ? '#d9b567' : '#8ea2bf'; ?>; background:<?php echo $isActive ? 'rgba(199,154,68,0.1)' : 'transparent'; ?>; transition:background 0.15s ease;">
										<span style="display:flex; align-items:center; gap:8px;">
											<span><?php echo $c['flag']; ?></span>
											<strong style="color:<?php echo $isActive ? '#d9b567' : '#f2efe6'; ?>;"><?php echo $c['code']; ?></strong>
											<span style="font-family:'IBM Plex Sans'; font-size:11px; color:#5e7391;"><?php echo e($c['name']); ?></span>
										</span>
										<span style="color:#d9b567; font-weight:700;"><?php echo $c['symbol']; ?></span>
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
function filterCurrencies(q) {
	q = q.toLowerCase().trim();
	var items = document.querySelectorAll('#currencyListUl .currency-item');
	items.forEach(function(el) {
		var s = el.getAttribute('data-search') || '';
		el.style.display = (s.indexOf(q) !== -1) ? 'block' : 'none';
	});
}
</script>