# Roadmap

Current: v1.1.0. Planned milestones leading to v2.0.0.

## v1.1.0 — Stability & UX polish
- [x] Add client-side validation (file size/type) with clear upload errors.
- [x] Persist uploaded filename and last-viewed section via query params/local storage.
- [x] Add "copy XML" and "download XML snippet" actions in the XML pane.
- [x] Improve empty/error states and add loading indicators during parse.

## v1.2.0 — Packaging & Ops
- [ ] Ship local Bootstrap/FontAwesome assets with a toggle for CDN/local to avoid tracking-prevention blocks.
- [ ] Add Docker healthcheck and minimal `docker-compose` for local dev.
- [ ] Add env config for upload size limits and log level.
- [ ] Add request/parse logging and rotate uploads/logs.

## v1.3.0 — Parsing depth
- [ ] Expand metadata (providers, organizations, encounter info).
- [ ] Enrich section parsing for common templates (problems, meds, allergies, labs).
- [ ] Normalize code systems (LOINC, SNOMED, RxNorm) to consistent labels.
- [ ] Add narrative-to-plain-text fallback for sections without entries.

## v1.4.0 — Quality & Security
- [ ] Add unit tests for CCDParser and integration tests with fixture CCD/CCDA files.
- [ ] Add CI pipeline (lint, tests, Docker build).
- [ ] Sanitize/strip unsafe HTML in narratives; harden upload constraints/permissions.
- [ ] Add CSP and stricter headers (X-Content-Type-Options, Referrer-Policy, etc.).

## v1.5.0 — UI enhancements
- [ ] Add search/filter within sections and global search across sections.
- [ ] Make section list collapsible; keep counts/badges in sync with filters.
- [ ] Paginate or virtualize large entry tables.
- [ ] Add keyboard navigation for sections/entries.

## v1.6.0 — Accessibility & i18n
- [ ] Audit ARIA roles/labels; ensure focus states and tab order.
- [ ] Improve contrast and add text sizing controls.
- [ ] Externalize UI strings and add basic locale switch.

## v2.0.0 — Advanced features
- [ ] Support multiple uploads per session and quick switching between files.
- [ ] Add CCD diff/compare view with highlighted section/entry differences.
- [ ] Export views to PDF/CSV (metadata, sections, selected entries).
- [ ] Pluggable parser adapters for other CDA flavors; configurable code-system display.
- [ ] Optional auth layer for shared deployments (basic/OIDC) with role-based access to uploads.
