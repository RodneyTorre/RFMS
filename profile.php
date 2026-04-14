<?php
session_start();

include 'database.php';


// Redirect if not logged in
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

$page_title = "Profile";
include 'header.php';

$email = $_SESSION['email'];

// Fetch user data
$query = "SELECT * FROM users WHERE email = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Handle update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $name = $_POST['name'];


    $name = $_POST['name'];
    $role = $_POST['role'];
    $location = $_POST['address'];
    

    $update = "UPDATE users 
               SET name=?, role=?, address=? 
               WHERE email=?";
               
    $stmt = $conn->prepare($update);
    $stmt->bind_param("ssss", $name, $role, $location, $email);

    if ($stmt->execute()) {
        echo "<script>alert('Profile updated successfully!');</script>";
        
        // Refresh values
        $user['name'] = $name;
        $user['role'] = $role;
        $user['address'] = $location;
        
    } else {

        echo "<script>alert('Update failed!');</script>";

        echo "<script>alert('Error updating profile');</script>";



    $name = $_POST['name'];
    $role = $_POST['role'];
    $location = $_POST['address'];
    

    $update = "UPDATE users 
               SET name=?, role=?, address=? 
               WHERE email=?";
               
    $stmt = $conn->prepare($update);
    $stmt->bind_param("ssss", $name, $role, $location, $email);

    if ($stmt->execute()) {
        echo "<script>alert('Profile updated successfully!');</script>";
        
        // Refresh values
        $user['name'] = $name;
        $user['role'] = $role;
        $user['address'] = $location;
        
    } else {
        echo "<script>alert('Update failed!');</script>";



    }
}
?>



<link rel="stylesheet" href="assets/css/dashboard.css">


<!DOCTYPE html>
<html lang="en">
<head>
    <title>Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .profile-card {
            max-width: 500px;
            margin: auto;
            margin-top: 50px;
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }
        .profile-avatar {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: #0d6efd;
            color: white;
            font-size: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: auto;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="profile-card text-center">

        <!-- Avatar -->
        <div class="profile-avatar mb-3">
            <?php echo strtoupper(substr($user['email'], 0, 1)); ?>
        </div>

        <h3><?php echo $user['name'] ?? 'No Name'; ?></h3>
        <p class="text-muted"><?php echo $user['email']; ?></p>

        <hr>

        <!-- Update Form -->
        <form method="POST">
            <div class="mb-3 text-start">
                <label>Name</label>
                <input type="text" name="name" class="form-control" 
                       value="<?php echo $user['name']; ?>" required>
            </div>

            <div class="mb-3 text-start">
                <label>Email</label>
                <input type="email" class="form-control" 
                       value="<?php echo $user['email']; ?>" disabled>
            </div>

            <button type="submit" class="btn btn-primary w-100">
                Update Profile
            </button>
        </form>


<!-- Page Header -->
<div class="page-header">
    <div>
        <h1 class="page-title">Profile</h1>
        <p class="page-subtitle">Manage your personal information</p>
    </div>
</div>




</body>
</html>

<link rel="stylesheet" href="assets/css/dashboard.css">

<!-- Page Header -->
<div class="page-header">
    <div>
        <h1 class="page-title">Profile</h1>
        <p class="page-subtitle">Manage your personal information</p>
    </div>
</div>


<!-- PROFILE CARD -->
<div class="card" style="margin-bottom:20px;">
    <div style="display:flex; align-items:center; gap:20px; flex-wrap:wrap;">

        <!-- Avatar -->
        <div style="
            width:100px;
            height:100px;
            border-radius:50%;
            background:#4CAF50;
            color:white;
            font-size:40px;
            display:flex;
            align-items:center;
            justify-content:center;
        ">
            <?= strtoupper(substr($user['email'], 0, 1)) ?>
        </div>

        <!-- Info -->
        <div>
            <h2><?= htmlspecialchars($user['name'] ?? 'No Name') ?></h2>
            <p style="color:gray;"><?= htmlspecialchars($user['email']) ?></p>
            <p><?= htmlspecialchars($user['role'] ?? '-') ?></p>
        </div>

        <!-- Actions -->
        <div style="margin-left:auto;">
            <button class="btn btn-secondary" onclick="toggleEdit()">Edit Profile</button>
        </div>
    </div>
</div>

<!-- ABOUT -->
<div class="card" style="margin-top:20px;">
    <h3>About</h3>

    <p><strong>📍 Location:</strong> <?= htmlspecialchars($user['address'] ?? '-') ?></p>
</div>

<!-- EDIT FORM -->
<div class="card" id="editForm" style="margin-top:20px; display:none;">
    <h3>Edit Profile</h3>

    <form method="POST">

        <div class="form-group">
            <label>Name</label>
            <input type="text" name="name" class="form-control"
                   value="<?= htmlspecialchars($user['name'] ?? '') ?>" required>
        </div>

        <div class="form-group">
            <label>Role</label>
            <input type="text" name="role" class="form-control"
                   value="<?= htmlspecialchars($user['role'] ?? '') ?>">
        </div>

        <div class="form-group">
            <label>Location</label>
            <input type="text" name="location" class="form-control"
                   value="<?= htmlspecialchars($user['address'] ?? '') ?>">
        </div>

        <br>
        <button type="submit" class="btn btn-primary">Save Changes</button>
    </form>
</div>

<script>
function toggleEdit() {
    var form = document.getElementById("editForm");
    form.style.display = (form.style.display === "none") ? "block" : "none";
}
</script> 
</body>
</html>


