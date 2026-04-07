<?php
// inventory.php - Inventory Management Page
session_start();

$page_title = "Inventory Management";
$current_user = ['name' => 'Admin User', 'role' => 'System Administrator', 'initials' => 'AU'];

$stats = [
    ['icon' => 'inventory_2', 'value' => '2,340', 'label' => 'Items in Stock', 'color' => 'green'],
    ['icon' => 'warehouse', 'value' => '8', 'label' => 'Warehouses', 'color' => 'blue'],
    ['icon' => 'warning', 'value' => '34', 'label' => 'Low Stock', 'color' => 'orange'],
    ['icon' => 'trending_up', 'value' => '₱23.5M', 'label' => 'Total Value', 'color' => 'purple']
];

$inventory = [
    ['code' => 'ITM-001', 'name' => 'Rice Seeds NSIC Rc222', 'category' => 'Seeds', 'warehouse' => 'Warehouse A', 'quantity' => '2,340 bags', 'status' => 'In Stock'],
    ['code' => 'ITM-002', 'name' => 'Corn Seeds IPB Var 6', 'category' => 'Seeds', 'warehouse' => 'Warehouse A', 'quantity' => '1,200 bags', 'status' => 'In Stock'],
    ['code' => 'ITM-003', 'name' => 'Urea Fertilizer 46-0-0', 'category' => 'Fertilizer', 'warehouse' => 'Warehouse B', 'quantity' => '450 bags', 'status' => 'Low Stock']
];

include 'header.php';
?>

<link rel="stylesheet" href="assets/css/inventory.css">

<div class="page-header">
    <div class="page-title-section">
        <h1>Inventory Management</h1>
        <p class="page-subtitle">Supplies, equipment, and warehouse monitoring</p>
    </div>
    <div class="page-actions">
        <button class="btn btn-secondary">
            <span class="material-icons">description</span>
            Stock Report
        </button>
        <button class="btn btn-primary">
            <span class="material-icons">add</span>
            Add Stock
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
    <button class="tab-btn active">Supplies Inventory</button>
    <button class="tab-btn">Equipment Assets</button>
    <button class="tab-btn">Warehouse Monitoring</button>
    <button class="tab-btn">Issuance Logs</button>
</div>

<div class="table-card">
    <table class="data-table">
        <thead>
            <tr>
                <th>ITEM CODE</th>
                <th>ITEM NAME</th>
                <th>CATEGORY</th>
                <th>WAREHOUSE</th>
                <th>QUANTITY</th>
                <th>STATUS</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($inventory as $item): ?>
            <tr>
                <td><?php echo $item['code']; ?></td>
                <td><?php echo $item['name']; ?></td>
                <td><?php echo $item['category']; ?></td>
                <td><?php echo $item['warehouse']; ?></td>
                <td><?php echo $item['quantity']; ?></td>
                <td><span class="status-badge <?php echo strtolower(str_replace(' ', '.', $item['status'])); ?>"><?php echo $item['status']; ?></span></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

