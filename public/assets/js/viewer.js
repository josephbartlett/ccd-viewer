/*
 * CCD Viewer client-side script
 * Handles dynamic rendering of sections, entries, and XML snippets.
 */

document.addEventListener('DOMContentLoaded', () => {
    const data = window.CCD_VIEWER_DATA;
    const sectionList = document.getElementById('section-list');
    const sectionContent = document.getElementById('section-content');
    const xmlContainer = document.getElementById('xml-container');
    const defaultXmlMessage = 'Select an entry to view its XML snippet.';

    if (!data) return;

    // Set initial hint text
    xmlContainer.textContent = defaultXmlMessage;

    // Render the first section by default
    if (data.sections && data.sections.length > 0) {
        renderSection(0);
        // Highlight first item
        const firstItem = sectionList.querySelector('[data-index="0"]');
        if (firstItem) {
            firstItem.classList.add('active');
        }
    }

    // Add click handlers for section list
    sectionList.querySelectorAll('.section-item').forEach(item => {
        item.addEventListener('click', () => {
            // Remove active class from all items
            sectionList.querySelectorAll('.section-item').forEach(el => el.classList.remove('active'));
            item.classList.add('active');
            const index = parseInt(item.getAttribute('data-index'));
            renderSection(index);
        });
    });

    /**
     * Render the selected section's content
     * @param {number} index
     */
    function renderSection(index) {
        const section = data.sections[index];
        if (!section) return;
        // Reset XML container with a helpful hint
        xmlContainer.textContent = defaultXmlMessage;

        // Build card with nav tabs for structured and narrative
        let html = '';
        html += '<div class="card">';
        html += '<div class="card-header d-flex justify-content-between align-items-center">';
        html += '<div><i class="fa-solid fa-layer-group me-2"></i>' + escapeHtml(section.title) + '</div>';
        if (section.code) {
            html += '<span class="badge bg-info text-dark">' + escapeHtml(section.code) + '</span>';
        }
        html += '</div>';
        html += '<div class="card-body">';
        html += '<ul class="nav nav-tabs" id="sectionTab" role="tablist">';
        html += '<li class="nav-item" role="presentation">';
        html += '<button class="nav-link active" id="structured-tab" data-bs-toggle="tab" data-bs-target="#structured" type="button" role="tab" aria-controls="structured" aria-selected="true">Structured</button>';
        html += '</li>';
        html += '<li class="nav-item" role="presentation">';
        html += '<button class="nav-link" id="narrative-tab" data-bs-toggle="tab" data-bs-target="#narrative" type="button" role="tab" aria-controls="narrative" aria-selected="false">Narrative</button>';
        html += '</li>';
        html += '</ul>';
        html += '<div class="tab-content pt-3" id="sectionTabContent">';
        // Structured tab
        html += '<div class="tab-pane fade show active" id="structured" role="tabpanel" aria-labelledby="structured-tab">';
        if (section.entries && section.entries.length > 0) {
            html += '<div class="table-responsive table-fixed">';
            html += '<table class="table table-sm table-hover">';
            html += '<thead class="table-light">';
            html += '<tr><th>#</th><th>Description</th><th>Code</th><th>System</th><th class="text-end">XML</th></tr>';
            html += '</thead><tbody>';
            section.entries.forEach((entry, idx) => {
                html += '<tr>';
                html += '<td>' + (idx + 1) + '</td>';
                html += '<td>' + escapeHtml(entry.label) + '</td>';
                html += '<td>' + escapeHtml(entry.code || '') + '</td>';
                html += '<td>' + escapeHtml(entry.codeSystem || '') + '</td>';
                html += '<td class="text-end">';
                html += '<button type="button" class="btn btn-outline-secondary btn-sm btn-xml" data-entry-index="' + idx + '">';
                html += '<i class="fa-solid fa-magnifying-glass"></i>';
                html += '</button>';
                html += '</td>';
                html += '</tr>';
            });
            html += '</tbody></table></div>';
        } else {
            html += '<p class="text-muted">No entries found in this section.</p>';
        }
        html += '</div>';
        // Narrative tab
        html += '<div class="tab-pane fade" id="narrative" role="tabpanel" aria-labelledby="narrative-tab">';
        if (section.narrative) {
            html += '<div class="narrative-content">' + section.narrative + '</div>';
        } else {
            html += '<p class="text-muted">No narrative text available.</p>';
        }
        html += '</div>';
        html += '</div>'; // end tab content
        html += '</div>'; // card-body
        html += '</div>'; // card

        sectionContent.innerHTML = html;
        // Attach click handlers to newly inserted XML buttons
        sectionContent.querySelectorAll('.btn-xml').forEach(btn => {
            btn.addEventListener('click', () => {
                const entryIdx = parseInt(btn.getAttribute('data-entry-index'));
                const entry = section.entries[entryIdx];
                if (entry && entry.xmlSnippet) {
                    xmlContainer.textContent = entry.xmlSnippet;
                } else {
                    xmlContainer.textContent = 'XML not available.';
                }
            });
        });
        // Auto-select the first entry to show users how the XML pane works
        if (section.entries && section.entries.length > 0) {
            const firstEntry = section.entries[0];
            xmlContainer.textContent = firstEntry.xmlSnippet || defaultXmlMessage;
        }
    }

    /**
     * Escape HTML special characters to prevent XSS.
     * @param {string} text
     * @returns {string}
     */
    function escapeHtml(text) {
        return text ? text.replace(/[&<>"']/g, function (m) {
            switch (m) {
                case '&': return '&amp;';
                case '<': return '&lt;';
                case '>': return '&gt;';
                case '"': return '&quot;';
                case '\'': return '&#39;';
                default: return m;
            }
        }) : '';
    }
});
