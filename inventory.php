<?php
session_start();
include 'database.php';

$page_title = "Inventory Management";

/* =========================
   INSERT INVENTORY
========================= */

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {

    $item_name = trim($_POST['item_name']);
    $category  = trim($_POST['category']);
    $warehouse = trim($_POST['warehouse']);
    $quantity  = (int) $_POST['quantity'];
    $item_code = 'INV-' . strtoupper(substr(md5(uniqid()), 0, 6));
    $status    = ($quantity <= 20) ? 'Low Stock' : 'In Stock';

    $stmt = $conn->prepare("
        INSERT INTO inventory (item_code, item_name, category, warehouse, quantity, status, created_at)
        VALUES (?, ?, ?, ?, ?, ?, NOW())
    ");

    $stmt->bind_param("sssiss", $item_code, $item_name, $category, $warehouse, $quantity, $status);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Stock added successfully.";
    } else {
        $_SESSION['error'] = "Failed to save inventory: " . $stmt->error;
    }

    $stmt->close();
    header("Location: inventory.php");
    exit();
}

/* =========================
   UPDATE INVENTORY
========================= */

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update') {

    $id        = (int) $_POST['id'];
    $item_name = trim($_POST['item_name']);
    $category  = trim($_POST['category']);
    $warehouse = trim($_POST['warehouse']);
    $quantity  = (int) $_POST['quantity'];
    $status    = ($quantity <= 20) ? 'Low Stock' : 'In Stock';

    $stmt = $conn->prepare("
        UPDATE inventory
        SET item_name=?, category=?, warehouse=?, quantity=?, status=?
        WHERE id=?
    ");

    // FIX: correct type string — s,s,s,i,s,i (was "sssisi" which mis-typed status as int)
    $stmt->bind_param("sssiis", $item_name, $category, $warehouse, $quantity, $status, $id);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Stock updated successfully.";
    } else {
        $_SESSION['error'] = "Failed to update inventory: " . $stmt->error;
    }

    $stmt->close();
    header("Location: inventory.php");
    exit();
}

/* =========================
   DELETE INVENTORY
========================= */

if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {

    $id = (int) $_GET['id'];

    $stmt = $conn->prepare("DELETE FROM inventory WHERE id=?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Item deleted successfully.";
    } else {
        $_SESSION['error'] = "Failed to delete item: " . $stmt->error;
    }

    $stmt->close();
    header("Location: inventory.php");
    exit();
}

/* =========================
   FETCH INVENTORY
========================= */

$inventory = [];

$result = mysqli_query($conn, "SELECT * FROM inventory ORDER BY created_at DESC");

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $inventory[] = $row;
    }
}

/* =========================
   STATS
========================= */

$totalItems = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) as total FROM inventory")
)['total'] ?? 0;

$totalWarehouses = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(DISTINCT warehouse) as total FROM inventory")
)['total'] ?? 0;

$lowStock = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) as total FROM inventory WHERE quantity <= 20")
)['total'] ?? 0;

$totalValue = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT SUM(quantity) as total FROM inventory")
)['total'] ?? 0;

include 'header.php';
?>

<link rel="stylesheet" href="assets/css/inventory.css">

<style>
/* ── Alerts ── */
.alert {
    padding: 14px 18px;
    border-radius: 10px;
    margin-bottom: 20px;
    font-size: 14px;
    font-weight: 500;
}
.success-alert {
    background: #dcfce7;
    color: #166534;
    border: 1px solid #bbf7d0;
}
.error-alert {
    background: #fee2e2;
    color: #991b1b;
    border: 1px solid #fecaca;
}

/* ── Modal backdrop ── */
.modal {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(15, 23, 42, 0.55);
    backdrop-filter: blur(4px);
    justify-content: center;
    align-items: center;
    z-index: 1000;
}
.modal.open { display: flex; }

/* ── Modal card ── */
.modal-card {
    background: #ffffff;
    width: 100%;
    max-width: 460px;
    border-radius: 14px;
    padding: 0;
    box-shadow: 0 20px 60px rgba(0,0,0,0.25);
    animation: modalFade 0.2s ease;
    overflow: hidden;
}

@keyframes modalFade {
    from { opacity: 0; transform: translateY(-10px); }
    to   { opacity: 1; transform: translateY(0); }
}

