<?php
/**
 * farmer_details.php
 * Called via AJAX – returns the inner HTML for the farmer quick-view modal.
 *
 * Adjust table/column names to match your actual schema.
 */

include 'database.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    echo '<p style="color:#dc2626">Invalid farmer ID.</p>';
    exit;
}

/* ── Farmer row ── */
$sql  = "SELECT * FROM farmers WHERE id = ? LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $id);
$stmt->execute();
$farmer = $stmt->get_result()->fetch_assoc();

if (!$farmer) {
    echo '<p style="color:#dc2626">Farmer not found.</p>';
    exit;
}

/* ── Insurance enrollments ── */
$sql2  = "
    SELECT i.*, ip.name AS program_name
    FROM insurance_enrollments i
    LEFT JOIN insurance_programs ip ON ip.id = i.program_id
    WHERE i.farmer_id = ?
    ORDER BY i.id DESC
";
$stmt2 = $conn->prepare($sql2);
$stmt2->bind_param('i', $id);
$stmt2->execute();
$insurances = $stmt2->get_result()->fetch_all(MYSQLI_ASSOC);

$full_name    = htmlspecialchars(trim($farmer['first_name'] . ' ' . $farmer['last_name']));
$parts        = explode(' ', $full_name);
$initials     = strtoupper(substr($parts[0], 0, 1) . (isset($parts[1]) ? substr($parts[1], 0, 1) : ''));
$isActive     = strtolower($farmer['status'] ?? '') === 'active';
$statusColor  = $isActive ? '#15803d' : '#991b1b';
$statusBg     = $isActive ? '#dcfce7'  : '#fee2e2';
$statusLabel  = $isActive ? 'Registered / Active' : 'Inactive';

/* ── Helper: row ── */
function detailRow($icon, $label, $value) {
    if (!$value || trim($value) === '') return;
    echo '
    <div style="display:flex;gap:9px;align-items:flex-start;margin-bottom:10px;">
        <span class="material-icons" style="font-size:15px;color:#7a8f7a;margin-top:1px;flex-shrink:0;">' . $icon . '</span>
        <div>
            <div style="font-size:10.5px;font-weight:700;color:#7a8f7a;letter-spacing:.05em;text-transform:uppercase;">' . $label . '</div>
            <div style="font-size:13px;color:#0f1f0f;font-weight:500;">' . htmlspecialchars($value) . '</div>
        </div>
    </div>';
}
?>

<!-- Avatar + name header -->
<div style="display:flex;align-items:center;gap:13px;margin-bottom:18px;padding-bottom:16px;border-bottom:1px solid #edf2ed;">
    <div style="
        width:48px;height:48px;border-radius:50%;
        background:#16a34a;color:#fff;
        font-size:16px;font-weight:700;
        display:flex;align-items:center;justify-content:center;
        flex-shrink:0;
    "><?php echo $initials; ?></div>

    <div style="flex:1;min-width:0;">
        <div style="font-size:15px;font-weight:700;color:#0f1f0f;"><?php echo $full_name; ?></div>
        <span style="
            display:inline-flex;align-items:center;gap:3px;
            margin-top:4px;padding:2px 9px;border-radius:99px;
            font-size:10.5px;font-weight:700;
            background:<?php echo $statusBg; ?>;color:<?php echo $statusColor; ?>;
        ">
            <span class="material-icons" style="font-size:11px;"><?php echo $isActive ? 'check_circle' : 'cancel'; ?></span>
            <?php echo $statusLabel; ?>
        </span>
    </div>

    <a href="registry.php?id=<?php echo $id; ?>"
       style="display:inline-flex;align-items:center;gap:4px;padding:6px 11px;border-radius:8px;border:1px solid #e2e8e2;background:#fff;font-size:12px;font-weight:600;color:#15803d;text-decoration:none;flex-shrink:0;transition:background .15s;"
       onmouseover="this.style.background='#f0fdf4'" onmouseout="this.style.background='#fff'">
        <span class="material-icons" style="font-size:14px;">open_in_new</span> View
    </a>
