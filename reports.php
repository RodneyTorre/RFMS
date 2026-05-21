<?php
session_start();

if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

include 'database.php';
$page_title = "Reports & Analytics";
include 'header.php';

/* =========================
   INSURANCE
========================= */
$ins_total = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM insurance_policies"))['total'] ?? 0;

$ins_active = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM insurance_policies WHERE status='Active'"))['total'] ?? 0;

$ins_payment = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(payment) AS total FROM insurance_policies"))['total'] ?? 0;

$ins_expiring = mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT COUNT(*) AS total FROM insurance_policies
     WHERE valid_until <= DATE_ADD(CURDATE(), INTERVAL 30 DAY)
     AND status='Active'"
))['total'] ?? 0;

/* =========================
   PROGRAM
========================= */
$totalBeneficiaries = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(beneficiaries) AS total FROM distributions"))['total'] ?? 0;

$totalQuantity = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(quantity) AS total FROM distributions"))['total'] ?? 0;

$totalTrainings = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM trainings"))['total'] ?? 0;

/* =========================
   DISASTER
========================= */
$totalClaims = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM incidents"))['total'] ?? 0;

$totalApproved = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM incidents WHERE status='Approved'"))['total'] ?? 0;

$totalActiveIncidents = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM incidents WHERE status='Assessment'"))['total'] ?? 0;

$totalLoss = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(damage) AS total FROM incidents WHERE status='Approved'"))['total'] ?? 0;

/* =========================
   FARMERS
========================= */
$totalFarmers = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM farmers"))['total'] ?? 0;

$totalMale = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM farmers WHERE gender='Male'"))['total'] ?? 0;

$totalFemale = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM farmers WHERE gender='Female'"))['total'] ?? 0;

$totalBarangays = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(DISTINCT address) AS total FROM farmers"))['total'] ?? 0;
$totalFarmSize = mysqli_fetch_assoc(mysqli_query(
    $conn,
    "SELECT SUM(farm_size) AS total FROM farmers"
))['total'] ?? 0;

$totalCrops = mysqli_fetch_assoc(mysqli_query(
    $conn,
    "SELECT COUNT(DISTINCT crop_type) AS total FROM farmers"
))['total'] ?? 0;
?>

<link rel="stylesheet" href="assets/css/reports.css">

