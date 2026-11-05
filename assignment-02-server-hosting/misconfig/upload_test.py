#!/usr/bin/env python3
"""
upload_test.py — File Upload Security & Filter Bypass Tester
Assignment 02 — Web Server Hosting Lab (Task 4)

Uploads benign test payloads to evaluate server-side file upload controls:
  1. Plain text harmless file (baseline accept): test.txt
  2. Double extension: payload.php.txt
  3. Reverse double extension: payload.txt.php
  4. Null-byte / alternative extension: payload.php5
  5. Oversized benign payload (> 2MB buffer)

STRICT ALLOWLIST ENFORCEMENT:
Only targets localhost, 127.0.0.1, or dvwa.
Records accept/reject status for each test case.
"""

import sys
import argparse
import json
import uuid
from urllib.parse import urlparse
from urllib.request import Request, urlopen
from urllib.error import HTTPError, URLError

ALLOWED_HOSTS = {"localhost", "127.0.0.1", "dvwa", "php-apache"}

TEST_CASES = [
    {
        "filename": "benign_doc.txt",
        "mime_type": "text/plain",
        "content": b"Hello world, benign test file.",
        "description": "Baseline plain text file (expected: Accepted)",
        "expected_secure_result": "ACCEPT"
    },
    {
        "filename": "test_script.php.txt",
        "mime_type": "text/plain",
        "content": b"<?php echo 'harmless demo'; ?>",
        "description": "Double extension .php.txt (harmless text content)",
        "expected_secure_result": "ACCEPT_OR_REJECT"
    },
    {
        "filename": "exploit_sample.txt.php",
        "mime_type": "application/x-php",
        "content": b"<?php echo 'test'; ?>",
        "description": "Executable PHP extension .txt.php (expected: Rejected)",
        "expected_secure_result": "REJECT"
    },
    {
        "filename": "alternate_ext.php5",
        "mime_type": "application/x-httpd-php",
        "content": b"<?php echo 'test'; ?>",
        "description": "Alternative PHP5 extension (expected: Rejected)",
        "expected_secure_result": "REJECT"
    },
    {
        "filename": "oversized_file.dat",
        "mime_type": "application/octet-stream",
        "content": b"A" * (3 * 1024 * 1024),  # 3MB exceeds typical 2MB default
        "description": "Oversized file (3MB > 2MB limit, expected: Rejected/413)",
        "expected_secure_result": "REJECT"
    }
]

def build_multipart_body(filename: str, mime: str, data: bytes, boundary: str) -> bytes:
    crlf = b"\r\n"
    lines = [
        f"--{boundary}".encode(),
        f'Content-Disposition: form-data; name="uploaded_file"; filename="{filename}"'.encode(),
        f"Content-Type: {mime}".encode(),
        b"",
        data,
        f"--{boundary}--".encode(),
        b""
    ]
    return crlf.join(lines)

def run_upload_tests(target_url: str) -> list:
    parsed = urlparse(target_url)
    host = (parsed.hostname or "").split(":")[0].lower()
    if host not in ALLOWED_HOSTS:
        print(f"[SECURITY BLOCKED] Host '{host}' is outside allowlist: {sorted(ALLOWED_HOSTS)}", file=sys.stderr)
        sys.exit(1)

    results = []

    for tc in TEST_CASES:
        boundary = f"----WebKitFormBoundary{uuid.uuid4().hex[:16]}"
        body = build_multipart_body(tc["filename"], tc["mime_type"], tc["content"], boundary)

        req = Request(
            target_url,
            data=body,
            headers={
                "User-Agent": "SecLab-UploadTester/1.0",
                "Content-Type": f"multipart/form-data; boundary={boundary}"
            },
            method="POST"
        )

        status_code = None
        outcome = "UNKNOWN"
        response_snippet = ""

        try:
            with urlopen(req, timeout=5) as resp:
                status_code = resp.status
                resp_text = resp.read(256).decode(errors="replace")
                response_snippet = resp_text.replace("\n", " ").strip()
                outcome = "ACCEPTED" if status_code in (200, 201) else "REJECTED"
        except HTTPError as e:
            status_code = e.code
            outcome = "REJECTED"
            response_snippet = f"HTTP {e.code}: {e.reason}"
        except (URLError, TimeoutError) as e:
            status_code = "Unreachable"
            outcome = "FAILED_CONNECTION"
            response_snippet = str(e)

        results.append({
            "test_file": tc["filename"],
            "mime_type": tc["mime_type"],
            "description": tc["description"],
            "status_code": status_code,
            "server_decision": outcome,
            "response": response_snippet[:80]
        })

    return results

def main():
    parser = argparse.ArgumentParser(description="Upload Security Tester (Assignment 02)")
    parser.add_argument("url", nargs="?", default="http://localhost:8081/upload.php", help="Target upload endpoint URL")
    parser.add_argument("--json", action="store_true", help="Output JSON results")
    args = parser.parse_args()

    results = run_upload_tests(args.url)

    if args.json:
        print(json.dumps({"target": args.url, "results": results}, indent=2))
    else:
        print(f"# File Upload Security Test Summary")
        print(f"**Endpoint:** `{args.url}`\n")
        print("| Tested Filename | Declared MIME | Test Description | Status | Outcome |")
        print("| :--- | :--- | :--- | :---: | :---: |")
        for r in results:
            badge = "🟢 REJECTED (DEFENSE OK)" if r["server_decision"] == "REJECTED" else "🟡 " + r["server_decision"]
            print(f"| `{r['test_file']}` | `{r['mime_type']}` | {r['description']} | `{r['status_code']}` | {badge} |")

if __name__ == "__main__":
    main()
