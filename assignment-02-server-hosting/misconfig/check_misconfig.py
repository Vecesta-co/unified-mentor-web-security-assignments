#!/usr/bin/env python3
"""
check_misconfig.py — Web Server Security Misconfiguration Auditor
Assignment 02 — Web Server Hosting Lab (Task 4)

Checks localhost targets for:
  - Exposed diagnostic information (phpinfo.php, info.php)
  - Common editor and backup files (.bak, .old, .zip, .swp, ~)
  - Exposed directory listing on test directories (/uploads/, /backup/)
  - Path-traversal response codes on file endpoints (/download?file=)

ENFORCES STRICT TARGET ALLOWLIST:
Only connects to localhost, 127.0.0.1, or dvwa. Rejects all external hosts.
Outputs both structured JSON and a formatted Markdown table.
"""

import sys
import argparse
import json
from urllib.parse import urlparse
from urllib.request import urlopen, Request
from urllib.error import HTTPError, URLError

ALLOWED_HOSTS = {"localhost", "127.0.0.1", "dvwa", "php-apache"}

TEST_PATHS = [
    # Exposed diagnostics
    {"path": "/phpinfo.php", "desc": "PHP Diagnostics Info Page", "expect_secure": [404, 403]},
    {"path": "/info.php", "desc": "Alternative PHP Info Page", "expect_secure": [404, 403]},
    # Backup & swap extensions
    {"path": "/index.php.bak", "desc": "Backup PHP Source Code", "expect_secure": [404, 403]},
    {"path": "/index.php.old", "desc": "Old Version PHP Source", "expect_secure": [404, 403]},
    {"path": "/index.php.swp", "desc": "Vim Swap Artifact", "expect_secure": [404, 403]},
    {"path": "/backup.zip", "desc": "Compressed Backup Archive", "expect_secure": [404, 403]},
    {"path": "/index.php~", "desc": "Emacs/Gedit Backup Artifact", "expect_secure": [404, 403]},
    {"path": "/.env", "desc": "Environment Secrets File", "expect_secure": [404, 403]},
    # Directory listing on test directories
    {"path": "/uploads/", "desc": "Uploads Directory Index", "expect_secure": [403, 404, 200]},
    {"path": "/backup/", "desc": "Backup Directory Index", "expect_secure": [403, 404]},
    # Path traversal probe on query endpoints
    {"path": "/download?file=../../../../etc/passwd", "desc": "LFI Path Traversal Probe", "expect_secure": [400, 403, 404]},
]

def enforce_allowlist(target_url: str):
    parsed = urlparse(target_url)
    host = parsed.hostname or ""
    host = host.split(":")[0].lower()
    if host not in ALLOWED_HOSTS:
        print(f"[SECURITY REFUSAL] Target host '{host}' is NOT in allowed list.", file=sys.stderr)
        print(f"Allowed targets: {sorted(ALLOWED_HOSTS)}", file=sys.stderr)
        sys.exit(1)

def run_audit(base_url: str) -> list:
    enforce_allowlist(base_url)
    clean_base = base_url.rstrip("/")
    results = []

    for item in TEST_PATHS:
        full_url = clean_base + item["path"]
        req = Request(full_url, headers={"User-Agent": "SecLab-MisconfigChecker/1.0"})
        status = 0
        finding_flag = False
        body_sample = ""

        try:
            with urlopen(req, timeout=4) as resp:
                status = resp.status
                body = resp.read(512).decode(errors="replace")
                body_sample = body[:120].replace("\n", " ").strip()
                # Check for indicators in 200 OK
                if "phpinfo()" in body or "Configuration File (php.ini)" in body:
                    finding_flag = True
                elif "Index of" in body or "<title>Index of" in body:
                    finding_flag = True
                elif "root:x:0:0" in body:
                    finding_flag = True
                elif status == 200 and item["path"] in ("/index.php.bak", "/.env", "/backup.zip"):
                    finding_flag = True
        except HTTPError as e:
            status = e.code
            body_sample = f"HTTP Error {e.code}: {e.reason}"
        except (URLError, TimeoutError) as e:
            status = "Unreachable"
            body_sample = str(e)

        results.append({
            "path": item["path"],
            "description": item["desc"],
            "status_code": status,
            "is_vulnerable": finding_flag,
            "sample": body_sample
        })

    return results

def print_markdown_table(results: list, base_url: str):
    print(f"# Server Misconfiguration Audit Report")
    print(f"**Target Host:** `{base_url}`\n")
    print("| Target Path | Probe Description | Status Code | Finding State | Response Excerpt |")
    print("| :--- | :--- | :---: | :---: | :--- |")
    for r in results:
        status_badge = "🔴 VULNERABLE" if r["is_vulnerable"] else "🟢 SECURE / BLOCKED"
        print(f"| `{r['path']}` | {r['description']} | `{r['status_code']}` | {status_badge} | `{r['sample'][:60]}` |")

def main():
    parser = argparse.ArgumentParser(description="Web Server Misconfiguration Checker (Assignment 02)")
    parser.add_argument("base_url", nargs="?", default="http://localhost:8081", help="Target base URL (default: http://localhost:8081)")
    parser.add_argument("--json", action="store_true", help="Output raw JSON")
    args = parser.parse_args()

    results = run_audit(args.base_url)

    if args.json:
        print(json.dumps({"target": args.base_url, "audit": results}, indent=2))
    else:
        print_markdown_table(results, args.base_url)

if __name__ == "__main__":
    main()
