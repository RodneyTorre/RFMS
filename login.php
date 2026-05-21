<?php
session_start();

/* IF ALREADY LOGGED IN */
if (isset($_SESSION['email'])) {

    header("Location: dashboard.php");
    exit();
}

include 'database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);   
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {

        $row = $result->fetch_assoc();

        if (password_verify($password, $row['password']) || $password === $row['password']) {
            session_regenerate_id(true);
            $_SESSION['email'] = $row['email'];
            $_SESSION['name']  = $row['name'];

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

<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="Expires" content="0">

<title>RFIMS Login</title>

<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

<style>

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
}

:root{

    --brand:#16a34a;
    --brand-dark:#15803d;
    --brand-light:#dcfce7;

    --bg:#f0f4f0;
    --surface:#ffffff;

    --border:#e2e8e2;

    --text-primary:#0f1f0f;
    --text-secondary:#4a5c4a;
    --text-muted:#7a8f7a;

    --radius-sm:8px;
    --radius-md:12px;
    --radius-lg:18px;

    --shadow:
        0 10px 30px rgba(0,0,0,.08);

    --font:'Plus Jakarta Sans',sans-serif;
}

body{
    font-family:var(--font);
    background:linear-gradient(
        135deg,
        #e8f5e9 0%,
        #f0f4f0 100%
    );

    min-height:100vh;

    display:flex;
    align-items:center;
    justify-content:center;

    padding:24px;
}

/* =========================
   LOGIN WRAPPER
========================= */

.login-wrapper{
    width:100%;
    max-width:1000px;

    display:grid;
    grid-template-columns:1fr 420px;

    background:var(--surface);

    border:1px solid var(--border);

    border-radius:28px;

    overflow:hidden;

    box-shadow:var(--shadow);
}

/* =========================
   LEFT SIDE
========================= */

.login-left{
    background:
        linear-gradient(
            135deg,
            #16a34a 0%,
            #15803d 100%
        );

    color:white;

    padding:60px;

    display:flex;
    flex-direction:column;
    justify-content:center;

    position:relative;
}

.logo-box{
    width:70px;
    height:70px;

    border-radius:18px;

    background:rgba(255,255,255,.15);

    display:flex;
    align-items:center;
    justify-content:center;

    margin-bottom:28px;
}

.logo-box .material-icons{
    font-size:38px;
}

.login-left h1{
    font-size:42px;
    line-height:1.1;
    margin-bottom:18px;
}

.login-left p{
    font-size:15px;
    line-height:1.8;
    opacity:.9;
    max-width:420px;
}

/* =========================
   RIGHT SIDE
========================= */

.login-right{
    padding:48px 40px;
    position:relative;
}

.close-btn{
    position:absolute;
    top:18px;
    right:18px;

    width:38px;
    height:38px;

    border-radius:50%;

    border:1px solid var(--border);

    background:white;

    color:var(--text-secondary);

    display:flex;
    align-items:center;
    justify-content:center;

    text-decoration:none;

    transition:.2s;
}

.close-btn:hover{
    background:#fee2e2;
    color:#dc2626;
}

.login-header{
    margin-bottom:32px;
}

.login-header h2{
    font-size:28px;
    color:var(--text-primary);
    margin-bottom:8px;
}

.login-header p{
    font-size:14px;
    color:var(--text-muted);
}

/* =========================
   ERROR
========================= */

.error{
    background:#fee2e2;
    color:#b91c1c;

    border:1px solid #fecaca;

    padding:12px 14px;

    border-radius:12px;

    margin-bottom:18px;

    font-size:14px;
}

/* =========================
   FORM
========================= */

.form-group{
    margin-bottom:20px;
}

.form-group label{
    display:block;

    font-size:13px;
    font-weight:600;

    color:var(--text-secondary);

    margin-bottom:8px;
}

.input-box{
    position:relative;
}

.input-box .material-icons{
    position:absolute;

    left:14px;
    top:50%;

    transform:translateY(-50%);

    color:var(--text-muted);
    font-size:20px;
}

.form-input{
    width:100%;

    padding:14px 14px 14px 46px;

    border:1px solid var(--border);

    border-radius:14px;

    font-size:14px;

    font-family:var(--font);

    outline:none;

    transition:.2s;

    background:#fafdfb;
}

.form-input:focus{
    border-color:var(--brand);

    box-shadow:
        0 0 0 4px rgba(22,163,74,.12);

    background:white;
}

/* =========================
   OPTIONS
========================= */

.form-options{
    display:flex;
    justify-content:space-between;
    align-items:center;

    margin-bottom:24px;
}

.form-options a{
    text-decoration:none;
    color:var(--brand);
    font-size:13px;
    font-weight:600;
}

.form-options a:hover{
    text-decoration:underline;
}

/* =========================
   BUTTON
========================= */

.btn-login{
    width:100%;

    border:none;

    background:linear-gradient(
        135deg,
        #16a34a 0%,
        #15803d 100%
    );

    color:white;

    padding:15px;

    border-radius:14px;

    font-size:15px;
    font-weight:700;

    cursor:pointer;

    transition:.2s;
}

.btn-login:hover{
    transform:translateY(-2px);

    box-shadow:
        0 10px 20px rgba(22,163,74,.25);
}

/* =========================
   FOOTER
========================= */

.login-footer{
    margin-top:24px;

    text-align:center;

    font-size:13px;
    color:var(--text-muted);
}

/* =========================
   MOBILE
========================= */

@media(max-width:900px){

    .login-wrapper{
        grid-template-columns:1fr;
    }

    .login-left{
        display:none;
    }

    .login-right{
        padding:40px 24px;
    }
}
/* LOGO BRAND */
.sidebar-brand{
    display:flex;
    align-items:center;
    gap:16px;
    text-decoration:none;
    margin-bottom:30px;
}

/* SVG BOX */
.sidebar-brand svg{
    width:80px;
    height:80px;
    padding:10px;
    background:rgba(255,255,255,0.12);
    backdrop-filter:blur(10px);
    border:1px solid rgba(255,255,255,0.25);
    border-radius:22px;

    box-shadow:
        0 8px 20px rgba(0,0,0,0.18),
        0 0 20px rgba(255,255,255,0.15);

    transition:all .3s ease;
}

/* HOVER EFFECT */
.sidebar-brand:hover svg{
    transform:scale(1.08) rotate(-3deg);

    box-shadow:
        0 12px 30px rgba(0,0,0,0.25),
        0 0 25px rgba(255,255,255,0.25);
}

/* BRAND TEXT */
.brand-name{
    font-size:28px;
    font-weight:800;
    letter-spacing:2px;

    background:linear-gradient(
        90deg,
        #ffffff,
        #dcfce7
    );

    -webkit-background-clip:text;
    -webkit-text-fill-color:transparent;

    text-shadow:
        0 2px 8px rgba(0,0,0,0.15);
}

/* LOGO ANIMATION */
@keyframes floatLogo{
    0%{ transform:translateY(0); }
    50%{ transform:translateY(-5px); }
    100%{ transform:translateY(0); }
}

.sidebar-brand svg{
    animation:floatLogo 3s ease-in-out infinite;
}
</style>
</head>

<body>

<div class="login-wrapper">

    <!-- LEFT -->

    <div class="login-left">

        <a href="dashboard.php" class="sidebar-brand">

    <svg width="34" height="34" viewBox="0 0 400 400"
         xmlns="http://www.w3.org/2000/svg"
         style="flex-shrink:0;border-radius:6px;">

        <rect width="400" height="400" rx="72" fill="#16a34a"/>

        <line x1="200" y1="340" x2="200" y2="90"
              stroke="#fff" stroke-width="16" stroke-linecap="round"/>

        <ellipse cx="163" cy="130" rx="28" ry="12"
                 fill="#fff" transform="rotate(-38 163 130)"/>

        <ellipse cx="150" cy="170" rx="28" ry="12"
                 fill="#fff" transform="rotate(-35 150 170)"/>

        <ellipse cx="143" cy="210" rx="26" ry="11"
                 fill="#fff" transform="rotate(-32 143 210)"/>

        <ellipse cx="237" cy="130" rx="28" ry="12"
                 fill="#fff" transform="rotate(38 237 130)"/>

        <ellipse cx="250" cy="170" rx="28" ry="12"
                 fill="#fff" transform="rotate(35 250 170)"/>

        <ellipse cx="257" cy="210" rx="26" ry="11"
                 fill="#fff" transform="rotate(32 257 210)"/>

        <ellipse cx="200" cy="97" rx="12" ry="28" fill="#fff"/>

        <path d="M200 338 Q165 322 146 296"
              stroke="#fff" stroke-width="12"
              stroke-linecap="round" fill="none"/>

        <path d="M200 338 Q235 322 254 296"
              stroke="#fff" stroke-width="12"
              stroke-linecap="round" fill="none"/>

    </svg>

    <div class="brand-name">RFIMS</div>

</a>

        <h1>
            Rice Farmer Inventory
            Management System
        </h1>

        <p>
            Manage farmers, inventory, programs,
            insurance, and agricultural operations
            in one modern platform.
        </p>

    </div>

    <!-- RIGHT -->

    <div class="login-right">

        <a href="home.php" class="close-btn">
            <span class="material-icons">close</span>
        </a>

        <div class="login-header">
            <h2>Welcome Back</h2>
            <p>Sign in to continue to RFIMS</p>
        </div>

        <?php if(!empty($error)): ?>
            <div class="error">
                <?= $error ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="login.php">

            <!-- EMAIL -->

            <div class="form-group">

                <label>Email Address</label>

                <div class="input-box">

                    <span class="material-icons">mail</span>

                    <input
                        type="email"
                        name="email"
                        class="form-input"
                        placeholder="Enter your email"
                        required
                    >

                </div>

            </div>

            <!-- PASSWORD -->

            <div class="form-group">

                <label>Password</label>

                <div class="input-box">

                    <span class="material-icons">lock</span>

                    <input
                        type="password"
                        name="password"
                        class="form-input"
                        placeholder="Enter your password"
                        required
                    >

                </div>

            </div>

            <div class="form-options">

                <div></div>

                <a href="#">
                    Forgot Password?
                </a>

            </div>

            <button type="submit" class="btn-login">
                Sign In
            </button>

        </form>

        <div class="login-footer">
            © <?= date('Y') ?> RFIMS Agriculture Platform
        </div>

    </div>

</div>

</body>
</html>