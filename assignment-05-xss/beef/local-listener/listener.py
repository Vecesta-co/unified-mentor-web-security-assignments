#!/usr/bin/env python3
"""
local-listener/listener.py — Local HTTP Hook Listener for BeEF / XSS Telemetry
Assignment 05 — Cross-Site Scripting (XSS)

Listens on localhost (127.0.0.1:8888) to receive simulated hook events
and telemetry pings from local lab pages.

STRICT ALLOWLIST ENFORCEMENT:
Binds strictly to 127.0.0.1. Rejects any non-local requests.
"""

import sys
from http.server import HTTPServer, BaseHTTPRequestHandler
import json
import datetime
from urllib.parse import urlparse, parse_qs

BIND_HOST = "127.0.0.1"
BIND_PORT = 8888
ALLOWED_HOSTS = {"127.0.0.1", "localhost", "dvwa"}

class LocalHookHandler(BaseHTTPRequestHandler):
    def _send_cors_headers(self):
        self.send_header("Access-Control-Allow-Origin", "http://localhost")
        self.send_header("Access-Control-Allow-Methods", "GET, POST, OPTIONS")
        self.send_header("Access-Control-Allow-Headers", "Content-Type")

    def do_OPTIONS(self):
        self.send_response(200)
        self._send_cors_headers()
        self.end_headers()

    def do_GET(self):
        client_ip = self.client_address[0]
        if client_ip not in ("127.0.0.1", "::1"):
            self.send_error(403, "Access restricted to localhost only.")
            return

        parsed = urlparse(self.path)
        params = parse_qs(parsed.query)
        timestamp = datetime.datetime.now().isoformat()

        print(f"[{timestamp}] [HOOK RECEIVED - GET] Path: {parsed.path} | Query: {params}")

        self.send_response(200)
        self.send_header("Content-Type", "application/json")
        self._send_cors_headers()
        self.end_headers()
        self.wfile.write(json.dumps({"status": "received", "time": timestamp}).encode('utf-8'))

    def do_POST(self):
        client_ip = self.client_address[0]
        if client_ip not in ("127.0.0.1", "::1"):
            self.send_error(403, "Access restricted to localhost only.")
            return

        content_length = int(self.headers.get("Content-Length", 0))
        post_body = self.rfile.read(content_length).decode('utf-8', errors='replace')
        timestamp = datetime.datetime.now().isoformat()

        print(f"[{timestamp}] [HOOK TELEMETRY - POST] Payload: {post_body}")

        self.send_response(200)
        self.send_header("Content-Type", "application/json")
        self._send_cors_headers()
        self.end_headers()
        self.wfile.write(json.dumps({"status": "acknowledged", "size": content_length}).encode('utf-8'))

    def log_message(self, format, *args):
        # Suppress noisy standard server logs to keep evidence clean
        sys.stderr.write(f"[LocalHookListener] {args[0]} - - [{self.log_date_time_string()}] {format%args}\n")

def run():
    print(f"[*] Starting Local BeEF Hook Telemetry Listener on http://{BIND_HOST}:{BIND_PORT}")
    print("[*] Strict Allowlist Active: Only local traffic accepted.")
    server = HTTPServer((BIND_HOST, BIND_PORT), LocalHookHandler)
    try:
        server.serve_forever()
    except KeyboardInterrupt:
        print("\n[!] Shutting down listener.")
        server.server_close()

if __name__ == '__main__':
    run()

