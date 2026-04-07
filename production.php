<?php
// production.php - Production Management Page
session_start();

// Set page title
$page_title = "Production Management";

// User data
$current_user = [
    'name' => 'Admin User',
    'role' => 'System Administrator',
    'initials' => 'AU'
];

// Summary statistics
$stats = [
    [
        'icon' => 'agriculture',
        'value' => '23,450 MT',
        'label' => 'Crop Production',
        'color' => 'green'
    ],
    [
        'icon' => 'event',
        'value' => '156 farms',
        'label' => 'Due for Harvest',
        'color' => 'purple'
    ]
];

// Production data - In real app, this would come from database
$production_data = [
    [
        'farm_id' => 'FM-001234',
        'farmer' => 'Juan Dela Cruz',
        'crop_type' => 'Rice (NSIC Rc222)',
        'area' => '2.5 ha',
        'planting_date' => 'Nov 15, 2025',
        'expected_harvest' => 'Feb 20, 2026',
        'status' => 'Growing'
    ],
    [
        'farm_id' => 'FM-001235',
        'farmer' => 'Maria Santos',
        'crop_type' => 'Corn (IPB Var 6)',
        'area' => '1.8 ha',
        'planting_date' => 'Dec 01, 2025',
        'expected_harvest' => 'Mar 05, 2026',
        'status' => 'Growing'
    ],
    [
        'farm_id' => 'FM-001236',
        'farmer' => 'Pedro Reyes',
        'crop_type' => 'Rice (PSB Rc18)',
        'area' => '3.2 ha',
        'planting_date' => 'Oct 20, 2025',
        'expected_harvest' => 'Feb 10, 2026',
        'status' => 'Ready'
    ]
];

// // Include header
// include 'header.php';
include 'header.php';
?>
<!-- Style -->
    <link rel="stylesheet" href="assets/css/production.css">
<!-- Page Content -->
<div class="page-header">
    <div class="page-title-section">
        <h1>Production Management</h1>
        <p class="page-subtitle">Track crop</p>
    </div>
    <div class="page-actions">
        <button class="btn btn-secondary">
            <span class="material-icons">event</span>
            Planting Schedule
        </button>
        <button class="btn btn-primary">
            <span class="material-icons">add</span>
            Record Harvest
        </button>
    </div>
</div>

<!-- Stats Grid -->
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

<!-- Tabs -->
<div class="tabs-container">
    <button class="tab-btn active">Crop Production</button>
    <button class="tab-btn">Schedules</button>
    <button class="tab-btn">Harvest Records</button>
    
</div>

<!-- Data Table -->
<div class="table-card">
    <table class="data-table">
        <thead>
            <tr>
                <th>FARM ID</th>
                <th>FARMER</th>
                <th>CROP TYPE</th>
                <th>AREA</th>
                <th>PLANTING DATE</th>
                <th>EXPECTED HARVEST</th>
                <th>STATUS</th>
                <th>ACTIONS</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($production_data as $prod): ?>
            <tr>
                <td><?php echo $prod['farm_id']; ?></td>
                <td><?php echo $prod['farmer']; ?></td>
                <td><?php echo $prod['crop_type']; ?></td>
                <td><?php echo $prod['area']; ?></td>
                <td><?php echo $prod['planting_date']; ?></td>
                <td><?php echo $prod['expected_harvest']; ?></td>
                <td>
                    <span class="status-badge <?php echo strtolower($prod['status']); ?>">
                        <?php echo $prod['status']; ?>
                    </span>
                </td>
                <td>
                    <div class="action-buttons">
                        <button class="action-btn view" title="View Details">
                            <span class="material-icons">visibility</span>
                        </button>
                        <button class="action-btn edit" title="Edit">
                            <span class="material-icons">edit</span>
                        </button>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script>
    // Tab switching functionality
    document.querySelectorAll('.tab-btn').forEach(tab => {
        tab.addEventListener('click', function() {
            // Remove active class from all tabs
            document.querySelectorAll('.tab-btn').forEach(t => t.classList.remove('active'));
            // Add active class to clicked tab
            this.classList.add('active');
            
            // Here you would normally load different data based on the tab
            console.log('Switched to:', this.textContent);
        });
    });

    // Action button handlers
    document.querySelectorAll('.action-btn.view').forEach(btn => {
        btn.addEventListener('click', function() {
            const row = this.closest('tr');
            const farmId = row.cells[0].textContent;
            alert('View production details for: ' + farmId);
            // In real app, open modal or navigate to detail page
        });
    });

    document.querySelectorAll('.action-btn.edit').forEach(btn => {
        btn.addEventListener('click', function() {
            const row = this.closest('tr');
            const farmId = row.cells[0].textContent;
            alert('Edit production data for: ' + farmId);
            // In real app, open edit modal or form
        });
    });

    // Planting Schedule button
    document.querySelector('.btn-secondary').addEventListener('click', function() {
        alert('Opening Planting Schedule...');
        // In real app, navigate to schedule page or open calendar
    });

    // Record Harvest button
    document.querySelector('.btn-primary').addEventListener('click', function() {
        alert('Opening Harvest Recording Form...');
        // In real app, open modal with harvest form
    });
</script>

