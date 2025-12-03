/* Upload page helpers: light validation, loading hint, resume link (do not disable file input). */

document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('upload-form');
    if (!form) return;

    const fileInput = document.getElementById('ccd_file');
    const errorBox = document.getElementById('upload-error');
    const submitBtn = document.getElementById('upload-submit');
    const loading = document.getElementById('upload-loading');
    const recentContainer = document.getElementById('recent-file-container');
    const allowedExtensions = ['xml', 'ccd', 'ccda'];
    const maxSizeBytes = 10 * 1024 * 1024; // 10 MB

    // Show last file shortcut if available
    try {
        const lastFile = localStorage.getItem('ccd_viewer:lastFile');
        if (lastFile && recentContainer) {
            recentContainer.innerHTML = `
                <div class="alert alert-info mb-0" role="alert">
                    <i class="fa-solid fa-clock-rotate-left me-2"></i>
                    Resume last file:
                    <a class="alert-link" href="viewer.php?file=${encodeURIComponent(lastFile)}">${lastFile}</a>
                </div>`;
            recentContainer.style.display = 'block';
        }
    } catch (e) {
        // ignore storage errors (tracking prevention)
    }

    form.addEventListener('submit', (event) => {
        clearError();
        const file = fileInput?.files?.[0];
        if (!file) {
            showError('Please choose a file to upload.');
            event.preventDefault();
            return;
        }
        const ext = (file.name.split('.').pop() || '').toLowerCase();
        if (!allowedExtensions.includes(ext)) {
            showError('Please upload a .xml, .ccd, or .ccda file.');
            event.preventDefault();
            return;
        }
        if (file.size > maxSizeBytes) {
            showError('File is too large. Maximum size is 10 MB.');
            event.preventDefault();
            return;
        }
        // Show loading; do NOT disable file input to avoid stripping the payload
        if (submitBtn) submitBtn.disabled = true;
        if (loading) loading.classList.remove('d-none');
    });

    function showError(message) {
        if (!errorBox) return;
        errorBox.textContent = message;
        errorBox.classList.remove('d-none');
    }

    function clearError() {
        if (!errorBox) return;
        errorBox.classList.add('d-none');
        errorBox.textContent = '';
    }
});
