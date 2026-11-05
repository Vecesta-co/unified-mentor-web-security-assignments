# SECURITY.md

## ⚠️ Legal & Ethical Notice

**This repository is an educational security lab portfolio. All offensive techniques,
payloads, and scanning scripts are intended EXCLUSIVELY for use against systems
you own or have explicit written authorization to test.**

Using these tools, scripts, or payloads against systems without authorization is:
- Illegal in most jurisdictions (e.g., Computer Fraud and Abuse Act, UK Computer Misuse Act)
- An academic integrity violation
- An ethical violation

The lab targets in this repository are:
- **DVWA** (Damn Vulnerable Web Application) — intentionally vulnerable, localhost only
- **Custom Docker containers** you build and run on your own machine
- **Local PHP/Apache vhosts** running in Docker

**DVWA must NEVER be exposed to the internet.** The Docker Compose configuration
binds services to `127.0.0.1` only. Do not change bind addresses without understanding the risk.

---

## Reporting a Vulnerability in This Repository

This repo contains intentionally vulnerable code. If you find an unintentional
vulnerability in the **tooling or scaffolding** (not in DVWA/harness pages), please:

1. **Do not** open a public GitHub issue.
2. Email a summary to the repository owner (see GitHub profile).
3. Allow 14 days for a response before public disclosure.

We will acknowledge reports within 72 hours and work to resolve confirmed issues promptly.

---

## Hardcoded Allowlist

Every scanning and exploitation script in this repo enforces a hardcoded target allowlist:

```
ALLOWED_HOSTS = {"127.0.0.1", "localhost", "dvwa", "php-apache"}
```

Passing any other host will cause the script to **refuse and exit** with a clear error message.

---

## No Real Credentials

No real credentials, API keys, or personal data exist in this repository.
All database passwords, app secrets, and API keys are placeholder strings
in `.env.example`. Copy to `.env` and change all values before running.
