<?php
// reports.php - Reports & Analytics Page
session_start();

$page_title = "Reports & Analytics";
$current_user = ['name' => 'Admin User', 'role' => 'System Administrator', 'initials' => 'AU'];

$reports = [
    ['icon' => 'bar_chart', 'title' => 'Quarterly Production Report', 'description' => 'Comprehensive production data across all sectors', 'color' => 'green'],
    ['icon' => 'shield', 'title' => 'Insurance Coverage Report', 'description' => 'Active policies, premiums, and coverage analysis', 'color' => 'green'],
    ['icon' => 'inventory_2', 'title' => 'Program Effectiveness Report', 'description' => 'Distribution metrics and beneficiary outcomes', 'color' => 'green'],
    ['icon' => 'warning', 'title' => 'Disaster Impact Assessment', 'description' => 'Incident summary and claims analysis', 'color' => 'green']
];

include 'header.php';
?>

<link rel="stylesheet" href="assets/css/reports.css">

<div class="page-header">
    <div class="page-title-section">
        <h1>Reports & Analytics</h1>
        <p class="page-subtitle">Generate and export comprehensive reports</p>
    </div>
    <div class="page-actions">
        <button class="btn btn-secondary">
            <span class="material-icons">date_range</span>
            Date Range
        </button>
        <button class="btn btn-primary">
            <span class="material-icons">download</span>
            Export All
        </button>
    </div>
</div>

<div class="tabs-container">
    <button class="tab-btn active">Production Reports</button>
    <button class="tab-btn">Insurance Reports</button>
    <button class="tab-btn">Program Reports</button>
    <button class="tab-btn">Custom Builder</button>
</div>

<div class="reports-grid">
    <?php foreach ($reports as $report): ?>
    <div class="report-card">
        <div class="report-icon-wrapper">
            <span class="material-icons"><?php echo $report['icon']; ?></span>
        </div>
        <h3><?php echo $report['title']; ?></h3>
        <p><?php echo $report['description']; ?></p>
        <div class="report-actions">
            <button class="btn btn-sm btn-secondary">
                <span class="material-icons">visibility</span>
                Preview
            </button>
            <button class="btn btn-sm btn-primary">
                <span class="material-icons">download</span>
                Generate
            </button>
        </div>
    </div>
    <?php endforeach; ?>
</div>

