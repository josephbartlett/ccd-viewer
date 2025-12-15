# Agent Guide

Notes for future agents working on this repository.

## Project basics
- PHP 8.2 on Apache (official `php:8.2-apache` image). DOM extension compiled; `libxml2-dev` installed in Docker build.
- Custom minimal MVC: controllers in `app/Controllers`, views in `app/Views`, parser in `app/Models/CCDParser.php`.
- Entry point: `public/index.php` (upload form) → `public/upload.php` → `public/viewer.php`.
- Client-side behavior in `public/assets/js/viewer.js` renders section list, tabbed content, and XML snippet pane.

## Build and run
- Docker build: `docker build -t ccd-viewer .`
- Run: `docker run -p 8080:80 --name ccd-viewer ccd-viewer`
- Persist uploads: mount `$(pwd)/storage/uploads` to `/var/www/html/storage/uploads`.
- Local dev without Docker: `cd public && php -S localhost:8000` (requires PHP 8.0+ with DOM).

## Frontend behavior
- Section list items are clickable; the first section and first entry auto-load.
- Magnifying-glass button in the Structured tab loads the entry’s XML into the right-hand pane.
- Narrative tab shows the section’s `<text>` content as raw HTML.
- Data is exposed via `window.CCD_VIEWER_DATA` in `app/Views/viewer.php`.

## Assets
- Bootstrap 5.3.2 and FontAwesome 6.4.0 are served locally (see `public/assets/vendor/`) and referenced in `app/Views/layout.php`.
- If you switch back to CDN-hosted assets, add/update SRI attributes and handle environments that block CDN traffic.

## Sample data
- Keep `ccd_sample.xml` (user-provided example) for quick demos/tests.
- Remove any temporary upload fixtures you create after use.

## Tests
- No automated test suite currently. Manual verification: upload a CCD, click sections, toggle tabs, check XML pane updates.

## Conventions
- Keep files ASCII unless a file already uses other encodings.
- Use `apply_patch` for edits when practical; avoid reverting user changes.
- `rg` is preferred for searching.
