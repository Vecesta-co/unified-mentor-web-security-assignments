# Assignment 01 Technical Report: Web Technologies Fundamentals (HTML, PHP, HTTP)

## 1. Executive Summary
Assignment 01 establishes the baseline understanding of client-side architecture (semantic HTML5), server-side application logic (PHP 8.x), request/response lifecycles, and automated source analysis for security indicators. The objective is to analyze web application mechanics, identify common developer pitfalls (unvalidated inputs, missing CSRF controls, sensitive information disclosure), and implement defense-in-depth mitigations.

## 2. Lab Artifacts & Architecture

| Component | Scope / Description | File Path |
| :--- | :--- | :--- |
| **HTML5 Suite** | 5 semantic pages (forms, tables, lists, multi-field register, home) with ARIA attributes and client validation | `assignment-01-web-php-http/site/` |
| **PHP Script Suite** | 10 modular scripts covering syntax, arrays, loops, functions, file I/O, and secure PDO interactions | `assignment-01-web-php-http/php/` |
| **Authentication System** | Production-pattern login system with CSRF tokens, bcrypt hashing, and session fixation defense | `assignment-01-web-php-http/php/` (`login.php`, `auth.php`, `dashboard.php`, `logout.php`) |
| **Database Schema** | Relational schema with indexed credentials, role-based access, and audit metadata | `assignment-01-web-php-http/db/schema.sql` |
| **HTTP Inspection Probe** | Python probe tool supporting Burp Suite proxy routing and security header evaluation | `assignment-01-web-php-http/http/http_probe.py` |
| **Source Code Extractor** | Automated DOM and comment parser extracting hidden fields, form actions, and security flags | `assignment-01-web-php-http/source-analysis/extractor.py` |

---

## 3. Detailed Technical Findings

### 3.1 Client-Side Markup & Form Controls
- **Form Method Discrepancies**: The distinction between idempotent `GET` and state-altering `POST` was implemented in `forms.html` and `register.html`. Using `GET` for sensitive inputs (e.g. passwords or tokens) was flagged as an anti-pattern due to browser history caching, server log persistence, and `Referer` leakage.
- **Client Validation vs. Server Defense**: While HTML5 attributes (`required`, `pattern`, `minlength`, `maxlength`) enhance user experience, they provide zero offensive barrier against intercepted requests. All business rules and authorization constraints must be reinforced server-side.

### 3.2 PHP Security & Defensive Engineering
- **SQL Injection Prevention**: Deprecated procedural functions (`mysqli_query` with concatenated variables) are strictly eliminated in favor of PDO parameterized statements (`PDO::prepare()` with bound parameters and `PDO::ATTR_EMULATE_PREPARES => false`).
- **Cryptographic Storage**: Passwords are saved exclusively as one-way salted hashes using `password_hash($raw, PASSWORD_BCRYPT)` and authenticated with timing-attack resistant `password_verify()`.
- **Session Security**: Session cookies are configured with `HttpOnly=true` to mitigate DOM-based session theft via XSS, and `SameSite=Lax` to prevent standard cross-site request forgery. Login calls trigger `session_regenerate_id(true)` to eliminate session fixation.
- **Path Traversal Mitigation**: File operations in `09_file_io.php` enforce path boundary checks via `realpath()` validation against the intended root directory, preventing `../` traversal attacks.

### 3.3 HTTP Header & Protocol Analysis
The automated `http_probe.py` checks target endpoints and audits essential defense headers:
- `Content-Security-Policy`: Restricts unauthorized script execution.
- `X-Frame-Options: SAMEORIGIN`: Prevents UI clickjacking attacks.
- `X-Content-Type-Options: nosniff`: Prevents MIME-sniffing bypasses.
- `Strict-Transport-Security` (HSTS): Enforces transport security over HTTPS.

---

## 4. Execution & Verification Status

| Task / Requirement | Implementation Method | Execution Status | Evidence Reference |
| :--- | :--- | :--- | :--- |
| 5 Semantic HTML5 Pages | Built with accessible HTML5 & ARIA | `AUTOMATED-PASS` | `assignment-01-web-php-http/site/` |
| 10 PHP Scripts + Login | Built with PHP 8.x, PDO, CSRF | `AUTOMATED-PASS` | `assignment-01-web-php-http/php/` |
| MySQL Database Schema | Schema file with seed accounts | `AUTOMATED-PASS` | `assignment-01-web-php-http/db/schema.sql` |
| HTTP Probe Tool | Python script with localhost allowlist | `AUTOMATED-PASS` | `assignment-01-web-php-http/http/http_probe.py` |
| Automated Source Extractor | BeautifulSoup CLI tool with unit test | `AUTOMATED-PASS` | `tests/test_extractor.py` (3 passed) |
| Burp Suite Interception | Manual proxy interception & header manipulation | `[HUMAN-EXECUTED — PENDING]` | `assignment-01-web-php-http/http/burp-notes.md` |
| Browser Console DOM Manipulation | Live DevTools console experiments | `NOT APPLICABLE (GUI/LIVE)` | `assignment-01-web-php-http/devtools-console.md` |

---

## 5. Reviewer Reproduction Steps
1. Navigate to repository root.
2. Run automated test suite:
   ```bash
   pytest tests/test_extractor.py -v
   ```
3. Test the HTTP probe against localhost allowlist:
   ```bash
   python assignment-01-web-php-http/http/http_probe.py --target http://127.0.0.1:8081
   ```
   *(Note: Attempts to target external hosts will be rejected with an immediate security exit).*
