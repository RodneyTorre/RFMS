<?php
// feedback.php - Feedback & Support Page
session_start();

$page_title = "Feedback & Support";
$current_user = ['name' => 'Admin User', 'role' => 'System Administrator', 'initials' => 'AU'];

$stats = [
    ['icon' => 'chat', 'value' => '156', 'label' => 'Total Feedback', 'color' => 'blue'],
    ['icon' => 'warning', 'value' => '23', 'label' => 'Active Complaints', 'color' => 'orange'],
    ['icon' => 'headset_mic', 'value' => '12', 'label' => 'Open Tickets', 'color' => 'green'],
    ['icon' => 'star', 'value' => '4.6/5', 'label' => 'Avg Satisfaction', 'color' => 'purple']
];

$feedback = [
    [
        'name' => 'Juan Dela Cruz',
        'initials' => 'JD',
        'role' => 'Farmer • Brgy. San Jose',
        'date' => 'Jan 30, 2026',
        'rating' => 5,
        'message' => 'The seed distribution program was excellent. Quality seeds and timely delivery. Very satisfied with the support provided.',
        'tags' => ['Positive Feedback']
    ],
    [
        'name' => 'Maria Santos',
        'initials' => 'MS',
        'role' => 'Fisherfolk • Laguna Lake',
        'date' => 'Jan 28, 2026',
        'rating' => 3,
        'message' => 'Insurance claim processing is taking too long. Been waiting for 3 weeks. Need faster response on this.',
        'tags' => ['Complaint', 'In Progress']
    ]
];

include 'header.php';
?>

<link rel="stylesheet" href="assets/css/feedback.css">

<div class="page-header">
    <div class="page-title-section">
        <h1>Feedback & Support</h1>
        <p class="page-subtitle">Farmer feedback, complaints, and support tickets</p>
    </div>
    <div class="page-actions">
        <button class="btn btn-secondary">
            <span class="material-icons">filter_list</span>
            Filter
        </button>
        <button class="btn btn-primary">
            <span class="material-icons">add</span>
            New Ticket
        </button>
    </div>
</div>

<div class="stats-grid">
    <?php foreach ($stats as $stat): ?>
    <div class="stat-card">
        <div class="stat-icon <?php echo $stat['color']; ?>">
            <span class="material-icons"><?php echo $stat['icon']; ?></span>
        </div>
        <div>
            <div class="stat-value"><?php echo $stat['value']; ?></div>
            <div class="stat-label"><?php echo $stat['label']; ?></div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<div class="tabs-container">
    <button class="tab-btn active">Farmer Feedback</button>
    <button class="tab-btn">Complaints & Requests</button>
    <button class="tab-btn">Support Tickets</button>
</div>

<div class="feedback-grid">
    <?php foreach ($feedback as $fb): ?>
    <div class="feedback-card">
        <div class="feedback-header">
            <div class="user-info">
                <div class="user-avatar"><?php echo $fb['initials']; ?></div>
                <div class="user-details">
                    <h4><?php echo $fb['name']; ?></h4>
                    <span><?php echo $fb['role']; ?></span>
                </div>
            </div>
            <div class="feedback-date"><?php echo $fb['date']; ?></div>
        </div>
        <div class="rating">
            <?php for ($i = 1; $i <= 5; $i++): ?>
                <span class="material-icons <?php echo $i > $fb['rating'] ? 'empty' : ''; ?>">star</span>
            <?php endfor; ?>
        </div>
        <p class="feedback-message"><?php echo $fb['message']; ?></p>
        <div class="feedback-tags">
            <?php foreach ($fb['tags'] as $tag): ?>
                <span class="tag <?php echo strtolower(str_replace(' ', '-', $tag)); ?>"><?php echo $tag; ?></span>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endforeach; ?>
</div>

