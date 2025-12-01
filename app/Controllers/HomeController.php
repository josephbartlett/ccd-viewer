<?php
namespace App\Controllers;

use App\Views\View;

/**
 * HomeController displays the upload form and processes file submissions.
 */
class HomeController
{
    /**
     * Render the home page.
     */
    public function index(): void
    {
        $view = new View('home');
        $view->render();
    }

    /**
     * Process the uploaded file and redirect to the viewer.
     */
    public function upload(): void
    {
        if (!isset($_FILES['ccd_file']) || $_FILES['ccd_file']['error'] !== UPLOAD_ERR_OK) {
            $error = 'Failed to upload file. Please try again.';
            $view = new View('home', ['error' => $error]);
            $view->render();
            return;
        }
        $uploadDir = __DIR__ . '/../../storage/uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        $originalName = basename($_FILES['ccd_file']['name']);
        $targetPath = $uploadDir . uniqid('ccd_', true) . '_' . $originalName;
        if (!move_uploaded_file($_FILES['ccd_file']['tmp_name'], $targetPath)) {
            $error = 'Could not save uploaded file.';
            $view = new View('home', ['error' => $error]);
            $view->render();
            return;
        }
        // Redirect to viewer with file param
        $fileName = basename($targetPath);
        header('Location: viewer.php?file=' . urlencode($fileName));
        exit;
    }
}