// Helper to toggle elements
function toggleElement(id) {
    const el = document.getElementById(id);
    if (el) {
        el.classList.toggle('hidden');
    }
}

// Auto-hide flash messages after 3 seconds
document.addEventListener('DOMContentLoaded', () => {
    const flashMsg = document.getElementById('msg-flash');
    if (flashMsg) {
        setTimeout(() => {
            flashMsg.style.transition = 'opacity 0.5s ease';
            flashMsg.style.opacity = '0';
            setTimeout(() => flashMsg.remove(), 500);
        }, 3000);
    }
});