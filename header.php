<?php
// header.php - Header template matching AgriMS design

include 'database.php'; // Include database connection

// Get current page for active navigation
$current_page = basename($_SERVER['PHP_SELF'], '.php');

// User information from session
if (isset($_SESSION['email'])) {
    $current_user = [
        'email' => $_SESSION['email'],
        'initials' => strtoupper(substr($_SESSION['email'], 0, 1) . substr(explode(" ", $_SESSION['email'])[1] ?? '', 0, 1))
    ];
    // Get unread notifications count for this user
    $email = $_SESSION['email'];
    $sql = "SELECT COUNT(*) AS unread_count FROM notifications WHERE user_id = ? AND status = 'unread'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $unread_count = $row['unread_count'];
} else {
    $current_user = [
        'email' => 'Guest',
        'initials' => 'G'
    ];
    $unread_count = 0;
}

// Sidebar navigation items matching the image
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
    
    <!-- Google Material Icons -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/header.css">
</head>
<body>
    <div class="app-container">
        <!-- Sidebar Navigation -->
        <aside class="sidebar" id="sidebar">
            <!-- Close Button for Mobile -->
            <button class="close-sidebar" onclick="toggleSidebar()">
                <span class="material-icons">close</span>
            </button>

            <!-- Logo -->
            <div class="sidebar-logo">
                <div class="logo-icon">
                    <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 2L2 7v10c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V7l-10-5z"/>
                    </svg>
                </div>
                <span class="logo-text">RFMS</span>
            </div>

            <!-- Navigation Menu -->
            <nav class="sidebar-nav">
                <?php foreach ($nav_items as $item): ?>
                    <a href="<?php echo $item['url']; ?>" 
                       class="nav-item <?php echo ($current_page === $item['id']) ? 'active' : 'dashboard'; ?>">
                        <span><?php echo $item['label']; ?></span>
                    </a>
                <?php endforeach; ?>
            </nav>
        </aside>

        <!-- Main Content Area -->
        <div class="main-content">
            <!-- Top Header -->
            <header class="top-header">
                <!-- Menu Toggle -->
                <button class="menu-toggle" onclick="toggleSidebar()">
                    <span class="material-icons">menu</span>
                </button>

                <!-- Search Bar -->
                <div class="search-container">
                    <div class="search-icon">
                        <span class="material-icons">search</span>
                    </div>
                    <input type="text" 
                           class="search-input" 
                           placeholder="Search farmers, farms, records...">
                </div>

                <!-- Right Side -->
                <div class="header-right">
                    <!-- Notification Button -->
                    <button class="notification-btn">
                        <span class="material-icons">notifications</span>
                        <?php if($unread_count > 0): ?>
                            <span class="notification-badge"><?php echo $unread_count; ?></span>
                        <?php endif; ?>
                    </button>

                    <!-- User Menu -->
                    <div class="user-menu" onclick="toggleUserDropdown(event)">
                        <div class="user-avatar"><?php echo $current_user['initials']; ?></div>
                        <div class="user-info">
                            <span class="user-name"><?php echo $current_user['email']; ?></span>  
                        </div>
                        <div class="user-dropdown-icon" >
                            <span id="arrow" class="material-icons">expand_more</span>
                        </div>

                        <!-- Dropdown menu -->
                        <div id="userDropdownMenu" class="dropdown-menu">
                            <a href="profile.php">Profile</a>
                            <a href="switch_account.php">Switch Account</a>
                            <a href="logout.php">Logout</a>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Content Container -->
            <div class="content-container">
                <!-- Page content will be inserted here -->
<script >
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

// Close dropdown if clicked outside
window.addEventListener('click', function(event) {
    const menu = document.getElementById('userDropdownMenu');
    const arrow = document.getElementById('arrow');

    if (!event.target.closest('.user-menu')) {
        menu.style.display = 'none';
        arrow.style.transform = 'rotate(0deg)';
    }
});

</script>