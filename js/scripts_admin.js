// scripts_admin.js
document.addEventListener("DOMContentLoaded", () => {
    const toggle = document.querySelector('.menu-toggle');
    const sidebar = document.querySelector('.sidebar');
    
    if (toggle && sidebar) {
        toggle.addEventListener('click', () => {
            sidebar.classList.toggle('active');
        });
    }
});
