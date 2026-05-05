<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RFIMS - Rice Farming Inventory Management System</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/home.css">
       <style>.stat-number {
            font-size: 48px;
            font-weight: 700;
            margin-bottom: 8px;
            color: #10b981;
        }

        .stat-label {
            font-size: 16px;
            color: rgba(224, 223, 223, 0.7);
}
</style>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="nav-container">
            <div class="logo">
                <span class="material-icons">agriculture</span>
                <span>RFIMS</span>
            </div>
            <nav class="nav-links">
                <a href="#features">Features</a>
                <a href="#about">About</a>
                <a href="#footer">Contact</a>
            </nav>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-container">
            <div class="hero-content">
                <h1>
                    Modernizing <span class="highlight">Agriculture</span> Management
                </h1>
                <p>
                    Comprehensive digital platform for managing farmers, production, insurance, and agricultural operations across the Inabanga.
                </p>
            </div>
    </section>

    <!-- Features Section -->
    <section class="features" id="features">
        <div class="features-container">
            <div class="section-header">
                <h2>Comprehensive Agriculture Solutions</h2>
                <p>Everything you need to manage agricultural operations efficiently</p>
            </div>
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">
                        <span class="material-icons">database</span>
                    </div>
                    <h3>Registry Management</h3>
                    <p>Centralized database for farmers, and farms with complete profile management.</p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">
                        <span class="material-icons">agriculture</span>
                    </div>
                    <h3>Production Tracking</h3>
                    <p>Monitor rice production</p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">
                        <span class="material-icons">shield</span>
                    </div>
                    <h3>Insurance Management</h3>
                    <p>Track enrollment, policies, premium subsidies, and manage insurance coverage for all assets.</p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">
                        <span class="material-icons">assessment</span>
                    </div>
                    <h3>Monitoring & Reports</h3>
                    <p>Field visit reports, crop conditions, water quality monitoring with GPS and photo uploads.</p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">
                        <span class="material-icons">inventory_2</span>
                    </div>
                    <h3>Inventory Control</h3>
                    <p>Manage supplies, equipment, warehouse operations, and track distribution to beneficiaries.</p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">
                        <span class="material-icons">map</span>
                    </div>
                    <h3>GIS & Mapping</h3>
                    <p>Geographic visualization of farm locations, disaster zones, and production density analysis.</p>
                </div>
            </div>
        </div>
    </section>
    <!-- About section -->
    <section class="about" id="about">
            <div class="about-content">
                <h2>About RFIMS</h2>
                <p>
                    RFIMS is a comprehensive agriculture management system designed to modernize and streamline agricultural operations across the Philippines. Developed in collaboration with the Department of Agriculture, RFIMS provides a centralized platform for managing farmers, production, insurance, and monitoring activities. With features like registry management, production tracking, insurance management, and GIS mapping, RFIMS empowers farmers and agricultural stakeholders to make informed decisions, improve productivity, and enhance resilience against climate change and other challenges.
                </p>
            </div>
    </section>
    
    <!-- Footer -->
    <footer class="footer" id="footer">
        <div class="footer-container">
            <div class="footer-content">
                <div class="footer-brand">
                    <h3>
                        <span class="material-icons">agriculture</span>
                        RFIMS
                    </h3>
                    <p>
                        A comprehensive agriculture management system designed to modernize and streamline agricultural operations across the Philippines.
                    </p>
                </div>
                <div class="footer-section">
                    <h4>System</h4>
                    
                    <ul class="footer-links">
                        <li><a>Registry</a></li>
                        <li><a>Inventory</a></li>
                        <li><a>Insurance</a></li>
                        <li><a>Monitoring</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h4>Resources</h4>
                    <ul class="footer-links">
                        <li><a >Documentation</a></li>
                        <li><a >User Guide</a></li>
                        <li><a >Training</a></li>
                        <li><a >Support</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h4>Contact</h4>
                    <ul class="footer-links">
                        <li><a >Help Center</a></li>
                        <li><a >Email Support</a></li>
                        <li><a >Feedback</a></li>
                        <li><a >Report Issue</a></li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2026 Rice Farming Inventory Management System. Department of Agriculture in municipality of Inabanga- Republic of the Philippines. All rights reserved.</p>
            </div>
        </div>
    </footer>
    <script>
    const requestDropdown = document.getElementById('request_type');
    const insuranceFields = document.getElementById('insurance_fields');
    const registrationFields = document.getElementById('registration_fields');

    requestDropdown.addEventListener('change', function() {
        // Hide all fields first
        insuranceFields.style.display = 'none';
        registrationFields.style.display = 'none';

        // Show fields based on selection
        if (this.value === 'Insurance Request') {
            insuranceFields.style.display = 'block';
        } else if (this.value === 'Farm Registration') {
            registrationFields.style.display = 'block';
        }
    });
</script>
</body>
</html>