<?php
session_start();
include 'database.php';

$page_title = "Insurance Management";

/* =========================
   FETCH POLICIES
========================= */
$policies = [];

$sql = "SELECT * FROM insurance_policies ORDER BY created_at DESC";
$result = mysqli_query($conn, $sql);

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $policies[] = $row;
    }
}

/* =========================
   STATS (DYNAMIC)
========================= */
$totalInsured = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM insurance_policies"))['total'] ?? 0;

$totalPremium = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(premium) as total FROM insurance_policies"))['total'] ?? 0;

$activePolicies = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM insurance_policies WHERE status='Active'"))['total'] ?? 0;

$expiringSoon = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM insurance_policies WHERE valid_until <= DATE_ADD(CURDATE(), INTERVAL 30 DAY)"))['total'] ?? 0;

include 'header.php';
?>

<link rel="stylesheet" href="assets/css/insurance.css">
<style>
    .modal {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.5);
    justify-content: center;
    align-items: center;
}

/* LOGIN STYLE CARD */
.modal-card {
    background: #fff;
    width: 380px;
    padding: 25px;
    border-radius: 12px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.2);
    text-align: center;
}

/* TITLE */
.modal-title {
    margin-bottom: 20px;
    font-size: 22px;
    font-weight: bold;
    color: #10b981;
}

/* FORM LAYOUT */
.modal-form {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

/* INPUT STYLE (LOGIN STYLE LOOK) */
.modal-form input {
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 8px;
    outline: none;
    font-size: 14px;
}

.modal-form input:focus {
    border-color: #2e7d32;
}
</style>
<!-- HEADER -->
<div class="page-header">
    <div class="page-title-section">
        <h1>Insurance Management</h1>
        <p class="page-subtitle">Enrollment, policies, and premium tracking</p>
    </div>

    <div class="page-actions">
        <button class="btn btn-secondary">
            <span class="material-icons">download</span>
            Export Data
        </button>

        <button class="btn btn-primary" onclick="openInsuranceModal()">
            <span class="material-icons">add</span>
            New Enrollment
        </button>
    </div>
</div>

<!-- STATS -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon green"><span class="material-icons">shield</span></div>
        <div>
            <div class="stat-value"><?= $totalInsured ?></div>
            <div class="stat-label">Total Insured</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon blue"><span class="material-icons">payments</span></div>
        <div>
            <div class="stat-value">₱<?= number_format($totalPremium, 2) ?></div>
            <div class="stat-label">Premium Subsidies</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon purple"><span class="material-icons">check_circle</span></div>
        <div>
            <div class="stat-value"><?= $activePolicies ?></div>
            <div class="stat-label">Active Policies</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon orange"><span class="material-icons">schedule</span></div>
        <div>
            <div class="stat-value"><?= $expiringSoon ?></div>
            <div class="stat-label">Expiring Soon</div>
        </div>
    </div>
</div>

<!-- TABS -->
<div class="tabs-container">
    <button class="tab-btn active">Insurance Enrollment</button>
    <button class="tab-btn">Insured Assets</button>
    <button class="tab-btn">Premium Tracking</button>
    <button class="tab-btn">Policy Status</button>
</div>

<!-- TABLE -->
<div class="table-card">
<table class="data-table">
    <thead>
        <tr>
            <th>POLICY NO.</th>
            <th>FARMER</th>
            <th>ASSET TYPE</th>
            <th>COVERAGE</th>
            <th>PREMIUM</th>
            <th>VALID UNTIL</th>
            <th>STATUS</th>
        </tr>
    </thead>

    <tbody>
        <?php foreach ($policies as $pol): ?>
        <tr>
            <td><?= htmlspecialchars($pol['policy_no']) ?></td>
            <td><?= htmlspecialchars($pol['farmer_name']) ?></td>
            <td><?= htmlspecialchars($pol['asset_type']) ?></td>
            <td>₱<?= number_format($pol['coverage'], 2) ?></td>
            <td>₱<?= number_format($pol['premium'], 2) ?></td>
            <td><?= date("M d, Y", strtotime($pol['valid_until'])) ?></td>

            <?php $statusClass = strtolower($pol['status']); ?>

            <td>
                <span class="status-badge <?= $statusClass ?>">
                    <?= $pol['status'] ?>
                </span>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
</div>

<!-- =========================
     MODAL (NEW ENROLLMENT)
========================= -->
<div id="insuranceModal" class="modal">
    <div class="modal-content">

        <h2>New Enrollment</h2>

        <form method="POST" action="save_insurance.php">

            <input type="text" name="farmer_name" placeholder="Farmer Name" required>
            <input type="text" name="asset_type" placeholder="Asset Type" required>
            <input type="number" name="coverage" placeholder="Coverage Amount" required>
            <input type="number" name="premium" placeholder="Premium" required>
            <input type="date" name="valid_until" required>

            <div class="modal-buttons">
                <button type="submit" class="btn btn-primary">Save</button>
                <button type="button" class="btn btn-secondary" onclick="closeInsuranceModal()">Cancel</button>
            </div>

        </form>

    </div>
</div>

<!-- JS -->
<script>
function openInsuranceModal() {
    document.getElementById("insuranceModal").style.display = "flex";
}

function closeInsuranceModal() {
    document.getElementById("insuranceModal").style.display = "none";
}
</script>