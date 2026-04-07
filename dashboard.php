<?php
// dashboard.php - Dashboard page using AgriMS header template
session_start();

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
        <button class="btn btn-primary">
            <span class="material-icons">download</span>
            Export Report
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
            <div class="stat-value">12,847</div>
            <div class="stat-trend">
                <span class="material-icons">trending_up</span>
                +5.2%
            </div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon-wrapper yellow">🌾</div>
        <div class="stat-label">Total Production (MT)</div>
        <div class="stat-header">
            <div class="stat-value">45,892</div>
            <div class="stat-trend">
                <span class="material-icons">trending_up</span>
                +12.1%
            </div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon-wrapper purple">🛡️</div>
        <div class="stat-label">Insured Assets</div>
        <div class="stat-header">
            <div class="stat-value">8,934</div>
            <div class="stat-trend">
                <span class="material-icons">trending_up</span>
                +8.4%
            </div>
        </div>
    </div>
</div>

