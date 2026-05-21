<?php
session_start();
include 'database.php';

$page_title = "Registry";

/* =========================
   DELETE FARMER
========================= */
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $sql = "DELETE FROM farmers WHERE farmer_id='$id'";
    if (mysqli_query($conn, $sql)) {
        header("Location: registry.php");
        exit();
    } else {
        echo "Delete failed";
    }
}

/* =========================
   UPDATE FARMER
========================= */
if (isset($_POST['update_farmer'])) {
    $id        = $_POST['edit_id'];
    $name      = $_POST['edit_name'];
    $address   = $_POST['edit_address'];
    $farm_size = $_POST['edit_farm_size'];
    $crop_type = $_POST['edit_crop_type'];
    $sql = "UPDATE farmers SET full_name='$name', address='$address', farm_size='$farm_size', crop_type='$crop_type' WHERE farmer_id='$id'";
    if (mysqli_query($conn, $sql)) {
        header("Location: registry.php");
        exit();
    } else {
        echo "Update failed";
    }
}

/* =========================
   INSERT FARMER
========================= */
if ($_SERVER["REQUEST_METHOD"] == "POST" && !isset($_POST['update_farmer'])) {
    $full_name = $_POST['full_name'];
    $gender    = $_POST['gender'];
    $contact   = $_POST['contact_number'];
    $address   = $_POST['address'];
    $farm_size = $_POST['farm_size'];
    $crop_type = $_POST['crop_type'];
    $sql  = "INSERT INTO farmers (full_name, gender, contact_number, address, farm_size, crop_type) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssds", $full_name, $gender, $contact, $address, $farm_size, $crop_type);
    if ($stmt->execute()) {
        header("Location: registry.php");
        exit();
    } else {
        echo "Insert failed";
    }
}

/* =========================
   STAT CARDS
========================= */
$stat_total     = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS t FROM farmers"))['t'] ?? 0;
$stat_male      = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS t FROM farmers WHERE gender='Male'"))['t'] ?? 0;
$stat_female    = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS t FROM farmers WHERE gender='Female'"))['t'] ?? 0;
$stat_farm_size = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(farm_size) AS t FROM farmers"))['t'] ?? 0;
$stat_crops     = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(DISTINCT crop_type) AS t FROM farmers"))['t'] ?? 0;
$stat_barangays = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(DISTINCT address) AS t FROM farmers"))['t'] ?? 0;

/* =========================
   FILTER
========================= */
$barangay = isset($_GET['barangay']) ? $_GET['barangay'] : '';
$farmers  = [];

if ($barangay != '') {
    $stmt = $conn->prepare("SELECT * FROM farmers WHERE address = ? ORDER BY farmer_id DESC");
    $stmt->bind_param("s", $barangay);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $conn->query("SELECT * FROM farmers ORDER BY farmer_id DESC");
}

while ($row = $result->fetch_assoc()) {
    $farmers[] = $row;
}

/* =========================
   BARANGAY DROPDOWN
========================= */
$barangay_query = $conn->query("SELECT DISTINCT address FROM farmers ORDER BY address ASC");

include 'header.php';
?>

<link rel="stylesheet" href="assets/css/registry.css">

