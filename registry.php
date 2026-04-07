<?php
// registry.php - Registry Management Page
session_start();

// Set page title
$page_title = "Registry Management";

// User data
$current_user = [
    'name' => 'Admin User',
    'role' => 'System Administrator',
    'initials' => 'AU'
];

// Sample farmer data - In real app, this would come from database
$farmers = [
    [
        'rsbsa' => 'RSBSA-2024-001234',
        'name' => 'Juan Dela Cruz',
        'barangay' => 'San Jose',
        'farm_size' => '2.5 ha',
        'crops' => 'Rice, Corn',
        'status' => 'Active'
    ],
    [
        'rsbsa' => 'RSBSA-2024-001235',
        'name' => 'Maria Santos',
        'barangay' => 'Santa Maria',
        'farm_size' => '1.8 ha',
        'crops' => 'Vegetables',
        'status' => 'Active'
    ],
    [
        'rsbsa' => 'RSBSA-2024-001236',
        'name' => 'Pedro Reyes',
        'barangay' => 'San Miguel',
        'farm_size' => '3.2 ha',
        'crops' => 'Rice',
        'status' => 'Active'
    ],
    [
        'rsbsa' => 'RSBSA-2024-001237',
        'name' => 'Ana Garcia',
        'barangay' => 'Santo Niño',
        'farm_size' => '1.5 ha',
        'crops' => 'Corn, Cassava',
        'status' => 'Pending'
    ],
];

// Include header
include 'header.php';
?>
<link rel="stylesheet" href="assets/css/registry.css">

<!-- Page Content -->
<div class="page-header">
    <div class="page-title-section">
        <h1>Registry Management</h1>
        <p class="page-subtitle">Master data for farmers</p>
    </div>
    <div class="page-actions">
        <button class="btn btn-filter">
            <span class="material-icons">filter_list</span>
            Filter
        </button>
        <button class="btn btn-primary">
            <span class="material-icons">add</span>
            Add New Entry
        </button>
    </div>
</div>

<!-- Tabs -->
<div class="tabs-container">
    <button class="tab-btn active">Farmer Registry</button>
    <button class="tab-btn">Farm Profiles</button>

</div>

<!-- Search Box -->
<div class="search-box">
    <span class="material-icons">search</span>
    <input type="text" placeholder="Search by name, RSBSA number, location...">
</div>

<!-- Data Table -->
<div class="table-card">
    <table class="data-table">
        <thead>
            <tr>
                <th>RSBSA NO.</th>
                <th>NAME</th>
                <th>BARANGAY</th>
                <th>FARM SIZE</th>
                <th>CROPS</th>
                <th>STATUS</th>
                <th>ACTIONS</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($farmers as $farmer): ?>
            <tr>
                <td><?php echo $farmer['rsbsa']; ?></td>
                <td><?php echo $farmer['name']; ?></td>
                <td><?php echo $farmer['barangay']; ?></td>
                <td><?php echo $farmer['farm_size']; ?></td>
                <td><?php echo $farmer['crops']; ?></td>
                <td>
                    <span class="status-badge <?php echo strtolower($farmer['status']); ?>">
                        <?php echo $farmer['status']; ?>
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
                        <button class="action-btn delete" title="Delete">
                            <span class="material-icons">delete</span>
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
            const rsbsa = row.cells[0].textContent;
            alert('View details for: ' + rsbsa);
            // In real app, open modal or navigate to detail page
        });
    });

    document.querySelectorAll('.action-btn.edit').forEach(btn => {
        btn.addEventListener('click', function() {
            const row = this.closest('tr');
            const rsbsa = row.cells[0].textContent;
            alert('Edit: ' + rsbsa);
            // In real app, open edit modal or form
        });
    });

    document.querySelectorAll('.action-btn.delete').forEach(btn => {
        btn.addEventListener('click', function() {
            const row = this.closest('tr');
            const rsbsa = row.cells[0].textContent;
            const name = row.cells[1].textContent;
            if (confirm('Are you sure you want to delete ' + name + '?')) {
                alert('Delete: ' + rsbsa);
                // In real app, send delete request to server
            }
        });
    });

    // Search functionality
    document.querySelector('.search-box input').addEventListener('input', function(e) {
        const searchTerm = e.target.value.toLowerCase();
        const rows = document.querySelectorAll('.data-table tbody tr');
        
        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            if (text.includes(searchTerm)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });
</script>