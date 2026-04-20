<?php
// dashboard.php - Dashboard page using AgriMS header template
session_start();
include 'database.php';
// TOTAL REGISTERED FARMERS
$farmersQuery = $conn->query("SELECT COUNT(*) AS total_farmers FROM farmers");
$farmersData = $farmersQuery->fetch_assoc();
$totalFarmers = $farmersData['total_farmers'];

// TOTAL PRODUCTION (example: sum of quantity column)
$productionQuery = $conn->query("SELECT SUM(quantity) AS total_production FROM production");
$productionData = $productionQuery->fetch_assoc();
$totalProduction = $productionData['total_production'] ?? 0;

// TOTAL INSURED ASSETS
$assetsQuery = $conn->query("SELECT COUNT(*) AS total_assets FROM assets");
$assetsData = $assetsQuery->fetch_assoc();
$totalAssets = $assetsData['total_assets'];
// Set page title
$page_title = "Dashboard";

// Include header
include 'header.php';

?>
<!-- Style -->
    <link rel="stylesheet" href="assets/css/dashboard.css">
<!-- Page Content -->
<div class="page-header">
    <div>
        <h1 class="page-title">Dashboard</h1>
        <p class="page-subtitle">Agricultural Operations Overview</p>
    </div>
    <div class="header-actions">
        <button class="btn btn-secondary">
            <span class="material-icons">calendar_today</span>
            Filter Period
            </button>
    </div>
</div>
<!-- Welcome Card -->
<div class="card welcome-card">
    <h2>Welcome to Agriculture Management System</h2>
    <p>Monitor and manage all agricultural operations from this centralized dashboard. Track farmer registrations, production data, insurance coverage, and more.</p>
</div>

<!-- Stats Grid -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon-wrapper green">👥</div>
        <div class="stat-label">Registered Farmers</div>
        <div class="stat-header">
            <div class="stat-value">
                <p><?php echo $totalFarmers; ?></p>
            </div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon-wrapper yellow">🌾</div>
        <div class="stat-label">Total Production (MT)</div>
        <div class="stat-header">
            <div class="stat-value">
                <p><?php echo $totalProduction; ?></p>
            </div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon-wrapper purple">🛡️</div>
        <div class="stat-label">Insured Assets</div>
        <div class="stat-header">
            <div class="stat-value">
                <p><?php echo $totalAssets; ?></p>
            </div>
            
        </div>
    </div>
</div>

