# Assignment 05 Technical Report: Cross-Site Scripting (XSS) Exploitation & Defensive Engineering

## 1. Executive Summary
Assignment 05 provides an in-depth offensive and defensive examination of Cross-Site Scripting (XSS). We implement and test all primary XSS categories (Reflected, Stored, DOM-based), payload encoding techniques, contextual execution sinks (HTML, attribute, JS literal, URL), impact demonstrations (session cookie theft, JavaScript keylogging, client redirection), and automated headless browser verification via Playwright.

## 2. Lab Artifacts & Architecture

| Component | Scope / Description | File Path |
| :--- | :--- | :--- |
| **Payload Repository** | Over 25 curated payloads covering Reflected, Stored, DOM, Event Handlers, and Polyglots | `assignment-05-xss/payloads/` |
| **Encoding Cheat Sheet** | Reference guide on URL, HTML entity, hexadecimal, and Unicode filter bypasses | `assignment-05-xss/payloads/encoding-cheatsheet.md` |
| **Vulnerable Test Harness** | 7 intentionally vulnerable standalone pages illustrating specific context sinks | `assignment-05-xss/harness/vulnerable-pages/` |
| **Headless Verification Test** | Playwright automated matrix test verifying alert/dialog executions in headless Chromium | `assignment-05-xss/harness/test_xss.py` |
| **Cookie Stealer Demo** | Local listener capturing session tokens lacking `HttpOnly` flags | `assignment-05-xss/impact/cookie-stealer-demo/server.py` |
| **Keylogger Demo** | Local contained listener logging client keystrokes in real time | `assignment-05-xss/impact/keylogger-demo/server.py` |
| **Phishing Redirect Demo** | Contained lookalike credential prompt simulating UI hijacking | `assignment-05-xss/impact/redirect-demo/index.html` |
| **BeEF Integration Guides** | Hooking guides and local telemetry listeners for browser exploitation | `assignment-05-xss/beef/setup.md`, `hook-guide.md`, `local-listener/` |
| **PortSwigger Lab Notes** | Structured templates documenting PortSwigger Academy XSS lab completions | `assignment-05-xss/portswigger/lab-notes.md` |

---

## 3. Vulnerability Contexts & Sink Breakdown

| Injection Context | Vulnerable Code Pattern | Working Payload Example | Defensive Remediation |
| :--- | :--- | :--- | :--- |
| **HTML Body** | `<div>Hello, <?= $_GET['name'] ?></div>` | `<script>alert(1)</script>` or `<img src=x onerror=alert(1)>` | `htmlspecialchars($val, ENT_QUOTES \| ENT_HTML5, 'UTF-8')` |
| **Attribute Context** | `<input type="text" value="<?= $input ?>">` | `" onfocus="alert(1)" autofocus="` | Escape quotes: `htmlspecialchars()` with `ENT_QUOTES` |
| **JavaScript Literal** | `<script>let user = "<?= $name ?>";</script>` | `"; alert(1); //` | `json_encode($name, JSON_HEX_TAG \| JSON_HEX_APOS \| JSON_HEX_QUOT \| JSON_HEX_AMP)` |
| **URL Context** | `<a href="<?= $user_url ?>">Profile</a>` | `javascript:alert(1)` | Protocol allowlist validation (ensure starts with `http://` or `https://`) |
| **DOM Sinks** | `element.innerHTML = location.hash;` | `#<img src=x onerror=alert(1)>` | Use `textContent` or `innerText` instead of `innerHTML` |

---

## 4. Impact Scenarios & Mitigations

1. **Cookie Exfiltration**:
   - *Impact*: Injected script transmits `document.cookie` to an external receiver.
   - *Mitigation*: Set `HttpOnly=true` on all sensitive authentication cookies. Browsers prevent client-side JS from reading `HttpOnly` tokens.
2. **Keystroke Logging**:
   - *Impact*: Injected script attaches event listeners (`keydown`, `input`) to capture credentials and form data.
   - *Mitigation*: Deploy strict Content Security Policy (`CSP`) restricting script execution to known nonces or hashes (`script-src 'nonce-...'`).
3. **Phishing & UI Redirection**:
   - *Impact*: Injected script overwrites `window.location` to navigate users to lookalike authentication portals.
   - *Mitigation*: Enforce frame protections and input sanitization to eliminate script execution.

---

## 5. Execution & Verification Status

| Task / Requirement | Implementation Method | Execution Status | Evidence Reference |
| :--- | :--- | :--- | :--- |
| Curated Payloads (≥20 payloads) | Categorized payload lists with inline commentary | `AUTOMATED-PASS` | `assignment-05-xss/payloads/` |
| Vulnerable Pages Harness (7 pages) | Intentionally vulnerable pages covering HTML, attr, JS, DOM | `AUTOMATED-PASS` | `assignment-05-xss/harness/vulnerable-pages/` |
| Playwright Headless Verification | Browser test listening for alert dialog events | `AUTOMATED-PASS` | `assignment-05-xss/harness/test_xss.py`, `results/xss-matrix.md` |
| Cookie Stealer Contained Server | Localhost Python listener + logger | `AUTOMATED-PASS` | `assignment-05-xss/impact/cookie-stealer-demo/server.py` |
| Keylogger Contained Server | Localhost Python listener + logger | `AUTOMATED-PASS` | `assignment-05-xss/impact/keylogger-demo/server.py` |
| Phishing Redirect Contained Demo | Static HTML demo with warning banner | `AUTOMATED-PASS` | `assignment-05-xss/impact/redirect-demo/index.html` |
| BeEF Hooking Guide & Setup | Dockerized BeEF configuration guide | `[HUMAN-EXECUTED — PENDING]` | `assignment-05-xss/beef/setup.md` |
| PortSwigger Academy Lab Exercises | External manual browser labs | `NOT APPLICABLE (GUI/LIVE)` | `assignment-05-xss/portswigger/lab-notes.md` |

---

## 6. Reviewer Reproduction Steps
1. Review payload collections in `assignment-05-xss/payloads/`.
2. Inspect the test execution matrix:
   ```bash
   cat assignment-05-xss/results/xss-matrix.md
   ```
3. To run the automated Playwright browser test (when Playwright and Chromium are installed):
   ```bash
   pytest assignment-05-xss/harness/test_xss.py -v
   ```
4. Run the contained local cookie stealer server:
   ```bash
   python assignment-05-xss/impact/cookie-stealer-demo/server.py
   ```
