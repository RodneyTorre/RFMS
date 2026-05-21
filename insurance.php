<?php
session_start();
include 'database.php';

$page_title = "Insurance Management";

/* =========================
   FETCH INSURANCE POLICIES
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
   STATS SECTION
========================= */

$totalInsured = mysqli_fetch_assoc(
    mysqli_query(
        $conn,
        "SELECT COUNT(*) as total FROM insurance_policies WHERE valid_until >= CURDATE()"
    )
)['total'] ?? 0;

$totalPayment = mysqli_fetch_assoc(
    mysqli_query(
        $conn,
        "SELECT SUM(payment) as total FROM insurance_policies"
    )
)['total'] ?? 0;

$activePolicies = mysqli_fetch_assoc(
    mysqli_query(
        $conn,
        "SELECT COUNT(*) as total 
         FROM insurance_policies
         WHERE valid_until >= CURDATE()"
    )
)['total'] ?? 0;

$expiringSoon = mysqli_fetch_assoc(
    mysqli_query(
        $conn,
        "SELECT COUNT(*) as total
         FROM insurance_policies
         WHERE valid_until BETWEEN CURDATE()
         AND DATE_ADD(CURDATE(), INTERVAL 30 DAY)"
    )
)['total'] ?? 0;

/* =========================
   STATS ARRAY
========================= */

$stats = [

    [
        'icon' => 'shield',
        'value' => $totalInsured,
        'label' => 'Total Insured',
        'color' => 'green'
    ],

    [
        'icon' => 'payments',
        'value' => '₱' . number_format($totalPayment, 2),
        'label' => 'Total Payments',
        'color' => 'blue'
    ],

    [
        'icon' => 'check_circle',
        'value' => $activePolicies,
        'label' => 'Active Policies',
        'color' => 'purple'
    ],

    [
        'icon' => 'schedule',
        'value' => $expiringSoon,
        'label' => 'Expiring Soon',
        'color' => 'orange'
    ]

];

include 'header.php';
?>

<link rel="stylesheet" href="assets/css/insurance.css">

<style>

/* =========================
   MODAL
========================= */

.modal{
    display:none;
    position:fixed;
    inset:0;
    background:rgba(0,0,0,0.5);
    justify-content:center;
    align-items:center;
    z-index:999;
}

.modal-card{
    background:#fff;
    width:450px;
    border-radius:16px;
    padding:25px;
    box-shadow:0 10px 30px rgba(0,0,0,0.2);
    animation:fadeIn .3s ease;
}

.modal-header{
    margin-bottom:20px;
}

.modal-header h2{
    margin:0;
    color:#10b981;
    font-size:26px;
}

.modal-header p{
    margin-top:5px;
    color:#666;
    font-size:14px;
}

.modal-form{
    display:flex;
    flex-direction:column;
    gap:15px;
}

.form-group{
    display:flex;
    flex-direction:column;
}

.form-group label{
    margin-bottom:6px;
    font-weight:600;
    color:#333;
}

.form-group input,
.form-group select{
    padding:12px;
    border:1px solid #d1d5db;
    border-radius:10px;
    font-size:14px;
    width:100%;
    background:white;
}

.form-group input:focus,
.form-group select:focus{
    border-color:#10b981;
    outline:none;
    box-shadow:0 0 0 3px rgba(16,185,129,0.2);
}

.form-row{
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:15px;
}

.modal-actions{
    display:flex;
    justify-content:flex-end;
    gap:10px;
    margin-top:10px;
}

.btn-save{
    background:#10b981;
    color:white;
    border:none;
    padding:12px 18px;
    border-radius:10px;
    cursor:pointer;
    font-weight:600;
}

.btn-save:hover{
    background:#059669;
}

.btn-cancel{
    background:#e5e7eb;
    border:none;
    padding:12px 18px;
    border-radius:10px;
    cursor:pointer;
    font-weight:600;
}

.btn-cancel:hover{
    background:#d1d5db;
}

/* =========================
   TABS
========================= */

.tabs-container{
    margin-top:20px;
}

.tab-btn{
    padding:12px 20px;
    border:none;
    border-radius:10px;
    background:#f3f4f6;
    cursor:pointer;
    font-weight:600;
}

.tab-btn.active{
    background:#10b981;
    color:#fff;
}

/* =========================
   TABLE
========================= */

.table-card{
    background:#fff;
    border-radius:14px;
    padding:20px;
    overflow-x:auto;
    margin-top:20px;
}

.data-table{
    width:100%;
    border-collapse:collapse;
}

.data-table th{
    background:#f3f4f6;
    padding:14px;
    text-align:left;
    font-size:14px;
}

.data-table td{
    padding:14px;
    border-bottom:1px solid #e5e7eb;
    font-size:14px;
}

.status-badge{
    padding:6px 12px;
    border-radius:30px;
    font-size:12px;
    font-weight:600;
}

.status-badge.active{
    background:#dcfce7;
    color:#166534;
}

.status-badge.expired{
    background:#fee2e2;
    color:#991b1b;
}

