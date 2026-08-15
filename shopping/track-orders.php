<?php
session_start();
error_reporting(0);
include('includes/config.php');

$searchTr = trim($_GET['tr'] ?? ($_POST['trackingNumber'] ?? ($_POST['orderid'] ?? '')));
$shipment = null;
$order = null;

if (!empty($searchTr)) {
    // Check by tracking number first
    $shipment = db_fetch_one("SELECT * FROM shipping_shipments WHERE tracking_number = ?", [$searchTr], "s");
    
    // If not found, check by order ID
    if (!$shipment && is_numeric($searchTr)) {
        $shipment = db_fetch_one("SELECT * FROM shipping_shipments WHERE order_id = ? ORDER BY id DESC LIMIT 1", [intval($searchTr)], "i");
    }

    if ($shipment) {
        $order = db_fetch_one("SELECT * FROM orders WHERE id = ?", [$shipment['order_id']], "i");
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Track Your Shipment — ZeyTech Casablanca Logistics</title>
    <!-- Authentic ZeyTech Typography (Fraunces, IBM Plex Sans, Space Mono) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,400;0,9..144,600;0,9..144,700;0,9..144,800;1,9..144,400&family=IBM+Plex+Sans:ital,wght@0,400;0,500;0,600;0,700;1,400&family=Space+Mono:ital,wght@0,400;0,700;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/font-awesome.min.css">
    <link rel="stylesheet" href="assets/css/modern-storefront.css">
    <style>
        .tracking-card {
            background: #0c1526;
            border-radius: 2px;
            border: 1px solid rgba(142, 162, 191, 0.18);
            padding: 32px;
            margin-top: 24px;
            margin-bottom: 40px;
        }

        .timeline-steps {
            display: flex;
            justify-content: space-between;
            position: relative;
            margin: 40px 0 30px 0;
        }

        .timeline-steps::before {
            content: "";
            position: absolute;
            top: 18px;
            left: 40px;
            right: 40px;
            height: 2px;
            background: rgba(142, 162, 191, 0.18);
            z-index: 1;
        }

        .timeline-step {
            position: relative;
            z-index: 2;
            text-align: center;
            flex: 1;
        }

        .step-icon {
            width: 38px;
            height: 38px;
            border-radius: 2px;
            background: #111d33;
            color: #8ea2bf;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            margin: 0 auto 10px auto;
            border: 1px solid rgba(142, 162, 191, 0.25);
            transition: all 0.2s ease;
        }

        .step-completed .step-icon {
            background: #c79a44;
            color: #080e1a;
            border-color: #d9b567;
            font-weight: 700;
        }

        .step-active .step-icon {
            background: #111d33;
            color: #d9b567;
            border-color: #c79a44;
            box-shadow: 0 0 0 3px rgba(199, 154, 68, 0.25);
        }

        .step-label {
            font-size: 12px;
            font-weight: 700;
            font-family: 'Space Mono', monospace;
            color: #f2efe6;
            text-transform: uppercase;
        }

        .step-sub {
            font-size: 11px;
            color: #8ea2bf;
            margin-top: 2px;
        }

        @media (max-width: 768px) {
            .tracking-card {
                padding: 20px 16px;
            }
            .timeline-steps {
                flex-direction: column;
                align-items: flex-start;
                gap: 16px;
                margin: 24px 0;
                padding-left: 10px;
            }
            .timeline-steps::before {
                top: 20px;
                bottom: 20px;
                left: 28px;
                right: auto;
                width: 2px;
                height: calc(100% - 40px);
            }
            .timeline-step {
                display: flex;
                align-items: center;
                gap: 14px;
                text-align: left;
                width: 100%;
            }
            .step-icon {
                margin: 0;
                flex-shrink: 0;
            }
        }
    </style>
</head>
<body class="cnt-home" style="background:#080e1a; color:#f2efe6;">

<header class="header-style-1">
    <?php include('includes/top-header.php');?>
    <?php include('includes/main-header.php');?>
    <?php include('includes/menu-bar.php');?>
</header>

<div class="body-content outer-top-bd" style="padding-top:24px; padding-bottom:60px;">
    <div class="container">
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                
                <!-- Search Box (Manifest Ledger Search) -->
                <div style="background:#0c1526; border-radius:2px; border:1px solid rgba(142,162,191,0.18); padding:32px;">
                    <div style="font-family:'Space Mono', monospace; font-size:11px; color:#c79a44; font-weight:700; text-transform:uppercase; letter-spacing:0.08em; margin-bottom:6px;">
                        [LOGISTICS.WAYBILL_DISPATCH]
                    </div>
                    <h2 style="font-family:'Fraunces', serif; font-size:24px; font-weight:700; margin:0 0 8px 0; color:#f2efe6; letter-spacing:-0.02em;">
                        Domestic Transit &amp; Parcel Checkpoints
                    </h2>
                    <p style="color:#8ea2bf; font-size:13px; margin-bottom:20px;">
                        Enter carrier tracking number (e.g. <span style="font-family:'Space Mono'; color:#d9b567;">CTM-MA-8849102</span>) or Order ID.
                    </p>

                    <form method="GET" action="track-orders.php" style="display:flex; gap:10px; flex-wrap:wrap;">
                        <input type="text" name="tr" value="<?php echo htmlspecialchars($searchTr); ?>" placeholder="Enter Tracking # or Order ID..." style="height:44px; border-radius:2px; font-size:13px; font-family:'Space Mono'; background:#080e1a; border:1px solid rgba(142,162,191,0.25); color:#f2efe6; padding:0 14px; flex:1; min-width:260px;" required>
                        <button type="submit" class="btn-primary" style="height:44px; padding:0 24px; font-family:'Space Mono'; font-size:12px;">
                            TRACK PARCEL &rarr;
                        </button>
                    </form>
                </div>

                <?php if (!empty($searchTr)): ?>
                    <?php if ($shipment): ?>
                        <!-- Shipment Result Card -->
                        <div class="tracking-card">
                            <div style="display:flex; justify-content:space-between; align-items:flex-start; border-bottom:1px solid rgba(142,162,191,0.18); padding-bottom:20px; flex-wrap:wrap; gap:12px;">
                                <div>
                                    <span style="font-size:10px; font-weight:700; font-family:'Space Mono'; text-transform:uppercase; letter-spacing:0.08em; color:#8ea2bf;">CARRIER WAYBILL</span>
                                    <div style="font-size:22px; font-weight:700; font-family:'Space Mono'; color:#d9b567; margin-top:2px;"><?php echo htmlspecialchars($shipment['tracking_number']); ?></div>
                                    <div style="font-size:12px; color:#8ea2bf; font-family:'Space Mono'; margin-top:4px;">
                                        CARRIER: <?php echo htmlspecialchars($shipment['carrier']); ?> &bull; CASABLANCA HUB-A1
                                    </div>
                                </div>
                                <div style="text-align:right;">
                                    <span class="tag-pill tag-gold" style="font-size:12px; padding:4px 10px;">
                                        [STATUS: <?php echo htmlspecialchars($shipment['status']); ?>]
                                    </span>
                                    <div style="font-size:11px; font-family:'Space Mono'; color:#8ea2bf; margin-top:8px;">
                                        EST. ARRIVAL: <strong style="color:#f2efe6;"><?php echo htmlspecialchars($shipment['estimated_delivery']); ?></strong>
                                    </div>
                                </div>
                            </div>

                            <!-- Stepper Timeline -->
                            <?php
                            $status = $shipment['status'];
                            $step1 = true;
                            $step2 = in_array($status, ['IN_TRANSIT', 'OUT_FOR_DELIVERY', 'DELIVERED']);
                            $step3 = in_array($status, ['IN_TRANSIT', 'OUT_FOR_DELIVERY', 'DELIVERED']);
                            $step4 = in_array($status, ['OUT_FOR_DELIVERY', 'DELIVERED']);
                            $step5 = ($status === 'DELIVERED');
                            ?>
                            <div class="timeline-steps">
                                <div class="timeline-step step-completed">
                                    <div class="step-icon">01</div>
                                    <div class="step-label">Verified</div>
                                    <div class="step-sub">Payment Settled</div>
                                </div>
                                <div class="timeline-step <?php echo $step2 ? 'step-completed' : ''; ?>">
                                    <div class="step-icon">02</div>
                                    <div class="step-label">Packed</div>
                                    <div class="step-sub">Hub-A1 Casablanca</div>
                                </div>
                                <div class="timeline-step <?php echo $step5 ? 'step-completed' : ($step3 ? 'step-active' : ''); ?>">
                                    <div class="step-icon">03</div>
                                    <div class="step-label">In Transit</div>
                                    <div class="step-sub"><?php echo htmlspecialchars($shipment['carrier']); ?></div>
                                </div>
                                <div class="timeline-step <?php echo $step5 ? 'step-completed' : ($step4 ? 'step-active' : ''); ?>">
                                    <div class="step-icon">04</div>
                                    <div class="step-label">Out Delivery</div>
                                    <div class="step-sub"><?php echo htmlspecialchars($shipment['city']); ?></div>
                                </div>
                                <div class="timeline-step <?php echo $step5 ? 'step-completed' : ''; ?>">
                                    <div class="step-icon">05</div>
                                    <div class="step-label">Delivered</div>
                                    <div class="step-sub"><?php echo $step5 ? 'Signed' : 'Pending'; ?></div>
                                </div>
                            </div>

                            <!-- Destination & Details Ledger Grid -->
                            <div style="background:#111d33; border:1px solid rgba(142,162,191,0.15); border-radius:2px; padding:20px; display:grid; grid-template-columns:repeat(auto-fit, minmax(200px, 1fr)); gap:16px;">
                                <div>
                                    <div style="font-size:10px; font-family:'Space Mono'; color:#8ea2bf; font-weight:700; text-transform:uppercase;">RECIPIENT</div>
                                    <div style="font-size:14px; font-weight:600; color:#f2efe6; margin-top:2px;"><?php echo htmlspecialchars($shipment['recipient_name']); ?></div>
                                    <div style="font-size:11px; font-family:'Space Mono'; color:#8ea2bf;"><?php echo htmlspecialchars($shipment['recipient_phone']); ?></div>
                                </div>
                                <div>
                                    <div style="font-size:10px; font-family:'Space Mono'; color:#8ea2bf; font-weight:700; text-transform:uppercase;">DESTINATION REGION</div>
                                    <div style="font-size:14px; font-weight:600; color:#f2efe6; margin-top:2px;"><?php echo htmlspecialchars($shipment['city']); ?></div>
                                    <div style="font-size:11px; font-family:'Space Mono'; color:#8ea2bf;"><?php echo htmlspecialchars($shipment['region']); ?></div>
                                </div>
                                <div>
                                    <div style="font-size:10px; font-family:'Space Mono'; color:#8ea2bf; font-weight:700; text-transform:uppercase;">SHIPPING TARIFF</div>
                                    <div style="font-size:14px; font-weight:700; font-family:'Space Mono'; color:#d9b567; margin-top:2px;"><?php echo number_format($shipment['shipping_cost_mad'], 2); ?> MAD</div>
                                    <div style="font-size:11px; font-family:'Space Mono'; color:#22c55e;">[EXPRESS_COURIER]</div>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <!-- No Shipment Found Alert -->
                        <div style="background:rgba(234,179,8,0.1); border:1px solid #eab308; border-radius:2px; padding:20px; margin-top:24px; color:#fde047;">
                            <h4 style="margin-top:0; font-family:'Space Mono'; font-weight:700; font-size:14px;">[ALERT: SHIPMENT NOT FOUND]</h4>
                            <p style="margin:0; font-size:13px;">No active domestic shipment found matching "<strong><?php echo htmlspecialchars($searchTr); ?></strong>". Please verify the tracking number or contact Casablanca logistics support.</p>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>

            </div>
        </div>
    </div>
</div>

<?php include('includes/footer.php');?>

<script src="assets/js/jquery-1.11.1.min.js"></script>
<script src="assets/js/bootstrap.min.js"></script>
</body>
</html>