<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AgriMS - Agriculture Management System</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/home.css">
       
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="nav-container">
            <div class="logo">
                <span class="material-icons">agriculture</span>
                <span>RFMS</span>
            </div>
            <nav class="nav-links">
                <a href="#features">Features</a>
                <a href="#about">About</a>
                <a href="#contact">Contact</a>
                <a href="#request">Request / Registration</a>
                <a href="#feedback">Feedback</a>
                <div class="login-btn">
                    <a href="login.php" class="btn btn-primary">
                        Login to System
                    </a>
                </div>
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
                <div class="hero-buttons">
                    <a href="login.php" class="btn btn-primary">
                        Get Started
                        <span class="material-icons">arrow_forward</span>
                    </a>
                    <a href="#features" class="btn btn-secondary">
                        Learn More
                    </a>
                </div>
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

    <!-- Stats Section -->
    <section class="stats">
        <div class="stats-container">
            <div class="stat-item">
                <div class="stat-number">12,847</div>
                <div class="stat-label">Registered Farmers</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">45,892</div>
                <div class="stat-label">MT Production</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">8,934</div>
                <div class="stat-label">Insured Assets</div>
            </div>
        </div>
    </section>
    <!-- Request / Registration Section -->
    <section class="request-section" id="request">
        <div class="request-container">
            <h2>Farmer Requests & Registration</h2>
            <p>Submit your request for insurance, farm registration, or equipment assistance.</p>

            <form action="submit_request.php" method="POST" class="request-form" id="requestForm">

                <!-- Basic Farmer Info -->
                <div class="form-group">
                    <input type="text" class="form-input" name="farmer_name" placeholder=" " required>
                    <label><span>Your Full Name</span></label>
                </div>
                <div class="form-group">
                    <input type="text" class="form-input" name="contact_info" placeholder=" " required>
                    <label><span>Phone / Email</span></label>
                </div>
                <div class="form-group">
                    <input type="text" class="form-input" name="address" placeholder=" " required>
                    <label><span>Address (Barangay)</span></label>
                </div>
                <!-- Request Type Dropdown -->
                <select name="request_type" id="request_type" required>
                    <option value="">Select Request Type</option>
                    <option value="Insurance Request">Insurance Request</option>
                    <option value="Farm Registration">Farm Registration</option>
                </select>

                <!-- Insurance Fields -->
                <div class="dynamic-fields" id="insurance_fields" style="display:none;">
                    <div class="form-group">
                        <input type="text" class="dynamic-input" name="farm_name_insurance" placeholder=" ">
                        <label for="farm_name_insurance" ><span>Farm Name (if applicable)</span></label>
                    </div>
                    <div class="form-group">
                        <input type="text" class="dynamic-input" name="farm_location_insurance" placeholder=" ">
                        <label for="farm_location_insurance" ><span>Location of Farm</span></label>
                    </div>
                    <select name="insurance_type">
                        <option value="">Select Insurance Type</option>
                        <option value="Crop">Crop</option>
                        <option value="Livestock">Livestock</option>
                        <option value="Equipment">Equipment</option>
                    </select>
                    <div class="form-group">
                        <input type="text" class="dynamic-input" name="insured_assets" placeholder=" ">
                        <label ><span>Describe Assets to Insure</span></label>
                    </div>
                    <div class="form-group">
                        <input type="text" class="dynamic-input" name="coverage_period" placeholder=" ">
                        <label ><span>Coverage Period (Start - End)</span></label>
                    </div>
                    <input type="file" name="supporting_docs" accept=".pdf,.jpg,.png">
                </div>

                <!-- Registration Fields -->
                <div class="dynamic-fields" id="registration_fields" style="display:none;">
                    <div class="form-group">
                        <input type="text" class="dynamic-input" name="farm_name_registration" placeholder=" ">
                        <label ><span>Farm Name (Optional)</span></label>
                    </div>
                    <div class="form-group">
                        <input type="text" class="dynamic-input" name="farm_location_registration" placeholder=" ">
                        <label ><span>Farm Location</span></label>
                    </div>
                    <div class="form-group">
                        <input type="text" class="dynamic-input" name="farm_type" placeholder=" ">
                        <labe><span>Type of Farm (Crop, Livestock, Fisheries)</span></label>
                    </div>
                    <div class="form-group">
                        <input type="text" class="dynamic-input" name="farm_size" placeholder=" ">
                        <label ><span>Farm Size (Hectares)</span></label>
                    </div>
                    <input type="file" name="ownership_docs" accept=".pdf,.jpg,.png">
                </div>

                <button type="submit" class="btn btn-primary">Submit Request</button>
            </form>
        </div>
    </section>
    <!-- Feedback Section -->
    <section class="feedback-section" id="feedback">
        <div class="feedback-container">
            <h2>Farmer Feedback</h2>
            <p>Your suggestions and feedback help us improve our agriculture services.</p>

            <form action="submit_feedback.php" method="POST" class="feedback-form">

                <input type="text" name="farmer_name" placeholder="Your Name" required>

                <textarea name="message" placeholder="Write your feedback..." required></textarea>

                <button type="submit" class="btn btn-primary">
                    Send Feedback
                </button>

            </form>
        </div>
    </section>
    <!-- Footer -->
    <footer class="footer">
        <div class="footer-container">
            <div class="footer-content">
                <div class="footer-brand">
                    <h3>
                        <span class="material-icons">agriculture</span>
                        AgriMS
                    </h3>
                    <p>
                        A comprehensive agriculture management system designed to modernize and streamline agricultural operations across the Philippines.
                    </p>
                </div>
                <div class="footer-section">
                    <h4>System</h4>
                    <ul class="footer-links">
                        <li><a href="registry-agrims.php">Registry</a></li>
                        <li><a href="production-agrims.php">Production</a></li>
                        <li><a href="insurance-agrims.php">Insurance</a></li>
                        <li><a href="monitoring-agrims.php">Monitoring</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h4>Resources</h4>
                    <ul class="footer-links">
                        <li><a href="#">Documentation</a></li>
                        <li><a href="#">User Guide</a></li>
                        <li><a href="#">Training</a></li>
                        <li><a href="#">Support</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h4>Contact</h4>
                    <ul class="footer-links">
                        <li><a href="#">Help Center</a></li>
                        <li><a href="#">Email Support</a></li>
                        <li><a href="#">Feedback</a></li>
                        <li><a href="#">Report Issue</a></li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2026 Agriculture Management System. Department of Agriculture - Republic of the Philippines. All rights reserved.</p>
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