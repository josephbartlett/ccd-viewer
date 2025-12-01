<?php
namespace App\Views;

/**
 * Simple view renderer for the CCD viewer MVC.
 *
 * It locates view templates in the app/Views directory and wraps them in a common layout.
 */
class View
{
    /** @var string The name of the view template (without .php) */
    private string $template;
    /** @var array Data passed to the view */
    private array $data;

    /**
     * Constructor.
     *
     * @param string $template The base name of the view to render.
     * @param array  $data     Associative array of variables to be extracted for the view.
     */
    public function __construct(string $template, array $data = [])
    {
        $this->template = $template;
        $this->data = $data;
    }

    /**
     * Render the view within the layout.
     *
     * @return void
     */
    public function render(): void
    {
        // Extract data variables into the local scope
        extract($this->data);
        $data = $this->data;

        // Define the path to the actual view file
        $viewFile = __DIR__ . '/' . $this->template . '.php';

        // Define a closure to capture the view output
        $renderView = function () use ($viewFile, $data) {
            extract($data);
            if (file_exists($viewFile)) {
                include $viewFile;
            } else {
                echo "View file not found: " . htmlspecialchars($viewFile);
            }
        };

        // Include the layout and pass the closure as a variable
        include __DIR__ . '/layout.php';
    }
}
