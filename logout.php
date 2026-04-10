<?php
session_start();

// Unset all session variables
$_SESSION = [];

// Destroy the session
session_destroy();

// Delete the session cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(), 
        '', 
        time() - 42000, 
        $params["path"], 
        $params["domain"], 
        $params["secure"], 
        $params["httponly"]
    );
}

// Redirect to login page
header("Location: login.php");
exit();

include 'header.php';

?>
<body>
     <!-- Logout Modal -->
            <div id="logoutModal">
            <div class="modal-content">
                <h3>Are you sure you want to log out?</h3>
                <div class="modal-buttons">
                <button id="cancelLogout">Cancel</button>
                <form action="logout.php" method="POST" style="margin:0;">
                    <button type="submit" id="confirmLogout">Logout</button>
                </form>
                </div>
            </div>
            </div>
</body>