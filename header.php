<?php
include 'database.php';

// Get current page
$current_page = basename($_SERVER['PHP_SELF'], '.php');

// User session
if (isset($_SESSION['email'])) {
    $current_user = [
        'email' => $_SESSION['email'],
        'initials' => strtoupper(
            substr($_SESSION['email'], 0, 1) .
            substr(explode(" ", $_SESSION['email'])[1] ?? '', 0, 1)
        )
    ];

    // Notification count
    $email = $_SESSION['email'];
    $sql = "SELECT COUNT(*) AS unread_count FROM notifications WHERE user_id = ? AND status = 'unread'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $unread_count = $row['unread_count'];
} else {
    $current_user = ['email' => 'Guest', 'initials' => 'G'];
    $unread_count = 0;
}

// Navigation
$nav_items = [
    ['id' => 'dashboard', 'label' => 'Dashboard', 'url' => 'dashboard.php'],
    ['id' => 'registry', 'label' => 'Registry', 'url' => 'registry.php'],
    ['id' => 'production', 'label' => 'Production', 'url' => 'production.php'],
    ['id' => 'monitoring', 'label' => 'Monitoring', 'url' => 'monitoring.php'],
    ['id' => 'programs', 'label' => 'Programs', 'url' => 'programs.php'],
    ['id' => 'insurance', 'label' => 'Insurance', 'url' => 'insurance.php'],
    ['id' => 'incidents', 'label' => 'Incidents & Claims', 'url' => 'incidents.php'],
    ['id' => 'inventory', 'label' => 'Inventory', 'url' => 'inventory.php'],
    ['id' => 'maps', 'label' => 'Maps & GIS', 'url' => 'maps.php'],
    ['id' => 'reports', 'label' => 'Reports', 'url' => 'reports.php'],
    ['id' => 'feedback', 'label' => 'Feedback', 'url' => 'feedback.php']
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo isset($page_title) ? $page_title . ' - ' : ''; ?>AgriMS</title>

<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
<link rel="stylesheet" href="assets/css/header.css">
</head>

<body>
<div class="app-container">

<!-- Sidebar -->
<aside class="sidebar" id="sidebar">
    <button class="close-sidebar" onclick="toggleSidebar()">
        <span class="material-icons">close</span>
    </button>

    <div class="sidebar-logo">
        <div class="logo-icon">
            <svg viewBox="0 0 24 24">
                <path d="M12 2L2 7v10c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V7l-10-5z"/>
            </svg>
        </div>
        <span class="logo-text">RFMS</span>
    </div>

    <nav class="sidebar-nav">
        <?php foreach ($nav_items as $item): ?>
            <a href="<?php echo $item['url']; ?>"
               class="nav-item <?php echo ($current_page === $item['id']) ? 'active' : ''; ?>">
                <span><?php echo $item['label']; ?></span>
            </a>
        <?php endforeach; ?>
    </nav>
</aside>

<!-- Main -->
<div class="main-content">

<header class="top-header">

    <button class="menu-toggle" onclick="toggleSidebar()">
        <span class="material-icons">menu</span>
    </button>

    <div class="search-container">
        <div class="search-icon">
            <span class="material-icons">search</span>
        </div>
        <input type="text" class="search-input" placeholder="Search...">
    </div>

    <div class="header-right">

        <!-- Notifications -->
        <button class="notification-btn">
            <span class="material-icons">notifications</span>
            <?php if ($unread_count > 0): ?>
                <span class="notification-badge"><?php echo $unread_count; ?></span>
            <?php endif; ?>
        </button>

        <!-- User -->
        <div class="user-menu" onclick="toggleUserDropdown(event)">
            <div class="user-avatar"><?php echo $current_user['initials']; ?></div>
            <span><?php echo $current_user['email']; ?></span>
            <span id="arrow" class="material-icons">expand_more</span>

            <div id="userDropdownMenu" class="dropdown-menu">
                <a href="#" onclick="openLogoutModal(); return false;">Logout</a>
            </div>
        </div>

    </div>
</header>

<!-- Logout Modal -->
<div id="logoutModal" class="modal">
    <div class="modal-content">
        <h3>Are you sure you want to log out?</h3>

        <div class="modal-buttons">
            <button id="cancelLogout">Cancel</button>

            <form action="logout.php" method="POST">
                <button type="submit">Logout</button>
            </form>
        </div>

    </div>
</div>

<!-- Page Content -->
<div class="content-container">
<script>
// Logout Modal
function openLogoutModal() {
    document.getElementById("logoutModal").style.display = "flex";
}

document.getElementById("cancelLogout").onclick = function () {
    document.getElementById("logoutModal").style.display = "none";
};

window.onclick = function (event) {
    let modal = document.getElementById("logoutModal");
    if (event.target === modal) {
        modal.style.display = "none";
    }
};


// Dropdown
function toggleUserDropdown(event) {
    event.stopPropagation();
            }
// Open modal
function openLogoutModal() {
    document.getElementById("logoutModal").classList.add("active");
}

// Close modal
document.getElementById("cancelLogout").onclick = function() {
    document.getElementById("logoutModal").classList.remove("active");
};

// Close when clicking outside
window.onclick = function(event) {
    let modal = document.getElementById("logoutModal");
    if (event.target === modal) {
        modal.classList.remove("active");
    }
};
    //For user dropdown                
    function toggleUserDropdown(event) {
    event.stopPropagation(); // Prevent triggering parent clicks

    const menu = document.getElementById('userDropdownMenu');
    const arrow = document.getElementById('arrow');

    if (menu.style.display === 'block') {
        menu.style.display = 'none';
        arrow.style.transform = 'rotate(0deg)';
    } else {
        menu.style.display = 'block';
        arrow.style.transform = 'rotate(180deg)';
    }
}
window.addEventListener('click', function(event) {
    if (!event.target.closest('.user-menu')) {
        document.getElementById('userDropdownMenu').style.display = 'none';
        document.getElementById('arrow').style.transform = 'rotate(0deg)';
    }
});
</script>
</body>
</html>