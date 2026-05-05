<?php
// programs.php - Programs & Interventions Page
session_start();
include 'database.php';

$page_title = "Programs & Interventions";

/* =========================
   STATS SECTION (SAFE)
========================= */

// Seeds
$seedData = ['total' => 0];
$seedQuery = "SELECT SUM(quantity) as total FROM distributions WHERE item LIKE '%Seed%'";
$seedResult = mysqli_query($conn, $seedQuery);
if ($seedResult) {
    $seedData = mysqli_fetch_assoc($seedResult);
}

// Equipment (basic logic)
$equipData = ['total' => 0];
$equipQuery = "SELECT SUM(quantity) as total FROM distributions WHERE item LIKE '%Equipment%'";
$equipResult = mysqli_query($conn, $equipQuery);
if ($equipResult) {
    $equipData = mysqli_fetch_assoc($equipResult);
}

// Trainings
$trainingData = ['total' => 0];
$trainingQuery = "SELECT COUNT(*) as total FROM trainings";
$trainingResult = mysqli_query($conn, $trainingQuery);
if ($trainingResult) {
    $trainingData = mysqli_fetch_assoc($trainingResult);
}

/* =========================
   DISTRIBUTIONS DATA
========================= */
$distributions = [];
$sql = "SELECT * FROM distributions ORDER BY date DESC";
$result = mysqli_query($conn, $sql);

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $distributions[] = $row;
    }
}

/* =========================
   TRAININGS DATA
========================= */
$trainings = [];
$sql2 = "SELECT * FROM trainings ORDER BY date DESC";
$result2 = mysqli_query($conn, $sql2);

if ($result2) {
    while ($row = mysqli_fetch_assoc($result2)) {
        $trainings[] = $row;
    }
}


/* =========================
   STATS ARRAY
========================= */
$stats = [
    ['icon' => 'inventory_2', 'value' => ($seedData['total'] ?? 0) . ' Bags', 'label' => 'Seeds Distributed', 'color' => 'green'],
    ['icon' => 'agriculture', 'value' => ($equipData['total'] ?? 0) . ' units', 'label' => 'Equipment Given', 'color' => 'orange'],
    ['icon' => 'menu_book', 'value' => ($trainingData['total'] ?? 0) . ' sessions', 'label' => 'Training Sessions', 'color' => 'purple']
];

include 'header.php';
?>

<link rel="stylesheet" href="assets/css/programs.css">
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

/* BUTTONS */
.btn-primary {
    background: #10b981;
    color: white;
    border: none;
    padding: 10px;
    border-radius: 8px;
    cursor: pointer;
}

.btn-primary:hover {
    background: #256428;
}

.btn-secondary {
    background: #ccc;
    color: black;
    border: none;
    padding: 10px;
    border-radius: 8px;
    cursor: pointer;
}

.btn-secondary:hover {
    background: #b3b3b3;
}
     .tab-content {
    display: none;
}

.tab-content.active {
    display: block;
}

.tab-btn.active {
    background: #10b981;
    color: #fff;
}
.filter-box {
    margin-bottom: 10px;
}

.filter-box select {
    padding: 8px;
    border-radius: 6px;
    border: 1px solid #ccc;
}
</style>
<div class="page-header">
    <div class="page-title-section">
        <h1>Programs & Interventions</h1>
        <p class="page-subtitle">Distribution, training, and extension services</p>
    </div>

    <div class="page-actions">
        <button class="btn btn-secondary" onclick="openBeneficiaries()">
        <span class="material-icons">description</span>
        Beneficiary List
    </button>
        <button class="btn btn-primary" onclick="openModal()">
            <span class="material-icons">add</span>
            New Distribution
        </button>
    </div>
</div>

<div id="distributionModal" class="modal">
    <div class="modal-content">

        <h2>New Distribution</h2>

        <form method="POST" action="save_distribution.php">

            <input type="text" name="program" placeholder="Program" required>
            <input type="text" name="item" placeholder="Item" required>
            <input type="number" name="quantity" placeholder="Quantity" required>
            <input type="number" name="beneficiaries" placeholder="Beneficiaries" required>
            <input type="date" name="date" required>

            <button type="submit" class="btn btn-primary">Save</button>
            <button type="button" class="btn btn-secondary" onclick="closeModal()">Cancel</button>

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
                <span class="material-icons"><?php echo $stat['icon']; ?></span>
            </div>
            <div class="stat-content">
                <div class="stat-value"><?php echo $stat['value']; ?></div>
                <div class="stat-label"><?php echo $stat['label']; ?></div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<!-- =========================
     TABS
========================= -->
<div class="tabs-container">

    <button class="tab-btn active" onclick="showTab('distribution', this)">
        Input Distribution
    </button>

    <button class="tab-btn" onclick="showTab('equipment', this)">
        Equipment
    </button>

    <button class="tab-btn" onclick="showTab('training', this)">
        Training
    </button>

    <button class="tab-btn" onclick="showTab('beneficiaries', this)">
        Beneficiaries
    </button>