/* ── Modal sections ── */
.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    padding: 1.25rem 1.5rem 0;
}
.modal-header h2 {
    font-size: 18px;
    font-weight: 600;
    color: #111827;
    margin: 0;
}
.modal-header p {
    font-size: 13px;
    color: #6b7280;
    margin: 4px 0 0;
}
.modal-close {
    background: none;
    border: none;
    cursor: pointer;
    color: #94a3b8;
    padding: 4px;
    border-radius: 6px;
    line-height: 1;
    font-size: 20px;
}
.modal-close:hover { background: #f1f5f9; color: #475569; }

.modal-body  { padding: 1.25rem 1.5rem; }
.modal-footer {
    display: flex;
    justify-content: flex-end;
    gap: 10px;
    padding: 1rem 1.5rem;
    border-top: 1px solid #f1f5f9;
}

/* ── Form ── */
.form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
.form-group { display: flex; flex-direction: column; gap: 6px; }
.form-group.full { grid-column: 1 / -1; }
.form-group label {
    font-size: 12px;
    font-weight: 600;
    color: #475569;
    letter-spacing: .03em;
}
.form-group input,
.form-group select,
.form-group textarea {
    padding: 10px 12px;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    font-size: 14px;
    font-family: inherit;
    outline: none;
    transition: .2s;
    background: #fff;
    color: #0f172a;
    box-sizing: border-box;
    width: 100%;
}
.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
    border-color: #10b981;
    box-shadow: 0 0 0 3px rgba(16,185,129,0.15);
}

/* ── Modal buttons ── */
.btn-save {
    background: #1D9E75;
    color: #fff;
    border: none;
    padding: 10px 16px;
    border-radius: 10px;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    font-family: inherit;
    transition: .2s;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}