.status-badge.expiring-soon{
    background:#fef3c7;
    color:#92400e;
}

/* =========================
   ANIMATION
========================= */

@keyframes fadeIn{
    from{
        opacity:0;
        transform:translateY(-10px);
    }

    to{
        opacity:1;
        transform:translateY(0);
    }
}

</style>

<!-- =========================
     PAGE HEADER
========================= -->

<div class="page-header">

    <div class="page-header-left">
        <h1>Insurance</h1>
        <p>Enrollment, policies, and payment tracking</p>
    </div>

    <div class="page-actions">

        <button class="btn btn-primary" onclick="openInsuranceModal()">
            <span class="material-icons">add</span>
            New Enrollment
        </button>

    </div>

</div>

<!-- =========================
     MODAL
========================= -->

<div id="insuranceModal" class="modal">

    <div class="modal-card">

        <div class="modal-header">
            <h2>New Insurance Enrollment</h2>
            <p>Register a new insurance policy</p>
        </div>

        <form method="POST"
              action="save_insurance.php"
              class="modal-form">

            <div class="form-group">
                <label>Farmer Name</label>
                <input type="text"
                       name="farmer_name"
                       required>
            </div>

            <div class="form-group">
                <label>Asset Type</label>
                <input type="text"
                       name="asset_type"
                       required>
            </div>

            <div class="form-row">

                <div class="form-group">
                    <label>Coverage Amount</label>
                    <input type="number"
                           name="coverage"
                           required>
                </div>

                <div class="form-group">
                    <label>Payment</label>
                    <input type="number"
                           name="payment"
                           required>
                </div>

            </div>

            <div class="form-group">
                <label>Valid Until</label>
                <input type="date"
                       name="valid_until"
                       required>
            </div>

            <div class="modal-actions">

                <button type="submit"
                        class="btn-save">
                    Save Enrollment
                </button>

                <button type="button"
                        class="btn-cancel"
                        onclick="closeInsuranceModal()">
                    Cancel
                </button>

            </div>

        </form>

    </div>

</div>

<!-- =========================
     STATS
========================= -->

<div class="stats-grid">

    <?php foreach ($stats as $stat): ?>

        <div class="stat-card">

            <div class="stat-icon <?php echo $stat['color']; ?>">

                <span class="material-icons">
                    <?php echo $stat['icon']; ?>
                </span>

            </div>

            <div class="stat-content">

                <div class="stat-value">
                    <?php echo $stat['value']; ?>
                </div>

                <div class="stat-label">
                    <?php echo $stat['label']; ?>
                </div>

            </div>

        </div>

    <?php endforeach; ?>

</div>

<!-- =========================
     TABS
========================= -->

<div class="tabs-container">

    <button class="tab-btn active">
        Insurance Enrollment
    </button>

</div>

<!-- =========================
     TABLE
========================= -->

<div class="table-card">

    <table class="data-table">

        <thead>

            <tr>
                <th>Policy No.</th>
                <th>Farmer</th>
                <th>Asset Type</th>
                <th>Coverage</th>
                <th>Payment</th>
                <th>Valid Until</th>
                <th>Status</th>
            </tr>

        </thead>

        <tbody>

            <?php foreach ($policies as $pol): ?>

            <tr>

                <td>
                    <?php echo htmlspecialchars($pol['policy_no']); ?>
                </td>

                <td>
                    <?php echo htmlspecialchars($pol['farmer_name']); ?>
                </td>

                <td>
                    <?php echo htmlspecialchars($pol['asset_type']); ?>
                </td>

                <td>
                    ₱<?php echo number_format($pol['coverage'], 2); ?>
                </td>

                <td>
                    ₱<?php echo number_format($pol['payment'], 2); ?>
                </td>

                <td>
                    <?php echo date(
                        "M d, Y",
                        strtotime($pol['valid_until'])
                    ); ?>
                </td>

                <?php

                $today = date("Y-m-d");
                $validUntil = $pol['valid_until'];

                if ($validUntil < $today) {

                    $displayStatus = "Expired";

                } elseif (
                    $validUntil <= date(
                        "Y-m-d",
                        strtotime("+30 days")
                    )
                ) {

                    $displayStatus = "Expiring Soon";

                } else {

                    $displayStatus = "Active";
                }

                $statusClass = strtolower(
                    str_replace(' ', '-', $displayStatus)
                );

                ?>

                <td>

                    <span class="status-badge <?php echo $statusClass; ?>">
                        <?php echo $displayStatus; ?>
                    </span>

                </td>

            </tr>

            <?php endforeach; ?>

        </tbody>

    </table>

</div>

<!-- =========================
     SCRIPT
========================= -->

<script>

function openInsuranceModal() {

    document.getElementById(
        "insuranceModal"
    ).style.display = "flex";
}

function closeInsuranceModal() {

    document.getElementById(
        "insuranceModal"
    ).style.display = "none";
}

window.onclick = function(event) {

    const modal =
        document.getElementById("insuranceModal");

    if (event.target === modal) {

        closeInsuranceModal();
    }
}

</script>