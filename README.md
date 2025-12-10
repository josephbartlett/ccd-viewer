# CCD Viewer

CCD Viewer is a lightweight web application that parses and displays structured data and human-readable narratives from CCD/CCDA XML documents. It uses a simple custom MVC architecture built with plain PHP (no frameworks) and Bootstrap for styling. FontAwesome icons provide visual cues throughout the interface.

## Features

- Upload a CCD/CCDA XML document via a web form.
- Parse and display document metadata (title, effective time, patient name, DOB, gender, filename).
- Navigate between sections of the document; each section shows the number of entries.
- View structured data in a responsive table with description, code, and code system.
- Toggle between the structured view and the narrative (human-readable) view for each section.
- Inspect the raw XML snippet for each entry by clicking a magnifying glass icon.
- Clean, responsive UI built with Bootstrap 5 and FontAwesome 6 icons (served locally).

See `ROADMAP.md` for planned milestones toward v2.0.0.
See `CHANGELOG.md` for release notes.

## Screenshot

![CCD Viewer interface](screenshot.png)

## Quick start (Docker)

```sh
docker build -t ccd-viewer .
docker run -p 8080:80 --name ccd-viewer ccd-viewer
```

Open http://localhost:8080 and upload a CCD/CCDA XML file. Uploaded files live at `/var/www/html/storage/uploads` inside the container. To persist uploads between runs:

```sh
docker run -p 8080:80 \
  -v $(pwd)/storage/uploads:/var/www/html/storage/uploads \
  --name ccd-viewer ccd-viewer
```

## Security & PHI

**Intended for test and de-identified data; not a production PHI workflow without hardening.**

## Usage tips

- Left column lists sections; click a section to load its details.
- In the Structured tab, click the magnifying-glass button to show that entry’s XML in the right-hand pane (the first entry auto-loads).
- Switch to the Narrative tab for the human-readable section text.
- Copy or download the XML snippet using the buttons in the XML pane.

## Sample data

- A small example file is included at `ccd_sample.xml`. Upload it to try the viewer quickly.
- The sample CCD uses fully fictional names and identifiers for demonstration.

## Directory structure

```
ccd_viewer/
  app/
    Controllers/  # HomeController and ViewerController
    Models/       # CCDParser
    Views/        # layout.php, home.php, viewer.php, View class
  public/
    assets/
      css/styles.css
      js/app.js
      js/viewer.js
    index.php     # Entry point (upload form)
    upload.php    # Handles file uploads
    viewer.php    # Displays the parsed document
  storage/
    uploads/      # Uploaded files (created at runtime)
  vendor/
    autoload.php  # Simple PSR-4 autoloader
  Dockerfile      # Container definition
  README.md       # This file
  AGENTS.md       # Notes for future maintainers/agents
```

## Local development

If you prefer to run the application without Docker, you need PHP 8.0+ with the DOM extension enabled. You can start the built-in PHP web server from the `ccd_viewer/public` directory:

```sh
cd public
php -S localhost:8000
```

Then open [http://localhost:8000/index.php](http://localhost:8000/index.php) in your browser.

## Notes and troubleshooting

- PHP 8.0+ with the DOM extension is required. The Dockerfile installs `libxml2-dev` and compiles DOM explicitly.
- If CDN assets fail to load due to corporate firewall or tracking-prevention rules, download Bootstrap and FontAwesome locally and update the links in `app/Views/layout.php`.
- This viewer is intended as an exercise and does not fully implement all CCDA specification nuances. It extracts basic metadata, section titles, narrative text, and a simple representation of entries. For production use, add stricter validation, richer parsing, and security hardening.
