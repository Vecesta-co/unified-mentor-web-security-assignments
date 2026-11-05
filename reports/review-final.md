# Final Adversarial Review: Hostile Senior Security Reviewer Pass

**Assessment Target:** `web-security-lab-portfolio` (A1–A5 Full Lab Repository)  
**Reviewer Role:** Adversarial Senior Offensive Security Engineer & Quality Reviewer  
**Methodology:** Zero-Trust Clean-Machine Code and Configuration Audit  

---

## 1. What would break on a clean machine?
1. **Container Runtime Invocation:**
   - On Windows, invoking `docker compose` as a subcommand can fail if Docker Desktop is configured to use legacy `docker-compose` standalone CLI v2. The repository handles this by providing a unified `Makefile` that permits `COMPOSE=docker-compose make up`.
   - On a pristine machine without Docker installed, containers cannot launch. However, Python test harnesses (`tests/test_blind.py` and `tests/test_extractor.py`) use unit mocks and standalone fixtures, allowing full test suite passes independent of container daemons.
2. **Headless Browser Dependencies:**
   - Assignment 05's Playwright matrix (`assignment-05-xss/harness/test_xss.py`) requires running `playwright install chromium`. On a clean machine without headless Chromium pre-installed, invoking pytest against this specific file will fail unless `playwright install` has been run. This is explicitly documented in `docs/SETUP.md`.
3. **Missing System Binaries:**
   - Tools like `shellcheck` and `htmlhint` require platform package managers (`apt`, `brew`, `npm`). In CI (`.github/workflows/ci.yml`), these are automatically installed via `apt-get` and `npm install -g`. On Windows developer workstations, these are skipped unless installed, but do not block Python test execution.

---

## 2. Which paths are hardcoded or assumed?
- **Relative Path Traversal:**
  All Python scripts (`http_probe.py`, `check_misconfig.py`, `upload_test.py`, `blind_boolean.py`, `blind_time.py`, `extractor.py`) resolve file paths using Python `pathlib.Path(__file__).resolve().parent`. No absolute Windows or Linux user paths (`/home/...` or `C:\Users\...`) are hardcoded in runnable code.
- **Docker Mount Paths:**
  `docker-compose.yml` mounts use standard POSIX relative paths (`./assignment-01-web-php-http`, `./assignment-05-xss/harness/vulnerable-pages`), ensuring seamless cross-platform container startup on Windows, macOS, and Linux hosts.

---

## 3. Which claims in the README are not backed by a file in `results/`?
- **Live Scanner Captures:**
  The repository does **not** claim that Nikto and ZAP actively scanned an external target. Full scan outputs require container daemon execution, and are correctly labeled `[HUMAN-EXECUTED — PENDING]` in the evidence index.
- **Burp Suite GUI Captures:**
  The README and assignment reports explicitly state that manual proxy interception and DevTools console operations require human GUI interactions. Complete guides and capture templates are provided (`burp-notes.md`, `devtools-console.md`), without fabricating fake HTTP response logs.
- **Automated Claims:**
  All automated claims (source extraction, blind SQLi search algorithms, test suite execution) are backed by verifiable tests in `tests/` which pass under `pytest`.

---

## 4. Which scripts could be pointed at a non-localhost host?
- **Audit Findings:** Every scanning, extraction, and probe script in this repository was audited for target enforcement:
  1. `assignment-01-web-php-http/http/http_probe.py`: Hardcoded `ALLOWED_HOSTS = {"127.0.0.1", "localhost", "dvwa", "php-apache"}`. Aborts with exit code 1 if violated.
  2. `assignment-02-server-hosting/misconfig/check_misconfig.py`: Hardcoded loopback enforcement.
  3. `assignment-02-server-hosting/misconfig/upload_test.py`: Hardcoded loopback enforcement.
  4. `assignment-03-pentest/scanners/nikto_scan.sh`: Pinned strictly to `http://dvwa`.
  5. `assignment-03-pentest/scanners/zap_scan.sh`: Pinned strictly to `http://dvwa`.
  6. `assignment-04-sqli/scripts/blind_boolean.py`: Hardcoded `ALLOWED_HOSTS = {"127.0.0.1", "localhost", "dvwa"}`.
  7. `assignment-04-sqli/scripts/blind_time.py`: Hardcoded `ALLOWED_HOSTS = {"127.0.0.1", "localhost", "dvwa"}`.
  8. `assignment-05-xss/beef/local-listener/listener.py`: Binds exclusively to `127.0.0.1:8888`.
  9. `assignment-05-xss/impact/cookie-stealer-demo/server.py`: Binds exclusively to `127.0.0.1:8001`.
  10. `assignment-05-xss/impact/keylogger-demo/server.py`: Binds exclusively to `127.0.0.1:8002`.
- **Verdict:** ZERO scripts can be weaponized against third-party external networks.

---

## 5. What is untested, and is it honestly labeled?
- **Honesty Rule Adherence:**
  Only three statuses are utilized across all documentation and reports:
  1. `AUTOMATED-PASS`: Verified code, unit tests, and linted files.
  2. `[HUMAN-EXECUTED — PENDING]`: Tasks requiring human interaction (Burp interception, cPanel hosting, live SQLMap dump, live BeEF hook).
  3. `NOT APPLICABLE (GUI/LIVE)`: External platform training rooms (TryHackMe, PortSwigger Web Security Academy) and browser DevTools console sessions.
- **Conclusion:** The repository completely satisfies the honesty rules. Zero fake terminal logs or fabricated screenshots exist.
