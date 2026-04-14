<link rel="stylesheet" href="assets/css/registry.css">
<!-- FARMER MODAL -->
<div id="farmerModal" class="modal-overlay">

    <div class="modal-container">

        <div class="modal-header">
            <h2>Add New Farmer</h2>
            <span class="close-btn" onclick="closeFarmerModal()">×</span>
        </div>

        <form method="POST" action="" class="modal-body">

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

            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Save</button>
                <button type="button" class="btn btn-secondary" onclick="closeFarmerModal()">Cancel</button>
            </div>

        </form>

    </div>
</div>