</div>

<!-- =========================
     DISTRIBUTION TAB
========================= -->
<div id="distribution" class="tab-content active">
<table class="data-table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Program</th>
            <th>Item</th>
            <th>Quantity</th>
            <th>Beneficiaries</th>
            <th>Date</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($distributions as $dist): ?>
        <tr>
            <td><?php echo htmlspecialchars($dist['distribution_id']); ?></td>
            <td><?php echo htmlspecialchars($dist['program']); ?></td>
            <td><?php echo htmlspecialchars($dist['item']); ?></td>
            <td><?php echo htmlspecialchars($dist['quantity']); ?></td>
            <td><?php echo htmlspecialchars($dist['beneficiaries']); ?></td>
            <td><?php echo date("M d, Y", strtotime($dist['date'])); ?></td>

            <?php $statusClass = strtolower(str_replace(' ', '-', $dist['status'])); ?>

            <td>
                <span class="status-badge <?php echo $statusClass; ?>">
                    <?php echo htmlspecialchars($dist['status']); ?>
                </span>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
</div>

<!-- =========================
     EQUIPMENT TAB (STATIC for now)
========================= -->
<div id="equipment" class="tab-content">
<table class="data-table">
    <thead>
        <tr>
            <th>Equipment ID</th>
            <th>Name</th>
            <th>Type</th>
            <th>Quantity</th>
            <th>Assigned To</th>
            <th>Date Issued</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>EQ-001</td>
            <td>Hand Tractor</td>
            <td>Machinery</td>
            <td>5</td>
            <td>Farmers Assoc.</td>
            <td>Feb 01, 2026</td>
            <td>Active</td>
        </tr>
    </tbody>
</table>
</div>

<!-- =========================
     TRAINING TAB
========================= -->
<div id="training" class="tab-content">
<table class="data-table">
    <thead>
        <tr>
            <th>Training ID</th>
            <th>Title</th>
            <th>Location</th>
            <th>Trainer</th>
            <th>Participants</th>
            <th>Date</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($trainings as $trn): ?>
        <tr>
            <td><?php echo htmlspecialchars($trn['training_id']); ?></td>
            <td><?php echo htmlspecialchars($trn['title']); ?></td>
            <td><?php echo htmlspecialchars($trn['location']); ?></td>
            <td><?php echo htmlspecialchars($trn['trainer']); ?></td>
            <td><?php echo htmlspecialchars($trn['participants']); ?></td>
            <td><?php echo date("M d, Y", strtotime($trn['date'])); ?></td>
            <td><?php echo htmlspecialchars($trn['status']); ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
</div>

<!-- =========================
     BENEFICIARIES TAB
========================= -->
<div id="beneficiaries" class="tab-content">
<table class="data-table">
    <thead>
        <tr class="beneficiary-row" data-barangay="Barangay 1">
            <td>F-001</td>
            <td>Juan Dela Cruz</td>
            <td>Male</td>
            <td>09123456789</td>
            <td>Barangay 1</td>
            <td>Rice Program</td>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>F-001</td>
            <td>Juan Dela Cruz</td>
            <td>Male</td>
            <td>09123456789</td>
            <td>Barangay 1</td>
            <td>Rice Program</td>
        </tr>
    </tbody>
</table>
</div>

<!-- =========================
     TAB SCRIPT
========================= -->
<script>
function openModal() {
    document.getElementById("distributionModal").style.display = "flex";
}

function closeModal() {
    document.getElementById("distributionModal").style.display = "none";
}

function goToTab(tabId) {
    // switch tab content
    const tabs = document.querySelectorAll('.tab-content');
    tabs.forEach(tab => tab.classList.remove('active'));

    document.getElementById(tabId).classList.add('active');

    // optional: update active button
    const buttons = document.querySelectorAll('.tab-btn');
    buttons.forEach(btn => btn.classList.remove('active'));
}

function filterBeneficiaries() {
    let filter = document.getElementById("barangayFilter").value;
    let rows = document.querySelectorAll(".beneficiary-row");

    rows.forEach(row => {
        let barangay = row.getAttribute("data-barangay");

        if (filter === "all" || barangay === filter) {
            row.style.display = "";
        } else {
            row.style.display = "none";
        }
    });
}
    // Tables
function showTab(tabId, element) {

    // hide all tables
    const tabs = document.querySelectorAll('.tab-content');
    tabs.forEach(tab => {
        tab.classList.remove('active');
    });

    // remove active buttons
    const buttons = document.querySelectorAll('.tab-btn');
    buttons.forEach(btn => {
        btn.classList.remove('active');
    });

    // show selected table
    document.getElementById(tabId).classList.add('active');

    // activate clicked button
    element.classList.add('active');
}
</script>