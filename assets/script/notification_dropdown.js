function toggleNotif(e) {
    e.stopPropagation();
    const dd = document.getElementById('notifDropdown');

    dd.classList.toggle('open');

    if (dd.classList.contains('open')) {
        loadNotifications();
    }
}

function loadNotifications() {
    fetch('notifications/fetch/fetch_notifications.php')
        .then(res => res.text())
        .then(data => {
            document.getElementById('notifList').innerHTML = data;
        });
}

function markAsRead(id) {
    fetch('notifications/actions/mark_notification.php?id=' + id)
        .then(() => loadNotifications());
}

// LIVE AUTO UPDATE (Facebook style)
setInterval(() => {
    fetch('notifications/fetch/get_unread_count.php')
        .then(res => res.json())
        .then(data => {
            const dot = document.querySelector('.notif-dot');

            if (data.count > 0 && !dot) {
                const btn = document.querySelector('.icon-btn');
                const span = document.createElement('span');
                span.className = 'notif-dot';
                btn.appendChild(span);
            }

            if (data.count == 0 && dot) {
                dot.remove();
            }
        });
}, 5000);