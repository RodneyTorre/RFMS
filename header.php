<?php
include 'database.php';

$current_page = basename($_SERVER['PHP_SELF'], '.php');

if (isset($_SESSION['email'])) {
    $email = $_SESSION['email'];
    $name  = $_SESSION['name'] ?? $email;
    $initials = strtoupper(substr($name, 0, 1) . (strpos($name, ' ') !== false ? substr($name, strpos($name, ' ') + 1, 1) : substr($name, 1, 1)));
    $current_user = ['email' => $email, 'name' => $name, 'initials' => $initials];

    $sql  = "SELECT COUNT(*) AS unread_count FROM notifications WHERE user_id = ? AND status = 'unread'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $unread_count = $stmt->get_result()->fetch_assoc()['unread_count'] ?? 0;
} else {
    $current_user = ['email' => 'Guest', 'name' => 'Guest', 'initials' => 'G'];
    $unread_count = 0;
}

$nav_items = [
    ['id' => 'dashboard',  'label' => 'Dashboard',         'icon' => 'grid_view',        'url' => 'dashboard.php'],
    ['id' => 'registry',   'label' => 'Registry',          'icon' => 'people',           'url' => 'registry.php'],
    ['id' => 'programs',   'label' => 'Programs',          'icon' => 'volunteer_activism','url' => 'programs.php'],
    ['id' => 'insurance',  'label' => 'Insurance',         'icon' => 'security',         'url' => 'insurance.php'],
    ['id' => 'incidents',  'label' => 'Incidents & Claims','icon' => 'report_problem',   'url' => 'incidents.php'],
    ['id' => 'inventory',  'label' => 'Inventory',         'icon' => 'inventory_2',      'url' => 'inventory.php'],
    ['id' => 'reports',    'label' => 'Reports',           'icon' => 'bar_chart',        'url' => 'reports.php'],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo isset($page_title) ? htmlspecialchars($page_title) . ' — ' : ''; ?>RFIMS</title>
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
<style>
/* ═══════════════════════════════════════════════
   RESET & TOKENS
═══════════════════════════════════════════════ */
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

:root {
    --sidebar-w: 240px;
    --header-h:  60px;

    --brand:        #16a34a;
    --brand-dark:   #15803d;
    --brand-light:  #dcfce7;
    --brand-muted:  #bbf7d0;

    --bg:           #f0f4f0;
    --surface:      #ffffff;
    --surface-2:    #f8faf8;
    --border:       #e2e8e2;
    --border-light: #edf2ed;

    --text-primary:   #0f1f0f;
    --text-secondary: #4a5c4a;
    --text-muted:     #7a8f7a;

    --shadow-sm:  0 1px 3px rgba(0,0,0,.08);
    --shadow-md:  0 4px 16px rgba(0,0,0,.10);
    --shadow-lg:  0 8px 32px rgba(0,0,0,.12);

    --radius-sm:  6px;
    --radius-md:  10px;
    --radius-lg:  14px;
    --radius-xl:  20px;

    --font: 'Plus Jakarta Sans', sans-serif;
    --mono: 'DM Mono', monospace;

    --transition: 0.18s cubic-bezier(.4,0,.2,1);
}

html, body { height: 100%; font-family: var(--font); background: var(--bg); color: var(--text-primary); }

/* ═══════════════════════════════════════════════
   LAYOUT
═══════════════════════════════════════════════ */
.app-shell { display: flex; height: 100vh; overflow: hidden; }

/* ═══════════════════════════════════════════════
   SIDEBAR
═══════════════════════════════════════════════ */
.sidebar {
    width: var(--sidebar-w);
    background: var(--surface);
    border-right: 1px solid var(--border);
    display: flex;
    flex-direction: column;
    flex-shrink: 0;
    overflow-y: auto;
    overflow-x: hidden;
    transition: transform var(--transition);
    z-index: 200;
}

/* ── Clickable brand link ── */
.sidebar-brand {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 0 20px;
    height: var(--header-h);
    border-bottom: 1px solid var(--border-light);
    flex-shrink: 0;
    text-decoration: none;
    transition: background var(--transition);
}
.sidebar-brand:hover {
    background: var(--surface-2);
}

.brand-name {
    font-size: 15px;
    font-weight: 700;
    color: var(--text-primary);
    letter-spacing: -.3px;
    line-height: 1;
}

.brand-sub {
    font-size: 10px;
    color: var(--text-muted);
    letter-spacing: .02em;
}

.sidebar-section-label {
    font-size: 10px;
    font-weight: 700;
    letter-spacing: .08em;
    text-transform: uppercase;
    color: var(--text-muted);
    padding: 18px 20px 6px;
}

.sidebar-nav { padding: 8px 10px; flex: 1; }

.nav-link {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 9px 12px;
    border-radius: var(--radius-md);
    color: var(--text-secondary);
    text-decoration: none;
    font-size: 13.5px;
    font-weight: 500;
    transition: background var(--transition), color var(--transition);
    margin-bottom: 2px;
    position: relative;
}

.nav-link .material-icons { font-size: 18px; flex-shrink: 0; }

.nav-link:hover { background: var(--surface-2); color: var(--text-primary); }

.nav-link.active {
    background: var(--brand-light);
    color: var(--brand-dark);
    font-weight: 600;
}

.nav-link.active .material-icons { color: var(--brand); }

.nav-link .nav-badge {
    margin-left: auto;
    font-size: 10px;
    font-weight: 700;
    background: var(--brand);
    color: #fff;
    padding: 2px 6px;
    border-radius: 99px;
}

.sidebar-footer {
    padding: 12px 10px;
    border-top: 1px solid var(--border-light);
}

/* ═══════════════════════════════════════════════
   MAIN AREA
═══════════════════════════════════════════════ */
.main-area {
    flex: 1;
    display: flex;
    flex-direction: column;
    overflow: hidden;
    min-width: 0;
}

/* ═══════════════════════════════════════════════
   TOP BAR
═══════════════════════════════════════════════ */
.topbar {
    height: var(--header-h);
    background: var(--surface);
    border-bottom: 1px solid var(--border);
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 0 20px;
    flex-shrink: 0;
}

.topbar-menu-btn {
    display: none;
    width: 34px;
    height: 34px;
    border: none;
    background: none;
    cursor: pointer;
    border-radius: var(--radius-sm);
    align-items: center;
    justify-content: center;
    color: var(--text-secondary);
}

.topbar-menu-btn:hover { background: var(--surface-2); }

/* Search */
.topbar-search {
    flex: 1;
    max-width: 420px;
    position: relative;
}

.topbar-search .material-icons {
    position: absolute;
    left: 10px;
    top: 50%;
    transform: translateY(-50%);
    font-size: 17px;
    color: var(--text-muted);
    pointer-events: none;
}

.topbar-search input {
    width: 100%;
    padding: 8px 12px 8px 36px;
    border: 1px solid var(--border);
    border-radius: var(--radius-md);
    font-size: 13px;
    font-family: var(--font);
    background: var(--surface-2);
    color: var(--text-primary);
    outline: none;
    transition: border-color var(--transition), box-shadow var(--transition);
}

.topbar-search input:focus {
    border-color: var(--brand);
    box-shadow: 0 0 0 3px rgba(22,163,74,.12);
    background: var(--surface);
}

.search-results-dropdown {
    position: absolute;
    top: calc(100% + 6px);
    left: 0;
    right: 0;
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: var(--radius-md);
    box-shadow: var(--shadow-lg);
    z-index: 500;
    max-height: 300px;
    overflow-y: auto;
}

/* Right side */
.topbar-right { margin-left: auto; display: flex; align-items: center; gap: 8px; }

.icon-btn {
    width: 36px;
    height: 36px;
    border-radius: var(--radius-md);
    border: 1px solid var(--border);
    background: var(--surface);
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--text-secondary);
    position: relative;
    transition: background var(--transition), border-color var(--transition);
}

