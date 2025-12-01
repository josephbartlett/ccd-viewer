<?php
// Entry point: display home page with upload form.
// Using a simple MVC approach: route to HomeController.

require_once __DIR__ . '/../vendor/autoload.php';

use App\Controllers\HomeController;

$controller = new HomeController();
$controller->index();