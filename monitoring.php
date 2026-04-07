<?php
// monitoring.php - Monitoring & Field Reports Page
session_start();

// Set page title
$page_title = "Monitoring & Field Reports";

// User data
$current_user = [
    'name' => 'Admin User',
    'role' => 'System Administrator',
    'initials' => 'AU'
];

// Field reports data - In real app, this would come from database
$reports = [
    [
        'id' => 1,
        'icon' => 'description',
        'icon_bg' => 'green',
        'date' => 'Feb 03, 2026',
        'title' => 'Rice Field Inspection - Brgy. San Jose',
        'description' => 'Crop health assessment completed. Overall condition: Good. Minor pest presence detected.',
        'location' => '14.5995°N, 120.9842°E',
        'officer' => 'Officer: J. Santos',
        'photos' => 5,
        'type' => 'field_visit'
    ],
    [
        'id' => 2,
        'icon' => 'water_drop',
        'icon_bg' => 'blue',
        'date' => 'Feb 02, 2026',
        'title' => 'Water Quality Monitoring - Fishpond A-12',
        'description' => 'pH: 7.2, DO: 6.5 mg/L, Temperature: 28°C. All parameters within optimal range.',
        'location' => '14.6091°N, 121.0223°E',
        'officer' => 'Tech: M. Reyes',
        'parameters' => [
            ['label' => 'pH: 7.2', 'color' => 'blue'],
            ['label' => 'DO: 6.5', 'color' => 'blue'],
            ['label' => 'Temp: 28°C', 'color' => 'blue']
        ],
        'type' => 'water_quality'
    ],
    [
        'id' => 3,
        'icon' => 'bug_report',
        'icon_bg' => 'yellow',
        'date' => 'Feb 01, 2026',
        'title' => 'Pest Monitoring - Corn Fields',
        'description' => 'Fall armyworm larvae observed. Damage: 15%. Recommended immediate intervention.',
        'location' => '16.9754°N, 121.8107°E',
        'officer' => 'Specialist: A. Cruz',
        'action_required' => true,
        'type' => 'pest'
    ]
];

// Include header
include 'header.php';
?>

<link rel="stylesheet" href="assets/css/monitoring.css">
    

<!-- Page Content -->
<div class="page-header">
    <div class="page-title-section">
        <h1>Monitoring & Field Reports</h1>
        <p class="page-subtitle">Field visits, conditions, and monitoring data</p>
    </div>
    <div class="page-actions">
        <button class="btn btn-secondary">
            <span class="material-icons">upload</span>
            Upload Photos
        </button>
        <button class="btn btn-primary">
            <span class="material-icons">add</span>
            New Report
        </button>
    </div>
</div>

<!-- Tabs -->
<div class="tabs-container">
    <button class="tab-btn active">Field Visit Reports</button>
    <button class="tab-btn">Crop Condition</button>
    <button class="tab-btn">Water Quality</button>
    <button class="tab-btn">Photo & GPS</button>
    
</div>

<!-- Reports Grid -->
<div class="reports-grid">
    <?php foreach ($reports as $report): ?>
    <div class="report-card">
        <div class="report-header">
            <div class="report-icon <?php echo $report['icon_bg']; ?>">
                <span class="material-icons"><?php echo $report['icon']; ?></span>
            </div>
            <div class="report-date"><?php echo $report['date']; ?></div>
        </div>

        <h3 class="report-title"><?php echo $report['title']; ?></h3>
        <p class="report-description"><?php echo $report['description']; ?></p>

        <div class="report-meta">
            <div class="report-meta-item">
                <span class="material-icons">location_on</span>
                <span><?php echo $report['location']; ?></span>
            </div>
            <div class="report-meta-item">
                <span class="material-icons">person</span>
                <span><?php echo $report['officer']; ?></span>
            </div>
        </div>

        <?php if (isset($report['photos'])): ?>
        <div class="photo-indicators">
            <div class="photo-thumb">
                <span class="material-icons">photo_camera</span>
            </div>
            <div class="photo-thumb">
                <span class="material-icons">photo_camera</span>
            </div>
            <div class="photo-thumb">
                <span class="material-icons">photo_camera</span>
            </div>
            <span class="photo-count">+<?php echo $report['photos'] - 3; ?> more</span>
        </div>
        <?php endif; ?>

        <?php if (isset($report['parameters'])): ?>
        <div class="water-parameters">
            <?php foreach ($report['parameters'] as $param): ?>
            <span class="param-badge"><?php echo $param['label']; ?></span>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <?php if (isset($report['action_required']) && $report['action_required']): ?>
        <div class="action-required-badge">
            <span class="material-icons">warning</span>
            Action Required
        </div>
        <?php endif; ?>
    </div>
    <?php endforeach; ?>
</div>

<script>
    // Tab switching functionality
    document.querySelectorAll('.tab-btn').forEach(tab => {
        tab.addEventListener('click', function() {
            // Remove active class from all tabs
            document.querySelectorAll('.tab-btn').forEach(t => t.classList.remove('active'));
            // Add active class to clicked tab
            this.classList.add('active');
            
            console.log('Switched to:', this.textContent);
            // In real app, load different report types based on tab
        });
    });

    // Upload Photos button
    document.querySelector('.btn-secondary').addEventListener('click', function() {
        // Create file input
        const input = document.createElement('input');
        input.type = 'file';
        input.multiple = true;
        input.accept = 'image/*';
        
        input.onchange = function(e) {
            const files = e.target.files;
            alert(`Selected ${files.length} photo(s) for upload`);
            // In real app, upload files to server
        };
        
        input.click();
    });

    // New Report button
    document.querySelector('.btn-primary').addEventListener('click', function() {
        alert('Opening New Field Report Form...');
        // In real app, open modal with report form
    });

    // Report card click handler
    document.querySelectorAll('.report-card').forEach(card => {
        card.style.cursor = 'pointer';
        card.addEventListener('click', function() {
            const title = this.querySelector('.report-title').textContent;
            alert('Opening detailed view for: ' + title);
            // In real app, navigate to report detail page or open modal
        });
    });
</script>
