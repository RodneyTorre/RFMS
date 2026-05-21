<?php
session_start();

if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

include 'database.php';

$month = date('F Y');
$filename = 'Monthly_Report_' . date('Y-m') . '.xls';

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=\"$filename\"");
header("Cache-Control: no-cache, no-store, must-revalidate");

$tab = "\t";
$nl  = "\n";

echo "MONTHLY REPORT - $month" . $nl . $nl;

// ── INSURANCE ──────────────────────────────────────────────
echo "=== INSURANCE COVERAGE ===" . $nl;
echo "Farmer Name{$tab}Payment{$tab}Status{$tab}Valid Until" . $nl;

$res = mysqli_query($conn, "SELECT * FROM insurance_policies ORDER BY valid_until ASC");
while ($r = mysqli_fetch_assoc($res)) {
    echo $r['farmer_name'] . $tab
       . $r['payment']     . $tab
       . $r['status']      . $tab
       . $r['valid_until'] . $nl;
}

echo $nl;

// ── PROGRAM ────────────────────────────────────────────────
echo "=== PROGRAM EFFECTIVENESS ===" . $nl;
echo "Program{$tab}Quantity{$tab}Beneficiaries" . $nl;

$res = mysqli_query($conn, "SELECT * FROM distributions ORDER BY program ASC");
while ($r = mysqli_fetch_assoc($res)) {
    echo $r['program']       . $tab
       . $r['quantity']      . $tab
       . $r['beneficiaries'] . $nl;
}

echo $nl;

// ── DISASTER ───────────────────────────────────────────────
echo "=== DISASTER IMPACT ===" . $nl;
echo "Incident Code{$tab}Type{$tab}Location{$tab}Date Reported{$tab}Damage{$tab}Status" . $nl;

$res = mysqli_query($conn, "SELECT * FROM incidents ORDER BY date_reported DESC");
while ($r = mysqli_fetch_assoc($res)) {
    echo $r['incident_code'] . $tab
       . $r['type']          . $tab
       . $r['location']      . $tab
       . $r['date_reported'] . $tab
       . $r['damage']        . $tab
       . $r['status']        . $nl;
}

echo $nl;

// ── FARMERS ────────────────────────────────────────────────
echo "=== FARMER DEMOGRAPHICS ===" . $nl;
echo "Full Name{$tab}Gender{$tab}Address{$tab}Farm Size (ha){$tab}Crop Type" . $nl;

$res = mysqli_query($conn, "SELECT * FROM farmers ORDER BY full_name ASC");
while ($r = mysqli_fetch_assoc($res)) {
    echo $r['full_name']  . $tab
       . $r['gender']     . $tab
       . $r['address']    . $tab
       . $r['farm_size']  . $tab
       . $r['crop_type']  . $nl;
}

exit();
?>