<?php
session_start();
include 'database.php';

$page_title = "Incidents & Claims";

/* =========================
   FETCH INCIDENTS
========================= */
$incidents = [];

$sql = "SELECT * FROM incidents ORDER BY date_reported DESC";
$result = mysqli_query($conn, $sql);

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $incidents[] = $row;
    }
}

/* =========================
   STATS
========================= */
$totalActive = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM incidents WHERE status='Assessment'"))['total'] ?? 0;

$totalClaims = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM incidents"))['total'] ?? 0;

$totalApproved = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM incidents WHERE status='Approved'"))['total'] ?? 0;

$totalPayout = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(damage) as total FROM incidents WHERE status='Approved'"))['total'] ?? 0;

include 'header.php';
?>

<link rel="stylesheet" href="assets/css/incidents.css">
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
        <h1>Incidents & Claims</h1>
        <p class="page-subtitle">Disaster reports, assessments, and claims</p>
    </div>

    <div class="page-actions">
        <button class="btn btn-secondary">
            <span class="material-icons">filter_list</span>
            Filter Type
        </button>

        <button class="btn btn-primary" onclick="openIncidentModal()">
            <span class="material-icons">add</span>
            Report Incident
        </button>
    </div>

</div>

<!-- STATS -->
<div class="stats-grid">

    <div class="stat-card">
        <div class="stat-icon red"><span class="material-icons">warning</span></div>
        <div>
            <div class="stat-value"><?= $totalActive ?></div>
            <div class="stat-label">Active Incidents</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon orange"><span class="material-icons">description</span></div>
        <div>
            <div class="stat-value"><?= $totalClaims ?></div>
            <div class="stat-label">Claims Filed</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon green"><span class="material-icons">check_circle</span></div>
        <div>
            <div class="stat-value"><?= $totalApproved ?></div>
            <div class="stat-label">Claims Approved</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon purple"><span class="material-icons">payments</span></div>
        <div>
            <div class="stat-value">₱<?= number_format($totalPayout, 2) ?></div>
            <div class="stat-label">Total Payout</div>
        </div>
    </div>

</div>

<!-- TABS -->
<div class="tabs-container">

    <button class="tab-btn active" onclick="filterIncidentTab('all', this)">
        Incident Reports
    </button>

    <button class="tab-btn" onclick="filterIncidentTab('assessment', this)">
        Damage Assessment
    </button>

    <button class="tab-btn" onclick="filterIncidentTab('processing', this)">
        Claims Filing
    </button>

    <button class="tab-btn" onclick="filterIncidentTab('approved', this)">
        Payout Tracking
    </button>

</div>

<!-- TABLE -->
<div class="table-card">
<table class="data-table">
    <thead>
        <tr>
            <th>ID</th>
            <th>TYPE</th>
            <th>LOCATION</th>
            <th>DATE</th>
            <th>AFFECTED</th>
            <th>DAMAGE</th>
            <th>STATUS</th>
        </tr>
    </thead>

    <tbody>
        <?php foreach ($incidents as $inc): ?>
        <tr 
            class="incident-row"
            data-status="<?= strtolower($inc['status']) ?>"
        >
            <td><?= htmlspecialchars($inc['incident_code']) ?></td>
            <td><?= htmlspecialchars($inc['type']) ?></td>
            <td><?= htmlspecialchars($inc['location']) ?></td>
            <td><?= date("M d, Y", strtotime($inc['date_reported'])) ?></td>
            <td><?= htmlspecialchars($inc['affected']) ?></td>
            <td>₱<?= number_format($inc['damage'], 2) ?></td>

            <td>
                <span class="status-badge <?= strtolower($inc['status']) ?>">
                    <?= $inc['status'] ?>
                </span>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>

</table>
</div>

<!-- =========================
     INCIDENT MODAL
========================= -->
<div id="incidentModal" class="modal">
    <div class="modal-content">

        <h2>Report Incident</h2>

        <form method="POST" action="save_incident.php">

            <input type="text" name="type" placeholder="Incident Type" required>
            <input type="text" name="location" placeholder="Location" required>
            <input type="date" name="date_reported" required>
            <input type="text" name="affected" placeholder="Affected Farmers/Fisherfolk" required>
            <input type="number" name="damage" placeholder="Estimated Damage" required>

            <div class="modal-buttons">
                <button type="submit" class="btn btn-primary">Save</button>
                <button type="button" class="btn btn-secondary" onclick="closeIncidentModal()">Cancel</button>
            </div>

        </form>

    </div>
</div>

<!-- JS -->
<script>

function openIncidentModal() {
    document.getElementById("incidentModal").style.display = "flex";
}

function closeIncidentModal() {
    document.getElementById("incidentModal").style.display = "none";
}

function filterIncidentTab(type, element) {

    const rows = document.querySelectorAll(".incident-row");

    rows.forEach(row => {

        const status = row.getAttribute("data-status");

        let show = false;

        if (type === "all") show = true;
        else if (type === "assessment") show = status === "assessment";
        else if (type === "processing") show = status === "processing";
        else if (type === "approved") show = status === "approved";

        row.style.display = show ? "" : "none";
    });

    document.querySelectorAll(".tab-btn").forEach(btn => btn.classList.remove("active"));
    element.classList.add("active");
}

</script>