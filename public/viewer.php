<?php
// Display the parsed CCD file using ViewerController
require_once __DIR__ . '/../vendor/autoload.php';

use App\Controllers\ViewerController;

// Get the file parameter from the query string
$file = isset($_GET['file']) ? basename($_GET['file']) : '';

$controller = new ViewerController();
$controller->show($file);