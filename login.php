    <?php
    session_start();
    include 'database.php'; // your database.php connection file

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

        $email = $_POST['email'];
        $password = $_POST['password'];

    // Check if user exists
    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

        if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password']) || $password === $row['password']) {
        $_SESSION['email'] = $row['email'];
        header("Location: dashboard.php");
        exit();
            } else {
                $error = "Incorrect password!";
            }
        } else {
            $error = "User not found!";
        }
    }
    ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login - AgriMS</title>
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }

    body {
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', 'Oxygen', 'Ubuntu', 'Cantarell', sans-serif;
        background: linear-gradient(135deg, #005540 0%, #003d2e 100%);
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 1rem;
    }
    .login-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        width: 100%;
        max-width: 400px;
        padding: 2.5rem;
        position: relative;
    }

    .logo { text-align: center; margin-bottom: 2rem; }
    .logo-icon { font-size: 3rem; margin-bottom: 0.5rem; }
    .logo-text { font-size: 1.8rem; font-weight: 700; color: #005540; margin-bottom: 0.3rem; }
    .logo-subtitle { font-size: 0.85rem; color: #666; }

    .form-group {
        position: relative;
        margin-bottom: 1.5rem;
    }
    .error {
        color: red;
        text-align: center;
        margin-bottom: 10px;
        padding-bottom: 20px;
    }
    .form-input {
        width: 100%;
        padding: 0.85rem;
        border: 2px solid #e0e0e0;
        border-radius: 8px;
        font-size: 0.95rem;
        transition: all 0.3s;
        outline: none;
        background: none;
    }

    .form-group label {
        position: absolute;
        left: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: #999;
        font-size: 0.95rem;
        pointer-events: none;
        transition: all 0.3s ease;
        background: white;
        padding: 0 4px;
    }

    .form-input:focus + label,
    .form-input:not(:placeholder-shown) + label {
        top: -10px;
        font-size: 0.75rem;
        color: #3679ff;
    }

    .forgot-link { text-align: right; margin-bottom: 1.5rem; }
    .forgot-link a { color: #005540; text-decoration: none; font-size: 0.85rem; font-weight: 500; }
    .forgot-link a:hover { text-decoration: underline; }

    .btn-login {
        width: 100%;
        padding: 0.95rem;
        background: linear-gradient(135deg, #00d9a3 0%, #00a86b 100%);
        color: white;
        border: none;
        border-radius: 8px;
        font-size: 1rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
    }

    .btn-login:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,217,163,0.3);
    }

    @media (max-width: 480px) {
        .login-card { padding: 2rem 1.5rem; }
    }


.close-circle {
    position: absolute;
    top: 15px;
    right: 15px;    
    width: 35px;
    height: 35px;

    display: flex;
    align-items: center;
    justify-content: center;

    background: #fff;
    border: 2px solid #333;
    border-radius: 50%;

    font-size: 20px;
    font-weight: bold;
    text-decoration: none;
    color: #333;

    transition: 0.3s ease;
}

.close-circle:hover {
    background: red;
    color: white;
    border-color: red;
}
</style>
</head>
<body>
    
<div class="login-card">
    <a href="home.php" class="close-circle">×</a>
    <div class="logo">
        <div class="logo-icon">🌾</div>
        <div class="logo-text">AgriMS</div>
        <div class="logo-subtitle">Agricultural Management System</div>
    </div>
    

    <?php if (!empty($error)) { echo "<div class='error'>{$error}</div>"; } ?>

    <form id="loginForm" method="POST" action="login.php">
        <div class="form-group">
            <input type="text" name="email" class="form-input" placeholder=" " required>
            <label>Email or Username</label>
        </div>

        <div class="form-group">
            <input type="password" name="password" class="form-input" placeholder=" " required>
            <label>Password</label>
        </div>

        <div class="forgot-link">
            <a href="#">Forgot Password?</a>
        </div>

        <button type="submit" class="btn-login">Sign In</button>
    </form>

<script>
function handleLogin(event) {
    event.preventDefault();
    console.log('Login submitted');
    alert('Login successful!');
}
</script>
</body>
</html>