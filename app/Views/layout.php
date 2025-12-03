<?php
/**
 * Base layout for the CCD Viewer.
 *
 * This file defines the overall HTML document structure, including
 * including Bootstrap and FontAwesome, and yields a view section
 * where the individual templates can insert their content.
 */
?><!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CCD Viewer</title>
    <!-- Bootstrap CSS (local) -->
    <link href="/assets/vendor/bootstrap/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome icons (local) -->
    <link rel="stylesheet" href="/assets/vendor/fontawesome/css/all.min.css">
    <!-- Custom styles -->
    <link rel="stylesheet" href="/assets/css/styles.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
        <div class="container-fluid">
            <a class="navbar-brand" href="/index.php"><i class="fa-solid fa-file-medical"></i> CCD Viewer</a>
        </div>
    </nav>

    <div class="container-fluid">
        <?php
        // Render the view content passed by the controller
        if (isset($renderView) && is_callable($renderView)) {
            $renderView();
        }
        ?>
    </div>

    <!-- Bootstrap JS Bundle (local) -->
    <script src="/assets/vendor/bootstrap/bootstrap.bundle.min.js"></script>
    <!-- Custom JS -->
    <script src="/assets/js/app.js"></script>
</body>
</html>
