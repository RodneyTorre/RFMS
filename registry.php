<?php
session_start();
include 'database.php';


if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $full_name = $_POST['full_name'];
    $gender = $_POST['gender'];
    $contact = $_POST['contact_number'];
    $address = $_POST['address'];
    $farm_size = $_POST['farm_size'];
    $crop_type = $_POST['crop_type'];

    $sql = "INSERT INTO farmers (full_name, gender, contact_number, address, farm_size, crop_type)
            VALUES (?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssds", $full_name, $gender, $contact, $address, $farm_size, $crop_type);

    if ($stmt->execute()) {
        // ✅ REDIRECT after insert
        header("Location: registry.php?success=1");
        exit();
    } else {
        echo "Error: " . $conn->error;
    }
}
$farmers = [];

$sql = "SELECT * FROM farmers ORDER BY farmer_id DESC";
$result = $conn->query($sql);

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $farmers[] = $row;
    }
}
include 'header.php';
?>

<link rel="stylesheet" href="assets/css/registry.css">

<!-- Page Content -->
<div class="page-header">
    <div class="page-title-section">
        <h1>Registry Management</h1>
        <p class="page-subtitle">Master data for farmers</p>
    </div>
    <div class="page-actions">
        <button class="btn btn-filter">
            <span class="material-icons">filter_list</span>
            Filter
        </button>
        <button class="btn btn-primary" onclick="openFarmerModal()">
            <span class="material-icons">add</span>
            Add New Entry
        </button>
    </div>
</div>

<!-- Search Box -->
<div class="search-box">
    <span class="material-icons">search</span>
    <input type="text" placeholder="Search by name, RSBSA number, location...">
</div>

<!-- Data Table -->
<div class="table-card">
    <table class="data-table">
        <thead>
            <tr>
                <th>RSBSA NO.</th>
                <th>NAME</th>
                <th>BARANGAY</th>
                <th>FARM SIZE</th>
                <th>CROPS</th>
                <th>ACTIONS</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($farmers as $farmer): ?>
            <tr>
                <td> RSBA <?php echo $farmer['farmer_id']; ?></td>
                <td><?php echo $farmer['full_name']; ?></td>
                <td><?php echo $farmer['address']; ?></td>
                <td><?php echo $farmer['farm_size']; ?></td>
                <td><?php echo $farmer['crop_type']; ?></td>
                <td>Active</td>
                <td>
                </td>
                <td>
                    <div class="action-buttons">
                        <button class="action-btn edit" title="Edit">
                            <span class="material-icons">edit</span>
                        </button>
                        <button class="action-btn delete" title="Delete">
                            <span class="material-icons">delete</span>
                        </button>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<!-- FARMER MODAL -->
<div id="farmerModal" class="modal">
    
    <div class="modal-content">
         
        <!-- HEADER -->
        <div class="modal-header">
            <h2>Add New Farmer</h2>
        </div>
              
        <!-- FORM -->
        <form method="POST" action="">

            <input type="text" name="full_name" placeholder="Full Name" required>

            <select name="gender" required>
                <option value="">Select Gender</option>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
            </select>

            <input type="text" name="contact_number" placeholder="Contact Number">
            <input type="text" name="address" placeholder="Address">
            <input type="number" step="0.01" name="farm_size" placeholder="Farm Size">
            <input type="text" name="crop_type" placeholder="Crop Type">

            <!-- FOOTER BUTTONS -->
            <div class="modal-buttons">
                <button type="button" onclick="closeFarmerModal()">Cancel</button>
                <button type="submit">Save</button>
            </div>

        </form>
    
    
</div>         
</div>
<script>
    function openFarmerModal() {
        document.getElementById("farmerModal").style.display = "flex";
    }

    function closeFarmerModal() {
        document.getElementById("farmerModal").style.display = "none";
    }

    // close when clicking outside modal box
    window.onclick = function(event) {
        let modal = document.getElementById("farmerModal");
        if (event.target === modal) {
            modal.style.display = "none";
        }
    }
    // Tab switching functionality
    document.querySelectorAll('.tab-btn').forEach(tab => {
        tab.addEventListener('click', function() {
            // Remove active class from all tabs
            document.querySelectorAll('.tab-btn').forEach(t => t.classList.remove('active'));
            // Add active class to clicked tab
            this.classList.add('active');
            
            // Here you would normally load different data based on the tab
            console.log('Switched to:', this.textContent);
        });
    });

    // Action button handlers
    document.querySelectorAll('.action-btn.view').forEach(btn => {
        btn.addEventListener('click', function() {
            const row = this.closest('tr');
            const rsbsa = row.cells[0].textContent;
            alert('View details for: ' + rsbsa);
            // In real app, open modal or navigate to detail page
        });
    });

    document.querySelectorAll('.action-btn.edit').forEach(btn => {
        btn.addEventListener('click', function() {
            const row = this.closest('tr');
            const rsbsa = row.cells[0].textContent;
            alert('Edit: ' + rsbsa);
            // In real app, open edit modal or form
        });
    });

    document.querySelectorAll('.action-btn.delete').forEach(btn => {
        btn.addEventListener('click', function() {
            const row = this.closest('tr');
            const rsbsa = row.cells[0].textContent;
            const name = row.cells[1].textContent;
            if (confirm('Are you sure you want to delete ' + name + '?')) {
                alert('Delete: ' + rsbsa);
                // In real app, send delete request to server
            }
        });
    });

    // Search functionality
    document.querySelector('.search-box input').addEventListener('input', function(e) {
        const searchTerm = e.target.value.toLowerCase();
        const rows = document.querySelectorAll('.data-table tbody tr');
        
        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            if (text.includes(searchTerm)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });
</script>