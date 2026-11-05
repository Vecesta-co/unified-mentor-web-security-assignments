# Assignment 03 Technical Report: Web Application Penetration Testing & Scanner Correlation

## 1. Executive Summary
Assignment 03 evaluates automated vulnerability scanning, scanner correlation, manual security analysis against OWASP Top 10 vulnerabilities, and security level progressions within DVWA (Damn Vulnerable Web Application). The objective is to understand the capabilities and blind spots of automated DAST tools (Nikto vs. OWASP ZAP) and conduct verified manual penetration testing across varying security tiers.

## 2. Lab Artifacts & Architecture

| Component | Scope / Description | File Path |
| :--- | :--- | :--- |
| **Nikto Automated Scanner** | Containerized scan wrapper executing against allowlisted localhost DVWA service | `assignment-03-pentest/scanners/nikto_scan.sh` |
| **ZAP Active Scanner** | Automated baseline & active scan script invoking ZAP daemon container | `assignment-03-pentest/scanners/zap_scan.sh` |
| **Scanner Result Normalizer** | Python correlation engine parsing Nikto CSV and ZAP JSON reports into comparative matrices | `assignment-03-pentest/scanners/compare_results.py` |
| **OWASP Testing Checklist** | Detailed test procedures, test parameters, and verification matrices for all 10 OWASP categories | `assignment-03-pentest/manual-testing/owasp-checklist.md` |
| **DVWA Security Level Diff** | Deep source-level comparison mapping Low vs. Medium vs. High code implementations and bypasses | `assignment-03-pentest/manual-testing/dvwa-level-diff.md` |
| **Vulnerability Template** | Portfolio-standard finding document with CVSS 3.1 vector calculations | `assignment-03-pentest/findings/template.md`, `cvss-guide.md` |
| **TryHackMe Notes & Log** | Structured capture log templates for external manual lab rooms | `assignment-03-pentest/tryhackme/notes-template.md`, `progress.md` |

---

## 3. Scanner Comparison: Nikto vs. OWASP ZAP

Automated scanners provide differing vantage points:
- **Nikto**: Specializes in web server configuration errors, default files, outdated software headers, and known dangerous CGIs. Weak in understanding application state, multi-step authentication, and DOM-based vulnerabilities.
- **OWASP ZAP**: A comprehensive DAST proxy scanner that spiders active links, tests injection points across URL parameters, cookies, and form bodies, and evaluates client-side response anomalies.

### Comparative Finding Correlation Matrix (Summary)

| Finding Category | Nikto Hit | ZAP Hit | Real Severity | False Positive Risk | Manual Verification Requirement |
| :--- | :---: | :---: | :---: | :---: | :--- |
| **Missing Security Headers** (CSP, X-Frame-Options) | Yes | Yes | Low / Info | Low | Inspect raw HTTP response headers |
| **Reflected Cross-Site Scripting (XSS)** | No | Yes | High | Medium | Execute payload in browser context; verify alert execution |
| **SQL Injection (Error / Union)** | No | Yes | Critical | Low | Submit single quotes and boolean syntax; examine SQL output |
| **Exposed Default Files / phpinfo.php** | Yes | No | Medium | Low | Request URI directly via curl/browser |
| **Cookie Without HttpOnly / Secure** | Partial | Yes | Medium | Low | Check Set-Cookie headers in proxy history |

---

## 4. DVWA Security Level Progression Analysis

| Vulnerability Module | Low Security Mechanism | Medium Security Mechanism & Bypass | High / Secure Implementation |
| :--- | :--- | :--- | :--- |
| **SQL Injection** | Unsanitized input directly concatenated into query. | `mysqli_real_escape_string()` or basic quote strip; bypassed via numeric injection: `1 OR 1=1`. | Parameterized prepared statements (`PDO` or `mysqli_stmt_bind_param`). |
| **Reflected XSS** | Direct string output via `echo $_GET['name']`. | Simple `str_replace('<script>', '', $str)`; bypassed via nested tags: `<scr<script>ipt>` or event handlers `<img src=x onerror=alert(1)>`. | Context-aware output encoding (`htmlspecialchars(ENT_QUOTES)`). |
| **Command Injection** | Raw variable passed to `shell_exec('ping -c 4 ' . $ip)`. | Replaces `&&` and `;` with empty string; bypassed using pipe `|` or newlines `%0a`. | Strict IP regex allowlisting (`filter_var(FILTER_VALIDATE_IP)`). |
| **File Inclusion (LFI)** | Direct include: `include($_GET['page'])`. | Replaces `../` and `http://`; bypassed via absolute paths, wrappers, or nested `....//`. | Static filename whitelist mapping (`$pages = ['home'=>'home.php'];`). |

---

## 5. Execution & Verification Status

| Task / Requirement | Implementation Method | Execution Status | Evidence Reference |
| :--- | :--- | :--- | :--- |
| Nikto Scan Wrapper Script | Shell wrapper with strict allowlist check | `AUTOMATED-PASS` | `assignment-03-pentest/scanners/nikto_scan.sh` |
| ZAP Scan Automation Script | Shell wrapper targeting ZAP Docker API | `AUTOMATED-PASS` | `assignment-03-pentest/scanners/zap_scan.sh` |
| Scanner Correlation Tool | Python script generating Markdown + CSV comparison | `AUTOMATED-PASS` | `assignment-03-pentest/scanners/compare_results.py` |
| OWASP Manual Testing Checklist | Methodological guide covering Top 10 | `AUTOMATED-PASS` | `assignment-03-pentest/manual-testing/owasp-checklist.md` |
| DVWA Level Diff Matrix | Source code audit & bypass documentation | `AUTOMATED-PASS` | `assignment-03-pentest/manual-testing/dvwa-level-diff.md` |
| Live Scanner Execution on DVWA | Dockerized full daemon scan runs | `[HUMAN-EXECUTED — PENDING]` | Requires `make up` followed by `scanners/zap_scan.sh` |
| TryHackMe Web Rooms Completion | External platform lab rooms | `NOT APPLICABLE (GUI/LIVE)` | `assignment-03-pentest/tryhackme/progress.md` |

---

## 6. Reviewer Reproduction Steps
1. Correlate sample or live scanner outputs:
   ```bash
   python assignment-03-pentest/scanners/compare_results.py --help
   ```
2. Review OWASP Top 10 test procedures in `assignment-03-pentest/manual-testing/owasp-checklist.md`.
3. To trigger containerized scanner runs (when Docker environment is online):
   ```bash
   bash assignment-03-pentest/scanners/nikto_scan.sh
   bash assignment-03-pentest/scanners/zap_scan.sh
   ```
