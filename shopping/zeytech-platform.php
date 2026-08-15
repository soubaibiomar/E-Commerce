<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ZeyTech — Platform Analytics & Multi-Agent Telemetry</title>
    <!-- Harmonized ZeyTech Typography (Fraunces, IBM Plex Sans, Space Mono) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,400;0,9..144,600;0,9..144,700;1,9..144,400&family=IBM+Plex+Sans:ital,wght@0,400;0,500;0,600;0,700;1,400&family=Space+Mono:ital,wght@0,400;0,700;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/font-awesome.min.css">
    <link rel="stylesheet" href="assets/css/modern-storefront.css">
    <style>
        .metrics-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px;
            margin-bottom: 24px;
        }
        .metric-card {
            background: #121e36;
            border: 1px solid rgba(226, 232, 240, 0.12);
            border-radius: 8px;
            padding: 20px;
            box-shadow: var(--shadow-sm);
            transition: all 0.2s ease;
        }
        .metric-card:hover {
            border-color: var(--border-gold);
            transform: translateY(-2px);
        }
        .metric-title {
            font-size: 12px;
            font-weight: 600;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 8px;
        }
        .metric-value {
            font-family: var(--font-headline);
            font-size: 26px;
            font-weight: 700;
            color: #ffffff;
            line-height: 1.2;
        }
        .val-gold {
            color: #d9b45d;
        }
        .manifest-panel {
            background: #121e36;
            border: 1px solid rgba(226, 232, 240, 0.12);
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 24px;
            box-shadow: var(--shadow-sm);
        }
        .panel-title {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 15px;
            font-weight: 600;
            color: #ffffff;
            margin-bottom: 16px;
            padding-bottom: 12px;
            border-bottom: 1px solid rgba(226, 232, 240, 0.10);
        }
        .enterprise-table {
            width: 100%;
            border-collapse: collapse;
        }
        .enterprise-table th {
            background: #182847;
            color: #94a3b8;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 10px 14px;
        }
        .enterprise-table td {
            padding: 12px 14px;
            border-bottom: 1px solid rgba(226, 232, 240, 0.06);
            font-size: 13px;
        }
        .tag-pill {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
        }
        .tag-success {
            background: rgba(16, 185, 129, 0.12);
            color: #10b981;
            border: 1px solid rgba(16, 185, 129, 0.3);
        }
        .tag-gold {
            background: rgba(197, 155, 67, 0.12);
            color: #d9b45d;
            border: 1px solid rgba(197, 155, 67, 0.3);
        }
        @media (max-width: 992px) {
            .metrics-grid { grid-template-columns: repeat(2, 1fr); }
        }
        @media (max-width: 576px) {
            .metrics-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body style="background-color: #0b162c; color: #f8fafc; font-family: 'IBM Plex Sans', sans-serif;">

<header class="top-nav" style="background:#070e1c; border-bottom:1px solid rgba(226,232,240,0.12); padding:14px 28px; display:flex; justify-content:space-between; align-items:center; position:sticky; top:0; z-index:100;">
    <div style="display:flex; align-items:center; gap:12px;">
        <img src="assets/images/logo.jpg" alt="ZeyTech Logo" style="height:38px; width:auto; object-fit:contain; border-radius:6px;">
        <div>
            <span style="font-family:'Fraunces', serif; font-size:18px; font-weight:700; color:#ffffff; letter-spacing:-0.02em;">ZeyTech</span>
            <span style="font-size:12px; color:#94a3b8; margin-left:8px;">Platform Telemetry &bull; Casablanca</span>
        </div>
    </div>
    <div style="display:flex; gap:10px;">
        <a href="zeytech-ops-console.html" class="btn-ghost" style="font-size:12px; padding:6px 14px; border-radius:4px;">
            <i class="fa fa-sliders"></i> Operations Console
        </a>
        <a href="index.php" class="btn-primary" style="font-size:12px; padding:6px 14px; border-radius:4px;">
            <i class="fa fa-shopping-bag"></i> Storefront
        </a>
    </div>
</header>

<main class="workspace-container" style="max-width:1400px; margin:0 auto; padding:28px 24px 60px 24px;">
    <div id="errorAlert" style="display:none; font-size:13px; background:rgba(239,68,68,0.1); border:1px solid #ef4444; color:#fca5a5; padding:12px 16px; border-radius:4px; margin-bottom:24px;"></div>

    <!-- Section Header -->
    <div style="margin-bottom:20px; display:flex; justify-content:space-between; align-items:baseline; border-bottom:1px solid rgba(226,232,240,0.12); padding-bottom:12px;">
        <div>
            <span style="font-size:11px; color:#d9b45d; font-weight:600; text-transform:uppercase; letter-spacing:0.05em;">
                Real-Time Commerce Metrics
            </span>
            <h1 style="font-family:'Fraunces', serif; font-size:24px; font-weight:700; color:#ffffff; margin:4px 0 0 0;">
                Autonomous Business Telemetry
            </h1>
        </div>
        <div style="font-size:12px; color:#94a3b8; display:flex; align-items:center; gap:6px;">
            <span style="display:inline-block; width:8px; height:8px; background:#10b981; border-radius:50%;"></span>
            <span>Live Stream (10s sync)</span>
        </div>
    </div>

    <!-- Live KPIs -->
    <div class="metrics-grid">
        <div class="metric-card">
            <div class="metric-title">Gross Settlement (USD)</div>
            <div id="kpiRevenue" class="metric-value val-gold">---</div>
            <div id="kpiRevenueMAD" style="font-size:12px; color:#94a3b8; margin-top:6px;">Syncing regional currency...</div>
        </div>

        <div class="metric-card">
            <div class="metric-title">Orders Processed</div>
            <div id="kpiOrders" class="metric-value">---</div>
            <div style="font-size:12px; color:#10b981; margin-top:6px;"><i class="fa fa-shield"></i> 100% Verified Transactions</div>
        </div>

        <div class="metric-card">
            <div class="metric-title">Warehouse Inventory</div>
            <div id="kpiStockAvail" class="metric-value">---</div>
            <div id="kpiStockDetails" style="font-size:12px; color:#94a3b8; margin-top:6px;">Casablanca Hub-A1</div>
        </div>

        <div class="metric-card">
            <div class="metric-title">Daily AI Usage Budget</div>
            <div id="kpiLlmSpend" class="metric-value val-gold">---</div>
            <div style="font-size:12px; color:#10b981; margin-top:6px;"><i class="fa fa-check-circle"></i> Under $25.00 Daily Limit</div>
        </div>
    </div>

    <!-- 15 Agents Grid & Supervisor Decision Ledger -->
    <div class="row" style="margin-top:12px;">
        <!-- 15 Specialized Agents List -->
        <div class="col-md-7">
            <div class="manifest-panel">
                <div class="panel-title">
                    <span style="display:flex; align-items:center; gap:8px;">
                        <i class="fa fa-users" style="color:#d9b45d;"></i>
                        15 Specialized AI Agent Roster
                    </span>
                    <span class="tag-pill tag-success">● Active &amp; Online</span>
                </div>
                <div class="table-responsive">
                    <table class="enterprise-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Agent Domain</th>
                                <th>Primary Capability</th>
                                <th>Channel</th>
                                <th style="text-align: right;">Status</th>
                            </tr>
                        </thead>
                        <tbody id="agentTableBody">
                            <tr><td colspan="5" style="text-align:center; padding:30px; color:#94a3b8;">Loading agent topology...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Supervisor Decision Ledger (Audit Trail) -->
        <div class="col-md-5">
            <div class="manifest-panel">
                <div class="panel-title">
                    <span><i class="fa fa-history" style="color:#d9b45d; margin-right:6px;"></i> Decision Ledger</span>
                    <button class="btn-ghost" style="padding:4px 10px; font-size:11px; border-radius:4px;" onclick="fetchTelemetry()">
                        <i class="fa fa-refresh"></i> Refresh
                    </button>
                </div>
                <div id="auditLogList" style="max-height: 520px; overflow-y: auto; padding-right: 4px;">
                    <div style="text-align:center; padding:30px; color:#94a3b8;">Reading audit ledger...</div>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
function fetchTelemetry() {
    fetch('api-dashboard-kpis.php')
        .then(res => {
            if (!res.ok) throw new Error('HTTP ' + res.status);
            return res.json();
        })
        .then(data => {
            document.getElementById('errorAlert').style.display = 'none';

            if (data.kpis) {
                document.getElementById('kpiRevenue').textContent = '$' + Number(data.kpis.totalRevenueUSD).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
                document.getElementById('kpiRevenueMAD').textContent = '≈ ' + Number(data.kpis.totalRevenueMAD).toLocaleString(undefined, {minimumFractionDigits: 2}) + ' MAD';
                document.getElementById('kpiOrders').textContent = data.kpis.totalOrders;
                document.getElementById('kpiStockAvail').textContent = data.kpis.stockAvailable + ' Units';
                document.getElementById('kpiStockDetails').textContent = data.kpis.stockAvailable + ' Available • ' + data.kpis.stockReserved + ' In Carts • ' + data.kpis.stockSold + ' Settled';
                document.getElementById('kpiLlmSpend').textContent = '$' + Number(data.kpis.dailyLlmSpendUSD).toFixed(2);
            }

            if (data.agents) {
                var tbody = document.getElementById('agentTableBody');
                var html = '';
                data.agents.forEach(a => {
                    html += `
                        <tr>
                            <td><span style="font-family:'Space Mono'; font-size:11px; font-weight:700; color:#d9b45d;">#${String(a.id).padStart(2, '0')}</span></td>
                            <td><strong style="color:#ffffff;">${a.name}</strong></td>
                            <td style="color:#94a3b8; font-size:12px;">${a.role}</td>
                            <td><span class="tag-pill tag-gold">${a.channel}</span></td>
                            <td style="text-align: right;"><span class="tag-pill tag-success">Online</span></td>
                        </tr>
                    `;
                });
                tbody.innerHTML = html;
            }

            if (data.auditLogs) {
                var container = document.getElementById('auditLogList');
                if (data.auditLogs.length === 0) {
                    container.innerHTML = '<div style="color:#64748b; padding:24px; text-align:center;">No decision logs recorded in this session.</div>';
                    return;
                }
                var logHtml = '';
                data.auditLogs.forEach(l => {
                    logHtml += `
                        <div style="background: rgba(226,232,240,0.03); border-left: 3px solid #c59b43; padding: 12px 14px; margin-bottom: 8px; border-radius: 4px; border-top: 1px solid rgba(226,232,240,0.06); border-right: 1px solid rgba(226,232,240,0.06); border-bottom: 1px solid rgba(226,232,240,0.06);">
                            <div style="display:flex; justify-content:space-between; font-size: 11px; color: #94a3b8;">
                                <span><strong style="color:#ffffff;">${l.actor}</strong> &bull; ${l.channel}</span>
                                <span>${l.created_at ? l.created_at.slice(11, 19) : ''}</span>
                            </div>
                            <div style="font-size: 13px; color: #f8fafc; font-weight: 500; margin-top: 4px;">
                                Decision: <span style="color:#d9b45d;">${l.decision}</span>
                            </div>
                            <div style="font-size: 11px; font-family:'Space Mono'; color:#64748b; margin-top: 4px;">Trace: ${l.trace_id}</div>
                        </div>
                    `;
                });
                container.innerHTML = logHtml;
            }
        })
        .catch(err => {
            document.getElementById('errorAlert').style.display = 'block';
            document.getElementById('errorAlert').textContent = 'Telemetry sync error: ' + err.message;
        });
}

fetchTelemetry();
setInterval(fetchTelemetry, 10000);
</script>

</body>
</html>
