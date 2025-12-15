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
            $code = $_FILES['ccd_file']['error'] ?? UPLOAD_ERR_NO_FILE;
            $error = $this->uploadErrorMessage((int) $code);
            $view = new View('home', ['error' => $error]);
            $view->render();
            return;
        }

        $file = $_FILES['ccd_file'];
        $maxSize = 10 * 1024 * 1024; // 10 MB
        $allowedExt = ['xml', 'ccd', 'ccda'];
        $name = $file['name'] ?? '';
        $size = $file['size'] ?? 0;
        $tmpPath = $file['tmp_name'] ?? '';

        // Size check
        if ($size > $maxSize || $size <= 0) {
            $view = new View('home', ['error' => 'File is too large. Maximum allowed is 10 MB.']);
            $view->render();
            return;
        }

        // Extension check
        $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
        if (!in_array($ext, $allowedExt, true)) {
            $view = new View('home', ['error' => 'Invalid file type. Please upload a .xml, .ccd, or .ccda file.']);
            $view->render();
            return;
        }

        if (!is_uploaded_file($tmpPath)) {
            $view = new View('home', ['error' => 'Failed to read uploaded file.']);
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

    private function uploadErrorMessage(int $code): string
    {
        return match ($code) {
            UPLOAD_ERR_OK => 'Upload succeeded.',
            UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE => 'File is too large. Maximum allowed is 10 MB.',
            UPLOAD_ERR_PARTIAL => 'The file was only partially uploaded. Please try again.',
            UPLOAD_ERR_NO_FILE => 'No file was uploaded. Please choose a file and try again.',
            UPLOAD_ERR_NO_TMP_DIR => 'Server error: missing a temporary upload directory.',
            UPLOAD_ERR_CANT_WRITE => 'Server error: failed to write the uploaded file to disk.',
            UPLOAD_ERR_EXTENSION => 'Server error: a PHP extension stopped the upload.',
            default => 'Upload failed due to an unknown error.',
        };
    }
}
