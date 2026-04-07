<?php
// insurance.php - Insurance Management Page
session_start();

$page_title = "Insurance Management";
$current_user = [
    'name' => 'Admin User',
    'role' => 'System Administrator',
    'initials' => 'AU'
];

$stats = [
    ['icon' => 'shield', 'value' => '8,934', 'label' => 'Total Insured', 'color' => 'green'],
    ['icon' => 'payments', 'value' => '₱45.2M', 'label' => 'Premium Subsidies', 'color' => 'blue'],
    ['icon' => 'check_circle', 'value' => '7,823', 'label' => 'Active Policies', 'color' => 'purple'],
    ['icon' => 'schedule', 'value' => '234', 'label' => 'Expiring Soon', 'color' => 'orange']
];

$policies = [
    ['policy' => 'POL-2026-001234', 'name' => 'Juan Dela Cruz', 'asset' => 'Rice (2.5 ha)', 'coverage' => '₱125,000', 'premium' => '₱5,000', 'valid' => 'May 15, 2026', 'status' => 'Active'],
    ['policy' => 'POL-2026-001235', 'name' => 'Maria Santos', 'asset' => 'Corn (1.8 ha)', 'coverage' => '₱90,000', 'premium' => '₱3,600', 'valid' => 'Jun 20, 2026', 'status' => 'Active'],
    ['policy' => 'POL-2026-001236', 'name' => 'Pedro Reyes', 'asset' => 'Fishpond', 'coverage' => '₱200,000', 'premium' => '₱8,000', 'valid' => 'Mar 10, 2026', 'status' => 'Expiring']
];

include 'header.php';
?>

<link rel="stylesheet" href="assets/css/insurance.css">



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
        <button class="btn btn-primary">
            <span class="material-icons">add</span>
            New Enrollment
        </button>
    </div>
</div>

<div class="stats-grid">
    <?php foreach ($stats as $stat): ?>
    <div class="stat-card">
        <div class="stat-icon <?php echo $stat['color']; ?>">
            <span class="material-icons"><?php echo $stat['icon']; ?></span>
        </div>
        <div>
            <div class="stat-value"><?php echo $stat['value']; ?></div>
            <div class="stat-label"><?php echo $stat['label']; ?></div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<div class="tabs-container">
    <button class="tab-btn active">Insurance Enrollment</button>
    <button class="tab-btn">Insured Assets</button>
    <button class="tab-btn">Premium Tracking</button>
    <button class="tab-btn">Policy Status</button>
</div>

<div class="table-card">
    <table class="data-table">
        <thead>
            <tr>
                <th>POLICY NO.</th>
                <th>FARMER/FISHERFOLK</th>
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
                <td><?php echo $pol['policy']; ?></td>
                <td><?php echo $pol['name']; ?></td>
                <td><?php echo $pol['asset']; ?></td>
                <td><?php echo $pol['coverage']; ?></td>
                <td><?php echo $pol['premium']; ?></td>
                <td><?php echo $pol['valid']; ?></td>
                <td><span class="status-badge <?php echo strtolower($pol['status']); ?>"><?php echo $pol['status']; ?></span></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

