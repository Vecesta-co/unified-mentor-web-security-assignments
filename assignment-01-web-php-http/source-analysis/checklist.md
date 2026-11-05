# Source Code Security Analysis Checklist
Assignment 01 — Web Technologies Fundamentals

This checklist establishes a reproducible manual and semi-automated review standard for evaluating web markup and client-side code security.

---

## 1. HTML Form Review

| Verification Item | Risk Description | Remediation Standard | Status |
| :--- | :--- | :--- | :---: |
| **Method Selection (GET vs POST)** | Sensitive credentials or mutation actions passed via GET appear in server logs, browser history, and `Referer` headers. | Enforce `POST` for all state-changing or authentication forms. Use `GET` strictly for safe, idempotent search queries. | [ ] |
| **CSRF Protection** | Forms submitting via `POST` without unique anti-forgery tokens allow cross-origin request forging. | Ensure a hidden `csrf_token` field is rendered by the server and validated upon receipt. | [ ] |
| **Input Validation Constraints** | Missing client constraints allow unexpected data shapes and degrade user experience. | Apply `required`, `pattern`, `minlength`, `maxlength`, and semantic types (`email`, `number`, `url`). | [ ] |
| **Hidden Field Auditing** | Developers frequently store state, pricing, or authorization flags in `<input type="hidden">`, assuming users cannot tamper with them. | Inspect all hidden fields; ensure the server never trusts client-supplied hidden parameters for authorization or business logic decisions. | [ ] |
| **Autocomplete Directives** | Password and credit card inputs caching credentials in public terminal browsers. | Specify `autocomplete="current-password"` or `autocomplete="new-password"`; apply `autocomplete="off"` where appropriate. | [ ] |

---

## 2. Information Disclosure & Code Comments

| Verification Item | Risk Description | Remediation Standard | Status |
| :--- | :--- | :--- | :---: |
| **Developer Comments in HTML** | Comments (`<!-- TODO: ... -->`, internal server IPs, author notes) leaking architecture details. | Strip all comments in build/minification pipelines prior to production deployment. | [ ] |
| **Embedded Debugging Flags** | Debug parameters or staging API endpoints left commented in scripts. | Audit script blocks for commented configuration blocks or internal URLs. | [ ] |
| **Sensitive Tokens in Markup** | JWT tokens, secret keys, or database credentials accidentally rendered in page body. | Verify no API keys or private secrets are bundled into client-facing HTML or JS files. | [ ] |

---

## 3. External Dependencies & Asset Integrity

| Verification Item | Risk Description | Remediation Standard | Status |
| :--- | :--- | :--- | :---: |
| **Third-Party Script Loading** | Inclusion of scripts from external unverified CDNs risks supply chain compromise. | Host scripts locally where possible or verify trust in external origins. | [ ] |
| **Subresource Integrity (SRI)** | Altered CDN scripts executing malicious code within the application origin. | Add `integrity="sha384-..."` and `crossorigin="anonymous"` attributes to external `<script>` and `<link>` tags. | [ ] |
| **Hyperlink Security** | `target="_blank"` links leaking `window.opener` context to target sites. | Add `rel="noopener noreferrer"` to all external anchor links. | [ ] |

---

## 4. Technology Stack Fingerprinting (Wappalyzer)

| Verification Item | Risk Description | Remediation Standard | Status |
| :--- | :--- | :--- | :---: |
| **Server Banner Exposure** | `Server: Apache/2.4.58 (Ubuntu)` revealing exact version to automated scanners. | Set `ServerTokens Prod` and `ServerSignature Off` in Apache configuration. | [ ] |
| **Language Headers** | `X-Powered-By: PHP/8.2.1` informing attackers of back-end runtime version. | Set `expose_php = Off` in `php.ini` or unset via `.htaccess`. | [ ] |
| **Meta Generator Tags** | `<meta name="generator" content="WordPress 6.4">` revealing CMS and framework versions. | Remove generator tags from HTML template headers. | [ ] |