<style>
/* ── ACTION BUTTONS ── */
.action-btns { display:flex; gap:6px; }
.action-btn {
    display:inline-flex; align-items:center; justify-content:center;
    width:30px; height:30px; border-radius:7px;
    border:1px solid rgba(0,0,0,0.1); background:#fff;
    cursor:pointer; transition:background .15s;
}
.action-btn.edit .material-icons   { font-size:15px; color:#1D9E75; }
.action-btn.delete .material-icons { font-size:15px; color:#dc2626; }
.action-btn.edit:hover   { background:#dcfce7; border-color:#86efac; }
.action-btn.delete:hover { background:#fee2e2; border-color:#fca5a5; }

/* ── STAT CARDS ── */
.stat-cards {
    display: grid;
    grid-template-columns: repeat(6, 1fr);
    gap: 14px;
    margin-bottom: 22px;
}
.stat-card {
    background: #fff; border-radius: 14px; padding: 16px 18px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.06);
    display: flex; align-items: center; gap: 14px;
    transition: transform .15s, box-shadow .15s;
}
.stat-card:hover { transform: translateY(-2px); box-shadow: 0 6px 18px rgba(0,0,0,0.1); }
.stat-icon {
    width:44px; height:44px; border-radius:11px;
    display:flex; align-items:center; justify-content:center; flex-shrink:0;
}
.stat-icon .material-icons { font-size:22px; }
.stat-icon.green  { background:#d1fae5; color:#059669; }
.stat-icon.blue   { background:#dbeafe; color:#2563eb; }
.stat-icon.pink   { background:#fce7f3; color:#db2777; }
.stat-icon.amber  { background:#fef3c7; color:#d97706; }
.stat-icon.purple { background:#ede9fe; color:#7c3aed; }
.stat-icon.teal   { background:#ccfbf1; color:#0d9488; }
.stat-label {
    font-size:11px; color:#9ca3af; font-weight:600;
    text-transform:uppercase; letter-spacing:.4px; margin-bottom:3px;
}
.stat-value { font-size:22px; font-weight:800; color:#111827; line-height:1; }
.stat-sub   { font-size:11px; color:#6b7280; margin-top:2px; }

@media(max-width:1100px) { .stat-cards { grid-template-columns: repeat(3,1fr); } }
@media(max-width:640px)  { .stat-cards { grid-template-columns: repeat(2,1fr); } }

/* ── MODAL OVERLAY ── */
.modal-overlay {
    display: none;
    position: fixed; inset: 0;
    background: rgba(0,0,0,0.45);
    z-index: 999;
    align-items: center;
    justify-content: center;
}
.modal-overlay.active { display: flex; }

/* ── MODAL BOX ── */
.modal-box {
    background: #fff;
    width: 480px;
    max-width: 95vw;
    border-radius: 16px;
    box-shadow: 0 20px 60px rgba(0,0,0,0.18);
    overflow: hidden;
    animation: modalIn .2s ease;
}
@keyframes modalIn {
    from { transform: translateY(-16px); opacity:0; }
    to   { transform: translateY(0);     opacity:1; }
}

/* ── MODAL HEADER ── */
.modal-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 18px 22px;
    border-bottom: 1px solid #f0f0f0;
}
.modal-header-left { display:flex; align-items:center; gap:10px; }
.modal-header-icon {
    width:38px; height:38px; border-radius:10px;
    display:flex; align-items:center; justify-content:center;
}
.modal-header-icon.green  { background:#d1fae5; color:#059669; }
.modal-header-icon.blue   { background:#dbeafe; color:#2563eb; }
.modal-header-icon .material-icons { font-size:20px; }
.modal-title    { font-size:16px; font-weight:700; color:#111827; }
.modal-subtitle { font-size:12px; color:#9ca3af; margin-top:1px; }
.modal-close {
    width:32px; height:32px; border-radius:8px; border:none;
    background:#f3f4f6; cursor:pointer; display:flex;
    align-items:center; justify-content:center; transition:background .15s;
}
.modal-close:hover { background:#e5e7eb; }
.modal-close .material-icons { font-size:18px; color:#6b7280; }

/* ── MODAL BODY ── */
.modal-body { padding: 22px; }

/* ── FORM GRID ── */
.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 14px;
    margin-bottom: 14px;
}
.form-row.full { grid-template-columns: 1fr; }

.form-group { display:flex; flex-direction:column; gap:5px; }
.form-label {
    font-size:12px; font-weight:600; color:#374151;
    text-transform:uppercase; letter-spacing:.4px;
}
.form-label span.required { color:#ef4444; margin-left:2px; }

.form-input, .form-select {
    width: 100%;
    padding: 9px 12px;
    border: 1.5px solid #e5e7eb;
    border-radius: 9px;
    font-size: 14px;
    color: #111827;
    background: #f9fafb;
    transition: border-color .15s, box-shadow .15s;
    box-sizing: border-box;
    outline: none;
}
.form-input:focus, .form-select:focus {
    border-color: #10b981;
    background: #fff;
    box-shadow: 0 0 0 3px rgba(16,185,129,0.12);
}
.form-select { appearance: none; cursor:pointer; }

/* ── INPUT WITH ICON ── */
.input-wrap { position:relative; }
.input-wrap .form-input { padding-left: 36px; }
.input-wrap .input-icon {
    position:absolute; left:10px; top:50%; transform:translateY(-50%);
    font-size:17px; color:#9ca3af; pointer-events:none;
}

/* ── SECTION DIVIDER ── */
.form-section-label {
    font-size:11px; font-weight:700; color:#10b981;
    text-transform:uppercase; letter-spacing:.6px;
    margin-bottom:12px; padding-bottom:6px;
    border-bottom:1px solid #f0fdf4;
}

/* ── MODAL FOOTER ── */
.modal-footer {
    padding: 16px 22px;
    border-top: 1px solid #f0f0f0;
    display: flex;
    justify-content: flex-end;
    gap: 10px;
}
.btn-cancel {
    padding: 9px 20px; border-radius: 9px; border: 1.5px solid #e5e7eb;
    background: #fff; color: #374151; font-size:14px; font-weight:600;
    cursor:pointer; transition:background .15s;
}
.btn-cancel:hover { background:#f3f4f6; }
.btn-save {
    padding: 9px 22px; border-radius: 9px; border: none;
    background: #10b981; color: #fff; font-size:14px; font-weight:600;
    cursor:pointer; display:flex; align-items:center; gap:6px;
    transition:background .15s;
}
.btn-save:hover { background:#059669; }
.btn-update {
    padding: 9px 22px; border-radius: 9px; border: none;
    background: #2563eb; color: #fff; font-size:14px; font-weight:600;
    cursor:pointer; display:flex; align-items:center; gap:6px;
    transition:background .15s;
}
.btn-update:hover { background:#1d4ed8; }
</style>

<!-- PAGE HEADER -->
<div class="page-header">
    <div class="page-title-section">
        <h1>Registry Management</h1>
        <p class="page-subtitle">Master data for farmers</p>
    </div>
    <div class="page-actions">
        <button type="button" onclick="openForm()" class="btn btn-primary">
            <span class="material-icons">add</span>
            Add New Entry
        </button>
    </div>
</div>

<!-- STAT CARDS -->
<div class="stat-cards">
    <div class="stat-card">
        <div class="stat-icon green"><span class="material-icons">people</span></div>
        <div class="stat-info">
            <div class="stat-label">Total Farmers</div>
            <div class="stat-value"><?= number_format($stat_total) ?></div>
            <div class="stat-sub">Registered</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon blue"><span class="material-icons">man</span></div>
        <div class="stat-info">
            <div class="stat-label">Male</div>
            <div class="stat-value"><?= number_format($stat_male) ?></div>
            <div class="stat-sub"><?= $stat_total > 0 ? round($stat_male / $stat_total * 100) : 0 ?>% of total</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon pink"><span class="material-icons">woman</span></div>
        <div class="stat-info">
            <div class="stat-label">Female</div>
            <div class="stat-value"><?= number_format($stat_female) ?></div>
            <div class="stat-sub"><?= $stat_total > 0 ? round($stat_female / $stat_total * 100) : 0 ?>% of total</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon amber"><span class="material-icons">landscape</span></div>
        <div class="stat-info">
            <div class="stat-label">Total Farm Size</div>
            <div class="stat-value"><?= number_format($stat_farm_size, 1) ?></div>
            <div class="stat-sub">Hectares</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon purple"><span class="material-icons">grass</span></div>
        <div class="stat-info">
            <div class="stat-label">Crop Types</div>
            <div class="stat-value"><?= number_format($stat_crops) ?></div>
            <div class="stat-sub">Distinct crops</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon teal"><span class="material-icons">location_on</span></div>
        <div class="stat-info">
            <div class="stat-label">Barangays</div>
            <div class="stat-value"><?= number_format($stat_barangays) ?></div>
            <div class="stat-sub">Covered areas</div>
        </div>
    </div>
</div>

<!-- FILTER -->
<form method="GET">
    <select name="barangay" id="dropdown-filter" onchange="this.form.submit()" style="display:none;">
        <option value="">All Barangays</option>
        <?php while ($row = $barangay_query->fetch_assoc()): ?>
            <option value="<?= $row['address'] ?>" <?= ($row['address'] == $barangay) ? 'selected' : '' ?>>
                <?= $row['address'] ?>
            </option>
        <?php endwhile; ?>
    </select>
</form>

<!-- TABLE -->
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
                <td>RSBSA <?= $farmer['farmer_id'] ?></td>
                <td><?= $farmer['full_name'] ?></td>
                <td><?= $farmer['address'] ?></td>
                <td><?= $farmer['farm_size'] ?> ha</td>
                <td><?= $farmer['crop_type'] ?></td>
                <td>Active</td>
                <td>
                    <div class="action-btns">
                        <button type="button" class="action-btn edit" title="Edit Farmer"
                            onclick="editFarmer(
                                '<?= $farmer['farmer_id'] ?>',
                                '<?= addslashes($farmer['full_name']) ?>',
                                '<?= addslashes($farmer['address']) ?>',
                                '<?= $farmer['farm_size'] ?>',
                                '<?= addslashes($farmer['crop_type']) ?>'
                            )">
                            <span class="material-icons">edit</span>
                        </button>
                        <button type="button" class="action-btn delete" title="Delete Farmer"
                            onclick="deleteFarmer(<?= $farmer['farmer_id'] ?>)">
                            <span class="material-icons">delete</span>
                        </button>
                    </div>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- ══════════════════════════════════════
     ADD FARMER MODAL
══════════════════════════════════════ -->
<div id="popupForm" class="modal-overlay">
    <div class="modal-box">

        <div class="modal-header">
            <div class="modal-header-left">
                <div class="modal-header-icon green">
                    <span class="material-icons">person_add</span>
                </div>
                <div>
                    <div class="modal-title">Add New Farmer</div>
                    <div class="modal-subtitle">Fill in the farmer's information below</div>
                </div>
            </div>
            <button class="modal-close" onclick="closeForm()">
                <span class="material-icons">close</span>
            </button>
        </div>

        <div class="modal-body">
            <form method="POST" id="addFarmerForm">

                <div class="form-section-label">Personal Information</div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Full Name <span class="required">*</span></label>
                        <div class="input-wrap">
                            <span class="material-icons input-icon">person</span>
                            <input type="text" name="full_name" class="form-input" placeholder="e.g. Juan Dela Cruz" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Gender <span class="required">*</span></label>
                        <div class="input-wrap">
                            <span class="material-icons input-icon">wc</span>
                            <select name="gender" class="form-select" style="padding-left:36px;" required>
                                <option value="">Select gender</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Contact Number</label>
                        <div class="input-wrap">
                            <span class="material-icons input-icon">phone</span>
                            <input type="text" name="contact_number" class="form-input" placeholder="e.g. 09XX-XXX-XXXX">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Barangay / Address <span class="required">*</span></label>
                        <div class="input-wrap">
                            <span class="material-icons input-icon">location_on</span>
                            <input type="text" name="address" class="form-input" placeholder="e.g. Barangay San Jose" required>
                        </div>
                    </div>
                </div>

                <div class="form-section-label" style="margin-top:6px;">Farm Details</div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Farm Size (ha) <span class="required">*</span></label>
                        <div class="input-wrap">
                            <span class="material-icons input-icon">landscape</span>
                            <input type="number" step="0.01" name="farm_size" class="form-input" placeholder="e.g. 2.50" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Crop Type <span class="required">*</span></label>
                        <div class="input-wrap">
                            <span class="material-icons input-icon">grass</span>
                            <input type="text" name="crop_type" class="form-input" placeholder="e.g. Rice, Corn" required>
                        </div>
                    </div>
                </div>

            </form>
        </div>

        <div class="modal-footer">
            <button type="button" class="btn-cancel" onclick="closeForm()">Cancel</button>
            <button type="submit" form="addFarmerForm" class="btn-save">
                <span class="material-icons" style="font-size:17px;">save</span>
                Save Farmer
            </button>
        </div>

    </div>
</div>

<!-- ══════════════════════════════════════
     EDIT FARMER MODAL
══════════════════════════════════════ -->
<div id="editForm" class="modal-overlay">
    <div class="modal-box">

        <div class="modal-header">
            <div class="modal-header-left">
                <div class="modal-header-icon blue">
                    <span class="material-icons">edit</span>
                </div>
                <div>
                    <div class="modal-title">Edit Farmer</div>
                    <div class="modal-subtitle">Update the farmer's information</div>
                </div>
            </div>
            <button class="modal-close" onclick="closeEditForm()">
                <span class="material-icons">close</span>
            </button>
        </div>

        <div class="modal-body">
            <form method="POST" id="editFarmerForm">
                <input type="hidden" name="edit_id" id="edit_id">

                <div class="form-section-label">Personal Information</div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Full Name <span class="required">*</span></label>
                        <div class="input-wrap">
                            <span class="material-icons input-icon">person</span>
                            <input type="text" name="edit_name" id="edit_name" class="form-input" placeholder="Full Name" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Barangay / Address</label>
                        <div class="input-wrap">
                            <span class="material-icons input-icon">location_on</span>
                            <input type="text" name="edit_address" id="edit_address" class="form-input" placeholder="Address">
                        </div>
                    </div>
                </div>

                <div class="form-section-label" style="margin-top:6px;">Farm Details</div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Farm Size (ha)</label>
                        <div class="input-wrap">
                            <span class="material-icons input-icon">landscape</span>
                            <input type="number" step="0.01" name="edit_farm_size" id="edit_farm_size" class="form-input" placeholder="Farm Size">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Crop Type</label>
                        <div class="input-wrap">
                            <span class="material-icons input-icon">grass</span>
                            <input type="text" name="edit_crop_type" id="edit_crop_type" class="form-input" placeholder="Crop Type">
                        </div>
                    </div>
                </div>

            </form>
        </div>

        <div class="modal-footer">
            <button type="button" class="btn-cancel" onclick="closeEditForm()">Cancel</button>
            <button type="submit" name="update_farmer" form="editFarmerForm" class="btn-update">
                <span class="material-icons" style="font-size:17px;">check_circle</span>
                Update Farmer
            </button>
        </div>

    </div>
</div>

<script>
function openForm()      { document.getElementById("popupForm").classList.add("active"); }
function closeForm()     { document.getElementById("popupForm").classList.remove("active"); }
function closeEditForm() { document.getElementById("editForm").classList.remove("active"); }

function deleteFarmer(id) {
    if (confirm("Delete this farmer?")) {
        window.location.href = "registry.php?delete=" + id;
    }
}

function editFarmer(id, name, address, farm_size, crop_type) {
    document.getElementById("edit_id").value        = id;
    document.getElementById("edit_name").value      = name;
    document.getElementById("edit_address").value   = address;
    document.getElementById("edit_farm_size").value = farm_size;
    document.getElementById("edit_crop_type").value = crop_type;
    document.getElementById("editForm").classList.add("active");
}

// Close on overlay click
document.querySelectorAll(".modal-overlay").forEach(function(overlay) {
    overlay.addEventListener("click", function(e) {
        if (e.target === overlay) overlay.classList.remove("active");
    });
});

// Filter toggle
document.addEventListener("DOMContentLoaded", function () {
    const filterBtn = document.getElementById("filter-btn");
    const filterBox = document.getElementById("dropdown-filter");
    if (filterBtn) {
        filterBtn.addEventListener("click", function () {
            filterBox.style.display = filterBox.style.display === "none" ? "block" : "none";
        });
    }
});
</script>