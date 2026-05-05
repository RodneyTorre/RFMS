<?php
session_start();
include 'database.php';

$page_title = "Inventory Management";

/* =========================
   FETCH INVENTORY
========================= */
$inventory = [];

$sql = "SELECT * FROM inventory ORDER BY created_at DESC";
$result = mysqli_query($conn, $sql);

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $inventory[] = $row;
    }
}

/* =========================
   STATS
========================= */
$totalItems = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM inventory"))['total'] ?? 0;

$totalWarehouses = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(DISTINCT warehouse) as total FROM inventory"))['total'] ?? 0;

$lowStock = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM inventory WHERE quantity < 100"))['total'] ?? 0;

$totalValue = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(quantity) as total FROM inventory"))['total'] ?? 0;

include 'header.php';
?>

<link rel="stylesheet" href="assets/css/inventory.css">

<!-- HEADER -->
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

        <button class="btn btn-primary" onclick="openInventoryModal()">
            <span class="material-icons">add</span>
            Add Stock
        </button>
    </div>

</div>

<!-- STATS -->
<div class="stats-grid">

    <div class="stat-card">
        <div class="stat-icon green"><span class="material-icons">inventory_2</span></div>
        <div>
            <div class="stat-value"><?= $totalItems ?></div>
            <div class="stat-label">Items in Stock</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon blue"><span class="material-icons">warehouse</span></div>
        <div>
            <div class="stat-value"><?= $totalWarehouses ?></div>
            <div class="stat-label">Warehouses</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon orange"><span class="material-icons">warning</span></div>
        <div>
            <div class="stat-value"><?= $lowStock ?></div>
            <div class="stat-label">Low Stock</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon purple"><span class="material-icons">trending_up</span></div>
        <div>
            <div class="stat-value"><?= $totalValue ?></div>
            <div class="stat-label">Total Items</div>
        </div>
    </div>

</div>

<!-- TABS -->
<div class="tabs-container">

    <button class="tab-btn active" onclick="filterInventory('all', this)">
        Supplies Inventory
    </button>

    <button class="tab-btn" onclick="filterInventory('equipment', this)">
        Equipment Assets
    </button>

    <button class="tab-btn" onclick="filterInventory('warehouse', this)">
        Warehouse Monitoring
    </button>

    <button class="tab-btn" onclick="filterInventory('issuance', this)">
        Issuance Logs
    </button>

</div>

<!-- TABLE -->
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
        <tr class="inventory-row" data-category="<?= strtolower($item['category']) ?>">

            <td><?= htmlspecialchars($item['item_code']) ?></td>
            <td><?= htmlspecialchars($item['item_name']) ?></td>
            <td><?= htmlspecialchars($item['category']) ?></td>
            <td><?= htmlspecialchars($item['warehouse']) ?></td>
            <td><?= $item['quantity'] ?></td>

            <td>
                <span class="status-badge <?= strtolower(str_replace(' ', '-', $item['status'])) ?>">
                    <?= $item['status'] ?>
                </span>
            </td>

        </tr>
        <?php endforeach; ?>
    </tbody>

</table>
</div>

<!-- =========================
     ADD STOCK MODAL
========================= -->
<div id="inventoryModal" class="modal">
    <div class="modal-content">

        <h2>Add Stock</h2>

        <form method="POST" action="save_inventory.php">

            <input type="text" name="item_name" placeholder="Item Name" required>
            <input type="text" name="category" placeholder="Category" required>
            <input type="text" name="warehouse" placeholder="Warehouse" required>
            <input type="number" name="quantity" placeholder="Quantity" required>

            <div class="modal-buttons">
                <button type="submit" class="btn btn-primary">Save</button>
                <button type="button" class="btn btn-secondary" onclick="closeInventoryModal()">Cancel</button>
            </div>

        </form>

    </div>
</div>

<!-- JS -->
<script>

function openInventoryModal() {
    document.getElementById("inventoryModal").style.display = "flex";
}

function closeInventoryModal() {
    document.getElementById("inventoryModal").style.display = "none";
}

function filterInventory(type, element) {

    const rows = document.querySelectorAll(".inventory-row");

    rows.forEach(row => {

        const category = row.getAttribute("data-category");

        let show = false;

        if (type === "all") show = true;
        else if (type === "equipment") show = category.includes("equipment");
        else if (type === "warehouse") show = category.includes("warehouse");
        else if (type === "issuance") show = category.includes("issuance");

        row.style.display = show ? "" : "none";
    });

    document.querySelectorAll(".tab-btn").forEach(btn => btn.classList.remove("active"));
    element.classList.add("active");
}

</script>