<style>
body { background:#f3f4f6; }

/* HEADER */
.page-header{
    display:flex;
    justify-content:space-between;
    align-items:center;
    flex-wrap:wrap;
    gap:15px;
    margin-bottom:25px;
}

.page-title-section h1{ font-size:28px; margin-bottom:5px; }
.page-subtitle{ color:#6b7280; font-size:14px; }

/* BUTTON */
.btn{
    border:none;
    padding:12px 18px;
    border-radius:10px;
    cursor:pointer;
    font-weight:600;
    display:flex;
    align-items:center;
    justify-content:center;
    gap:6px;
    transition:0.3s;
    text-decoration:none;
}

.btn-primary{ background:#10b981; color:#fff; }
.btn-primary:hover{ background:#059669; }

.btn-generate{ background:#f59e0b; color:#fff; }
.btn-generate:hover{ background:#d97706; }

/* GRID */
.reports-grid{
    display:grid;
    grid-template-columns:repeat(2, 1fr);
    gap:20px;
}

/* CARD */
.report-card{
    background:#fff;
    border-radius:16px;
    padding:22px;
    box-shadow:0 4px 18px rgba(0,0,0,0.08);
    display:flex;
    flex-direction:column;
    justify-content:space-between;
    min-height:340px;
}

.report-header{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:18px;
}

.report-title{ font-size:18px; font-weight:700; }
.report-description{ font-size:14px; color:#6b7280; margin-top:4px; }

.report-icon{
    width:55px;
    height:55px;
    border-radius:14px;
    background:#d1fae5;
    display:flex;
    align-items:center;
    justify-content:center;
}

.report-icon span{ font-size:30px; color:#059669; }

/* STATS */
.stats-grid{
    display:grid;
    grid-template-columns:repeat(2,1fr);
    gap:12px;
    margin-bottom:20px;
}

.stat-box{
    background:#f9fafb;
    border-radius:12px;
    padding:14px;
}

.stat-label{
    font-size:12px;
    color:#6b7280;
    margin-bottom:6px;
}

.stat-value{
    font-size:18px;
    font-weight:700;
}

/* ACTION */
.report-buttons{
    margin-top:auto;
    display:flex;
    justify-content:flex-end;
}

/* RESPONSIVE */
@media(max-width:768px){
    .reports-grid{ grid-template-columns:1fr; }
    .stats-grid{ grid-template-columns:1fr; }
}
</style>

<!-- HEADER -->
<div class="page-header">
    <div class="page-title-section">
        <h1>Reports & Analytics</h1>
        <p class="page-subtitle">Live system reports</p>
    </div>

    <a href="export_monthly_report.php" class="btn btn-primary">
        <span class="material-icons">download</span>
        Export Excel
    </a>
</div>

<div class="reports-grid">

<!-- INSURANCE -->
<div class="report-card">
    <div>
        <div class="report-header">
            <div>
                <div class="report-title">Insurance Coverage</div>
                <div class="report-description">Policies and payments</div>
            </div>
            <div class="report-icon"><span class="material-icons">shield</span></div>
        </div>

        <div class="stats-grid">
            <div class="stat-box"><div class="stat-label">Active</div><div class="stat-value"><?= number_format($ins_active) ?></div></div>
            <div class="stat-box"><div class="stat-label">Payments</div><div class="stat-value">₱<?= number_format($ins_payment) ?></div></div>
            <div class="stat-box"><div class="stat-label">Expiring</div><div class="stat-value"><?= number_format($ins_expiring) ?></div></div>
            <div class="stat-box"><div class="stat-label">Total</div><div class="stat-value"><?= number_format($ins_total) ?></div></div>
        </div>
    </div>

    <div class="report-buttons">
        <button class="btn btn-generate" onclick="generateReport('insurance')">
            <span class="material-icons">description</span>
            Generate
        </button>
    </div>
</div>

<!-- PROGRAM -->
<div class="report-card">
    <div>
        <div class="report-header">
            <div>
                <div class="report-title">Program Effectiveness</div>
                <div class="report-description">Distribution & trainings</div>
            </div>
            <div class="report-icon"><span class="material-icons">inventory_2</span></div>
        </div>

        <div class="stats-grid">
            <div class="stat-box"><div class="stat-label">Beneficiaries</div><div class="stat-value"><?= number_format($totalBeneficiaries) ?></div></div>
            <div class="stat-box"><div class="stat-label">Quantity</div><div class="stat-value"><?= number_format($totalQuantity) ?></div></div>
            <div class="stat-box"><div class="stat-label">Trainings</div><div class="stat-value"><?= number_format($totalTrainings) ?></div></div>
        </div>
    </div>
    <div class="report-buttons">
        <button class="btn btn-generate" onclick="generateReport('program')">
            <span class="material-icons">description</span>
            Generate
        </button>
    </div>
</div>

<!-- DISASTER -->
<div class="report-card">
    <div>
        <div class="report-header">
            <div>
                <div class="report-title">Disaster Impact</div>
                <div class="report-description">Incident analysis</div>
            </div>
            <div class="report-icon"><span class="material-icons">warning</span></div>
        </div>

        <div class="stats-grid">
            <div class="stat-box"><div class="stat-label">Claims</div><div class="stat-value"><?= number_format($totalClaims) ?></div></div>
            <div class="stat-box"><div class="stat-label">Approved</div><div class="stat-value"><?= number_format($totalApproved) ?></div></div>
            <div class="stat-box"><div class="stat-label">Active</div><div class="stat-value"><?= number_format($totalActiveIncidents) ?></div></div>
            <div class="stat-box"><div class="stat-label">Loss</div><div class="stat-value">₱<?= number_format($totalLoss) ?></div></div>
        </div>
    </div>

    <div class="report-buttons">
        <button class="btn btn-generate" onclick="generateReport('disaster')">
            <span class="material-icons">description</span>
            Generate
        </button>
    </div>
</div>
<!-- FARMERS -->
<div class="report-card">
    <div>
        <div class="report-header">
            <div>
                <div class="report-title">Farmer Demographics</div>
                <div class="report-description">Population insights</div>
            </div>
            <div class="report-icon">
                <span class="material-icons">people</span>
            </div>
        </div>

        <div class="stats-grid">

            <div class="stat-box">
                <div class="stat-label">Total</div>
                <div class="stat-value"><?= number_format($totalFarmers) ?></div>
            </div>

            <div class="stat-box">
                <div class="stat-label">Male</div>
                <div class="stat-value"><?= number_format($totalMale) ?></div>
            </div>

            <div class="stat-box">
                <div class="stat-label">Female</div>
                <div class="stat-value"><?= number_format($totalFemale) ?></div>
            </div>

            <div class="stat-box">
                <div class="stat-label">Barangays</div>
                <div class="stat-value"><?= number_format($totalBarangays) ?></div>
            </div>

            <div class="stat-box">
                <div class="stat-label">Total Farm Size</div>
                <div class="stat-value"><?= number_format($totalFarmSize) ?> ha</div>
            </div>

            <div class="stat-box">
                <div class="stat-label">Crop Types</div>
                <div class="stat-value"><?= number_format($totalCrops) ?></div>
            </div>

        </div>
    </div>

    <div class="report-buttons">
        <button class="btn btn-generate" onclick="generateReport('farmers')">
            <span class="material-icons">description</span>
            Generate
        </button>
    </div>
</div>

</div>

<script>
function generateReport(type){
    window.location.href = "generate_report.php?type=" + type;
}
</script>