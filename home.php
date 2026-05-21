<?php
include 'database.php';

$scheduleQuery = $conn->query("
    SELECT * FROM distributions
    ORDER BY date ASC
");
$trainingQuery = $conn->query("
    SELECT * FROM trainings
    ORDER BY date ASC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RFIMS - Rice Farming Inventory Management System</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
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

            --header-h: 60px;
        }

        /* =========================
   SIDEBAR
========================= */

.sidebar{
    position:fixed;
    top:0;
    left:0;

    width:260px;
    height:100vh;

    background:var(--surface);

    border-right:1px solid var(--border);

    padding:24px 18px;

    z-index:999;
}

.sidebar-top{
    margin-bottom:30px;
}

.sidebar-menu{
    display:flex;
    flex-direction:column;
    gap:8px;
}

.sidebar-link{
    display:flex;
    align-items:center;
    gap:12px;

    padding:12px 14px;

    border-radius:12px;

    text-decoration:none;

    color:var(--text-secondary);

    font-size:14px;
    font-weight:600;

    transition:.2s;
}

.sidebar-link:hover{
    background:var(--brand-light);
    color:var(--brand-dark);
}

.sidebar-link .material-icons{
    font-size:20px;
}

/* =========================
   MAIN CONTENT
========================= */

.main-content{
    margin-left:260px;
}

/* REMOVE OLD HEADER */
.header{
    display:none;
}

/* MOBILE */
@media(max-width:900px){

    .sidebar{
        width:80px;
        padding:20px 10px;
    }

    .logo-text,
    .sidebar-link{
        font-size:0;
    }

    .sidebar-link{
        justify-content:center;
    }

    .sidebar-link .material-icons{
        font-size:22px;
    }

    .main-content{
        margin-left:80px;
    }
}

        html, body {
            font-family: var(--font);
            background: var(--bg);
            color: var(--text-primary);
            scroll-behavior: smooth;
        }

        /* ── Header ── */
        .header {
            position: sticky;
            top: 0;
            z-index: 100;
            height: var(--header-h);
            background: var(--surface);
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
        }

        .nav-container {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
        }
        .sidebar-brand {
    display: flex;
    align-items: center;
    gap: 12px;
    text-decoration: none;
    margin-bottom: 30px;
}

.brand-name {
    font-size: 18px;
    font-weight: 700;
    color: var(--text-primary);
    letter-spacing: -.3px;
}

/* MOBILE */
@media(max-width:900px){
    .brand-name {
        display: none;
    }
}

        .logo-mark .material-icons { font-size: 18px; color: #fff; }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 2px;
        }

        .nav-links a {
            padding: 7px 13px;
            border-radius: var(--radius-md);
            font-size: 13px;
            font-weight: 500;
            color: var(--text-secondary);
            text-decoration: none;
            transition: background var(--transition), color var(--transition);
            white-space: nowrap;
        }

        .nav-links a:hover {
            background: var(--surface-2);
            color: var(--text-primary);
        }

        .nav-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 14px;
            border-radius: var(--radius-md);
            background: var(--brand);
            color: #fff !important;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            transition: background var(--transition);
            border: 1px solid var(--brand-dark);
        }

        .nav-btn:hover { background: var(--brand-dark) !important; }
        .nav-btn .material-icons { font-size: 16px; }

        .hamburger {
            display: none;
            width: 36px;
            height: 36px;
            border: 1px solid var(--border);
            border-radius: var(--radius-md);
            background: none;
            cursor: pointer;
            align-items: center;
            justify-content: center;
            color: var(--text-secondary);
        }

        /* ── Hero ── */
        .hero {
            background: var(--surface);
            border-bottom: 1px solid var(--border);
            padding: 72px 24px 80px;
        }

        .hero-inner {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 48px;
            align-items: center;
        }

        .hero-label {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 5px 12px;
            border-radius: 99px;
            background: var(--brand-light);
            color: var(--brand-dark);
            font-size: 12px;
            font-weight: 600;
            margin-bottom: 16px;
            letter-spacing: .02em;
        }

        .hero-label .material-icons { font-size: 14px; }

        .hero h1 {
            font-size: 40px;
            font-weight: 800;
            line-height: 1.15;
            letter-spacing: -.6px;
            color: var(--text-primary);
            margin-bottom: 18px;
        }

        .hero h1 .highlight { color: var(--brand); }

        .hero p {
            font-size: 15px;
            color: var(--text-secondary);
            line-height: 1.7;
            margin-bottom: 28px;
            max-width: 480px;
        }

        .hero-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 10px 18px;
            border-radius: var(--radius-md);
            border: 1px solid var(--border);
            background: var(--surface);
            color: var(--text-primary);
            font-size: 13.5px;
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

        /* Hero visual */
        .hero-visual {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }

        .hero-stat-card {
            background: var(--surface-2);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            padding: 18px;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .hero-stat-card.accent {
            background: var(--brand-light);
            border-color: var(--brand-muted);
            grid-column: 1 / -1;
        }

        .hero-stat-icon {
            width: 38px;
            height: 38px;
            border-radius: var(--radius-sm);
            background: var(--surface);
            border: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .hero-stat-card.accent .hero-stat-icon {
            background: #fff;
            border-color: var(--brand-muted);
        }

        .hero-stat-icon .material-icons { font-size: 18px; color: var(--brand); }

        .hero-stat-value {
            font-size: 26px;
            font-weight: 700;
            color: var(--text-primary);
            letter-spacing: -.4px;
            line-height: 1;
        }

        .hero-stat-card.accent .hero-stat-value { color: var(--brand-dark); font-size: 20px; }

        .hero-stat-label {
            font-size: 12px;
            color: var(--text-muted);
            font-weight: 500;
        }

        /* ── Section wrapper ── */
        .section {
            max-width: 1200px;
            margin: 0 auto;
            padding: 56px 24px;
        }

        .section-header {
            margin-bottom: 28px;
        }

        .section-eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: var(--brand);
            margin-bottom: 8px;
        }

        .section-header h2 {
            font-size: 24px;
            font-weight: 700;
            color: var(--text-primary);
            letter-spacing: -.3px;
        }

        .section-header p {
            font-size: 14px;
            color: var(--text-muted);
            margin-top: 6px;
        }

        /* ── Cards grid ── */
        .cards-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 14px;
        }

        /* ── Schedule card ── */
        .schedule-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            padding: 18px 20px;
            display: flex;
            flex-direction: column;
            gap: 10px;
            transition: box-shadow var(--transition), border-color var(--transition);
        }

        .schedule-card:hover {
            box-shadow: var(--shadow-md);
            border-color: var(--brand-muted);
        }

        .schedule-card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
        }

        .schedule-card-icon {
            width: 36px;
            height: 36px;
            border-radius: var(--radius-sm);
            background: var(--brand-light);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .schedule-card-icon .material-icons { font-size: 17px; color: var(--brand); }

        .schedule-card-date {
            font-size: 11px;
            font-weight: 600;
            color: var(--brand);
            background: var(--brand-light);
            padding: 4px 10px;
            border-radius: 99px;
            letter-spacing: .02em;
        }

        .schedule-card h3 {
            font-size: 14px;
            font-weight: 600;
            color: var(--text-primary);
        }

        .schedule-card-meta {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .meta-row {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 12.5px;
            color: var(--text-secondary);
        }

        .meta-row .material-icons { font-size: 13px; color: var(--text-muted); }

        .meta-row strong {
            color: var(--text-primary);
            font-weight: 600;
        }

        .meta-divider {
            border: none;
            border-top: 1px solid var(--border-light);
            margin: 2px 0;
        }

        /* ── Training status badge ── */
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 4px 10px;
            border-radius: 99px;
            font-size: 11px;
            font-weight: 600;
        }

        .status-scheduled { background: #fef3c7; color: #92400e; }
        .status-ongoing   { background: #dbeafe; color: #1e40af; }
        .status-completed { background: #dcfce7; color: #15803d; }
        .status-cancelled { background: #fee2e2; color: #991b1b; }

        /* ── Divider section ── */
        .section-divider {
            border: none;
            border-top: 1px solid var(--border);
        }

        /* ── Features ── */
        .features-bg {
            background: var(--surface);
            border-top: 1px solid var(--border);
            border-bottom: 1px solid var(--border);
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
            gap: 14px;
        }

        .feature-card {
            background: var(--surface-2);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            padding: 22px;
            transition: box-shadow var(--transition), border-color var(--transition);
        }

        .feature-card:hover {
            box-shadow: var(--shadow-md);
            border-color: var(--brand-muted);
        }

        .feature-icon {
            width: 44px;
            height: 44px;
            border-radius: var(--radius-md);
            background: var(--brand-light);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 14px;
        }

        .feature-icon .material-icons { font-size: 22px; color: var(--brand); }

        .feature-card h3 {
            font-size: 14.5px;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 7px;
        }

        .feature-card p {
            font-size: 13px;
            color: var(--text-muted);
            line-height: 1.65;
        }

        /* ── About ── */
        .about-inner {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 48px;
            align-items: center;
        }

        .about-content h2 {
            font-size: 28px;
            font-weight: 800;
            color: var(--text-primary);
            letter-spacing: -.4px;
            margin-bottom: 14px;
        }

        .about-content p {
            font-size: 14px;
            color: var(--text-secondary);
            line-height: 1.75;
            margin-bottom: 14px;
        }

        .about-stats {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }

        .about-stat {
            background: var(--surface-2);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            padding: 18px;
        }

        .about-stat-value {
            font-size: 28px;
            font-weight: 800;
            color: var(--brand);
            letter-spacing: -.5px;
            line-height: 1;
            margin-bottom: 5px;
        }

        .about-stat-label {
            font-size: 12px;
            color: var(--text-muted);
            font-weight: 500;
        }

        /* ── Footer ── */
        .footer {
            background: var(--surface);
            border-top: 1px solid var(--border);
        }

        .footer-inner {
            max-width: 1200px;
            margin: 0 auto;
            padding: 48px 24px 24px;
        }

        .footer-grid {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr;
            gap: 32px;
            margin-bottom: 40px;
        }

        .footer-brand h3 {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 15px;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 10px;
        }

        .footer-brand h3 .material-icons {
            font-size: 18px;
            color: var(--brand);
        }

        .footer-brand p {
            font-size: 13px;
            color: var(--text-muted);
            line-height: 1.65;
        }

        .footer-col h4 {
            font-size: 11px;
            font-weight: 700;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: var(--text-muted);
            margin-bottom: 12px;
        }

        .footer-col ul {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 7px;
        }

        .footer-col ul li a {
            font-size: 13px;
            color: var(--text-secondary);
            text-decoration: none;
            transition: color var(--transition);
        }

        .footer-col ul li a:hover { color: var(--brand); }

        .footer-bottom {
            border-top: 1px solid var(--border-light);
            padding-top: 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
        }

        .footer-bottom p {
            font-size: 12px;
            color: var(--text-muted);
        }

        .footer-bottom-links {
            display: flex;
            gap: 16px;
        }

        .footer-bottom-links a {
            font-size: 12px;
            color: var(--text-muted);
            text-decoration: none;
            transition: color var(--transition);
        }

        .footer-bottom-links a:hover { color: var(--brand); }

        /* ── Responsive ── */
        @media (max-width: 900px) {
            .hero-inner { grid-template-columns: 1fr; }
            .hero-visual { display: none; }
            .about-inner { grid-template-columns: 1fr; }
            .footer-grid { grid-template-columns: 1fr 1fr; }
            .nav-links { display: none; }
            .hamburger { display: flex; }
        }

        @media (max-width: 600px) {
            .hero { padding: 48px 24px 56px; }
            .hero h1 { font-size: 28px; }
            .footer-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

<!-- SIDEBAR -->
<div class="sidebar" id="sidebar">

    <div class="sidebar-top">
<a href="home.php" class="sidebar-brand">

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

    </div>

    <div class="sidebar-menu">

        <a href="#schedule" class="sidebar-link">
            <span class="material-icons">volunteer_activism</span>
            Distribution
        </a>

        <a href="#training" class="sidebar-link">
            <span class="material-icons">school</span>
            Training
        </a>

        <a href="#features" class="sidebar-link">
            <span class="material-icons">apps</span>
            Features
        </a>

        <a href="#about" class="sidebar-link">
            <span class="material-icons">info</span>
            About
        </a>

        <a href="#footer" class="sidebar-link">
            <span class="material-icons">call</span>
            Contact
        </a>

    </div>

</div>

<!-- MAIN CONTENT -->
<div class="main-content">

<!-- ── Hero ── -->
<section class="hero">
    <div class="hero-inner">
        <div class="hero-content">
            <div class="hero-label">
                <span class="material-icons">eco</span>
                Department of Agriculture — Inabanga
            </div>
            <h1>
                Modernizing <span class="highlight">Agriculture</span> Management
            </h1>
            <p>
                A comprehensive digital platform for managing farmers, production, insurance, and agricultural operations across the municipality of Inabanga.
            </p>
            <div class="hero-actions">
                <a href="#features" class="btn">
                    <span class="material-icons">info</span>
                    Learn More
                </a>
            </div>
        </div>

        <div class="hero-visual">
            <div class="hero-stat-card accent">
                <div class="hero-stat-value">RFIMS</div>
                <div class="hero-stat-label">Rice Farming Inventory Management System</div>
            </div>
            <div class="hero-stat-card">
                <div class="hero-stat-icon">
                    <span class="material-icons">people</span>
                </div>
                <div class="hero-stat-value">Registry</div>
                <div class="hero-stat-label">Farmer profiles & farms</div>
            </div>
            <div class="hero-stat-card">
                <div class="hero-stat-icon">
                    <span class="material-icons">inventory_2</span>
                </div>
                <div class="hero-stat-value">Inventory</div>
                <div class="hero-stat-label">Supplies & distributions</div>
            </div>
            <div class="hero-stat-card">
                <div class="hero-stat-icon">
                    <span class="material-icons">security</span>
                </div>
                <div class="hero-stat-value">Insurance</div>
                <div class="hero-stat-label">Coverage & claims</div>
            </div>
            <div class="hero-stat-card">
                <div class="hero-stat-icon">
                    <span class="material-icons">bar_chart</span>
                </div>
                <div class="hero-stat-value">Reports</div>
                <div class="hero-stat-label">Analytics & monitoring</div>
            </div>
        </div>
    </div>
</section>

<!-- ── Distribution Schedule ── -->
<div id="schedule">
    <div class="section">
        <div class="section-header">
            <div class="section-eyebrow">
                <span class="material-icons" style="font-size:14px">volunteer_activism</span>
                Programs
            </div>
            <h2>Farmer Distribution Schedule</h2>
            <p>Upcoming distribution programs and schedules for registered farmers</p>
        </div>

        <div class="cards-grid">
             <?php while($row = $scheduleQuery->fetch_assoc()): ?>

<?php
    // AUTO STATUS FOR DISTRIBUTION
    $statusClass = 'status-scheduled';

    $today = strtotime(date("Y-m-d"));
    $distributionDate = strtotime($row['date']);

    if ($distributionDate < $today) {
        $status = 'completed';
        $statusClass = 'status-completed';
    }
    elseif ($distributionDate == $today) {
        $status = 'ongoing';
        $statusClass = 'status-ongoing';
    }
    else {
        $status = 'scheduled';
        $statusClass = 'status-scheduled';
    }
?>

<div class="schedule-card">

    <div class="schedule-card-header">

        <div class="schedule-card-icon">
            <span class="material-icons">local_shipping</span>
        </div>

        <span class="schedule-card-date">
            <?php echo date("M d, Y", strtotime($row['date'])); ?>
        </span>

    </div>

    <h3><?php echo htmlspecialchars($row['program']); ?></h3>

    <hr class="meta-divider">

    <div class="schedule-card-meta">

        <div class="meta-row">
            <span class="material-icons">category</span>
            Item:
            <strong><?php echo htmlspecialchars($row['item_name']); ?></strong>
        </div>
                 <div class="meta-row">
        <span class="material-icons">location_on</span>
        Location:
        <strong><?php echo htmlspecialchars($row['location']); ?></strong>
    </div>
                
        <div class="meta-row">
            <span class="material-icons">groups</span>
            Beneficiaries:
            <strong><?php echo htmlspecialchars($row['beneficiaries']); ?></strong>
        </div>

    </div>

    <!-- STATUS BADGE -->
    <div>
        <span class="status-badge <?php echo $statusClass; ?>">
            <?php echo ucfirst($status); ?>
        </span>
    </div>

</div>

<?php endwhile; ?>       
        </div>
    </div>
</div>

<hr class="section-divider">

<!-- ── Training Sessions ── -->
<div id="training">
    <div class="section">
        <div class="section-header">
            <div class="section-eyebrow">
                <span class="material-icons" style="font-size:14px">school</span>
                Education
            </div>
            <h2>Training Sessions</h2>
            <p>Upcoming agricultural training programs for farmers</p>
        </div>

        <div class="cards-grid">
            <?php while($row = $trainingQuery->fetch_assoc()): ?>
            <?php
                $statusClass = 'status-scheduled';
                $status = strtolower($row['status']);
                if ($status === 'ongoing')   $statusClass = 'status-ongoing';
                elseif ($status === 'completed') $statusClass = 'status-completed';
                elseif ($status === 'cancelled') $statusClass = 'status-cancelled';
            ?>
            <div class="schedule-card">
                <div class="schedule-card-header">
                    <div class="schedule-card-icon">
                        <span class="material-icons">school</span>
                    </div>
                    <span class="schedule-card-date">
                        <?php echo date("M d, Y", strtotime($row['date'])); ?>
                    </span>
                </div>
                <h3><?php echo htmlspecialchars($row['title']); ?></h3>
                <hr class="meta-divider">
                <div class="schedule-card-meta">
                    <div class="meta-row">
                        <span class="material-icons">location_on</span>
                        <strong><?php echo htmlspecialchars($row['location']); ?></strong>
                    </div>
                    <div class="meta-row">
                        <span class="material-icons">person</span>
                        Trainer: <strong><?php echo htmlspecialchars($row['trainer']); ?></strong>
                    </div>
                    <div class="meta-row">
                        <span class="material-icons">groups</span>
                        Participants: <strong><?php echo htmlspecialchars($row['participants']); ?></strong>
                    </div>
                </div>
                <div>
                    <span class="status-badge <?php echo $statusClass; ?>">
                        <?php echo htmlspecialchars($row['status']); ?>
                    </span>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </div>
</div>

<!-- ── Features ── -->
<div class="features-bg" id="features">
    <div class="section">
        <div class="section-header">
            <div class="section-eyebrow">
                <span class="material-icons" style="font-size:14px">apps</span>
                Platform
            </div>
            <h2>Comprehensive Agriculture Solutions</h2>
            <p>Everything you need to manage agricultural operations efficiently</p>
        </div>

        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">
                    <span class="material-icons">manage_accounts</span>
                </div>
                <h3>Registry Management</h3>
                <p>Centralized database for farmers and farms with complete profile management and record keeping.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <span class="material-icons">shield</span>
                </div>
                <h3>Insurance Management</h3>
                <p>Track enrollment, policies, payment subsidies, and manage insurance coverage for all registered assets.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <span class="material-icons">inventory_2</span>
                </div>
                <h3>Inventory Control</h3>
                <p>Manage supplies, monitor low stock levels, and track total item distributions to beneficiaries.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <span class="material-icons">report_problem</span>
                </div>
                <h3>Incidents & Claims</h3>
                <p>Report and monitor agricultural incidents, process insurance claims, and track resolution status.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <span class="material-icons">bar_chart</span>
                </div>
                <h3>Reports & Analytics</h3>
                <p>Generate comprehensive reports on production, distribution, and program performance across the municipality.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <span class="material-icons">volunteer_activism</span>
                </div>
                <h3>Programs & Distribution</h3>
                <p>Schedule and manage government aid programs, distributions, and training sessions for farmers.</p>
            </div>
        </div>
    </div>
</div>

<!-- ── About ── -->
<div id="about">
    <div class="section">
        <div class="about-inner">
            <div class="about-content">
                <div class="section-eyebrow">
                    <span class="material-icons" style="font-size:14px">info</span>
                    About
                </div>
                <h2>About RFIMS</h2>
                <p>
                    RFIMS is a comprehensive agriculture management system designed to modernize and streamline agricultural operations across the Philippines. Developed in collaboration with the Department of Agriculture, RFIMS provides a centralized platform for managing farmers, production, insurance, and monitoring activities.
                </p>
                <p>
                    With features like registry management, production tracking, insurance management, and GIS mapping, RFIMS empowers farmers and agricultural stakeholders to make informed decisions and enhance resilience against climate change.
                </p>
            </div>
            <div class="about-stats">
                <div class="about-stat">
                    <div class="about-stat-value">DA</div>
                    <div class="about-stat-label">Department of Agriculture</div>
                </div>
                <div class="about-stat">
                    <div class="about-stat-value">PH</div>
                    <div class="about-stat-label">Republic of the Philippines</div>
                </div>
                <div class="about-stat">
                    <div class="about-stat-value">6</div>
                    <div class="about-stat-label">Core Modules</div>
                </div>
                <div class="about-stat">
                    <div class="about-stat-value">24/7</div>
                    <div class="about-stat-label">System Availability</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ── Footer ── -->
<footer class="footer" id="footer">
    <div class="footer-inner">
        <div class="footer-grid">
            <div class="footer-brand">
                <h3>
                    RFIMS
                </h3>
                <p>A comprehensive agriculture management system designed to modernize and streamline agricultural operations across the municipality of Inabanga, Bohol.</p>
            </div>
            <div class="footer-col">
                <h4>System</h4>
                <ul>
                    <li><a href="#">Registry</a></li>
                    <li><a href="#">Inventory</a></li>
                    <li><a href="#">Insurance</a></li>
                    <li><a href="#">Monitoring</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h4>Resources</h4>
                <ul>
                    <li><a href="#">Documentation</a></li>
                    <li><a href="#">User Guide</a></li>
                    <li><a href="#">Training</a></li>
                    <li><a href="#">Support</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h4>Contact</h4>
                <ul>
                    <li><a href="#">Help Center</a></li>
                    <li><a href="#">Email Support</a></li>
                    <li><a href="#">Feedback</a></li>
                    <li><a href="#">Report Issue</a></li>
                </ul>
            </div>
        </div>

        <div class="footer-bottom">
            <p>&copy; 2026 Rice Farming Inventory Management System — Department of Agriculture, Inabanga, Bohol · Republic of the Philippines. All rights reserved.</p>
            <div class="footer-bottom-links">
                <a href="#">Privacy</a>
                <a href="#">Terms</a>
            </div>
        </div>
    </div>
</footer>

</script>
</div>
</body>
</html>