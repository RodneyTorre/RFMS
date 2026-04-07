<?php
// programs.php - Programs & Interventions Page
session_start();

$page_title = "Programs & Interventions";
$current_user = [
    'name' => 'Admin User',
    'role' => 'System Administrator',
    'initials' => 'AU'
];

// Summary statistics
$stats = [
    ['icon' => 'inventory_2', 'value' => '2,340 bags', 'label' => 'Seeds Distributed', 'color' => 'green'],
    ['icon' => 'agriculture', 'value' => '156 units', 'label' => 'Equipment Given', 'color' => 'orange'],
    ['icon' => 'menu_book', 'value' => '89 sessions', 'label' => 'Training Sessions', 'color' => 'purple']
];

// Distribution data
$distributions = [
    ['id' => 'DIST-2026-001', 'program' => 'Rice Program', 'item' => 'Certified Seeds', 'quantity' => '500 bags', 'beneficiaries' => '250 farmers', 'date' => 'Jan 25, 2026', 'status' => 'Completed'],
    ['id' => 'DIST-2026-002', 'program' => 'Corn Program', 'item' => 'Hybrid Seeds', 'quantity' => '300 bags', 'beneficiaries' => '150 farmers', 'date' => 'Jan 28, 2026', 'status' => 'Completed'],
    ['id' => 'DIST-2026-003', 'program' => 'Fertilizer Support', 'item' => 'Urea (46-0-0)', 'quantity' => '1,000 bags', 'beneficiaries' => '500 farmers', 'date' => 'Feb 01, 2026', 'status' => 'Ongoing']
];

include 'header.php';
?>

<link rel="stylesheet" href="assets/css/programs.css">
    

<div class="page-header">
    <div class="page-title-section">
        <h1>Programs & Interventions</h1>
        <p class="page-subtitle">Distribution, training, and extension services</p>
    </div>
    <div class="page-actions">
        <button class="btn btn-secondary">
            <span class="material-icons">description</span>
            Beneficiary List
        </button>
        <button class="btn btn-primary">
            <span class="material-icons">add</span>
            New Distribution
        </button>
    </div>
</div>

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

<div class="tabs-container">
    <button class="tab-btn active">Input Distribution</button>
    <button class="tab-btn">Equipment</button>
    <button class="tab-btn">Training</button>
    <button class="tab-btn">Beneficiaries</button>
</div>

<div class="table-card">
    <table class="data-table">
        <thead>
            <tr>
                <th>DISTRIBUTION ID</th>
                <th>PROGRAM</th>
                <th>ITEM</th>
                <th>QUANTITY</th>
                <th>BENEFICIARIES</th>
                <th>DATE</th>
                <th>STATUS</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($distributions as $dist): ?>
            <tr>
                <td><?php echo $dist['id']; ?></td>
                <td><?php echo $dist['program']; ?></td>
                <td><?php echo $dist['item']; ?></td>
                <td><?php echo $dist['quantity']; ?></td>
                <td><?php echo $dist['beneficiaries']; ?></td>
                <td><?php echo $dist['date']; ?></td>
                <td><span class="status-badge <?php echo strtolower($dist['status']); ?>"><?php echo $dist['status']; ?></span></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