.icon-btn:hover { background: var(--surface-2); border-color: var(--brand-muted); }
.icon-btn .material-icons { font-size: 19px; }

.notif-dot {
    position: absolute;
    top: 7px;
    right: 7px;
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: #ef4444;
    border: 2px solid var(--surface);
}

/* User chip */
.user-chip {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 4px 10px 4px 4px;
    border-radius: var(--radius-xl);
    border: 1px solid var(--border);
    background: var(--surface);
    cursor: pointer;
    position: relative;
    transition: background var(--transition);
    user-select: none;
}

.user-chip:hover { background: var(--surface-2); }

.user-avatar {
    width: 28px;
    height: 28px;
    border-radius: 50%;
    background: var(--brand);
    color: #fff;
    font-size: 11px;
    font-weight: 700;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.user-name { font-size: 12.5px; font-weight: 600; color: var(--text-primary); max-width: 120px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }

.user-dropdown {
    display: none;
    position: absolute;
    top: calc(100% + 6px);
    right: 0;
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: var(--radius-md);
    box-shadow: var(--shadow-lg);
    min-width: 180px;
    z-index: 500;
    padding: 6px;
    animation: dropIn .15s ease;
}

.user-dropdown.open { display: block; }

@keyframes dropIn { from { opacity:0; transform:translateY(-4px); } to { opacity:1; transform:translateY(0); } }

.dropdown-item {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 8px 10px;
    border-radius: var(--radius-sm);
    font-size: 13px;
    color: var(--text-secondary);
    cursor: pointer;
    text-decoration: none;
    transition: background var(--transition);
}

.dropdown-item:hover { background: var(--surface-2); color: var(--text-primary); }
.dropdown-item.danger { color: #dc2626; }
.dropdown-item.danger:hover { background: #fee2e2; }
.dropdown-item .material-icons { font-size: 16px; }

.dropdown-divider { height: 1px; background: var(--border-light); margin: 4px 0; }

/* ═══════════════════════════════════════════════
   CONTENT AREA
═══════════════════════════════════════════════ */
.page-content {
    flex: 1;
    overflow-y: auto;
    padding: 24px;
}

/* ═══════════════════════════════════════════════
   SHARED PAGE COMPONENTS
═══════════════════════════════════════════════ */

/* Page header */
.page-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    flex-wrap: wrap;
    gap: 12px;
    margin-bottom: 24px;
}

.page-header-left h1 {
    font-size: 21px;
    font-weight: 700;
    color: var(--text-primary);
    letter-spacing: -.3px;
}

.page-header-left p {
    font-size: 13px;
    color: var(--text-muted);
    margin-top: 3px;
}

.page-actions { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }

/* Buttons */
.btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 14px;
    border-radius: var(--radius-md);
    border: 1px solid var(--border);
    background: var(--surface);
    color: var(--text-primary);
    font-size: 13px;
    font-weight: 500;
    cursor: pointer;
    font-family: var(--font);
    transition: all var(--transition);
    white-space: nowrap;
    text-decoration: none;
}

.btn:hover { background: var(--surface-2); }
.btn .material-icons { font-size: 16px; }

.btn-primary {
    background: var(--brand);
    color: #fff;
    border-color: var(--brand-dark);
}

.btn-primary:hover { background: var(--brand-dark); }

.btn-danger { background: #fee2e2; color: #dc2626; border-color: #fca5a5; }
.btn-danger:hover { background: #fecaca; }

.btn-sm { padding: 6px 11px; font-size: 12px; }
.btn-sm .material-icons { font-size: 14px; }

/* Stats grid */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 14px;
    margin-bottom: 24px;
}

.stat-card {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: var(--radius-lg);
    padding: 18px 20px;
    display: flex;
    align-items: center;
    gap: 14px;
    transition: box-shadow var(--transition);
}

.stat-card:hover { box-shadow: var(--shadow-md); }

.stat-icon {
    width: 44px;
    height: 44px;
    border-radius: var(--radius-md);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.stat-icon .material-icons { font-size: 22px; }
.stat-icon.green  { background: #dcfce7; color: #15803d; }
.stat-icon.blue   { background: #dbeafe; color: #1d4ed8; }
.stat-icon.purple { background: #ede9fe; color: #6d28d9; }
.stat-icon.orange { background: #ffedd5; color: #c2410c; }
.stat-icon.red    { background: #fee2e2; color: #dc2626; }

.stat-body { min-width: 0; }
.stat-value { font-size: 22px; font-weight: 700; color: var(--text-primary); line-height: 1.1; }
.stat-label { font-size: 12px; color: var(--text-muted); margin-top: 3px; font-weight: 500; }

/* Tabs */
.tabs {
    display: flex;
    gap: 2px;
    border-bottom: 1px solid var(--border);
    margin-bottom: 20px;
    flex-wrap: wrap;
}

.tab-btn {
    padding: 9px 18px;
    border: none;
    background: none;
    color: var(--text-muted);
    font-size: 13.5px;
    font-weight: 500;
    cursor: pointer;
    border-bottom: 2px solid transparent;
    margin-bottom: -1px;
    font-family: var(--font);
    transition: color var(--transition), border-color var(--transition);
}

.tab-btn:hover { color: var(--text-primary); }
.tab-btn.active { color: var(--brand); border-bottom-color: var(--brand); font-weight: 600; }

/* Table card */
.table-card {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: var(--radius-lg);
    overflow: hidden;
}

.table-toolbar {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 14px 16px;
    border-bottom: 1px solid var(--border-light);
    flex-wrap: wrap;
}

.table-search {
    position: relative;
    flex: 1;
    min-width: 200px;
    max-width: 300px;
}

.table-search .material-icons {
    position: absolute;
    left: 9px;
    top: 50%;
    transform: translateY(-50%);
    font-size: 15px;
    color: var(--text-muted);
    pointer-events: none;
}

.table-search input {
    width: 100%;
    padding: 7px 10px 7px 30px;
    border: 1px solid var(--border);
    border-radius: var(--radius-md);
    font-size: 12.5px;
    font-family: var(--font);
    background: var(--surface-2);
    color: var(--text-primary);
    outline: none;
    transition: border-color var(--transition);
}

.table-search input:focus { border-color: var(--brand); background: var(--surface); }

.data-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 13px;
}

.data-table thead tr { background: var(--surface-2); }

.data-table th {
    text-align: left;
    padding: 10px 14px;
    border-bottom: 1px solid var(--border);
    color: var(--text-muted);
    font-size: 11px;
    font-weight: 700;
    letter-spacing: .06em;
    text-transform: uppercase;
    white-space: nowrap;
}

.data-table td {
    padding: 12px 14px;
    border-bottom: 1px solid var(--border-light);
    color: var(--text-primary);
    vertical-align: middle;
}

.data-table tbody tr:last-child td { border-bottom: none; }
.data-table tbody tr:hover { background: #f8fdf8; }

/* Status badges */
.badge {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 4px 10px;
    border-radius: 99px;
    font-size: 11px;
    font-weight: 600;
    white-space: nowrap;
}

.notif-wrapper { position: relative; }

.notif-dropdown {
    display: none;
    position: absolute;
    right: 0;
    top: 45px;
    width: 320px;
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: var(--radius-md);
    box-shadow: var(--shadow-lg);
    z-index: 999;
    overflow: hidden;
}

.notif-dropdown.open { display: block; }

.notif-header {
    padding: 12px 14px;
    font-weight: 700;
    font-size: 13px;
    border-bottom: 1px solid var(--border-light);
}

.notif-footer {
    padding: 10px;
    text-align: center;
    border-top: 1px solid var(--border-light);
    font-size: 12px;
}

.notif-footer a {
    color: var(--brand);
    text-decoration: none;
    font-weight: 600;
}

.notif-item {
    display: flex;
    gap: 10px;
    padding: 10px 12px;
    cursor: pointer;
    transition: background 0.2s;
    border-bottom: 1px solid var(--border-light);
}

.notif-item:hover { background: var(--surface-2); }
.notif-item.unread { background: #f0fdf4; }

.notif-dot-small {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: #22c55e;
    margin-top: 6px;
    flex-shrink: 0;
}

.notif-text { font-size: 12.5px; color: var(--text-secondary); }
.notif-time { font-size: 11px; color: var(--text-muted); margin-top: 2px; }

.badge-active, .badge-approved, .badge-completed, .badge-already-claimed {
    background: #dcfce7; color: #15803d;
}
.badge-inactive, .badge-expired, .badge-rejected, .badge-not-claimed {
    background: #fee2e2; color: #991b1b;
}
.badge-assessment, .badge-scheduled {
    background: #fef3c7; color: #92400e;
}
.badge-processing, .badge-ongoing {
    background: #dbeafe; color: #1e40af;
}
.badge-closed, .badge-pending {
    background: #f3f4f6; color: #374151;
}

/* Action buttons */
.action-group { display: flex; gap: 5px; }

.action-btn {
    width: 30px;
    height: 30px;
    border-radius: var(--radius-sm);
    border: 1px solid var(--border);
    background: var(--surface);
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    color: var(--text-secondary);
    transition: all var(--transition);
}

.action-btn:hover { background: var(--surface-2); }
.action-btn .material-icons { font-size: 15px; }
.action-btn.edit:hover   { background: #dcfce7; border-color: #86efac; color: #15803d; }
.action-btn.delete:hover { background: #fee2e2; border-color: #fca5a5; color: #dc2626; }
.action-btn.view:hover   { background: #dbeafe; border-color: #93c5fd; color: #1d4ed8; }

/* Empty state */
.empty-state {
    text-align: center;
    padding: 48px 20px;
    color: var(--text-muted);
}

.empty-state .material-icons { font-size: 48px; opacity: .25; display: block; margin-bottom: 12px; }
.empty-state p { font-size: 14px; }

/* ═══════════════════════════════════════════════
   MODAL SYSTEM
═══════════════════════════════════════════════ */
.modal-backdrop {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,.45);
    backdrop-filter: blur(3px);
    z-index: 900;
    align-items: center;
    justify-content: center;
    padding: 1rem;
}

.modal-backdrop.open { display: flex; }

.modal-box {
    background: var(--surface);
    border-radius: var(--radius-xl);
    border: 1px solid var(--border);
    box-shadow: var(--shadow-lg);
    width: 100%;
    max-width: 480px;
    animation: modalUp .2s ease;
    max-height: 90vh;
    overflow-y: auto;
}

.modal-box.wide   { max-width: 600px; }
.modal-box.narrow { max-width: 380px; }

@keyframes modalUp { from { opacity:0; transform:translateY(-10px); } to { opacity:1; transform:translateY(0); } }

.modal-head {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    padding: 20px 22px 0;
}

.modal-head h2 { font-size: 17px; font-weight: 700; color: var(--text-primary); }
.modal-head p  { font-size: 13px; color: var(--text-muted); margin-top: 3px; }

.modal-close-btn {
    width: 28px;
    height: 28px;
    border: none;
    background: none;
    cursor: pointer;
    border-radius: var(--radius-sm);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--text-muted);
    flex-shrink: 0;
    transition: background var(--transition);
}

.modal-close-btn:hover { background: var(--surface-2); color: var(--text-primary); }
.modal-close-btn .material-icons { font-size: 18px; }

.modal-body { padding: 20px 22px; }
.modal-foot {
    display: flex;
    justify-content: flex-end;
    gap: 8px;
    padding: 14px 22px;
    border-top: 1px solid var(--border-light);
}

/* Form elements */
.form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
.form-group { display: flex; flex-direction: column; gap: 5px; }
.form-group.full { grid-column: 1 / -1; }

.form-group label {
    font-size: 12px;
    font-weight: 600;
    color: var(--text-secondary);
    letter-spacing: .02em;
}

.form-group input,
.form-group select,
.form-group textarea {
    padding: 9px 12px;
    border: 1px solid var(--border);
    border-radius: var(--radius-md);
    font-size: 13.5px;
    font-family: var(--font);
    background: var(--surface);
    color: var(--text-primary);
    outline: none;
    transition: border-color var(--transition), box-shadow var(--transition);
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
    border-color: var(--brand);
    box-shadow: 0 0 0 3px rgba(22,163,74,.12);
}

.form-group textarea { resize: vertical; min-height: 76px; }

/* Logout modal */
#logoutModal {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,.45);
    backdrop-filter: blur(3px);
    z-index: 9999;
    align-items: center;
    justify-content: center;
}

#logoutModal.open { display: flex; }

/* Farmer modal */
.farmer-quick-modal {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,.45);
    backdrop-filter: blur(3px);
    z-index: 9999;
    align-items: center;
    justify-content: center;
    padding: 1rem;
}

.farmer-quick-modal.open { display: flex; }

.farmer-modal-card {
    background: var(--surface);
    border-radius: var(--radius-xl);
    border: 1px solid var(--border);
    box-shadow: var(--shadow-lg);
    width: 100%;
    max-width: 360px;
    padding: 22px;
    animation: modalUp .2s ease;
}

/* Sidebar overlay */
.sidebar-overlay {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,.3);
    z-index: 199;
}

/* ═══════════════════════════════════════════════
   RESPONSIVE
═══════════════════════════════════════════════ */
@media (max-width: 768px) {
    .sidebar {
        position: fixed;
        top: 0; left: 0;
        height: 100%;
        transform: translateX(-100%);
    }
    .sidebar.open { transform: translateX(0); }
    .sidebar-overlay.open { display: block; }
    .topbar-menu-btn { display: flex; }
}

@media (max-width: 520px) {
    .page-content { padding: 16px; }
    .stats-grid { grid-template-columns: 1fr 1fr; gap: 10px; }
    .stat-card { padding: 14px; }
    .stat-value { font-size: 18px; }
}
</style>
</head>
<body>

<div class="app-shell">

<!-- ── Sidebar ── -->
<aside class="sidebar" id="sidebar">

    <!-- ── Clickable logo → goes to dashboard ── -->
    <a href="dashboard.php" class="sidebar-brand">
        <svg width="34" height="34" viewBox="0 0 400 400" xmlns="http://www.w3.org/2000/svg"
             style="flex-shrink:0;border-radius:6px;">
            <rect width="400" height="400" rx="72" fill="#16a34a"/>
            <line x1="200" y1="340" x2="200" y2="90" stroke="#fff" stroke-width="16" stroke-linecap="round"/>
            <ellipse cx="163" cy="130" rx="28" ry="12" fill="#fff" transform="rotate(-38 163 130)"/>
            <ellipse cx="150" cy="170" rx="28" ry="12" fill="#fff" transform="rotate(-35 150 170)"/>
            <ellipse cx="143" cy="210" rx="26" ry="11" fill="#fff" transform="rotate(-32 143 210)"/>
            <ellipse cx="237" cy="130" rx="28" ry="12" fill="#fff" transform="rotate(38 237 130)"/>
            <ellipse cx="250" cy="170" rx="28" ry="12" fill="#fff" transform="rotate(35 250 170)"/>
            <ellipse cx="257" cy="210" rx="26" ry="11" fill="#fff" transform="rotate(32 257 210)"/>
            <ellipse cx="200" cy="97" rx="12" ry="28" fill="#fff"/>
            <path d="M200 338 Q165 322 146 296" stroke="#fff" stroke-width="12" stroke-linecap="round" fill="none"/>
            <path d="M200 338 Q235 322 254 296" stroke="#fff" stroke-width="12" stroke-linecap="round" fill="none"/>
        </svg>
        <div class="brand-name">RFIMS</div>
    </a>

    <div class="sidebar-section-label">Navigation</div>

    <nav class="sidebar-nav">
        <?php foreach ($nav_items as $item): ?>
        <a href="<?php echo $item['url']; ?>"
           class="nav-link <?php echo ($current_page === $item['id']) ? 'active' : ''; ?>">
            <span class="material-icons"><?php echo $item['icon']; ?></span>
            <span><?php echo $item['label']; ?></span>
        </a>
        <?php endforeach; ?>
    </nav>

</aside>

<div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>

<!-- ── Main area ── -->
<div class="main-area">

<!-- Top bar -->
<header class="topbar">
    <button class="topbar-menu-btn" onclick="toggleSidebar()" aria-label="Menu">
        <span class="material-icons">menu</span>
    </button>

    <div class="topbar-search">
        <span class="material-icons">search</span>
        <input type="text" id="topbarSearch" placeholder="Search farmers or address…" autocomplete="off">
        <div class="search-results-dropdown" id="searchResultsDropdown"></div>
    </div>

    <div class="topbar-right">
        <div class="notif-wrapper" onclick="toggleNotif(event)">
            <button class="icon-btn">
                <span class="material-icons">notifications</span>
                <?php if ($unread_count > 0): ?>
                    <span class="notif-dot"></span>
                <?php endif; ?>
            </button>
            <div class="notif-dropdown" id="notifDropdown">
                <div class="notif-header">Notifications</div>
                <div id="notifList">Loading...</div>
                <div class="notif-footer"><a href="notifications.php">See all</a></div>
            </div>
        </div>

        <div class="user-chip" onclick="toggleUserMenu(event)" id="userChip">
            <div class="user-avatar"><?php echo $current_user['initials']; ?></div>
            <span class="user-name"><?php echo htmlspecialchars($current_user['name']); ?></span>
            <span class="material-icons" style="font-size:16px;color:var(--text-muted)">expand_more</span>

            <div class="user-dropdown" id="userDropdown">
                <div style="padding:8px 10px 6px">
                    <div style="font-size:13px;font-weight:600;color:var(--text-primary)"><?php echo htmlspecialchars($current_user['name']); ?></div>
                    <div style="font-size:11px;color:var(--text-muted)"><?php echo htmlspecialchars($current_user['email']); ?></div>
                </div>
                <div class="dropdown-divider"></div>
                <a href="#" class="dropdown-item" onclick="openLogout();return false;">
                    <span class="material-icons">logout</span> Sign Out
                </a>
            </div>
        </div>
    </div>
</header>

<!-- Logout modal -->
<div id="logoutModal">
    <div class="modal-box narrow">
        <div style="padding:28px 24px;text-align:center">
            <div style="width:52px;height:52px;border-radius:50%;background:#fee2e2;display:flex;align-items:center;justify-content:center;margin:0 auto 14px">
                <span class="material-icons" style="color:#dc2626;font-size:26px">logout</span>
            </div>
            <h2 style="font-size:17px;font-weight:700;margin-bottom:6px">Sign out?</h2>
            <p style="font-size:13px;color:var(--text-muted);margin-bottom:20px">You'll need to sign in again to access RFIMS.</p>
            <div style="display:flex;gap:8px;justify-content:center">
                <button class="btn" onclick="closeLogout()">Cancel</button>
                <form action="logout.php" method="POST" style="margin:0">
                    <button type="submit" class="btn btn-danger">
                        <span class="material-icons">logout</span> Sign Out
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Farmer quick view modal -->
<div class="farmer-quick-modal" id="farmerModal">
    <div class="farmer-modal-card">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px">
            <h3 style="font-size:15px;font-weight:700">Farmer Details</h3>
            <button class="modal-close-btn" onclick="closeFarmerModal()">
                <span class="material-icons">close</span>
            </button>
        </div>
        <div id="farmerDetails" style="font-size:13.5px;color:var(--text-secondary);line-height:1.8"></div>
    </div>
</div>

<!-- Page content -->
<main class="page-content">

<script>
// Sidebar
function toggleSidebar() {
    document.getElementById('sidebar').classList.toggle('open');
    document.getElementById('sidebarOverlay').classList.toggle('open');
}
function closeSidebar() {
    document.getElementById('sidebar').classList.remove('open');
    document.getElementById('sidebarOverlay').classList.remove('open');
}

// User menu
function toggleUserMenu(e) {
    e.stopPropagation();
    document.getElementById('userDropdown').classList.toggle('open');
}
document.addEventListener('click', () => {
    document.getElementById('userDropdown').classList.remove('open');
});

// Logout
function openLogout() {
    document.getElementById('logoutModal').classList.add('open');
}
function closeLogout() {
    document.getElementById('logoutModal').classList.remove('open');
}
document.getElementById('logoutModal').addEventListener('click', e => {
    if (e.target === e.currentTarget) closeLogout();
});

// Live search
document.getElementById('topbarSearch').addEventListener('keyup', function() {
    const q = this.value.trim();
    const dd = document.getElementById('searchResultsDropdown');
    if (!q) { dd.innerHTML = ''; return; }
    fetch('search_farmer.php?query=' + encodeURIComponent(q))
        .then(r => r.text())
        .then(d => { dd.innerHTML = d; });
});

document.addEventListener('click', e => {
    if (!e.target.closest('.topbar-search'))
        document.getElementById('searchResultsDropdown').innerHTML = '';
});

// Farmer modal
function showFarmerDetails(id) {
    fetch('farmer_details.php?id=' + id)
        .then(r => r.text())
        .then(d => {
            document.getElementById('farmerDetails').innerHTML = d;
            document.getElementById('farmerModal').classList.add('open');
        });
}
function closeFarmerModal() {
    document.getElementById('farmerModal').classList.remove('open');
}
document.getElementById('farmerModal').addEventListener('click', e => {
    if (e.target === e.currentTarget) closeFarmerModal();
});

// Notifications
function toggleNotif(e) {
    e.stopPropagation();
    const dd = document.getElementById('notifDropdown');
    const isOpen = dd.classList.contains('open');
    document.querySelectorAll('.notif-dropdown').forEach(d => d.classList.remove('open'));
    if (!isOpen) {
        dd.classList.add('open');
        loadNotifications();
    }
}
document.addEventListener('click', function() {
    document.getElementById('notifDropdown').classList.remove('open');
});
function loadNotifications() {
    fetch('fetch_notifications.php')
        .then(res => res.text())
        .then(data => {
            document.getElementById('notifList').innerHTML = data;
        });
}
</script>