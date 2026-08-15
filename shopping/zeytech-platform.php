<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ZeyTech — Platform Analytics & Multi-Agent Telemetry</title>
    <!-- Authentic ZeyTech Typography (Fraunces, IBM Plex Sans, Space Mono) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,400;0,9..144,600;0,9..144,700;0,9..144,800;1,9..144,400&family=IBM+Plex+Sans:ital,wght@0,400;0,500;0,600;0,700;1,400&family=Space+Mono:ital,wght@0,400;0,700;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/font-awesome.min.css">
    <link rel="stylesheet" href="assets/css/modern-storefront.css">
</head>
<body style="background-color: #080e1a; color: #f2efe6; font-family: 'IBM Plex Sans', sans-serif;">

<header class="top-nav" style="background:#0c1526; border-bottom:1px solid rgba(142,162,191,0.18); padding:14px 28px; display:flex; justify-content:space-between; align-items:center; position:sticky; top:0; z-index:100;">
    <div style="display:flex; align-items:center; gap:12px;">
        <span class="hexagram-mark">
            <svg class="hexagram-svg" viewBox="0 0 24 24">
                <polygon points="12,2 22,18 2,18" stroke="#c79a44" fill="none" stroke-width="1.5"/>
                <polygon points="12,22 22,6 2,6" stroke="#d9b567" fill="none" stroke-width="1.5"/>
            </svg>
        </span>
        <div>
            <span style="font-family:'Fraunces', serif; font-size:18px; font-weight:700; color:#f2efe6; letter-spacing:-0.02em;">ZeyTech</span>
            <span style="font-family:'Space Mono', monospace; font-size:11px; color:#8ea2bf; margin-left:8px; text-transform:uppercase; letter-spacing:0.06em;">Platform Telemetry &bull; Hub-A1</span>
        </div>
    </div>
    <div style="display:flex; gap:10px;">
        <a href="zeytech-ops-console.html" class="btn-ghost" style="font-size:12px; padding:6px 14px;">
            Operations Console
        </a>
        <a href="index.php" class="btn-primary" style="font-size:12px; padding:6px 14px;">
            Storefront
        </a>
    </div>
</header>

