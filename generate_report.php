<?php
session_start();

if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

include 'database.php';

$type = $_GET['type'] ?? '';
$allowed = ['insurance', 'program', 'disaster', 'farmers'];

if (!in_array($type, $allowed)) {
    header("Location: reports.php");
    exit();
}

$generated_date = date('F j, Y g:i A');
$generated_by   = $_SESSION['email'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Generate Report</title>
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
<style>
  body { font-family: Arial, sans-serif; background: #f3f4f6; margin: 0; padding: 20px; }
  .toolbar { display: flex; gap: 10px; margin-bottom: 20px; }
  .btn { display: inline-flex; align-items: center; gap: 5px; padding: 8px 16px; border: none; border-radius: 8px; cursor: pointer; font-size: 13px; font-weight: 600; text-decoration: none; }
  .btn-back  { background: #e5e7eb; color: #374151; }
  .btn-print { background: #10b981; color: #fff; }

  .report { background: #fff; border-radius: 12px; padding: 30px; max-width: 900px; margin: 0 auto; box-shadow: 0 2px 10px rgba(0,0,0,0.08); }
  h2 { margin: 0 0 4px; font-size: 20px; }
  .meta { color: #6b7280; font-size: 13px; margin-bottom: 24px; }

  .stats { display: flex; flex-wrap: wrap; gap: 12px; margin-bottom: 28px; }
  .stat { background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 10px; padding: 14px 20px; min-width: 140px; }
  .stat-label { font-size: 12px; color: #6b7280; margin-bottom: 4px; }
  .stat-value { font-size: 20px; font-weight: 700; }

  table { width: 100%; border-collapse: collapse; font-size: 13px; }
  th { background: #10b981; color: #fff; padding: 10px 12px; text-align: left; }
  td { padding: 9px 12px; border-bottom: 1px solid #f3f4f6; }
  tr:nth-child(even) td { background: #f9fafb; }

  @media print {
    body { background: #fff; }
    .toolbar { display: none; }
    .report { box-shadow: none; }
  }
</style>
</head>
<body>

<div class="toolbar">
  <a href="reports.php" class="btn btn-back">
    <span class="material-icons" style="font-size:18px">arrow_back</span> Back
  </a>
  <button class="btn btn-print" onclick="window.print()">
    <span class="material-icons" style="font-size:18px">print</span> Print
  </button>
</div>

<div class="report">

<?php if ($type === 'insurance'): ?>

  <?php
  $total    = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS t FROM insurance_policies"))['t'] ?? 0;
  $active   = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS t FROM insurance_policies WHERE status='Active'"))['t'] ?? 0;
  $payment  = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(payment) AS t FROM insurance_policies"))['t'] ?? 0;
  $expiring = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS t FROM insurance_policies WHERE valid_until <= DATE_ADD(CURDATE(), INTERVAL 30 DAY) AND status='Active'"))['t'] ?? 0;
  $rows     = mysqli_query($conn, "SELECT * FROM insurance_policies ORDER BY valid_until ASC");
  ?>

  <h2>Insurance Coverage Report</h2>
  <div class="meta">Generated: <?= $generated_date ?> &nbsp;|&nbsp; By: <?= htmlspecialchars($generated_by) ?></div>

  <div class="stats">
    <div class="stat"><div class="stat-label">Total</div><div class="stat-value"><?= number_format($total) ?></div></div>
    <div class="stat"><div class="stat-label">Active</div><div class="stat-value"><?= number_format($active) ?></div></div>
    <div class="stat"><div class="stat-label">Payments</div><div class="stat-value">₱<?= number_format($payment) ?></div></div>
    <div class="stat"><div class="stat-label">Expiring (30d)</div><div class="stat-value"><?= number_format($expiring) ?></div></div>
  </div>

  <table>
    <thead><tr><th>#</th><th>Farmer</th><th>Coverage</th><th>Payment</th><th>Status</th><th>Valid Until</th></tr></thead>
    <tbody>
      <?php $i = 1; while ($r = mysqli_fetch_assoc($rows)): ?>
      <tr>
        <td><?= $i++ ?></td>
        <td><?= htmlspecialchars($r['farmer_name']) ?></td>
        <td><?= htmlspecialchars($r['coverage']) ?></td>
        <td>₱<?= number_format($r['payment'], 2) ?></td>
        <td><?= htmlspecialchars($r['status']) ?></td>
        <td><?= htmlspecialchars($r['valid_until']) ?></td>
      </tr>
      <?php endwhile; ?>
    </tbody>
  </table>

<?php elseif ($type === 'program'): ?>

  <?php
  $beneficiaries = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(beneficiaries) AS t FROM distributions"))['t'] ?? 0;
  $quantity      = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(quantity) AS t FROM distributions"))['t'] ?? 0;
  $trainings     = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS t FROM trainings"))['t'] ?? 0;
  $rows          = mysqli_query($conn, "SELECT * FROM distributions ORDER BY date DESC");
  ?>

  <h2>Program Effectiveness Report</h2>
  <div class="meta">Generated: <?= $generated_date ?> &nbsp;|&nbsp; By: <?= htmlspecialchars($generated_by) ?></div>

  <div class="stats">
    <div class="stat"><div class="stat-label">Beneficiaries</div><div class="stat-value"><?= number_format($beneficiaries) ?></div></div>
    <div class="stat"><div class="stat-label">Items Distributed</div><div class="stat-value"><?= number_format($quantity) ?></div></div>
    <div class="stat"><div class="stat-label">Trainings</div><div class="stat-value"><?= number_format($trainings) ?></div></div>
  </div>

  <table>
    <thead><tr><th>#</th><th>Program</th><th>Item</th><th>Quantity</th><th>Beneficiaries</th><th>Date</th></tr></thead>
    <tbody>
      <?php $i = 1; while ($r = mysqli_fetch_assoc($rows)): ?>
      <tr>
        <td><?= $i++ ?></td>
        <td><?= htmlspecialchars($r['program']) ?></td>
        <td><?= htmlspecialchars($r['item_name']) ?></td>
        <td><?= number_format($r['quantity']) ?></td>
        <td><?= number_format($r['beneficiaries']) ?></td>
        <td><?= htmlspecialchars($r['date']) ?></td>
      </tr>
      <?php endwhile; ?>
    </tbody>
  </table>

<?php elseif ($type === 'disaster'): ?>

  <?php
  $claims   = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS t FROM incidents"))['t'] ?? 0;
  $approved = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS t FROM incidents WHERE status='Approved'"))['t'] ?? 0;
  $active   = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS t FROM incidents WHERE status='Assessment'"))['t'] ?? 0;
  $loss     = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(damage) AS t FROM incidents WHERE status='Approved'"))['t'] ?? 0;
  $rows     = mysqli_query($conn, "SELECT * FROM incidents ORDER BY date_reported DESC");
  ?>

  <h2>Disaster Impact Report</h2>
  <div class="meta">Generated: <?= $generated_date ?> &nbsp;|&nbsp; By: <?= htmlspecialchars($generated_by) ?></div>

  <div class="stats">
    <div class="stat"><div class="stat-label">Total Claims</div><div class="stat-value"><?= number_format($claims) ?></div></div>
    <div class="stat"><div class="stat-label">Approved</div><div class="stat-value"><?= number_format($approved) ?></div></div>
    <div class="stat"><div class="stat-label">Under Assessment</div><div class="stat-value"><?= number_format($active) ?></div></div>
    <div class="stat"><div class="stat-label">Total Loss</div><div class="stat-value">₱<?= number_format($loss) ?></div></div>
  </div>

  <table>
    <thead><tr><th>#</th><th>Type</th><th>Incident Type</th><th>Damage (₱)</th><th>Status</th><th>Date</th></tr></thead>
    <tbody>
      <?php $i = 1; while ($r = mysqli_fetch_assoc($rows)): ?>
      <tr>
        <td><?= $i++ ?></td>
        <td><?= htmlspecialchars($r['type']) ?></td>
        <td><?= htmlspecialchars($r['incident_code']) ?></td>
        <td><?= number_format($r['damage'], 2) ?></td>
        <td><?= htmlspecialchars($r['status']) ?></td>
        <td><?= htmlspecialchars($r['date_reported']) ?></td>
      </tr>
      <?php endwhile; ?>
    </tbody>
  </table>

<?php elseif ($type === 'farmers'): ?>

  <?php
  $total     = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS t FROM farmers"))['t'] ?? 0;
  $male      = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS t FROM farmers WHERE gender='Male'"))['t'] ?? 0;
  $female    = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS t FROM farmers WHERE gender='Female'"))['t'] ?? 0;
  $barangays = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(DISTINCT address) AS t FROM farmers"))['t'] ?? 0;
  $farm_size = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(farm_size) AS t FROM farmers"))['t'] ?? 0;
  $rows      = mysqli_query($conn, "SELECT * FROM farmers ORDER BY full_name ASC");
  ?>

  <h2>Farmer Demographics Report</h2>
  <div class="meta">Generated: <?= $generated_date ?> &nbsp;|&nbsp; By: <?= htmlspecialchars($generated_by) ?></div>

  <div class="stats">
    <div class="stat"><div class="stat-label">Total Farmers</div><div class="stat-value"><?= number_format($total) ?></div></div>
    <div class="stat"><div class="stat-label">Male</div><div class="stat-value"><?= number_format($male) ?></div></div>
    <div class="stat"><div class="stat-label">Female</div><div class="stat-value"><?= number_format($female) ?></div></div>
    <div class="stat"><div class="stat-label">Barangays</div><div class="stat-value"><?= number_format($barangays) ?></div></div>
    <div class="stat"><div class="stat-label">Total Farm Area</div><div class="stat-value"><?= number_format($farm_size, 2) ?> ha</div></div>
  </div>

  <table>
    <thead><tr><th>#</th><th>Name</th><th>Gender</th><th>Barangay</th><th>Crop Type</th><th>Farm Size (ha)</th></tr></thead>
    <tbody>
      <?php $i = 1; while ($r = mysqli_fetch_assoc($rows)): ?>
      <tr>
        <td><?= $i++ ?></td>
        <td><?= htmlspecialchars($r['full_name']) ?></td>
        <td><?= htmlspecialchars($r['gender']) ?></td>
        <td><?= htmlspecialchars($r['address']) ?></td>
        <td><?= htmlspecialchars($r['crop_type']) ?></td>
        <td><?= number_format($r['farm_size'], 2) ?></td>
      </tr>
      <?php endwhile; ?>
    </tbody>
  </table>

<?php endif; ?>

</div>
</body>
</html>