.btn-save:hover { background: #0F6E56; }
.btn-cancel {
    background: #f1f5f9;
    color: #334155;
    border: 1px solid #e2e8f0;
    padding: 10px 16px;
    border-radius: 10px;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    font-family: inherit;
    transition: .2s;
}
.btn-cancel:hover { background: #e2e8f0; }
.btn-delete-confirm {
    background: #dc2626;
    color: #fff;
    border: none;
    padding: 10px 16px;
    border-radius: 10px;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    font-family: inherit;
    transition: .2s;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}
.btn-delete-confirm:hover { background: #b91c1c; }

/* ── Action buttons in table ── */
.action-btns { display: flex; gap: 6px; }
.action-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 30px;
    height: 30px;
    border-radius: 7px;
    border: 1px solid rgba(0,0,0,0.1);
    background: #fff;
    cursor: pointer;
    transition: background .15s;
}
.action-btn.edit  .material-icons { font-size: 15px; color: #1D9E75; }
.action-btn.delete .material-icons { font-size: 15px; color: #dc2626; }
.action-btn.edit:hover  { background: #dcfce7; border-color: #86efac; }
.action-btn.delete:hover { background: #fee2e2; border-color: #fca5a5; }

/* ── Delete confirm modal icon ── */
.delete-icon-wrap { display: flex; justify-content: center; margin-bottom: 12px; }
.delete-icon-wrap .material-icons {
    font-size: 48px;
    color: #dc2626;
    background: #fee2e2;
    border-radius: 50%;
    padding: 12px;
}
</style>

<!-- ── Alerts ── -->
<?php if (isset($_SESSION['success'])): ?>
<div class="alert success-alert"><?= htmlspecialchars($_SESSION['success']) ?></div>
<?php unset($_SESSION['success']); endif; ?>

<?php if (isset($_SESSION['error'])): ?>
<div class="alert error-alert"><?= htmlspecialchars($_SESSION['error']) ?></div>
<?php unset($_SESSION['error']); endif; ?>

<!-- ── Page Header ── -->
<div class="page-header">
    <div class="page-title-section">
        <h1>Inventory Management</h1>
        <p class="page-subtitle">Supplies and warehouse monitoring</p>
    </div>
    <div class="page-actions">
        <button class="btn btn-primary" onclick="openAddModal()">
            <span class="material-icons">add</span> Add Stock
        </button>
    </div>
</div>

<!-- ── Stats ── -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon green"><span class="material-icons">inventory_2</span></div>
        <div><div class="stat-value"><?= $totalItems ?></div><div class="stat-label">Items in Stock</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon blue"><span class="material-icons">warehouse</span></div>
        <div><div class="stat-value"><?= $totalWarehouses ?></div><div class="stat-label">Warehouses</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon orange"><span class="material-icons">warning</span></div>
        <div><div class="stat-value"><?= $lowStock ?></div><div class="stat-label">Low Stock</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon purple"><span class="material-icons">trending_up</span></div>
        <div><div class="stat-value"><?= $totalValue ?></div><div class="stat-label">Total Quantity</div></div>
    </div>
</div>

<!-- ── Filter Tabs ── -->
<div class="tabs-container">
    <button class="tab-btn active" onclick="filterInventory('all', this)">All Inventory</button>
</div>

<!-- ── Inventory Table ── -->
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
                <th>ACTIONS</th>
            </tr>
        </thead>
        <tbody id="inventoryTableBody">
            <?php if (empty($inventory)): ?>
            <tr>
                <td colspan="7">
                    <div class="empty-state">
                        <span class="material-icons">inventory_2</span>
                        <p>No inventory items yet.</p>
                    </div>
                </td>
            </tr>
            <?php else: ?>
            <?php foreach ($inventory as $item): ?>
            <tr
                class="inventory-row"
                data-category="<?= strtolower(htmlspecialchars($item['category'], ENT_QUOTES)) ?>"
                data-id="<?= (int)$item['id'] ?>"
                data-code="<?= htmlspecialchars($item['item_code'], ENT_QUOTES) ?>"
                data-name="<?= htmlspecialchars($item['item_name'], ENT_QUOTES) ?>"
                data-cat="<?= htmlspecialchars($item['category'], ENT_QUOTES) ?>"
                data-warehouse="<?= htmlspecialchars($item['warehouse'], ENT_QUOTES) ?>"
                data-qty="<?= (int)$item['quantity'] ?>"
            >
                <td><?= htmlspecialchars($item['item_code']) ?></td>
                <td><?= htmlspecialchars($item['item_name']) ?></td>
                <td><?= htmlspecialchars($item['category']) ?></td>
                <td><?= htmlspecialchars($item['warehouse']) ?></td>
                <td><?= (int)$item['quantity'] ?></td>
                <td>
                    <span class="status-badge <?= strtolower(str_replace(' ', '-', $item['status'])) ?>">
                        <?= htmlspecialchars($item['status']) ?>
                    </span>
                </td>
                <td>
                    <div class="action-btns">
                        <button type="button"
                                class="action-btn edit"
                                title="Edit Item"
                                onclick="openEditModal(this)">
                            <span class="material-icons">edit</span>
                        </button>
                        <button type="button"
                                class="action-btn delete"
                                title="Delete Item"
                                onclick="openDeleteModal(this)">
                            <span class="material-icons">delete</span>
                        </button>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
            <tr id="emptyRow" style="display:none">
                <td colspan="7">
                    <div class="empty-state">
                        <span class="material-icons">search_off</span>
                        <p>No items match the current filter.</p>
                    </div>
                </td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- ══════════════════════════════════════════
     ADD STOCK MODAL
══════════════════════════════════════════ -->
<div id="addModal" class="modal">
    <div class="modal-card">
        <div class="modal-header">
            <div>
                <h2>Add New Stock</h2>
                <p>Enter inventory details</p>
            </div>
            <button class="modal-close" onclick="closeAddModal()" aria-label="Close">&#x2715;</button>
        </div>
        <form method="POST" action="inventory.php">
            <input type="hidden" name="action" value="add">
            <div class="modal-body">
                <div class="form-grid">
                    <div class="form-group full">
                        <label>Item Name</label>
                        <input type="text" name="item_name" placeholder="Enter item name" required>
                    </div>
                    <div class="form-group">
                        <label>Category</label>
                        <input type="text" name="category" placeholder="e.g. Supplies, Equipment" required>
                    </div>
                    <div class="form-group">
                        <label>Warehouse</label>
                        <input type="text" name="warehouse" placeholder="Warehouse location" required>
                    </div>
                    <div class="form-group full">
                        <label>Quantity</label>
                        <input type="number" name="quantity" placeholder="0" min="0" required>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-cancel" onclick="closeAddModal()">Cancel</button>
                <button type="submit" class="btn-save">
                    <span class="material-icons" style="font-size:16px">save</span>
                    Save Stock
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ══════════════════════════════════════════
     EDIT STOCK MODAL
══════════════════════════════════════════ -->
<div id="editModal" class="modal">
    <div class="modal-card">
        <div class="modal-header">
            <div>
                <h2>Edit Stock</h2>
                <p id="editSubtitle">Update inventory details</p>
            </div>
            <button class="modal-close" onclick="closeEditModal()" aria-label="Close">&#x2715;</button>
        </div>
        <form method="POST" action="inventory.php">
            <input type="hidden" name="action" value="update">
            <input type="hidden" name="id" id="edit_id">
            <div class="modal-body">
                <div class="form-grid">
                    <div class="form-group full">
                        <label>Item Name</label>
                        <input type="text" name="item_name" id="edit_item_name" required>
                    </div>
                    <div class="form-group">
                        <label>Category</label>
                        <input type="text" name="category" id="edit_category" required>
                    </div>
                    <div class="form-group">
                        <label>Warehouse</label>
                        <input type="text" name="warehouse" id="edit_warehouse" required>
                    </div>
                    <div class="form-group full">
                        <label>Quantity</label>
                        <input type="number" name="quantity" id="edit_quantity" min="0" required>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-cancel" onclick="closeEditModal()">Cancel</button>
                <button type="submit" class="btn-save">
                    <span class="material-icons" style="font-size:16px">save</span>
                    Update Stock
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ══════════════════════════════════════════
     DELETE CONFIRM MODAL
══════════════════════════════════════════ -->
<div id="deleteModal" class="modal">
    <div class="modal-card" style="max-width:400px">
        <div class="modal-header" style="padding-bottom:0">
            <div></div>
            <button class="modal-close" onclick="closeDeleteModal()" aria-label="Close">&#x2715;</button>
        </div>
        <div class="modal-body" style="text-align:center;padding-top:.5rem">
            <div class="delete-icon-wrap">
                <span class="material-icons">delete_forever</span>
            </div>
            <h2 style="font-size:17px;font-weight:600;color:#0f172a;margin:0 0 8px">Delete Item?</h2>
            <p style="font-size:13px;color:#64748b;margin:0">
                You are about to delete <strong id="deleteItemName"></strong>. This cannot be undone.
            </p>
        </div>
        <div class="modal-footer" style="justify-content:center;gap:12px">
            <button type="button" class="btn-cancel" onclick="closeDeleteModal()">Cancel</button>
            <a id="deleteConfirmLink" href="#" class="btn-delete-confirm">
                <span class="material-icons" style="font-size:15px">delete</span>
                Yes, Delete
            </a>
        </div>
    </div>
</div>

<script>
// ── ADD Modal ─────────────────────────────────────────────────────────────
function openAddModal() {
    document.getElementById('addModal').classList.add('open');
}
function closeAddModal() {
    document.getElementById('addModal').classList.remove('open');
}
document.getElementById('addModal').addEventListener('click', e => {
    if (e.target === e.currentTarget) closeAddModal();
});

// ── EDIT Modal ────────────────────────────────────────────────────────────
// Reads from data-* attributes on the <tr> — safe against apostrophes & special chars
function openEditModal(btn) {
    const row = btn.closest('tr');
    document.getElementById('edit_id').value        = row.getAttribute('data-id');
    document.getElementById('edit_item_name').value = row.getAttribute('data-name');
    document.getElementById('edit_category').value  = row.getAttribute('data-cat');
    document.getElementById('edit_warehouse').value = row.getAttribute('data-warehouse');
    document.getElementById('edit_quantity').value  = row.getAttribute('data-qty');
    document.getElementById('editSubtitle').textContent =
        row.getAttribute('data-code') + ' · ' + row.getAttribute('data-name');
    document.getElementById('editModal').classList.add('open');
}
function closeEditModal() {
    document.getElementById('editModal').classList.remove('open');
}
document.getElementById('editModal').addEventListener('click', e => {
    if (e.target === e.currentTarget) closeEditModal();
});

// ── DELETE Modal ──────────────────────────────────────────────────────────
function openDeleteModal(btn) {
    const row  = btn.closest('tr');
    const id   = row.getAttribute('data-id');
    const name = row.getAttribute('data-name');
    document.getElementById('deleteItemName').textContent    = name;
    document.getElementById('deleteConfirmLink').href        = 'inventory.php?action=delete&id=' + id;
    document.getElementById('deleteModal').classList.add('open');
}
function closeDeleteModal() {
    document.getElementById('deleteModal').classList.remove('open');
}
document.getElementById('deleteModal').addEventListener('click', e => {
    if (e.target === e.currentTarget) closeDeleteModal();
});

// ── Filter tabs ───────────────────────────────────────────────────────────
function filterInventory(type, element) {
    const rows     = document.querySelectorAll('.inventory-row');
    const emptyRow = document.getElementById('emptyRow');
    let visible    = 0;

    rows.forEach(row => {
        const cat  = row.getAttribute('data-category');
        const show = (type === 'all' || cat.includes(type));
        row.style.display = show ? '' : 'none';
        if (show) visible++;
    });

    if (emptyRow) {
        emptyRow.style.display = (visible === 0) ? '' : 'none';
    }

    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    element.classList.add('active');
}
</script>