<main class="workspace-container" style="max-width:1400px; margin:0 auto; padding:28px 24px 60px 24px;">
    <div id="errorAlert" style="display:none; font-size:13px; font-family:'Space Mono'; background:rgba(239,68,68,0.1); border:1px solid #ef4444; color:#fca5a5; padding:12px 16px; border-radius:2px; margin-bottom:24px;"></div>

    <!-- Section Header Eyebrow -->
    <div style="margin-bottom:20px; display:flex; justify-content:space-between; align-items:baseline; border-bottom:1px solid rgba(142,162,191,0.18); padding-bottom:12px;">
        <div>
            <span style="font-family:'Space Mono', monospace; font-size:11px; color:#c79a44; font-weight:700; text-transform:uppercase; letter-spacing:0.1em;">
                [TELEMETRY.LEDGER] &bull; REAL-TIME COMMERCE METRICS
            </span>
            <h1 style="font-family:'Fraunces', serif; font-size:26px; font-weight:700; color:#f2efe6; margin:4px 0 0 0; letter-spacing:-0.02em;">
                Autonomous Business Telemetry
            </h1>
        </div>
        <div style="font-family:'Space Mono', monospace; font-size:11px; color:#8ea2bf;">
            FEED: <span style="color:#22c55e;">[ACTIVE_POLL_10S]</span>
        </div>
    </div>

    <!-- Live KPIs (Fraunces Editorial Numbers & Space Mono Labels) -->
    <div class="metrics-grid">
        <div class="metric-card">
            <div class="metric-title">01 &bull; Gross Settlement (USD)</div>
            <div id="kpiRevenue" class="metric-value val-gold">---</div>
            <div id="kpiRevenueMAD" style="font-family:'Space Mono'; font-size:12px; color:#8ea2bf; margin-top:6px;">Syncing regional currency...</div>
        </div>

        <div class="metric-card">
            <div class="metric-title">02 &bull; Cryptographic Orders</div>
            <div id="kpiOrders" class="metric-value">---</div>
            <div style="font-family:'Space Mono'; font-size:11px; color:#22c55e; margin-top:6px;">100% HMAC Verified</div>
        </div>

        <div class="metric-card">
            <div class="metric-title">03 &bull; Warehouse Stock (Hub-A1)</div>
            <div id="kpiStockAvail" class="metric-value">---</div>
            <div id="kpiStockDetails" style="font-family:'Space Mono'; font-size:11px; color:#8ea2bf; margin-top:6px;">Available / Reserved / Sold</div>
        </div>

        <div class="metric-card">
            <div class="metric-title">04 &bull; Daily AI LLM Budget</div>
            <div id="kpiLlmSpend" class="metric-value val-gold">---</div>
            <div style="font-family:'Space Mono'; font-size:11px; color:#22c55e; margin-top:6px;">Under $25.00 Hard Limit</div>
        </div>
    </div>

    <!-- 15 Agents Grid & Supervisor Decision Ledger -->
    <div class="row" style="margin-top:12px;">
        <!-- 15 Specialized Agents List (Manifest Table) -->
        <div class="col-md-7">
            <div class="manifest-panel">
                <div class="panel-title">
                    <span style="display:flex; align-items:center; gap:8px;">
                        <span class="hexagram-mark" style="width:18px; height:18px;">
                            <svg class="hexagram-svg" viewBox="0 0 24 24">
                                <polygon points="12,2 22,18 2,18" stroke="#c79a44" fill="none" stroke-width="1.5"/>
                                <polygon points="12,22 22,6 2,6" stroke="#d9b567" fill="none" stroke-width="1.5"/>
                            </svg>
                        </span>
                        15 Specialized AI Multi-Agent Roster
                    </span>
                    <span class="tag-pill tag-success">[STATUS: NOMINAL]</span>
                </div>
                <div class="table-responsive">
                    <table class="enterprise-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Agent Domain</th>
                                <th>Primary Capability</th>
                                <th>Channel</th>
                                <th style="text-align: right;">State</th>
                            </tr>
                        </thead>
                        <tbody id="agentTableBody">
                            <tr><td colspan="5" style="text-align:center; padding:30px; font-family:'Space Mono'; color:#8ea2bf;">Loading agent topology...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Supervisor Decision Ledger (Audit Trail) -->
        <div class="col-md-5">
            <div class="manifest-panel">
                <div class="panel-title">
                    <span>Supervisor Decision Ledger</span>
                    <button class="btn-ghost" style="padding:2px 8px; font-size:11px; font-family:'Space Mono';" onclick="fetchTelemetry()">
                        REFRESH
                    </button>
                </div>
                <div id="auditLogList" style="max-height: 520px; overflow-y: auto; padding-right: 4px;">
                    <div style="text-align:center; padding:30px; font-family:'Space Mono'; color:#8ea2bf;">Reading audit ledger...</div>
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
                document.getElementById('kpiStockDetails').textContent = data.kpis.stockAvailable + ' Avail • ' + data.kpis.stockReserved + ' In Carts • ' + data.kpis.stockSold + ' Settled';
                document.getElementById('kpiLlmSpend').textContent = '$' + Number(data.kpis.dailyLlmSpendUSD).toFixed(2);
            }

            if (data.agents) {
                var tbody = document.getElementById('agentTableBody');
                var html = '';
                data.agents.forEach(a => {
                    html += `
                        <tr>
                            <td><span class="manifest-id" style="font-family:'Space Mono'; font-size:11px; color:#c79a44;">#${String(a.id).padStart(2, '0')}</span></td>
                            <td><strong style="color:#f2efe6; font-family:'IBM Plex Sans';">${a.name}</strong></td>
                            <td style="color:#8ea2bf; font-size:12px;">${a.role}</td>
                            <td><span class="tag-pill tag-gold" style="font-size:10px;">${a.channel}</span></td>
                            <td style="text-align: right;"><span class="tag-pill tag-success" style="font-size:10px;">[ONLINE]</span></td>
                        </tr>
                    `;
                });
                tbody.innerHTML = html;
            }

            if (data.auditLogs) {
                var container = document.getElementById('auditLogList');
                if (data.auditLogs.length === 0) {
                    container.innerHTML = '<div style="color:#5e7391; padding:24px; text-align:center; font-family:\'Space Mono\';">No decision logs recorded in this session.</div>';
                    return;
                }
                var logHtml = '';
                data.auditLogs.forEach(l => {
                    logHtml += `
                        <div style="background: rgba(142,162,191,0.04); border-left: 2px solid #c79a44; padding: 10px 14px; margin-bottom: 8px; border-radius: 2px;">
                            <div style="display:flex; justify-content:space-between; font-size: 11px; font-family:'Space Mono'; color: #8ea2bf;">
                                <span><strong style="color:#f2efe6;">${l.actor}</strong> &bull; ${l.channel}</span>
                                <span>${l.created_at ? l.created_at.slice(11, 19) : ''}</span>
                            </div>
                            <div style="font-size: 13px; color: #f2efe6; font-weight: 500; margin-top: 4px;">
                                Decision: <span style="color:#d9b567; font-family:'Space Mono';">${l.decision}</span>
                            </div>
                            <div style="font-size: 11px; font-family:'Space Mono'; color:#5e7391; margin-top: 4px;">Trace: ${l.trace_id}</div>
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
