// Helper to toggle elements
function toggleElement(id) {
    const el = document.getElementById(id);
    if (el) {
        el.classList.toggle('hidden');
    }
}

// Helper to toggle elements
function toggleElement(id) {
    const el = document.getElementById(id);
    if (el) {
        el.classList.toggle('hidden');
    }
}

// Global Loading Helper
function setLoading(el) {
    if (el) {
        el.classList.add('btn-loading');
        el.style.pointerEvents = 'none';
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

    // Global Form Submit Handler for Loading Buttons
    document.addEventListener('submit', (e) => {
        const submitBtn = e.target.querySelector('button[type="submit"]');
        if (submitBtn) {
            // Check if the form is actually submitting (not prevented)
            setTimeout(() => {
                if (!e.defaultPrevented) {
                    setLoading(submitBtn);
                }
            }, 10);
        }
    });

    // Handle Manual Loading Triggers (links or buttons with data-loading)
    document.addEventListener('click', (e) => {
        const loadingTrigger = e.target.closest('[data-loading]');
        if (loadingTrigger) {
            setLoading(loadingTrigger);
        }
    });
});