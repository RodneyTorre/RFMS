<?php
session_start();

if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

/* PREVENT CACHE */
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

include 'database.php';

/* =========================
   NOTIFICATION FUNCTIONS
========================= */

$today      = date("Y-m-d");
$next30days = date("Y-m-d", strtotime("+30 days"));

$insuranceQuery = $conn->query("
    SELECT * FROM insurance_policies
    WHERE valid_until BETWEEN '$today' AND '$next30days'
    AND status='Active'
");

$programQuery = $conn->query("
    SELECT * FROM distributions
    WHERE date BETWEEN '$today' AND '$next30days'
");

$trainingQuery = $conn->query("
    SELECT * FROM trainings
    WHERE date BETWEEN '$today' AND '$next30days'
");

$inventoryQuery = $conn->query("
    SELECT * FROM inventory
    WHERE quantity <= minimum_stock
");

$reportQuery = $conn->query("
    SELECT * FROM reports
    WHERE due_date BETWEEN '$today' AND '$next30days'
    AND status='Pending'
");

$notificationCount =
    ($insuranceQuery ? $insuranceQuery->num_rows : 0) +
    ($programQuery   ? $programQuery->num_rows   : 0) +
    ($trainingQuery  ? $trainingQuery->num_rows  : 0) +
    ($inventoryQuery ? $inventoryQuery->num_rows : 0) +
    ($reportQuery    ? $reportQuery->num_rows    : 0);

/* =========================
   DASHBOARD COUNTS
========================= */

$farmersResult = $conn->query("SELECT COUNT(*) AS total_farmers FROM farmers");
$totalFarmers  = $farmersResult ? $farmersResult->fetch_assoc()['total_farmers'] : 0;

$assetsResult = $conn->query("
    SELECT COUNT(*) AS total_assets FROM insurance_policies
    WHERE valid_until >= CURDATE()
");
$totalAssets  = $assetsResult ? ($assetsResult->fetch_assoc()['total_assets'] ?? 0) : 0;

$inactiveResult = $conn->query("
    SELECT COUNT(*) AS total FROM insurance_policies
    WHERE valid_until < CURDATE()
");
$inactiveAssets = $inactiveResult ? ($inactiveResult->fetch_assoc()['total'] ?? 0) : 0;

/* =========================
   INVENTORY STATS
========================= */

$totalItems = $conn->query("SELECT COUNT(*) AS t FROM inventory")->fetch_assoc()['t'] ?? 0;
$lowStock   = $conn->query("SELECT COUNT(*) AS t FROM inventory WHERE quantity <= 20")->fetch_assoc()['t'] ?? 0;

/* =========================
   INCIDENT CLAIM STATUS
========================= */

$claimResult = $conn->query("
    SELECT
        SUM(CASE WHEN claim_status = 'Already Claimed' THEN 1 ELSE 0 END) AS claimed,
        SUM(CASE WHEN claim_status != 'Already Claimed' OR claim_status IS NULL THEN 1 ELSE 0 END) AS not_claimed
    FROM incidents
");
$claimRow   = $claimResult ? $claimResult->fetch_assoc() : [];
$claimed    = (int)($claimRow['claimed']    ?? 0);
$notClaimed = (int)($claimRow['not_claimed'] ?? 0);
$claimTotal = $claimed + $notClaimed;

/* =========================
   MONTHLY FARMERS
========================= */

$farmerLabels = [];
$farmerData   = [];

$farmerMonthlyQuery = $conn->query("
    SELECT MONTHNAME(created_at) AS month, COUNT(*) AS total
    FROM farmers
    GROUP BY MONTH(created_at)
    ORDER BY MONTH(created_at)
");

if ($farmerMonthlyQuery) {
    while ($row = $farmerMonthlyQuery->fetch_assoc()) {
        $farmerLabels[] = $row['month'];
        $farmerData[]   = $row['total'];
    }
}

/* =========================
   INVENTORY STATUS (for chart)
========================= */

$inventoryLabels = [];
$inventoryData   = [];

$inventoryStatusQuery = $conn->query("SELECT item_name, quantity FROM inventory");

if ($inventoryStatusQuery) {
    while ($row = $inventoryStatusQuery->fetch_assoc()) {
        $inventoryLabels[] = $row['item_name'];
        $inventoryData[]   = $row['quantity'];
    }
}

$name = $_SESSION['name'] ?? 'Admin';

include 'header.php';
?>

<link rel="stylesheet" href="assets/css/dashboard.css">

<style>
/* ── Inventory stat cards ── */
.inv-stat-card {
    background: #fff;
    border-radius: 14px;
    padding: 18px 20px 14px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.07);
    border-left: 4px solid #10b981;
    display: flex;
    flex-direction: column;
    gap: 8px;
    transition: transform 0.2s, box-shadow 0.2s;
    animation: cardIn 0.3s ease both;
    cursor: default;
}
.inv-stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(0,0,0,0.11);
}
.inv-stat-card.low { border-left-color: #f59e0b; }
@keyframes cardIn {
    from { opacity: 0; transform: translateY(8px); }
    to   { opacity: 1; transform: translateY(0); }
}
.inv-stat-top {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 8px;
}
.inv-stat-name {
    font-size: 13px;
    font-weight: 700;
    color: #111827;
    line-height: 1.3;
    flex: 1;
}
.inv-stat-badge {
    font-size: 10px;
    font-weight: 700;
    padding: 3px 8px;
    border-radius: 20px;
    white-space: nowrap;
    flex-shrink: 0;
}
.inv-stat-badge.in-stock    { background: #d1fae5; color: #065f46; }
.inv-stat-badge.low-stock   { background: #fef3c7; color: #92400e; }
.inv-stat-badge.out-of-stock { background: #fee2e2; color: #991b1b; }
.inv-stat-meta { font-size: 11px; color: #9ca3af; }
.inv-stat-qty {
    font-size: 26px;
    font-weight: 800;
    color: #111827;
    line-height: 1;
}
.inv-stat-qty span {
    font-size: 12px;
    font-weight: 500;
    color: #9ca3af;
    margin-left: 3px;
}
.inv-progress {
    height: 4px;
    background: #f3f4f6;
    border-radius: 10px;
    overflow: hidden;
}
.inv-progress-fill {
    height: 100%;
    border-radius: 10px;
    background: #10b981;
    transition: width 0.6s ease;
}
.inv-stat-card.low .inv-progress-fill { background: #f59e0b; }
.inv-stat-card.out .inv-progress-fill { background: #ef4444; }

/* live dot */
.live-dot {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    font-size: 11px;
    color: #9ca3af;
    font-weight: 500;
}
.live-dot::before {
    content: '';
    width: 7px; height: 7px;
    border-radius: 50%;
    background: #10b981;
    display: inline-block;
    animation: pulse 1.6s ease-in-out infinite;
}
@keyframes pulse {
    0%,100% { opacity:1; transform:scale(1); }
    50%      { opacity:.4; transform:scale(1.4); }
}

/* ── Chart layout helpers ── */
.charts-row-2 {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
    margin-bottom: 20px;
}
.charts-row-3 {
    display: grid;
    grid-template-columns: 1fr 1fr 1fr;
    gap: 20px;
    margin-bottom: 20px;
}
@media (max-width: 1100px) {
    .charts-row-3 { grid-template-columns: 1fr 1fr; }
}
@media (max-width: 768px) {
    .charts-row-2,
    .charts-row-3 { grid-template-columns: 1fr; }
}

/* ── Uniform chart card height ── */
.chart-card-fixed {
    display: flex;
    flex-direction: column;
}
.chart-canvas-wrap {
    flex: 1;
    position: relative;
    min-height: 260px;
}
.chart-canvas-wrap canvas {
    position: absolute;
    top: 0; left: 0;
    width: 100% !important;
    height: 100% !important;
}

/* ── Doughnut centre ── */
.doughnut-wrap {
    position: relative;
    width: 200px;
    height: 200px;
    margin: 12px auto 0;
    flex-shrink: 0;
}
.doughnut-centre {
    position: absolute;
    top: 50%; left: 50%;
    transform: translate(-50%,-50%);
    text-align: center;
    pointer-events: none;
}
.doughnut-centre-num {
    font-size: 28px;
    font-weight: 800;
    color: #111827;
    line-height: 1;
}
.doughnut-centre-sub {
    font-size: 11px;
    color: #9ca3af;
    margin-top: 3px;
}

/* ── Claim summary pills ── */
.claim-summary {
    display: flex;
    gap: 10px;
    margin-top: 14px;
}
.claim-summary-item {
    flex: 1;
    background: #f9fafb;
    border-radius: 10px;
    padding: 10px 12px;
    text-align: center;
}
.cs-label { font-size: 11px; color: #6b7280; margin-bottom: 3px; }
.cs-value { font-size: 20px; font-weight: 800; line-height: 1; }
.cs-pct   { font-size: 11px; color: #9ca3af; margin-top: 2px; }
.claimed-color   { color: #3b82f6; }
.unclaimed-color { color: #6b7280; }

/* ── Claim legend ── */
.claim-legend {
    display: flex;
    gap: 16px;
    margin-top: 10px;
    flex-wrap: wrap;
}
.claim-legend-item {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 12px;
    color: #6b7280;
}
.claim-legend-dot {
    width: 10px; height: 10px;
    border-radius: 2px;
    flex-shrink: 0;
}

/* ── Section divider label ── */
.section-label {
    font-size: 11px;
    font-weight: 700;
    color: #9ca3af;
    text-transform: uppercase;
    letter-spacing: .06em;
    margin: 28px 0 12px;
}
</style>

<!-- PAGE HEADER -->
<div class="page-header">
    <div>
        <h1 class="page-title">Dashboard</h1>
        <p class="page-subtitle">Agricultural Operations Overview</p>
    </div>
</div>

<!-- WELCOME -->
<div class="page-subtitle">
    <h2>Welcome, <?php echo htmlspecialchars($name); ?>!</h2>
</div>

<!-- =========================
     STATS GRID
========================= -->
<div class="stats-grid" id="statsGrid">

    <div class="stat-card">
        <div class="stat-icon-wrapper green">👥</div>
        <div class="stat-label">Registered Farmers</div>
        <div class="stat-header">
            <div class="stat-value"><p><?php echo $totalFarmers; ?></p></div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon-wrapper purple">🛡️</div>
        <div class="stat-label">Insured Assets</div>
        <div class="stat-header">
            <div class="stat-value"><p><?php echo $totalAssets; ?></p></div>
        </div>
    </div>

    <div id="invCardsContainer" style="display:contents"></div>
</div>

<!-- =========================
     RECENT ACTIVITIES
========================= -->
<div class="chart-card full-width" style="margin-bottom:20px;">
    <div class="chart-top">
        <div>
            <h3>Recent Activities</h3>
            <p>Latest system updates</p>
        </div>
    </div>
    <table class="data-table">
        <thead>
            <tr>
                <th>Activity</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $activity = $conn->query("
                SELECT message, created_at
                FROM notifications
                ORDER BY created_at DESC
                LIMIT 5
            ");
            if ($activity && $activity->num_rows > 0):
                while ($row = $activity->fetch_assoc()):
            ?>
            <tr>
                <td><?= htmlspecialchars($row['message']) ?></td>
                <td><?= date("M d, Y h:i A", strtotime($row['created_at'])) ?></td>
            </tr>
            <?php
                endwhile;
            else:
            ?>
            <tr>
                <td colspan="2" style="text-align:center;color:#9ca3af;padding:20px;">
                    No recent activities.
                </td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- =========================
     ROW 1 — Farmers (bar) + Insurance (pie) — equal halves
========================= -->
<p class="section-label">Policy &amp; Farmer Overview</p>
<div class="charts-row-2">

    <!-- Registered Farmers -->
    <div class="chart-card chart-card-fixed">
        <div class="chart-top">
            <div>
                <h3>Registered Farmers</h3>
                <p>Monthly farmer registrations</p>
            </div>
            <span class="chart-badge blue">Live</span>
        </div>
        <div class="chart-canvas-wrap">
            <canvas id="farmersChart"></canvas>
        </div>
    </div>

    <!-- Insurance Analytics -->
    <div class="chart-card chart-card-fixed">
        <div class="chart-top">
            <div>
                <h3>Insurance Analytics</h3>
                <p>Active vs inactive policies</p>
            </div>
            <span class="chart-badge purple">Real-Time</span>
        </div>
        <div class="chart-canvas-wrap">
            <canvas id="insuredChart"></canvas>
        </div>
    </div>

</div>

<!-- =========================
     ROW 2 — Incident Claim Status (doughnut, narrower) + Inventory bar (wider)
========================= -->
<p class="section-label">Claims &amp; Inventory</p>
<div style="display:grid;grid-template-columns:1fr 2fr;gap:20px;margin-bottom:20px;">

    <!-- Incident Claim Status -->
    <div class="chart-card chart-card-fixed">
        <div class="chart-top">
            <div>
                <h3>Incident Claim Status</h3>
                <p>Claimed vs not claimed</p>
            </div>
            <span class="chart-badge" style="background:#fff7ed;color:#c2410c;border:1px solid #fed7aa;">Claims</span>
        </div>

        <!-- Summary pills -->
        <div class="claim-summary">
            <div class="claim-summary-item">
                <div class="cs-label">Claimed</div>
                <div class="cs-value claimed-color"><?php echo $claimed; ?></div>
                <div class="cs-pct"><?php echo $claimTotal > 0 ? round(($claimed / $claimTotal) * 100) : 0; ?>%</div>
            </div>
            <div class="claim-summary-item">
                <div class="cs-label">Not Claimed</div>
                <div class="cs-value unclaimed-color"><?php echo $notClaimed; ?></div>
                <div class="cs-pct"><?php echo $claimTotal > 0 ? round(($notClaimed / $claimTotal) * 100) : 0; ?>%</div>
            </div>
        </div>

        <div class="chart-canvas-wrap" style="min-height:160px;margin-top:16px;">
            <canvas id="claimChart"></canvas>
        </div>
    </div>

    <!-- Inventory Status -->
    <div class="chart-card chart-card-fixed">
        <div class="chart-top">
            <div>
                <h3>Inventory Status</h3>
                <p>Warehouse stock monitoring</p>
            </div>
        </div>
        <div class="chart-canvas-wrap">
            <canvas id="inventoryChart"></canvas>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>

<script>
Chart.register(ChartDataLabels);

/* ── Shared tooltip defaults ── */
const ttDefaults = {
    backgroundColor: '#fff',
    titleColor: '#111827',
    bodyColor: '#374151',
    borderColor: '#e5e7eb',
    borderWidth: 1,
    padding: 12,
    cornerRadius: 10
};

/* ── 1. Registered Farmers (bar) ── */
(function () {
    const ctx  = document.getElementById('farmersChart').getContext('2d');
    const grad = ctx.createLinearGradient(0, 0, 0, 260);
    grad.addColorStop(0, '#2563eb');
    grad.addColorStop(1, 'rgba(37,99,235,0.20)');

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?= json_encode($farmerLabels ?: ['Jan','Feb','Mar','Apr','May','Jun']) ?>,
            datasets: [{
                label: 'Farmers',
                data: <?= json_encode($farmerData ?: [0,0,0,0,0,0]) ?>,
                backgroundColor: grad,
                borderRadius: 8,
                borderSkipped: false,
                barThickness: 28
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                datalabels: {
                    anchor: 'end', align: 'top',
                    color: '#6b7280',
                    font: { size: 11, weight: '600' },
                    formatter: v => v > 0 ? v : ''
                },
                tooltip: { ...ttDefaults }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: '#f3f4f6' },
                    ticks: { color: '#6b7280', precision: 0 }
                },
                x: {
                    grid: { display: false },
                    ticks: { color: '#6b7280' }
                }
            }
        }
    });
})();

/* ── 2. Insurance Analytics (pie) ── */
(function () {
    const active   = <?= (int)$totalAssets ?>;
    const inactive = <?= (int)$inactiveAssets ?>;
    const total    = active + inactive;

    new Chart(document.getElementById('insuredChart'), {
        type: 'pie',
        data: {
            labels: ['Active Policies', 'Inactive / Expired'],
            datasets: [{
                data: [active, inactive],
                backgroundColor: ['#8b5cf6', '#e5e7eb'],
                borderWidth: 3,
                borderColor: '#fff',
                hoverOffset: 12
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { padding: 20, usePointStyle: true, color: '#374151' }
                },
                datalabels: {
                    color: '#fff',
                    font: { size: 13, weight: '700' },
                    formatter: v => (!total || v === 0) ? '' : Math.round((v / total) * 100) + '%'
                },
                tooltip: {
                    ...ttDefaults,
                    callbacks: { label: ctx => '  ' + ctx.parsed + ' policies' }
                }
            }
        }
    });
})();

/* ── 3. Incident Claim Status (horizontal bar) ── */
(function () {
    const claimed    = <?= (int)$claimed ?>;
    const notClaimed = <?= (int)$notClaimed ?>;
    const total      = claimed + notClaimed;

    new Chart(document.getElementById('claimChart'), {
        type: 'bar',
        data: {
            labels: ['Claimed', 'Not Claimed'],
            datasets: [{
                label: 'Policies',
                data: [claimed, notClaimed],
                backgroundColor: ['#3b82f6', '#f59e0b'],
                borderRadius: 8,
                borderSkipped: false,
                barThickness: 36
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                datalabels: {
                    anchor: 'end',
                    align: 'right',
                    color: '#6b7280',
                    font: { size: 12, weight: '600' },
                    formatter: function(v) {
                        if (!total || v === 0) return '0 (0%)';
                        return v + '  (' + Math.round((v / total) * 100) + '%)';
                    }
                },
                tooltip: {
                    ...ttDefaults,
                    callbacks: {
                        label: function(ctx) {
                            const v   = ctx.parsed.x;
                            const pct = total > 0 ? Math.round((v / total) * 100) : 0;
                            return '  ' + v + ' policies (' + pct + '%)';
                        }
                    }
                }
            },
            scales: {
                x: {
                    beginAtZero: true,
                    grid: { color: '#f3f4f6' },
                    ticks: { color: '#6b7280', precision: 0 }
                },
                y: {
                    grid: { display: false },
                    ticks: { color: '#374151', font: { size: 13, weight: '600' } }
                }
            }
        }
    });
})();

/* ── 4. Inventory Status (horizontal bar) ── */
(function () {
    const palette = ['#22c55e','#3b82f6','#f59e0b','#ef4444','#8b5cf6','#06b6d4','#ec4899'];
    const labels  = <?= json_encode($inventoryLabels ?: ['No Data']) ?>;
    const data    = <?= json_encode($inventoryData   ?: [0]) ?>;

    /* Assign colours cycling through palette */
    const bgColors = labels.map((_, i) => palette[i % palette.length]);

    new Chart(document.getElementById('inventoryChart'), {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Quantity',
                data: data,
                backgroundColor: bgColors,
                borderRadius: 6,
                borderSkipped: false,
                barThickness: 22
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                datalabels: {
                    anchor: 'end', align: 'right',
                    color: '#6b7280',
                    font: { size: 11, weight: '600' },
                    formatter: v => v > 0 ? Number(v).toLocaleString() : ''
                },
                tooltip: {
                    ...ttDefaults,
                    callbacks: { label: ctx => '  ' + Number(ctx.parsed.x).toLocaleString() + ' units' }
                }
            },
            scales: {
                x: {
                    beginAtZero: true,
                    grid: { color: '#f3f4f6' },
                    ticks: { color: '#6b7280', callback: v => Number(v).toLocaleString() }
                },
                y: {
                    grid: { display: false },
                    ticks: { color: '#6b7280' }
                }
            }
        }
    });
})();

/* =========================
   INVENTORY STOCK CARDS (real-time)
========================= */
(function () {
    const container = document.getElementById('invCardsContainer');

    function sc(status) {
        const s = (status || '').toLowerCase();
        if (s.includes('low')) return 'low-stock';
        if (s.includes('out')) return 'out-of-stock';
        return 'in-stock';
    }
    function borderClass(status) {
        const s = (status || '').toLowerCase();
        if (s.includes('low')) return 'low';
        if (s.includes('out')) return 'out';
        return '';
    }
    function esc(str) {
        return String(str || '')
            .replace(/&/g,'&amp;').replace(/</g,'&lt;')
            .replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }

    function render(items) {
        if (!items || items.length === 0) { container.innerHTML = ''; return; }
        const max = Math.max(...items.map(i => parseInt(i.quantity) || 0), 1);
        let html = '';
        items.forEach((item, idx) => {
            const qty = parseInt(item.quantity) || 0;
            const s   = sc(item.status);
            const bc  = borderClass(item.status);
            const pct = Math.min(Math.round((qty / max) * 100), 100);
            html += `
            <div class="inv-stat-card ${bc}" style="animation-delay:${idx * 40}ms">
                <div class="inv-stat-top">
                    <div class="inv-stat-name">${esc(item.item_name)}</div>
                    <span class="inv-stat-badge ${s}">${esc(item.status)}</span>
                </div>
                <div class="inv-stat-meta">${esc(item.category)} &bull; ${esc(item.warehouse)}</div>
                <div class="inv-stat-qty">${qty.toLocaleString()}<span>units</span></div>
                <div class="inv-progress">
                    <div class="inv-progress-fill" style="width:${pct}%"></div>
                </div>
            </div>`;
        });
        container.innerHTML = html;
    }

    function fetchData() {
        fetch('get_inventory_realtime.php')
            .then(r => r.json())
            .then(data => render(data.items))
            .catch(() => {});
    }

    fetchData();
    setInterval(fetchData, 15000);
})();

/* Auto-refresh every 60s */
setTimeout(() => location.reload(), 60000);
</script>