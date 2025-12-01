/* JavaScript for CCD Viewer */

document.addEventListener('DOMContentLoaded', () => {
    // Attach click handlers for XML navigation buttons
    const xmlContainer = document.getElementById('xml-container');
    if (xmlContainer) {
        document.querySelectorAll('[data-xml-id]').forEach(button => {
            button.addEventListener('click', () => {
                const targetId = button.getAttribute('data-xml-id');
                highlightXmlElement(targetId);
            });
        });
    }

    function highlightXmlElement(id) {
        if (!xmlContainer) return;
        // Remove existing highlights
        xmlContainer.querySelectorAll('.highlight').forEach(el => el.classList.remove('highlight'));
        // Find and scroll to the target
        const target = xmlContainer.querySelector('[data-id="' + id + '"]');
        if (target) {
            target.classList.add('highlight');
            target.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    }
});