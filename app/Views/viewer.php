<?php
/** @var array $ccd Structured CCD data from the parser */
/** @var string $fileName Name of the uploaded file */
?>
<div class="row">
    <div class="col-md-3 col-xl-2">
        <div class="card mb-3">
            <div class="card-header">
                <i class="fa-solid fa-info-circle me-1"></i>Document Metadata
            </div>
            <div class="card-body small">
                <p><strong>Title:</strong> <?= htmlspecialchars($ccd['metadata']['title'] ?? '') ?></p>
                <?php if (!empty($ccd['metadata']['effectiveTime'])): ?>
                    <p><strong>Effective:</strong> <?= htmlspecialchars($ccd['metadata']['effectiveTime']) ?></p>
                <?php endif; ?>
                <?php if (!empty($ccd['metadata']['patient']['name'])): ?>
                    <p><strong>Patient:</strong> <?= htmlspecialchars($ccd['metadata']['patient']['name']) ?></p>
                <?php endif; ?>
                <?php if (!empty($ccd['metadata']['patient']['dob'])): ?>
                    <p><strong>DOB:</strong> <?= htmlspecialchars($ccd['metadata']['patient']['dob']) ?></p>
                <?php endif; ?>
                <?php if (!empty($ccd['metadata']['patient']['gender'])): ?>
                    <p><strong>Gender:</strong> <?= htmlspecialchars($ccd['metadata']['patient']['gender']) ?></p>
                <?php endif; ?>
                <p><strong>Filename:</strong> <?= htmlspecialchars($fileName) ?></p>
            </div>
        </div>
        <div class="card mb-3">
            <div class="card-header">
                <i class="fa-solid fa-list-ul me-1"></i>Sections
            </div>
            <ul class="list-group list-group-flush" id="section-list">
                <?php foreach ($ccd['sections'] as $index => $section): ?>
                    <li class="list-group-item section-item d-flex justify-content-between align-items-center"
                        data-index="<?= $index ?>">
                        <span><?= htmlspecialchars($section['title']) ?></span>
                        <span class="badge bg-secondary"><?= count($section['entries']) ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
    <div class="col-md-5 col-xl-6">
        <div id="section-content" class="mb-3"></div>
    </div>
    <div class="col-md-4 col-xl-4">
        <div class="card mb-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div><i class="fa-solid fa-code me-1"></i>XML Snippet</div>
                <div class="btn-group btn-group-sm" role="group" aria-label="XML actions">
                    <button id="xml-copy-btn" type="button" class="btn btn-outline-secondary" title="Copy XML to clipboard">
                        <i class="fa-solid fa-copy"></i>
                    </button>
                    <button id="xml-download-btn" type="button" class="btn btn-outline-secondary" title="Download XML snippet">
                        <i class="fa-solid fa-download"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div id="xml-container" class="xml-container"></div>
                <div id="xml-status" class="form-text mt-2 text-muted"></div>
            </div>
        </div>
    </div>
</div>

<script>
    // Pass CCD data to the client-side script (attach to window for reuse)
    window.CCD_VIEWER_DATA = <?php echo json_encode($ccd, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
    window.CCD_VIEWER_FILENAME = <?php echo json_encode($fileName, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
</script>
<script src="/assets/js/viewer.js"></script>
