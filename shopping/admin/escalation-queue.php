<?php
session_start();
include('include/config.php');

if (empty($_SESSION['alogin'])) {
    header('location:index.php');
    exit();
}

$msg = '';
$err = '';

// Handle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    check_csrf();
    $actionType = $_POST['action'];
    $caseId = intval($_POST['case_id'] ?? 0);

    if ($actionType === 'RESOLVE') {
        $msg = "Escalation Case #$caseId has been marked as RESOLVED by Administrator (" . e($_SESSION['alogin']) . ").";
    } elseif ($actionType === 'APPROVE_REFUND') {
        $msg = "High-Value Refund for Case #$caseId has been APPROVED by Human Supervisor.";
    } elseif ($actionType === 'TAKEOVER') {
        $msg = "Human Support Agent has taken over live chat for Case #$caseId.";
    }
}

// Sample and dynamic escalation cases
$escalations = [
    [
        'id' => 101,
        'channel' => 'WHATSAPP',
        'customer' => 'Karim Alami',
        'contact' => '+212612345678',
        'query' => 'سلام عليكم، شريت MacBook Pro ولكن تعطل عليا الإرسال لمدينة طنجة وما وصلنيش',
        'reason' => 'Delivery Delay Inquiry & High Cart Value (24,999 MAD)',
        'riskLevel' => 'HIGH',
        'status' => 'PENDING',
        'timestamp' => '10 mins ago',
        'orderNo' => 'ORD-2026-8812'
    ],
    [
        'id' => 102,
        'channel' => 'TELEGRAM',
        'customer' => 'Yassine B.',
        'contact' => '@yassine_tech',
        'query' => 'Demande de remboursement de 5,400 MAD pour commande endommagée',
        'reason' => 'Refund Request > 5,000 MAD threshold (HITL Required)',
        'riskLevel' => 'HIGH',
        'status' => 'PENDING',
        'timestamp' => '25 mins ago',
        'orderNo' => 'ORD-2026-8805'
    ],
    [
        'id' => 103,
        'channel' => 'WEB_STOREFRONT',
        'customer' => 'Sara Mansouri',
        'contact' => 'sara@gmail.com',
        'query' => 'واش كاين كود برومو للطلبيات الكبيرة؟ بغيت نشري 3 هواتف Samsung',
        'reason' => 'VIP Bulk Discount Assistance (ZEYTECH10VIP applied)',
        'riskLevel' => 'LOW',
        'status' => 'IN_REVIEW',
        'timestamp' => '1 hour ago',
        'orderNo' => 'ORD-2026-8799'
    ]
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>ZeyTech Admin | AI Support Escalation Queue</title>
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
							<div class="module-head" style="display:flex; justify-content:space-between; align-items:center; background:#1e293b; color:#ffffff; padding:14px 20px;">
								<h3 style="margin:0; color:#ffffff; font-size:16px;">
									<i class="icon-warning-sign" style="color:#f59e0b; margin-right:8px;"></i>
									Human Support Escalation Queue (HITL)
								</h3>
								<span class="badge badge-warning" style="font-size:12px; padding:4px 10px;">
									<?php echo count($escalations); ?> Active Cases
								</span>
							</div>
							<div class="module-body" style="padding:20px;">

								<?php if (!empty($msg)) { ?>
									<div class="alert alert-success">
										<button type="button" class="close" data-dismiss="alert">×</button>
										<strong>Success!</strong> <?php echo e($msg); ?>
									</div>
								<?php } ?>

								<p style="color:#64748b; font-size:13px; margin-bottom:20px;">
									Conversations escalated by the <strong>ZeyTech AI Supervisor</strong> requiring human review, high-value financial approval (&gt; 5,000 MAD), or manual chat intervention.
								</p>

								<table class="table table-striped table-bordered" style="font-size:13px;">
									<thead>
										<tr style="background:#f8fafc;">
											<th>Case #</th>
											<th>Channel</th>
											<th>Customer</th>
											<th>Original Query & Reason</th>
											<th>Risk</th>
											<th>Actions</th>
										</tr>
									</thead>
									<tbody>
									<?php foreach ($escalations as $case) { 
										$channelBadge = 'badge-info';
										if ($case['channel'] === 'WHATSAPP') $channelBadge = 'badge-success';
										if ($case['channel'] === 'TELEGRAM') $channelBadge = 'badge-info';
										if ($case['channel'] === 'WEB_STOREFRONT') $channelBadge = 'badge-inverse';

										$riskBadge = ($case['riskLevel'] === 'HIGH') ? 'badge-important' : 'badge-warning';
									?>
										<tr>
											<td><strong>#<?php echo e($case['id']); ?></strong><br><small style="color:#94a3b8;"><?php echo e($case['orderNo']); ?></small></td>
											<td><span class="badge <?php echo $channelBadge; ?>"><?php echo e($case['channel']); ?></span></td>
											<td>
												<strong><?php echo e($case['customer']); ?></strong><br>
												<small style="color:#64748b;"><?php echo e($case['contact']); ?></small>
											</td>
											<td>
												<div style="background:#f1f5f9; padding:8px 10px; border-radius:6px; margin-bottom:4px; font-style:italic;">
													"<?php echo e($case['query']); ?>"
												</div>
												<small style="color:#475569;"><strong>Trigger:</strong> <?php echo e($case['reason']); ?></small>
											</td>
											<td><span class="badge <?php echo $riskBadge; ?>"><?php echo e($case['riskLevel']); ?></span></td>
											<td style="white-space:nowrap;">
												<form method="POST" style="margin:0; display:inline-flex; gap:4px;">
													<?php csrf_field(); ?>
													<input type="hidden" name="case_id" value="<?php echo e($case['id']); ?>">
													
													<?php if ($case['riskLevel'] === 'HIGH') { ?>
													<button type="submit" name="action" value="APPROVE_REFUND" class="btn btn-mini btn-success" title="Approve Refund">
														<i class="icon-ok"></i> Approve
													</button>
													<?php } ?>
													
													<button type="submit" name="action" value="TAKEOVER" class="btn btn-mini btn-primary" title="Takeover Live Chat">
														<i class="icon-comment"></i> Chat
													</button>

													<button type="submit" name="action" value="RESOLVE" class="btn btn-mini btn-inverse" title="Mark Resolved">
														<i class="icon-check"></i> Close
													</button>
												</form>
											</td>
										</tr>
									<?php } ?>
									</tbody>
								</table>

							</div>
						</div>

					</div>
				</div>
			</div>
		</div>
	</div>

<?php include('include/footer.php');?>

	<script src="scripts/jquery-1.9.1.min.js" type="text/javascript"></script>
	<script src="scripts/jquery-ui-1.10.1.custom.min.js" type="text/javascript"></script>
	<script src="bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
</body>
</html>
