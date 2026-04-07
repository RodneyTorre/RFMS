<?php
// maps.php - Maps & GIS Page
session_start();

$page_title = "Maps & GIS";
$current_user = ['name' => 'Admin User', 'role' => 'System Administrator', 'initials' => 'AU'];

include 'header.php';
?>

<link rel="stylesheet" href="assets/css/maps.css">

<div class="page-header">
    <div class="page-title-section">
        <h1>Maps & GIS</h1>
        <p class="page-subtitle">Geographic visualization and spatial analysis</p>
    </div>
    <div class="page-actions">
        <button class="btn btn-secondary">
            <span class="material-icons">layers</span>
            Layers
        </button>
        <button class="btn btn-primary">
            <span class="material-icons">download</span>
            Export Map
        </button>
    </div>
</div>

<div class="tabs-container">
    <button class="tab-btn active">Farm Location Map</button>
    <button class="tab-btn">Disaster & Risk Maps</button>
    <button class="tab-btn">Production Density</button>
</div>

<div class="map-container">
    <div class="map-controls">
        <div class="control-group">
            <label>Layer Selection</label>
            <div class="checkbox-list">
                <label><input type="checkbox" checked> Farm Boundaries</label>
                <label><input type="checkbox" checked> Crop Types</label>
                <label><input type="checkbox"> Irrigation Systems</label>
                <label><input type="checkbox"> Water Bodies</label>
            </div>
        </div>
        <div class="control-group">
            <label>Region Filter</label>
            <select>
                <option>All Barangay</option>
                <option>Baogo</option>
                <option>Ilaya</option>
                <option>Nabuad</option>
            </select>
        </div>
    </div>
    
    <div class="map-display">
        <span class="material-icons">map</span>
        <h3>Interactive GIS Map</h3>
        <p>Farm locations, and production zones</p>
        <div class="map-stats">
            <div class="map-stat">
                <span class="material-icons">location_on</span>
                <span>12,847 Farms Mapped</span>
            </div>
            <div class="map-stat">
                <span class="material-icons">warning</span>
                <span>23 Risk Zones</span>
            </div>
        </div>
    </div>
</div>

