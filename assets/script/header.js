function toggleUserDropdown(event) {
    event.stopPropagation(); // Prevent triggering parent clicks

    const menu = document.getElementById('userDropdownMenu');
    const arrow = document.getElementById('arrow');

    if (menu.style.display === 'block') {
        menu.style.display = 'none';
        arrow.style.transform = 'rotate(0deg)';
    } else {
        menu.style.display = 'block';
        arrow.style.transform = 'rotate(180deg)';
    }
}

// Close dropdown if clicked outside
window.addEventListener('click', function(event) {
    const menu = document.getElementById('userDropdownMenu');
    const arrow = document.getElementById('arrow');

    if (!event.target.closest('.user-menu')) {
        menu.style.display = 'none';
        arrow.style.transform = 'rotate(0deg)';
    }
});
