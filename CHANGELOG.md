# Changelog

All notable changes to this project will be documented in this file.

## [v1.1.0] - 2025-12-03
### Added
- Copy/download actions for the XML pane with status hints; first entry auto-loads and last-viewed section persists per file.
- Upload page UX: client-side extension/size checks (≤10 MB), inline errors, loading hint, and resume link (best-effort if storage is allowed).
- Local Bootstrap 5.3.2 and FontAwesome 6.4.0 assets to avoid tracking-prevention issues.

### Changed
- Light server guardrails: basic extension/size checks and friendly messages; PHP upload/post limits set to 10 MB in the container.

## [v1.0.0] - 2025-12-01
### Added
- Initial release: upload CCD/CCDA XML, parse metadata/sections/entries, view structured and narrative tabs, inspect XML snippets.

[v1.1.0]: https://github.com/josephbartlett/ccd-viewer/releases/tag/v1.1.0
[v1.0.0]: https://github.com/josephbartlett/ccd-viewer/releases/tag/v1.0.0
