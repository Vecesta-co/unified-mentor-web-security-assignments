# Setup Guide

## Prerequisites

| Tool | Minimum Version | Install |
|------|----------------|---------|
| Docker Desktop | 24.x | https://docs.docker.com/get-docker/ |
| Docker Compose | 2.24+ | Bundled with Docker Desktop |
| Python | 3.10+ | https://www.python.org/downloads/ |
| PHP CLI | 8.1+ | `apt install php-cli` / MAMP / Laragon |
| Node.js | 18+ | https://nodejs.org/ (for htmlhint) |
| shellcheck | 0.9+ | `apt install shellcheck` |
| htmlhint | 1.1+ | `npm install -g htmlhint` |
| Playwright | 1.40+ | `pip install playwright && playwright install chromium` |

## Quick Start

```bash
git clone https://github.com/YOUR_USERNAME/web-security-lab-portfolio.git
cd web-security-lab-portfolio
cp .env.example .env
# Edit .env to set strong passwords
make up
```

Services will be available at:
- **DVWA**: http://localhost:8080 (admin / password — change after first login)
- **PHP-Apache (site-a)**: http://localhost:8081
- **ZAP API**: http://localhost:8090
- **BeEF Panel**: http://localhost:3000/ui/panel

## First-Run DVWA Setup

1. Browse to http://localhost:8080/setup.php
2. Click **Create / Reset Database**
3. Log in with `admin` / `password`
4. Go to DVWA Security → set level to **Low** to start

## Running Tests

```bash
# Lint everything
make lint

# Run automated tests
make test

# Run XSS matrix (requires 'make up' first)
python -m pytest assignment-05-xss/harness/test_xss.py -v
```

## Stopping / Resetting

```bash
make down      # Stop containers (preserve data)
make reset     # Wipe volumes and restart fresh
```
