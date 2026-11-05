# Assignment 02 Technical Report: Web Server Hosting & Infrastructure Hardening

## 1. Executive Summary
Assignment 02 focuses on server infrastructure, multi-tenant virtual host configuration, web server security hardening (Apache HTTP Server), and detecting dangerous misconfigurations. We contrast an unhardened server posture against a hardened deployment featuring custom error handlers, directory index suppression, strict file extension blocks, and upload restrictions.

## 2. Lab Artifacts & Architecture

| Component | Scope / Description | File Path |
| :--- | :--- | :--- |
| **Multi-Site VHosts** | Two isolated Apache virtual host definitions (`site-a` on port 80, `site-b` on port 8082) | `assignment-02-server-hosting/infra/apache/site-a.conf`, `site-b.conf` |
| **Directory Listing Configs** | Comparative configurations demonstrating index exposure (`+Indexes`) vs. defense (`-Indexes`) | `assignment-02-server-hosting/infra/apache/directory-listing-on.conf`, `directory-listing-off.conf` |
| **Server Hardening Rules** | `.htaccess` and Apache rewrite configurations enforcing header injection and extension locks | `assignment-02-server-hosting/infra/apache/.htaccess`, `htaccess-rewrite.conf` |
| **Custom Error Documents** | Clean, non-disclosing error templates for 403 Forbidden, 404 Not Found, and 500 Internal Error | `assignment-02-server-hosting/infra/apache/403.html`, `404.html`, `500.html` |
| **Misconfiguration Auditor** | Python scanner auditing exposed `phpinfo()`, backup archives (`.bak`, `.zip`), and LFI traversals | `assignment-02-server-hosting/misconfig/check_misconfig.py` |
| **Upload Security Tester** | Script testing boundary defenses against dangerous extension mutations (`.php.txt`, `.txt.php`, double extensions) | `assignment-02-server-hosting/misconfig/upload_test.py` |
| **Cloud Hosting Walkthrough** | Reproducible manual deployment checklist for free-tier cPanel/FTP platforms | `assignment-02-server-hosting/cloud-hosting/guide.md`, `ftp-upload.md` |

---

## 3. Technical Analysis & Security Findings

### 3.1 Apache Virtual Host Segmentation
Hosting multiple sites on a single daemon introduces cross-vhost escalation risks if file permissions or execution boundaries are shared. By configuring distinct `DocumentRoot` directories and assigning isolated vhost listeners, `site-a` (primary application) and `site-b` (vulnerable XSS harness) are segregated to eliminate unauthorized path bleeding.

### 3.2 Directory Indexing & Information Disclosure
- **Vulnerability**: Enabling `Options +Indexes` allows attackers to browse internal directory hierarchies, revealing unlinked backups, configuration files, and staging scripts.
- **Remediation**: Explicitly setting `Options -Indexes` in virtual host roots and enforcing standard index directives (`DirectoryIndex index.php index.html`).

### 3.3 Dangerous File Extensions & Backup Leaks
- **Vulnerability**: Text editors and backup tools often generate temporary artifacts (`config.php.bak`, `schema.sql.old`, `.env.swp`). Web servers configured with naive handlers will serve these as plaintext rather than interpreting them as PHP, exposing database credentials and secrets.
- **Remediation**: Apache `FilesMatch` regex directives deny web access to any files matching `\.(bak|old|orig|save|swp|zip|tar|gz|sql|env|ini|log|conf)$`.

### 3.4 Upload Filter Bypass Defense
- **Vulnerability**: Relying solely on client-supplied `Content-Type` headers or simple right-to-left extension splits allows attackers to upload executable PHP code via names like `shell.php.jpg` or `shell.php5`.
- **Remediation**: The lab upload test harness tests four defensive tiers:
  1. Strict file extension whitelist (`.jpg`, `.png`, `.pdf`).
  2. Server-side MIME validation through magic byte inspection (`finfo`).
  3. Execution suppression inside upload directories (`php_flag engine off` and disabling script execution handlers).
  4. File content hashing and filename randomization upon storage.

---

## 4. Execution & Verification Status

| Task / Requirement | Implementation Method | Execution Status | Evidence Reference |
| :--- | :--- | :--- | :--- |
| Docker Multi-VHost Setup | Dockerfile + Apache virtual host configs | `AUTOMATED-PASS` | `docker-compose.yml`, `assignment-02-server-hosting/infra/` |
| Custom Error Pages (403/404/500) | Static non-disclosing error documents | `AUTOMATED-PASS` | `assignment-02-server-hosting/infra/apache/` |
| Directory Listing On/Off | VHost Apache configuration templates | `AUTOMATED-PASS` | `directory-listing-on.conf`, `directory-listing-off.conf` |
| Automated Misconfiguration Audit | Python script targeting localhost allowlist | `AUTOMATED-PASS` | `assignment-02-server-hosting/misconfig/check_misconfig.py` |
| Upload Security Test Script | Python upload fuzzing harness | `AUTOMATED-PASS` | `assignment-02-server-hosting/misconfig/upload_test.py` |
| Cloud Hosting / FTP Manual Guide | Step-by-step cPanel/FTP deployment guide | `[HUMAN-EXECUTED — PENDING]` | `assignment-02-server-hosting/cloud-hosting/guide.md` |
| Live Cloud Verification | Manual deployment to free-tier provider | `NOT APPLICABLE (GUI/LIVE)` | `assignment-02-server-hosting/cloud-hosting/ftp-upload.md` |

---

## 5. Reviewer Reproduction Steps
1. Verify Docker Compose configuration syntax:
   ```bash
   docker-compose config -q
   ```
2. Execute the automated misconfiguration test against localhost:
   ```bash
   python assignment-02-server-hosting/misconfig/check_misconfig.py --target http://127.0.0.1:8081
   ```
3. Run the upload test suite against the target endpoint:
   ```bash
   python assignment-02-server-hosting/misconfig/upload_test.py --target http://127.0.0.1:8081/upload.php
   ```