</div>

<!-- Farmer details -->
<div style="margin-bottom:16px;">
    <?php
        detailRow('badge',       'RSBSA / Farmer ID', $farmer['rsbsa_number'] ?? ($farmer['farmer_id'] ?? null));
        detailRow('location_on', 'Address',            $farmer['address'] ?? null);
        detailRow('phone',       'Contact',            $farmer['contact_number'] ?? ($farmer['phone'] ?? null));
        detailRow('cake',        'Date of Birth',      $farmer['date_of_birth'] ?? ($farmer['dob'] ?? null));
        detailRow('wc',          'Sex',                $farmer['sex'] ?? ($farmer['gender'] ?? null));
        detailRow('grass',       'Farm Type',          $farmer['farm_type'] ?? null);
        detailRow('straighten',  'Farm Area (ha)',     isset($farmer['farm_area']) ? $farmer['farm_area'] . ' ha' : null);
    ?>
</div>

<!-- Insurance section -->
<div style="border-top:1px solid #edf2ed;padding-top:14px;">
    <div style="font-size:11px;font-weight:700;letter-spacing:.07em;text-transform:uppercase;color:#7a8f7a;margin-bottom:10px;display:flex;align-items:center;gap:6px;">
        <span class="material-icons" style="font-size:14px;">security</span> Insurance Enrollments
    </div>

    <?php if (empty($insurances)): ?>
        <div style="text-align:center;padding:14px 0;font-size:12.5px;color:#7a8f7a;">
            <span class="material-icons" style="font-size:22px;display:block;margin-bottom:5px;opacity:.3;">shield</span>
            Not enrolled in any insurance program
        </div>
    <?php else: ?>
        <?php foreach ($insurances as $ins):
            $iStatus = strtolower($ins['status'] ?? 'unknown');
            $colors = [
                'active'   => ['#dbeafe','#1e40af'],
                'approved' => ['#dcfce7','#15803d'],
                'pending'  => ['#fef3c7','#92400e'],
                'expired'  => ['#fee2e2','#991b1b'],
                'rejected' => ['#fee2e2','#991b1b'],
            ];
            [$bg, $fg] = $colors[$iStatus] ?? ['#f3f4f6','#374151'];
        ?>
        <div style="
            display:flex;align-items:center;justify-content:space-between;
            padding:9px 11px;border-radius:8px;border:1px solid #e2e8e2;
            margin-bottom:7px;background:#f8faf8;
        ">
            <div>
                <div style="font-size:12.5px;font-weight:600;color:#0f1f0f;">
                    <?php echo htmlspecialchars($ins['program_name'] ?? 'Insurance Program'); ?>
                </div>
                <?php if (!empty($ins['policy_number'])): ?>
                <div style="font-size:11px;color:#7a8f7a;margin-top:2px;">
                    Policy # <?php echo htmlspecialchars($ins['policy_number']); ?>
                </div>
                <?php endif; ?>
                <?php if (!empty($ins['coverage_start']) || !empty($ins['coverage_end'])): ?>
                <div style="font-size:11px;color:#7a8f7a;margin-top:2px;">
                    <?php
                        $start = $ins['coverage_start'] ?? '';
                        $end   = $ins['coverage_end']   ?? '';
                        if ($start && $end) echo htmlspecialchars($start) . ' – ' . htmlspecialchars($end);
                        elseif ($start)     echo 'From ' . htmlspecialchars($start);
                    ?>
                </div>
                <?php endif; ?>
            </div>
            <span style="
                display:inline-block;padding:3px 10px;border-radius:99px;
                font-size:10.5px;font-weight:700;
                background:<?php echo $bg; ?>;color:<?php echo $fg; ?>;
                text-transform:capitalize;
            "><?php echo htmlspecialchars(ucfirst($iStatus)); ?></span>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>