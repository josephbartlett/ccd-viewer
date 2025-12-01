<?php
namespace App\Controllers;

use App\Models\CCDParser;
use App\Views\View;

/**
 * ViewerController loads the uploaded CCD file and displays it.
 */
class ViewerController
{
    /**
     * Show the viewer page for a given uploaded file.
     *
     * @param string $fileName Name of the file located in storage/uploads.
     */
    public function show(string $fileName): void
    {
        $filePath = __DIR__ . '/../../storage/uploads/' . $fileName;
        if (!file_exists($filePath)) {
            $view = new View('home', ['error' => 'File not found. Please upload a valid CCD file.']);
            $view->render();
            return;
        }
        $parser = new CCDParser();
        try {
            $ccd = $parser->parse($filePath);
        } catch (\Throwable $e) {
            $view = new View('home', ['error' => 'Failed to parse CCD file: ' . $e->getMessage()]);
            $view->render();
            return;
        }
        $view = new View('viewer', [
            'ccd' => $ccd,
            'fileName' => $fileName,
        ]);
        $view->render();
    }
}