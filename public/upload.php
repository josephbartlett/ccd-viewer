<?php
// Handle file upload via HomeController
require_once __DIR__ . '/../vendor/autoload.php';

use App\Controllers\HomeController;

$controller = new HomeController();
$controller->upload();