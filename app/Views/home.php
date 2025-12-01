<?php
/** @var string|null $error */
?>
<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
        <div class="card shadow-sm">
            <div class="card-header bg-light">
                <h4 class="mb-0"><i class="fa-solid fa-upload me-2"></i>Upload CCD/CCDA File</h4>
            </div>
            <div class="card-body">
                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger">
                        <i class="fa-solid fa-circle-exclamation me-2"></i><?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>
                <form action="upload.php" method="post" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="ccd_file" class="form-label">Select CCD/CCDA XML File</label>
                        <input class="form-control" type="file" id="ccd_file" name="ccd_file" accept=".xml,.ccd,.ccda" required>
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="fa-solid fa-file-arrow-up me-1"></i> Upload &amp; View
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>