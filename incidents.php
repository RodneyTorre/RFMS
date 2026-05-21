<?php
session_start();

if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

include 'database.php';

$page_title = "Incidents & Claims";

/* =========================
   FETCH INCIDENTS
========================= */
$incidents = [];
$sql = "SELECT * FROM incidents ORDER BY date_reported DESC";
$result = mysqli_query($conn, $sql);
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $incidents[] = $row;
    }
}

/* =========================
   STATS
========================= */
$totalActive   = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM incidents WHERE status='Assessment'"))['total'] ?? 0;
$totalClaims   = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM incidents"))['total'] ?? 0;
$totalApproved = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM incidents WHERE status='Approved'"))['total'] ?? 0;
$totalPayout   = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(damage) as total FROM incidents WHERE status='Approved'"))['total'] ?? 0;

$typeOptions = [];
$typeResult  = mysqli_query($conn, "SELECT DISTINCT type FROM incidents ORDER BY type ASC");
if ($typeResult) {
    while ($row = mysqli_fetch_assoc($typeResult)) {
        $typeOptions[] = $row['type'];
    }
}

include 'header.php';
?>

<link rel="stylesheet" href="assets/css/incidents.css">

<style>
.page-header { display:flex; justify-content:space-between; align-items:flex-start; flex-wrap:wrap; gap:1rem; margin-bottom:1.5rem; }
.page-title-section h1 { font-size:22px; font-weight:600; }
.page-subtitle { font-size:14px; color:#6b7280; margin-top:3px; }
.page-actions { display:flex; gap:8px; flex-wrap:wrap; }

.btn { display:inline-flex; align-items:center; gap:6px; padding:8px 14px; border-radius:8px; border:1px solid rgba(0,0,0,0.18); background:#fff; color:#1a1a1a; font-size:13px; cursor:pointer; font-family:inherit; transition:background .15s; }
.btn:hover { background:#f3f4f6; }
.btn-primary { background:#1D9E75; color:#fff; border-color:#0F6E56; }
.btn-primary:hover { background:#0F6E56; }
.btn-secondary { background:#fff; color:#374151; border-color:rgba(0,0,0,0.18); }
.btn-secondary:hover { background:#f9fafb; }
.btn-danger { background:#fee2e2; color:#dc2626; border-color:#fca5a5; }
.btn-danger:hover { background:#fecaca; }
.btn i, .btn .material-icons { font-size:16px; }

.stats-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(180px,1fr)); gap:12px; margin-bottom:1.5rem; }
.stat-card { background:#fff; border:1px solid rgba(0,0,0,0.08); border-radius:12px; padding:1rem 1.25rem; display:flex; align-items:center; gap:12px; }
.stat-icon { width:40px; height:40px; border-radius:8px; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
.stat-icon.red    { background:#fee2e2; color:#dc2626; }
.stat-icon.orange { background:#ffedd5; color:#ea580c; }
.stat-icon.green  { background:#dcfce7; color:#16a34a; }
.stat-icon.purple { background:#ede9fe; color:#7c3aed; }
.stat-icon .material-icons { font-size:20px; }
.stat-value { font-size:20px; font-weight:600; color:#1a1a1a; }
.stat-label { font-size:12px; color:#6b7280; margin-top:2px; }

.tabs-container { display:flex; gap:4px; border-bottom:1px solid rgba(0,0,0,0.1); margin-bottom:1.25rem; flex-wrap:wrap; }
.tab-btn { padding:8px 16px; border:none; background:none; color:#6b7280; font-size:14px; cursor:pointer; border-bottom:2px solid transparent; margin-bottom:-1px; transition:color .15s, border-color .15s; font-family:inherit; }
.tab-btn.active { color:#1D9E75; border-bottom-color:#1D9E75; font-weight:500; }

.filter-bar { display:flex; align-items:center; gap:10px; margin-bottom:1rem; flex-wrap:wrap; }
.filter-bar label { font-size:13px; color:#6b7280; }
.result-count { font-size:12px; color:#6b7280; margin-left:auto; }

.table-card { background:#fff; border:1px solid rgba(0,0,0,0.08); border-radius:12px; overflow:hidden; }
.data-table { width:100%; border-collapse:collapse; font-size:13px; }
.data-table thead tr { background:#f9fafb; }
.data-table th { text-align:left; padding:10px 12px; border-bottom:1px solid rgba(0,0,0,0.08); color:#6b7280; font-weight:600; font-size:11px; letter-spacing:.04em; white-space:nowrap; }
.data-table td { padding:11px 12px; border-bottom:1px solid rgba(0,0,0,0.06); color:#1a1a1a; vertical-align:middle; }
.data-table tbody tr:last-child td { border-bottom:none; }
.data-table tbody tr:hover { background:#f9fafb; }

/* action column — no row-click on buttons */
.action-btns { display:flex; gap:6px; }
.action-btn { display:inline-flex; align-items:center; justify-content:center; width:30px; height:30px; border-radius:7px; border:1px solid rgba(0,0,0,0.1); background:#fff; cursor:pointer; transition:background .15s; }
.action-btn:hover { background:#f3f4f6; }
.action-btn.edit  { color:#1D9E75; }
.action-btn.edit:hover { background:#dcfce7; border-color:#86efac; }
.action-btn.delete { color:#dc2626; }
.action-btn.delete:hover { background:#fee2e2; border-color:#fca5a5; }
.action-btn .material-icons { font-size:15px; }

.status-badge { display:inline-flex; align-items:center; gap:4px; padding:4px 10px; border-radius:99px; font-size:11px; font-weight:600; white-space:nowrap; }
.status-badge.assessment  { background:#fef3c7; color:#92400e; }
.status-badge.processing  { background:#dbeafe; color:#1e40af; }
.status-badge.approved    { background:#dcfce7; color:#166534; }
.status-badge.rejected    { background:#fee2e2; color:#991b1b; }
.status-badge.closed      { background:#f3f4f6; color:#374151; }
.status-badge.already-claimed { background:#dcfce7; color:#166534; }
.status-badge.not-claimed     { background:#fee2e2; color:#991b1b; }

.empty-state { text-align:center; padding:3rem 1rem; color:#6b7280; }
.empty-state .material-icons { font-size:48px; margin-bottom:12px; opacity:.3; display:block; }
.empty-state p { font-size:14px; margin:0; }
#emptyRow { display:none; }

.modal { display:none; position:fixed; inset:0; background:rgba(15,23,42,0.55); backdrop-filter:blur(4px); justify-content:center; align-items:center; z-index:9999; }
.modal.open { display:flex; }
.modal-card { width:460px; max-width:95vw; background:#fff; border-radius:14px; padding:0; box-shadow:0 20px 60px rgba(0,0,0,0.25); animation:slideUp .2s ease; overflow:hidden; }
@keyframes slideUp { from { opacity:0; transform:translateY(-10px); } to { opacity:1; transform:translateY(0); } }
.modal-header { display:flex; justify-content:space-between; align-items:flex-start; padding:1.25rem 1.5rem 0; }
.modal-header h2 { font-size:18px; font-weight:600; color:#0f172a; margin:0; }
.modal-header p  { font-size:13px; color:#64748b; margin:4px 0 0; }
.modal-close { background:none; border:none; cursor:pointer; color:#94a3b8; padding:4px; border-radius:6px; line-height:1; font-size:20px; }
.modal-close:hover { background:#f1f5f9; color:#475569; }
.modal-body { padding:1.25rem 1.5rem; }
.modal-footer { display:flex; justify-content:flex-end; gap:10px; padding:1rem 1.5rem; border-top:1px solid #f1f5f9; }

.form-grid { display:grid; grid-template-columns:1fr 1fr; gap:14px; }
.form-group { display:flex; flex-direction:column; gap:6px; }
.form-group.full { grid-column:1/-1; }
.form-group label { font-size:12px; font-weight:600; color:#475569; letter-spacing:.03em; }
.form-group input,
.form-group select,
.form-group textarea { padding:10px 12px; border:1px solid #e2e8f0; border-radius:10px; font-size:14px; font-family:inherit; outline:none; transition:.2s; background:#fff; color:#0f172a; }
.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus { border-color:#1D9E75; box-shadow:0 0 0 3px rgba(29,158,117,0.15); }
.form-group textarea { resize:vertical; min-height:72px; }

.btn-save   { background:#1D9E75; color:#fff; border:none; padding:10px 16px; border-radius:10px; font-size:13px; font-weight:600; cursor:pointer; font-family:inherit; transition:.2s; display:inline-flex; align-items:center; gap:6px; }
.btn-save:hover { background:#0F6E56; }
.btn-cancel { background:#f1f5f9; color:#334155; border:1px solid #e2e8f0; padding:10px 16px; border-radius:10px; font-size:13px; font-weight:600; cursor:pointer; font-family:inherit; transition:.2s; }
.btn-cancel:hover { background:#e2e8f0; }
.btn-delete-confirm { background:#dc2626; color:#fff; border:none; padding:10px 16px; border-radius:10px; font-size:13px; font-weight:600; cursor:pointer; font-family:inherit; transition:.2s; display:inline-flex; align-items:center; gap:6px; }
.btn-delete-confirm:hover { background:#b91c1c; }

.detail-modal-card { width:540px; max-width:95vw; }
.detail-grid { display:grid; grid-template-columns:1fr 1fr; gap:14px; }
.detail-item { display:flex; flex-direction:column; gap:4px; }
.detail-item.full { grid-column:1/-1; }
.detail-label { font-size:11px; font-weight:600; color:#94a3b8; letter-spacing:.05em; text-transform:uppercase; }
.detail-value { font-size:14px; color:#0f172a; font-weight:500; }

#filterDropdown { display:none; position:absolute; top:calc(100% + 4px); right:0; background:#fff; border:1px solid rgba(0,0,0,0.1); border-radius:10px; box-shadow:0 8px 24px rgba(0,0,0,0.12); padding:8px; min-width:200px; z-index:50; }
#filterDropdown.open { display:block; }
.filter-wrap { position:relative; }
.filter-option { display:flex; align-items:center; gap:8px; padding:7px 10px; border-radius:6px; font-size:13px; cursor:pointer; color:#374151; }
.filter-option:hover { background:#f3f4f6; }
.filter-option input[type=checkbox] { accent-color:#1D9E75; width:14px; height:14px; cursor:pointer; }
.filter-divider { height:1px; background:#f1f5f9; margin:4px 0; }
.filter-actions { display:flex; justify-content:flex-end; gap:6px; padding:6px 4px 2px; }
.filter-actions button { font-size:12px; padding:5px 10px; border-radius:6px; border:none; cursor:pointer; font-family:inherit; }
.filter-clear { background:#f1f5f9; color:#374151; }
.filter-apply { background:#1D9E75; color:#fff; }

/* delete confirm icon */
.delete-icon-wrap { display:flex; justify-content:center; margin-bottom:12px; }
.delete-icon-wrap .material-icons { font-size:48px; color:#dc2626; background:#fee2e2; border-radius:50%; padding:12px; }
</style>

<!-- ── Page header ── -->
<div class="page-header">
    <div class="page-title-section">
        <h1>Incidents &amp; Claims</h1>
        <p class="page-subtitle">Disaster reports, assessments, and claims</p>
    </div>
    <div class="page-actions">
        <div class="filter-wrap">
            <button class="btn btn-secondary" onclick="toggleFilterDropdown(event)">
                <span class="material-icons">filter_list</span>
                Filter Type
            </button>
            <div id="filterDropdown">
                <div class="filter-option">
                    <input type="checkbox" id="ft-all" checked onchange="toggleAllTypes(this)">
                    <label for="ft-all" style="cursor:pointer;font-weight:600">All types</label>
                </div>
                <div class="filter-divider"></div>
                <?php foreach ($typeOptions as $t): ?>
                <div class="filter-option">
                    <input type="checkbox" class="type-cb"
                           value="<?= htmlspecialchars($t) ?>"
                           id="ft-<?= htmlspecialchars(strtolower(str_replace(' ', '-', $t))) ?>"
                           checked onchange="onTypeCbChange()">
                    <label for="ft-<?= htmlspecialchars(strtolower(str_replace(' ', '-', $t))) ?>"
                           style="cursor:pointer"><?= htmlspecialchars($t) ?></label>
                </div>
                <?php endforeach; ?>
                <div class="filter-divider"></div>
                <div class="filter-actions">
                    <button class="filter-clear" onclick="clearTypeFilter()">Clear</button>
                    <button class="filter-apply" onclick="applyTypeFilter()">Apply</button>
                </div>
            </div>
        </div>
        <button class="btn btn-primary" onclick="openIncidentModal()">
            <span class="material-icons">add</span>
            Report Incident
        </button>
    </div>
</div>

<!-- ── Stats ── -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon red"><span class="material-icons">warning</span></div>
        <div>
            <div class="stat-value"><?= $totalActive ?></div>
            <div class="stat-label">Active Incidents</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon orange"><span class="material-icons">description</span></div>
        <div>
            <div class="stat-value"><?= $totalClaims ?></div>
            <div class="stat-label">Claims Filed</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green"><span class="material-icons">check_circle</span></div>
        <div>
            <div class="stat-value"><?= $totalApproved ?></div>
            <div class="stat-label">Claims Approved</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon purple"><span class="material-icons">payments</span></div>
        <div>
            <div class="stat-value">₱<?= number_format($totalPayout, 2) ?></div>
            <div class="stat-label">Total Payout</div>
        </div>
    </div>
</div>

<!-- ── Tabs ── -->
<div class="tabs-container">
    <button class="tab-btn active" onclick="filterIncidentTab('all', this)">All Incidents</button>
</div>

<!-- ── Active filter bar ── -->
<div class="filter-bar" id="filterBar" style="display:none">
    <span class="material-icons" style="font-size:16px;color:#6b7280">filter_alt</span>
    <label>Filtered by type:</label>
    <span id="activeFilterTags" style="font-size:13px;color:#1D9E75;font-weight:500"></span>
    <button class="btn" style="padding:4px 10px;font-size:12px" onclick="clearTypeFilter()">
        <span class="material-icons" style="font-size:14px">close</span> Clear
    </button>
    <span class="result-count" id="resultCount"></span>
</div>

<!-- ── Table ── -->
<div class="table-card">
    <table class="data-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>TYPE</th>
                <th>LOCATION</th>
                <th>DATE REPORTED</th>
                <th>AFFECTED</th>
                <th>DAMAGE (₱)</th>
                <th>STATUS</th>
                <th>CLAIM STATUS</th>
                <th>ACTIONS</th>
            </tr>
        </thead>
        <tbody id="incidentTableBody">
            <?php if (empty($incidents)): ?>
            <tr>
                <td colspan="9">
                    <div class="empty-state">
                        <span class="material-icons">report_off</span>
                        <p>No incidents recorded yet.</p>
                    </div>
                </td>
            </tr>
            <?php else: ?>
            <?php foreach ($incidents as $inc):
                $rawStatus   = trim($inc['status']);
                $statusClass = strtolower($rawStatus);
                $claimStatus = trim($inc['claim_status']) ?: 'Not Claimed';
                $claimClass  = (strtolower($claimStatus) === 'already claimed') ? 'already-claimed' : 'not-claimed';
            ?>
            <tr
                class="incident-row"
                data-status="<?= htmlspecialchars($statusClass) ?>"
                data-type="<?= htmlspecialchars($inc['type']) ?>"
                data-id="<?= htmlspecialchars($inc['incident_code']) ?>"
                data-dbid="<?= htmlspecialchars($inc['id']) ?>"
                data-location="<?= htmlspecialchars($inc['location']) ?>"
                data-date="<?= htmlspecialchars(date('M d, Y', strtotime($inc['date_reported']))) ?>"
                data-affected="<?= htmlspecialchars($inc['affected']) ?>"
                data-damage="<?= htmlspecialchars(number_format($inc['damage'], 2)) ?>"
                data-remarks="<?= htmlspecialchars($inc['remarks'] ?? '') ?>"
                data-rawstatus="<?= htmlspecialchars($rawStatus) ?>"
            >
                <td style="font-weight:500;font-family:monospace"><?= htmlspecialchars($inc['incident_code']) ?></td>
                <td><?= htmlspecialchars($inc['type']) ?></td>
                <td><?= htmlspecialchars($inc['location']) ?></td>
                <td><?= date("M d, Y", strtotime($inc['date_reported'])) ?></td>
                <td><?= number_format($inc['affected']) ?></td>
                <td>₱<?= number_format($inc['damage'], 2) ?></td>
                <td>
                    <span class="status-badge <?= $statusClass ?>">
                        <?= htmlspecialchars($rawStatus) ?>
                    </span>
                </td>
                <td>
                    <span class="status-badge <?= $claimClass ?>">
                        <?= htmlspecialchars($claimStatus) ?>
                    </span>
                </td>
                <td>
                    <div class="action-btns">
                        <button class="action-btn edit" title="Edit Status"
                            onclick="openEditModal(this)" type="button">
                            <span class="material-icons">edit</span>
                        </button>
                        <button class="action-btn delete" title="Delete"
                            onclick="openDeleteModal(this)" type="button">
                            <span class="material-icons">delete</span>
                        </button>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
            <tr id="emptyRow">
                <td colspan="9">
                    <div class="empty-state">
                        <span class="material-icons">search_off</span>
                        <p id="emptyMessage">No incidents match the current filters.</p>
                    </div>
                </td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- ── Report Incident modal ── -->
<div id="incidentModal" class="modal">
    <div class="modal-card">
        <div class="modal-header">
            <div>
                <h2>Report Incident</h2>
                <p>Fill in all details for the new incident record</p>
            </div>
            <button class="modal-close" onclick="closeIncidentModal()" aria-label="Close">&#x2715;</button>
        </div>
        <form method="POST" action="save_incident.php">
            <div class="modal-body">
                <div class="form-grid">
                    <div class="form-group">
                        <label>Incident Type</label>
                        <select name="type" required>
                            <option value="">Select type</option>
                            <option>Typhoon</option>
                            <option>Flooding</option>
                            <option>Drought</option>
                            <option>Pest Outbreak</option>
                            <option>Hailstorm</option>
                            <option>Fire</option>
                            <option>Other</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Date Reported</label>
                        <input type="date" name="date_reported" required>
                    </div>
                    <div class="form-group full">
                        <label>Location</label>
                        <input type="text" name="location" placeholder="e.g. Barangay San Jose, Camarines Sur" required>
                    </div>
                    <div class="form-group">
                        <label>Affected Farmers / Fisherfolk</label>
                        <input type="number" name="affected" placeholder="0" min="0" required>
                    </div>
                    <div class="form-group">
                        <label>Estimated Damage (₱)</label>
                        <input type="number" name="damage" placeholder="0.00" min="0" step="0.01" required>
                    </div>
                    <div class="form-group full">
                        <label>Status</label>
                        <select name="status" required>
                            <option value="">Select status</option>
                            <option value="Assessment">Assessment</option>
                            <option value="Processing">Processing</option>
                            <option value="Approved">Approved</option>
                            <option value="Rejected">Rejected</option>
                        </select>
                    </div>
                    <div class="form-group full">
                        <label>Remarks (optional)</label>
                        <textarea name="remarks" placeholder="Additional notes or observations…"></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-cancel" onclick="closeIncidentModal()">Cancel</button>
                <button type="submit" class="btn-save">
                    <span class="material-icons" style="font-size:16px">save</span>
                    Save Incident
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ── Edit Status modal ── -->
<div id="editModal" class="modal">
    <div class="modal-card">
        <div class="modal-header">
            <div>
                <h2>Edit Status</h2>
                <p id="editSubtitle">Update the status for this incident</p>
            </div>
            <button class="modal-close" onclick="closeEditModal()" aria-label="Close">&#x2715;</button>
        </div>
        <form method="POST" action="update_incident.php">
            <input type="hidden" name="id" id="editId">
            <div class="modal-body">
                <div class="form-grid">
                    <div class="form-group full">
                        <label>Status</label>
                        <select name="status" id="editStatus" required>
                            <option value="Assessment">Assessment</option>
                            <option value="Processing">Processing</option>
                            <option value="Approved">Approved</option>
                            <option value="Rejected">Rejected</option>
                        </select>
                    </div>
                    <div class="form-group full">
                        <label>Remarks (optional)</label>
                        <textarea name="remarks" id="editRemarks" placeholder="Additional notes…"></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-cancel" onclick="closeEditModal()">Cancel</button>
                <button type="submit" class="btn-save">
                    <span class="material-icons" style="font-size:16px">save</span>
                    Update
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ── Delete Confirm modal ── -->
<div id="deleteModal" class="modal">
    <div class="modal-card" style="width:400px">
        <div class="modal-header" style="padding-bottom:0">
            <div></div>
            <button class="modal-close" onclick="closeDeleteModal()" aria-label="Close">&#x2715;</button>
        </div>
        <div class="modal-body" style="text-align:center;padding-top:.5rem">
            <div class="delete-icon-wrap">
                <span class="material-icons">delete_forever</span>
            </div>
            <h2 style="font-size:17px;font-weight:600;color:#0f172a;margin:0 0 8px">Delete Incident?</h2>
            <p style="font-size:13px;color:#64748b;margin:0">
                You are about to delete <strong id="deleteIncidentCode"></strong>. This cannot be undone.
            </p>
        </div>
        <div class="modal-footer" style="justify-content:center;gap:12px">
            <button type="button" class="btn-cancel" onclick="closeDeleteModal()">Cancel</button>
            <form method="POST" action="delete_incident.php" style="margin:0">
                <input type="hidden" name="id" id="deleteId">
                <button type="submit" class="btn-delete-confirm">
                    <span class="material-icons" style="font-size:15px">delete</span>
                    Yes, Delete
                </button>
            </form>
        </div>
    </div>
</div>

<script>
// ── Report Incident Modal ─────────────────────────────────────────────────
function openIncidentModal() {
    document.getElementById('incidentModal').classList.add('open');
}
function closeIncidentModal() {
    document.getElementById('incidentModal').classList.remove('open');
}
document.getElementById('incidentModal').addEventListener('click', e => {
    if (e.target === e.currentTarget) closeIncidentModal();
});

// ── Edit Status Modal ─────────────────────────────────────────────────────
function openEditModal(btn) {
    const row = btn.closest('tr');
    document.getElementById('editId').value         = row.getAttribute('data-dbid');
    document.getElementById('editStatus').value     = row.getAttribute('data-rawstatus');
    document.getElementById('editRemarks').value    = row.getAttribute('data-remarks');
    document.getElementById('editSubtitle').textContent =
        row.getAttribute('data-id') + ' · ' + row.getAttribute('data-type');
    document.getElementById('editModal').classList.add('open');
}
function closeEditModal() {
    document.getElementById('editModal').classList.remove('open');
}
document.getElementById('editModal').addEventListener('click', e => {
    if (e.target === e.currentTarget) closeEditModal();
});

// ── Delete Confirm Modal ──────────────────────────────────────────────────
function openDeleteModal(btn) {
    const row = btn.closest('tr');
    document.getElementById('deleteId').value              = row.getAttribute('data-dbid');
    document.getElementById('deleteIncidentCode').textContent = row.getAttribute('data-id');
    document.getElementById('deleteModal').classList.add('open');
}
function closeDeleteModal() {
    document.getElementById('deleteModal').classList.remove('open');
}
document.getElementById('deleteModal').addEventListener('click', e => {
    if (e.target === e.currentTarget) closeDeleteModal();
});

// ── Tab filter ────────────────────────────────────────────────────────────
let activeTab   = 'all';
let activeTypes = null;

function filterIncidentTab(tabValue, element) {
    activeTab = tabValue;
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    element.classList.add('active');
    applyFilters();
}

// ── Type filter dropdown ──────────────────────────────────────────────────
function toggleFilterDropdown(e) {
    e.stopPropagation();
    document.getElementById('filterDropdown').classList.toggle('open');
}
document.addEventListener('click', () => {
    document.getElementById('filterDropdown').classList.remove('open');
});
document.getElementById('filterDropdown').addEventListener('click', e => e.stopPropagation());

function toggleAllTypes(cb) {
    document.querySelectorAll('.type-cb').forEach(c => c.checked = cb.checked);
}
function onTypeCbChange() {
    const allCb = document.getElementById('ft-all');
    allCb.checked = [...document.querySelectorAll('.type-cb')].every(c => c.checked);
}
function clearTypeFilter() {
    document.querySelectorAll('.type-cb').forEach(c => c.checked = true);
    document.getElementById('ft-all').checked = true;
    activeTypes = null;
    document.getElementById('filterDropdown').classList.remove('open');
    document.getElementById('activeFilterTags').textContent = '';
    applyFilters();
}
function applyTypeFilter() {
    const checked = [...document.querySelectorAll('.type-cb:checked')].map(c => c.value);
    const total   = document.querySelectorAll('.type-cb').length;
    activeTypes   = (checked.length === 0 || checked.length === total) ? null : checked;
    document.getElementById('filterDropdown').classList.remove('open');
    applyFilters();
}

// ── Core filter logic ─────────────────────────────────────────────────────
function applyFilters() {
    const rows     = document.querySelectorAll('.incident-row');
    const emptyRow = document.getElementById('emptyRow');
    let visible    = 0;

    rows.forEach(row => {
        const rowStatus = row.getAttribute('data-status').trim();
        const rowType   = row.getAttribute('data-type');
        const tabMatch  = (activeTab === 'all') || (rowStatus === activeTab);
        const typeMatch = !activeTypes || activeTypes.includes(rowType);
        const show      = tabMatch && typeMatch;
        row.style.display = show ? '' : 'none';
        if (show) visible++;
    });

    if (emptyRow) {
        if (visible === 0) {
            emptyRow.style.display = '';
            const tabLabel = document.querySelector('.tab-btn.active').textContent.trim();
            document.getElementById('emptyMessage').textContent =
                activeTypes
                    ? `No "${tabLabel}" incidents match the selected type filter.`
                    : `No incidents found under "${tabLabel}".`;
        } else {
            emptyRow.style.display = 'none';
        }
    }

    const filterBar = document.getElementById('filterBar');
    if (activeTypes && activeTypes.length > 0) {
        filterBar.style.display = 'flex';
        document.getElementById('activeFilterTags').textContent = activeTypes.join(', ');
        document.getElementById('resultCount').textContent =
            visible + ' result' + (visible !== 1 ? 's' : '');
    } else {
        filterBar.style.display = 'none';
    }
}
</script>