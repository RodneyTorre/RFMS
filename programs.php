<?php
// programs.php - Programs & Interventions Page
session_start();

date_default_timezone_set('Asia/Manila');
include 'database.php';

/* =========================
   AUTO UPDATE STATUS
========================= */

mysqli_query($conn, "
    UPDATE distributions
    SET status = CASE
        WHEN DATE(date) < CURDATE() THEN 'Completed'
        WHEN DATE(date) = CURDATE() THEN 'Ongoing'
        WHEN DATE(date) > CURDATE() THEN 'Scheduled'
    END
");

mysqli_query($conn, "
    UPDATE trainings
    SET status = CASE
        WHEN DATE(date) < CURDATE() THEN 'Completed'
        WHEN DATE(date) = CURDATE() THEN 'Ongoing'
        WHEN DATE(date) > CURDATE() THEN 'Scheduled'
    END
");

$page_title = "Programs & Interventions";

/* =========================
   STATS SECTION
========================= */

$seedData = ['total' => 0];
$seedResult = mysqli_query($conn, "
    SELECT SUM(quantity) as total
    FROM distributions
    WHERE item_name LIKE '%Seed%'
");
if ($seedResult) {
    $seedData = mysqli_fetch_assoc($seedResult);
}

$trainingData = ['total' => 0];
$trainingResult = mysqli_query($conn, "SELECT COUNT(*) as total FROM trainings");
if ($trainingResult) {
    $trainingData = mysqli_fetch_assoc($trainingResult);
}

/* =========================
   DISTRIBUTIONS DATA
========================= */

$distributions = [];
$result = mysqli_query($conn, "SELECT * FROM distributions ORDER BY date DESC");
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $distributions[] = $row;
    }
}

/* =========================
   TRAININGS DATA
========================= */

$trainings = [];
$result2 = mysqli_query($conn, "SELECT * FROM trainings ORDER BY date DESC");
if ($result2) {
    while ($row = mysqli_fetch_assoc($result2)) {
        $trainings[] = $row;
    }
}

/* =========================
   STATS ARRAY
========================= */

$stats = [
    [
        'icon'  => 'inventory_2',
        'value' => ($seedData['total'] ?? 0) . ' Bags',
        'label' => 'Seeds Distributed',
        'color' => 'green'
    ],
    [
        'icon'  => 'menu_book',
        'value' => ($trainingData['total'] ?? 0) . ' Sessions',
        'label' => 'Training Sessions',
        'color' => 'purple'
    ]
];

/* =========================
   HELPER: STATUS BADGE
========================= */

function getStatusBadge(string $dateStr): string {
    $today = date('Y-m-d');
    if ($dateStr < $today) {
        $status = 'Completed';
    } elseif ($dateStr == $today) {
        $status = 'Ongoing';
    } else {
        $status = 'Scheduled';
    }
    $class = strtolower($status);
    return "<span class='status-badge {$class}'>{$status}</span>";
}

include 'header.php';
?>

<link rel="stylesheet" href="assets/css/programs.css">

<style>

/* ── Alerts ── */
.alert {
    padding: 14px 18px;
    border-radius: 10px;
    margin-bottom: 20px;
    font-size: 14px;
    font-weight: 500;
}
.success-alert { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
.error-alert   { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }

/* ── Modal backdrop ── */
.modal {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(15, 23, 42, 0.55);
    backdrop-filter: blur(4px);
    justify-content: center;
    align-items: center;
    z-index: 999;
}
.modal.open { display: flex; }

/* ── Modal card ── */
.modal-card {
    background: #fff;
    width: 100%;
    max-width: 460px;
    border-radius: 14px;
    padding: 0;
    box-shadow: 0 20px 60px rgba(0,0,0,0.25);
    animation: fadeIn .2s ease;
    overflow: hidden;
    max-height: 90vh;
    overflow-y: auto;
}

@keyframes fadeIn {
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
    font-size: 20px;
    line-height: 1;
}
.modal-close:hover { background: #f1f5f9; color: #475569; }

.modal-body   { padding: 1.25rem 1.5rem; }
.modal-footer {
    display: flex;
    justify-content: flex-end;
    gap: 10px;
    padding: 1rem 1.5rem;
    border-top: 1px solid #f1f5f9;
    background: #fff;
    position: sticky;
    bottom: 0;
}

/* ── Form ── */
.form-grid { display: flex; flex-direction: column; gap: 14px; }
.form-row  { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
.form-group { display: flex; flex-direction: column; gap: 6px; }
.form-group label {
    font-size: 12px;
    font-weight: 600;
    color: #475569;
    letter-spacing: .03em;
}
.form-group input,
.form-group select {
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
.form-group select {
    appearance: none;
    -webkit-appearance: none;
    background-image: url("data:image/svg+xml;utf8,<svg fill='%23666' height='20' viewBox='0 0 20 20' width='20' xmlns='http://www.w3.org/2000/svg'><path d='M5 7l5 5 5-5H5z'/></svg>");
    background-repeat: no-repeat;
    background-position: right 12px center;
    background-size: 16px;
    padding-right: 40px;
}
.form-group input:focus,
.form-group select:focus {
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
    text-decoration: none;
}
.btn-delete-confirm:hover { background: #b91c1c; }

/* ── Tabs ── */
.tab-content { display: none; }
.tab-content.active { display: block; }
.tab-btn.active { background: #10b981; color: #fff; }

/* ── Table ── */
.table-card {
    background: #fff;
    border: 1px solid rgba(0,0,0,0.08);
    border-radius: 14px;
    overflow: hidden;
    margin-top: 20px;
}
.data-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 13px;
}
.data-table thead tr { background: #f9fafb; }
.data-table th {
    text-align: left;
    padding: 10px 12px;
    border-bottom: 1px solid rgba(0,0,0,0.08);
    color: #6b7280;
    font-weight: 600;
    font-size: 11px;
    letter-spacing: .04em;
    white-space: nowrap;
}
.data-table td {
    padding: 11px 12px;
    border-bottom: 1px solid rgba(0,0,0,0.06);
    color: #1a1a1a;
    vertical-align: middle;
}
.data-table tbody tr:last-child td { border-bottom: none; }
.data-table tbody tr:hover { background: #f9fafb; }

/* ── Status badges ── */
.status-badge {
    display: inline-flex;
    align-items: center;
    padding: 4px 10px;
    border-radius: 99px;
    font-size: 11px;
    font-weight: 600;
    white-space: nowrap;
}
.status-badge.completed { background: #dcfce7; color: #166534; }
.status-badge.ongoing   { background: #dbeafe; color: #1e40af; }
.status-badge.scheduled { background: #ede9fe; color: #5b21b6; }

/* ── Action buttons ── */
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
.action-btn.edit   .material-icons { font-size: 15px; color: #1D9E75; }
.action-btn.delete .material-icons { font-size: 15px; color: #dc2626; }
.action-btn.edit:hover   { background: #dcfce7; border-color: #86efac; }
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
    <div class="page-header-left">
        <h1>Programs</h1>
        <p>Distribution, training, and extension services</p>
    </div>
    <div class="page-actions">
        <button class="btn btn-primary" onclick="openModal('distributionModal')">
            <span class="material-icons">add</span>
            New Distribution
        </button>
        <button class="btn btn-primary" onclick="openModal('trainingModal')">
            <span class="material-icons">event</span>
            Schedule Training
        </button>
    </div>
</div>

<!-- ══════════════════════════════════════════
     ADD DISTRIBUTION MODAL
══════════════════════════════════════════ -->
<div id="distributionModal" class="modal">
    <div class="modal-card">
        <div class="modal-header">
            <div>
                <h2>New Distribution</h2>
                <p>Add distribution details for farmers</p>
            </div>
            <button class="modal-close" onclick="closeModal('distributionModal')" aria-label="Close">&#x2715;</button>
        </div>
        <form method="POST" action="save_distribution.php">
            <div class="modal-body">
                <div class="form-grid">
                    <div class="form-group">
                        <label>Program Name</label>
                        <input type="text" name="program" placeholder="Enter program name" required>
                    </div>
                    <div class="form-group">
                        <label>Location</label>
                        <input type="text" name="location" placeholder="Venue or barangay" required>
                    </div>
                    <div class="form-group">
                        <label>Item</label>
                        <select name="item" required>
                            <option value="">Select Item</option>
                            <?php
                            $items = mysqli_query($conn, "SELECT item_name FROM inventory");
                            while ($row = mysqli_fetch_assoc($items)):
                            ?>
                                <option value="<?= htmlspecialchars($row['item_name']) ?>">
                                    <?= htmlspecialchars($row['item_name']) ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Quantity</label>
                            <input type="number" name="quantity" placeholder="0" min="1" required>
                        </div>
                        <div class="form-group">
                            <label>Beneficiaries</label>
                            <input type="number" name="beneficiaries" placeholder="0" min="1" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Distribution Date</label>
                        <input type="date" name="date" required>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-cancel" onclick="closeModal('distributionModal')">Cancel</button>
                <button type="submit" class="btn-save">
                    <span class="material-icons" style="font-size:16px">save</span>
                    Save Distribution
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ══════════════════════════════════════════
     EDIT DISTRIBUTION MODAL
══════════════════════════════════════════ -->
<div id="editDistributionModal" class="modal">
    <div class="modal-card">
        <div class="modal-header">
            <div>
                <h2>Edit Distribution</h2>
                <p id="editDistSubtitle">Update distribution details</p>
            </div>
            <button class="modal-close" onclick="closeModal('editDistributionModal')" aria-label="Close">&#x2715;</button>
        </div>
        <form method="POST" action="update_distribution.php">
            <input type="hidden" name="id" id="editDistId">
            <div class="modal-body">
                <div class="form-grid">
                    <div class="form-group">
                        <label>Program Name</label>
                        <input type="text" name="program" id="editDistProgram" required>
                    </div>
                    <div class="form-group">
                        <label>Location</label>
                        <input type="text" name="location" id="editDistLocation" placeholder="Venue or barangay" required>
                    </div>
                    <div class="form-group">
                        <label>Item</label>
                        <select name="item" id="editDistItem" required>
                            <option value="">Select Item</option>
                            <?php
                            $items2 = mysqli_query($conn, "SELECT item_name FROM inventory");
                            while ($row = mysqli_fetch_assoc($items2)):
                            ?>
                                <option value="<?= htmlspecialchars($row['item_name']) ?>">
                                    <?= htmlspecialchars($row['item_name']) ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Quantity</label>
                            <input type="number" name="quantity" id="editDistQuantity" min="1" required>
                        </div>
                        <div class="form-group">
                            <label>Beneficiaries</label>
                            <input type="number" name="beneficiaries" id="editDistBeneficiaries" min="1" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Distribution Date</label>
                        <input type="date" name="date" id="editDistDate" required>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-cancel" onclick="closeModal('editDistributionModal')">Cancel</button>
                <button type="submit" class="btn-save">
                    <span class="material-icons" style="font-size:16px">save</span>
                    Update Distribution
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ══════════════════════════════════════════
     ADD TRAINING MODAL
══════════════════════════════════════════ -->
<div id="trainingModal" class="modal">
    <div class="modal-card">
        <div class="modal-header">
            <div>
                <h2>Schedule Training</h2>
                <p>Fill in training details</p>
            </div>
            <button class="modal-close" onclick="closeModal('trainingModal')" aria-label="Close">&#x2715;</button>
        </div>
        <form method="POST" action="save_training.php">
            <div class="modal-body">
                <div class="form-grid">
                    <div class="form-group">
                        <label>Training Title</label>
                        <input type="text" name="title" placeholder="Enter training title" required>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Location</label>
                            <input type="text" name="location" placeholder="Venue or location" required>
                        </div>
                        <div class="form-group">
                            <label>Trainer</label>
                            <input type="text" name="trainer" placeholder="Trainer name" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Participants</label>
                            <input type="number" name="participants" placeholder="0" min="1" required>
                        </div>
                        <div class="form-group">
                            <label>Date</label>
                            <input type="date" name="date" required>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-cancel" onclick="closeModal('trainingModal')">Cancel</button>
                <button type="submit" class="btn-save">
                    <span class="material-icons" style="font-size:16px">event_available</span>
                    Save Training
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ══════════════════════════════════════════
     EDIT TRAINING MODAL
══════════════════════════════════════════ -->
<div id="editTrainingModal" class="modal">
    <div class="modal-card">
        <div class="modal-header">
            <div>
                <h2>Edit Training</h2>
                <p id="editTrnSubtitle">Update training details</p>
            </div>
            <button class="modal-close" onclick="closeModal('editTrainingModal')" aria-label="Close">&#x2715;</button>
        </div>
        <form method="POST" action="update_training.php">
            <input type="hidden" name="id" id="editTrnId">
            <div class="modal-body">
                <div class="form-grid">
                    <div class="form-group">
                        <label>Training Title</label>
                        <input type="text" name="title" id="editTrnTitle" required>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Location</label>
                            <input type="text" name="location" id="editTrnLocation" required>
                        </div>
                        <div class="form-group">
                            <label>Trainer</label>
                            <input type="text" name="trainer" id="editTrnTrainer" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Participants</label>
                            <input type="number" name="participants" id="editTrnParticipants" min="1" required>
                        </div>
                        <div class="form-group">
                            <label>Date</label>
                            <input type="date" name="date" id="editTrnDate" required>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-cancel" onclick="closeModal('editTrainingModal')">Cancel</button>
                <button type="submit" class="btn-save">
                    <span class="material-icons" style="font-size:16px">save</span>
                    Update Training
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ══════════════════════════════════════════
     DELETE CONFIRM MODAL (shared)
══════════════════════════════════════════ -->
<div id="deleteModal" class="modal">
    <div class="modal-card" style="max-width:400px">
        <div class="modal-header" style="padding-bottom:0">
            <div></div>
            <button class="modal-close" onclick="closeModal('deleteModal')" aria-label="Close">&#x2715;</button>
        </div>
        <div class="modal-body" style="text-align:center;padding-top:.5rem">
            <div class="delete-icon-wrap">
                <span class="material-icons">delete_forever</span>
            </div>
            <h2 style="font-size:17px;font-weight:600;color:#0f172a;margin:0 0 8px">Delete Record?</h2>
            <p style="font-size:13px;color:#64748b;margin:0">
                You are about to delete <strong id="deleteRecordName"></strong>. This cannot be undone.
            </p>
        </div>
        <div class="modal-footer" style="justify-content:center;gap:12px">
            <button type="button" class="btn-cancel" onclick="closeModal('deleteModal')">Cancel</button>
            <a id="deleteConfirmLink" href="#" class="btn-delete-confirm">
                <span class="material-icons" style="font-size:15px">delete</span>
                Yes, Delete
            </a>
        </div>
    </div>
</div>

<!-- ── Stats ── -->
<div class="stats-grid">
    <?php foreach ($stats as $stat): ?>
    <div class="stat-card">
        <div class="stat-icon <?= $stat['color'] ?>">
            <span class="material-icons"><?= $stat['icon'] ?></span>
        </div>
        <div class="stat-content">
            <div class="stat-value"><?= $stat['value'] ?></div>
            <div class="stat-label"><?= $stat['label'] ?></div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- ── Tabs ── -->
<div class="tabs-container">
    <button class="tab-btn active" onclick="showTab('distribution', this)">Input Distribution</button>
    <button class="tab-btn" onclick="showTab('training', this)">Training</button>
</div>

<!-- ══════════════════════════════════════════
     DISTRIBUTION TAB
══════════════════════════════════════════ -->
<div id="distribution" class="tab-content active">
    <div class="table-card">
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>PROGRAM</th>
                    <th>LOCATION</th>
                    <th>ITEM</th>
                    <th>QUANTITY</th>
                    <th>BENEFICIARIES</th>
                    <th>DATE</th>
                    <th>STATUS</th>
                    <th>ACTIONS</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($distributions)): ?>
                <tr>
                    <td colspan="9" style="text-align:center;color:#9ca3af;padding:30px;">
                        No distributions found.
                    </td>
                </tr>
                <?php else: ?>
                <?php foreach ($distributions as $dist): ?>
                <tr
                    data-id="<?= (int)($dist['distribution_id'] ?? 0) ?>"
                    data-code="<?= htmlspecialchars($dist['distribution_id'] ?? '', ENT_QUOTES) ?>"
                    data-program="<?= htmlspecialchars($dist['program'] ?? '', ENT_QUOTES) ?>"
                    data-location="<?= htmlspecialchars($dist['location'] ?? '', ENT_QUOTES) ?>"
                    data-item="<?= htmlspecialchars($dist['item_name'] ?? '', ENT_QUOTES) ?>"
                    data-quantity="<?= (int)($dist['quantity'] ?? 0) ?>"
                    data-beneficiaries="<?= (int)($dist['beneficiaries'] ?? 0) ?>"
                    data-date="<?= date('Y-m-d', strtotime($dist['date'])) ?>"
                >
                    <td style="font-family:monospace;font-weight:500"><?= htmlspecialchars($dist['distribution_id'] ?? '') ?></td>
                    <td><?= htmlspecialchars($dist['program'] ?? '') ?></td>
                    <td><?= htmlspecialchars($dist['location'] ?? '') ?></td>
                    <td><?= htmlspecialchars($dist['item_name'] ?? '') ?></td>
                    <td><?= htmlspecialchars($dist['quantity'] ?? '') ?></td>
                    <td><?= htmlspecialchars($dist['beneficiaries'] ?? '') ?></td>
                    <td><?= date("M d, Y", strtotime($dist['date'])) ?></td>
                    <td><?= getStatusBadge(date('Y-m-d', strtotime($dist['date']))) ?></td>
                    <td>
                        <div class="action-btns">
                            <button type="button" class="action-btn edit" title="Edit"
                                onclick="openEditDistribution(this)">
                                <span class="material-icons">edit</span>
                            </button>
                            <button type="button" class="action-btn delete" title="Delete"
                                onclick="openDeleteRecord(this, 'delete_distribution.php')">
                                <span class="material-icons">delete</span>
                            </button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- ══════════════════════════════════════════
     TRAINING TAB
══════════════════════════════════════════ -->
<div id="training" class="tab-content">
    <div class="table-card">
        <table class="data-table">
            <thead>
                <tr>
                    <th>TRAINING ID</th>
                    <th>TITLE</th>
                    <th>LOCATION</th>
                    <th>TRAINER</th>
                    <th>PARTICIPANTS</th>
                    <th>DATE</th>
                    <th>STATUS</th>
                    <th>ACTIONS</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($trainings)): ?>
                <tr>
                    <td colspan="8" style="text-align:center;color:#9ca3af;padding:30px;">
                        No trainings found.
                    </td>
                </tr>
                <?php else: ?>
                <?php foreach ($trainings as $trn): ?>
                <tr
                    data-id="<?= (int)($trn['training_id'] ?? 0) ?>"
                    data-code="<?= htmlspecialchars($trn['training_id'] ?? '', ENT_QUOTES) ?>"
                    data-title="<?= htmlspecialchars($trn['title'] ?? '', ENT_QUOTES) ?>"
                    data-location="<?= htmlspecialchars($trn['location'] ?? '', ENT_QUOTES) ?>"
                    data-trainer="<?= htmlspecialchars($trn['trainer'] ?? '', ENT_QUOTES) ?>"
                    data-participants="<?= (int)($trn['participants'] ?? 0) ?>"
                    data-date="<?= date('Y-m-d', strtotime($trn['date'])) ?>"
                >
                    <td style="font-family:monospace;font-weight:500"><?= htmlspecialchars($trn['training_id'] ?? '') ?></td>
                    <td><?= htmlspecialchars($trn['title'] ?? '') ?></td>
                    <td><?= htmlspecialchars($trn['location'] ?? '') ?></td>
                    <td><?= htmlspecialchars($trn['trainer'] ?? '') ?></td>
                    <td><?= htmlspecialchars($trn['participants'] ?? '') ?></td>
                    <td><?= date("M d, Y", strtotime($trn['date'])) ?></td>
                    <td><?= getStatusBadge(date('Y-m-d', strtotime($trn['date']))) ?></td>
                    <td>
                        <div class="action-btns">
                            <button type="button" class="action-btn edit" title="Edit"
                                onclick="openEditTraining(this)">
                                <span class="material-icons">edit</span>
                            </button>
                            <button type="button" class="action-btn delete" title="Delete"
                                onclick="openDeleteRecord(this, 'delete_training.php')">
                                <span class="material-icons">delete</span>
                            </button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>

// ── Generic open/close ────────────────────────────────────────────────────
function openModal(id) {
    document.getElementById(id).classList.add('open');
}
function closeModal(id) {
    document.getElementById(id).classList.remove('open');
}

// Close on backdrop click for all modals
document.querySelectorAll('.modal').forEach(modal => {
    modal.addEventListener('click', e => {
        if (e.target === modal) modal.classList.remove('open');
    });
});

// ── Tab switching ─────────────────────────────────────────────────────────
function showTab(tabId, element) {
    document.querySelectorAll('.tab-content').forEach(t => t.classList.remove('active'));
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    document.getElementById(tabId).classList.add('active');
    element.classList.add('active');
}

// ── Edit Distribution ─────────────────────────────────────────────────────
function openEditDistribution(btn) {
    const row = btn.closest('tr');
    document.getElementById('editDistId').value            = row.dataset.id;
    document.getElementById('editDistProgram').value       = row.dataset.program;
    document.getElementById('editDistLocation').value      = row.dataset.location;
    document.getElementById('editDistQuantity').value      = row.dataset.quantity;
    document.getElementById('editDistBeneficiaries').value = row.dataset.beneficiaries;
    document.getElementById('editDistDate').value          = row.dataset.date;
    document.getElementById('editDistSubtitle').textContent =
        row.dataset.code + ' · ' + row.dataset.program;

    // Match current item in the select
    const itemSelect = document.getElementById('editDistItem');
    [...itemSelect.options].forEach(opt => {
        opt.selected = (opt.value === row.dataset.item);
    });

    openModal('editDistributionModal');
}

// ── Edit Training ─────────────────────────────────────────────────────────
function openEditTraining(btn) {
    const row = btn.closest('tr');
    document.getElementById('editTrnId').value           = row.dataset.id;
    document.getElementById('editTrnTitle').value        = row.dataset.title;
    document.getElementById('editTrnLocation').value     = row.dataset.location;
    document.getElementById('editTrnTrainer').value      = row.dataset.trainer;
    document.getElementById('editTrnParticipants').value = row.dataset.participants;
    document.getElementById('editTrnDate').value         = row.dataset.date;
    document.getElementById('editTrnSubtitle').textContent =
        row.dataset.code + ' · ' + row.dataset.title;

    openModal('editTrainingModal');
}

// ── Delete (shared modal) ─────────────────────────────────────────────────
function openDeleteRecord(btn, endpoint) {
    const row  = btn.closest('tr');
    const name = row.dataset.program || row.dataset.title;
    document.getElementById('deleteRecordName').textContent = name;
    document.getElementById('deleteConfirmLink').href = endpoint + '?id=' + row.dataset.id;
    openModal('deleteModal');
}
function openDeleteModal(btn) {
    const row = btn.closest('tr');
    document.getElementById('deleteId').value              = row.getAttribute('data-dbid');
    document.getElementById('deleteIncidentCode').textContent = row.getAttribute('data-id');
    document.getElementById('deleteModal').classList.add('open');
}
</script>