// ==========================================
//  DARK MODE - CTH Academy
//  File: js/darkmode.js
//  Works on ALL pages automatically
// ==========================================

(function () {
    const STORAGE_KEY = 'cth_theme';

    // Apply theme immediately (before page renders) to avoid flash
    function applyTheme(theme) {
        document.documentElement.setAttribute('data-theme', theme);
        localStorage.setItem(STORAGE_KEY, theme);
        updateToggleButton(theme);
    }

    // Get saved theme or default to light
    function getSavedTheme() {
        return localStorage.getItem(STORAGE_KEY) || 'light';
    }

    // Update button icon and text
    function updateToggleButton(theme) {
        const btn = document.getElementById('darkModeToggle');
        if (!btn) return;

        if (theme === 'dark') {
            btn.innerHTML = '<span class="toggle-icon">☀️</span><span class="toggle-text">Light</span>';
            btn.title = 'Switch to Light Mode';
        } else {
            btn.innerHTML = '<span class="toggle-icon">🌙</span><span class="toggle-text">Dark</span>';
            btn.title = 'Switch to Dark Mode';
        }
    }

    // Toggle between light and dark
    function toggleTheme() {
        const current = document.documentElement.getAttribute('data-theme') || 'light';
        const next = current === 'dark' ? 'light' : 'dark';
        applyTheme(next);
    }

    // Inject toggle button into navbar
    function injectToggleButton() {
        const nav = document.querySelector('nav ul');
        if (!nav) return;

        // Don't add if already exists
        if (document.getElementById('darkModeToggle')) return;

        const li = document.createElement('li');
        li.innerHTML = `
            <button 
                class="dark-mode-toggle" 
                id="darkModeToggle" 
                onclick="darkModeToggle()"
                title="Toggle Dark Mode">
                <span class="toggle-icon">🌙</span>
                <span class="toggle-text">Dark</span>
            </button>
        `;
        nav.appendChild(li);

        // Update button to match current theme
        updateToggleButton(getSavedTheme());
    }

    // Global function for onclick
    window.darkModeToggle = toggleTheme;

    // Apply saved theme immediately (prevents flash)
    applyTheme(getSavedTheme());

    // Inject button when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', injectToggleButton);
    } else {
        injectToggleButton();
    }
})();