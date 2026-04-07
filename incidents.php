<?php
// incidents.php - Incidents & Claims Page
session_start();

$page_title = "Incidents & Claims";
$current_user = ['name' => 'Admin User', 'role' => 'System Administrator', 'initials' => 'AU'];

$stats = [
    ['icon' => 'warning', 'value' => '23', 'label' => 'Active Incidents', 'color' => 'red'],
    ['icon' => 'description', 'value' => '156', 'label' => 'Claims Filed', 'color' => 'orange'],
    ['icon' => 'check_circle', 'value' => '89', 'label' => 'Claims Approved', 'color' => 'green'],
    ['icon' => 'payments', 'value' => '₱12.4M', 'label' => 'Total Payout', 'color' => 'purple']
];

$incidents = [
    ['id' => 'INC-2026-001', 'type' => 'Typhoon', 'location' => 'Cagayan Valley', 'date' => 'Jan 20, 2026', 'affected' => '234 farmers', 'damage' => '₱8.5M', 'status' => 'Assessment'],
    ['id' => 'INC-2026-002', 'type' => 'Pest Outbreak', 'location' => 'Isabela', 'date' => 'Jan 25, 2026', 'affected' => '67 farmers', 'damage' => '₱1.2M', 'status' => 'Processing'],
    ['id' => 'INC-2026-003', 'type' => 'Fish Kill', 'location' => 'Laguna Lake', 'date' => 'Jan 28, 2026', 'affected' => '12 fisherfolk', 'damage' => '₱450K', 'status' => 'Assessment']
];

include 'header.php';
?>

<link rel="stylesheet" href="assets/css/incidents.css">
    

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
        <button class="btn btn-primary">
            <span class="material-icons">add</span>
            Report Incident
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
    <button class="tab-btn active">Incident Reports</button>
    <button class="tab-btn">Damage Assessment</button>
    <button class="tab-btn">Claims Filing</button>
    <button class="tab-btn">Payout Tracking</button>
</div>

<div class="table-card">
    <table class="data-table">
        <thead>
            <tr>
                <th>INCIDENT ID</th>
                <th>TYPE</th>
                <th>LOCATION</th>
                <th>DATE REPORTED</th>
                <th>AFFECTED</th>
                <th>EST. DAMAGE</th>
                <th>STATUS</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($incidents as $inc): ?>
            <tr>
                <td><?php echo $inc['id']; ?></td>
                <td><?php echo $inc['type']; ?></td>
                <td><?php echo $inc['location']; ?></td>
                <td><?php echo $inc['date']; ?></td>
                <td><?php echo $inc['affected']; ?></td>
                <td><?php echo $inc['damage']; ?></td>
                <td><span class="status-badge <?php echo strtolower($inc['status']); ?>"><?php echo $inc['status']; ?></span></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

