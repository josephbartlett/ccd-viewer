/*
 * CCD Viewer client-side script
 * Adds copy/download for XML, auto-loads first entry, and remembers last section per file.
 * (Upload page is unchanged to avoid regressions.)
 */

document.addEventListener('DOMContentLoaded', () => {
    const data = window.CCD_VIEWER_DATA;
    const fileName = window.CCD_VIEWER_FILENAME || 'current-file';
    const sectionList = document.getElementById('section-list');
    const sectionContent = document.getElementById('section-content');
    const xmlContainer = document.getElementById('xml-container');
    const xmlStatus = document.getElementById('xml-status');
    const copyBtn = document.getElementById('xml-copy-btn');
    const downloadBtn = document.getElementById('xml-download-btn');
    const defaultXmlMessage = 'Select an entry to view its XML snippet.';
    let currentXmlSnippet = '';

    if (!data || !sectionList || !sectionContent || !xmlContainer) return;

    // Remember last file for resume link (upload page can use localStorage key)
    try {
        localStorage.setItem('ccd_viewer:lastFile', fileName);
    } catch (e) {
        // ignore storage errors
    }

    xmlContainer.textContent = defaultXmlMessage;
    updateXmlActionsState();

    // No sections edge case
    if (!data.sections || data.sections.length === 0) {
        sectionContent.innerHTML = '<div class="alert alert-warning">No sections found in this document.</div>';
        setXmlStatus('No sections available.');
        return;
    }

    const lastSectionKey = `ccd_viewer:lastSection:${fileName}`;
    const initialIndex = getStoredSectionIndex(lastSectionKey, data.sections.length);
    renderSection(initialIndex);
    highlightSectionItem(initialIndex);

    // Section click handlers
    sectionList.querySelectorAll('.section-item').forEach(item => {
        item.addEventListener('click', () => {
            sectionList.querySelectorAll('.section-item').forEach(el => el.classList.remove('active'));
            item.classList.add('active');
            const index = parseInt(item.getAttribute('data-index'), 10);
            storeSectionIndex(lastSectionKey, index);
            renderSection(index);
        });
    });

    // Copy XML
    if (copyBtn) {
        copyBtn.addEventListener('click', async () => {
            if (!currentXmlSnippet) {
                setXmlStatus('No XML to copy.');
                return;
            }
            try {
                await navigator.clipboard.writeText(currentXmlSnippet);
                setXmlStatus('Copied XML to clipboard.');
            } catch (e) {
                const area = document.createElement('textarea');
                area.value = currentXmlSnippet;
                document.body.appendChild(area);
                area.select();
                document.execCommand('copy');
                document.body.removeChild(area);
                setXmlStatus('Copied XML to clipboard.');
            }
        });
    }

    // Download XML
    if (downloadBtn) {
        downloadBtn.addEventListener('click', () => {
            if (!currentXmlSnippet) {
                setXmlStatus('No XML to download.');
                return;
            }
            const blob = new Blob([currentXmlSnippet], { type: 'application/xml' });
            const url = URL.createObjectURL(blob);
            const link = document.createElement('a');
            const safeName = (fileName || 'ccd').replace(/[^a-zA-Z0-9_-]/g, '_');
            link.href = url;
            link.download = `${safeName}_snippet.xml`;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            URL.revokeObjectURL(url);
            setXmlStatus('Downloaded XML snippet.');
        });
    }

    function renderSection(index) {
        const section = data.sections[index];
        if (!section) return;
        currentXmlSnippet = '';
        xmlContainer.textContent = defaultXmlMessage;
        updateXmlActionsState();
        setXmlStatus('');

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

        html += '</div>'; // tab content
        html += '</div>'; // card-body
        html += '</div>'; // card

        sectionContent.innerHTML = html;

        // XML button handlers
        sectionContent.querySelectorAll('.btn-xml').forEach(btn => {
            btn.addEventListener('click', () => {
                const entryIdx = parseInt(btn.getAttribute('data-entry-index'), 10);
                const entry = section.entries[entryIdx];
                if (entry && entry.xmlSnippet) {
                    setXmlSnippet(entry.xmlSnippet, entry.label);
                } else {
                    setXmlSnippet('', '');
                    xmlContainer.textContent = 'XML not available.';
                }
            });
        });

        // Auto-show first entry
        if (section.entries && section.entries.length > 0) {
            const firstEntry = section.entries[0];
            setXmlSnippet(firstEntry.xmlSnippet, firstEntry.label);
        }
    }

    function setXmlSnippet(snippet, label) {
        currentXmlSnippet = snippet || '';
        xmlContainer.textContent = currentXmlSnippet || defaultXmlMessage;
        updateXmlActionsState();
        if (label) {
            setXmlStatus(`Showing XML for "${label}".`);
        } else {
            setXmlStatus('');
        }
    }

    function updateXmlActionsState() {
        const disabled = !currentXmlSnippet;
        if (copyBtn) copyBtn.disabled = disabled;
        if (downloadBtn) downloadBtn.disabled = disabled;
    }

    function setXmlStatus(message) {
        if (xmlStatus) {
            xmlStatus.textContent = message || '';
        }
    }

    function highlightSectionItem(index) {
        const item = sectionList.querySelector(`[data-index="${index}"]`);
        if (!item) return;
        sectionList.querySelectorAll('.section-item').forEach(el => el.classList.remove('active'));
        item.classList.add('active');
    }

    function storeSectionIndex(key, index) {
        try {
            localStorage.setItem(key, String(index));
        } catch (e) {
            // ignore storage errors
        }
    }

    function getStoredSectionIndex(key, max) {
        try {
            const value = parseInt(localStorage.getItem(key), 10);
            if (!isNaN(value) && value >= 0 && value < max) {
                return value;
            }
        } catch (e) {
            // ignore storage errors
        }
        return 0;
    }

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
