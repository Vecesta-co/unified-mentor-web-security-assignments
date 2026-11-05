# Tool Versions

This file documents the exact versions of all tools used in this portfolio.
Update this file whenever you upgrade a tool.

## Docker Images (pinned in docker-compose.yml)

| Service | Image | Tag |
|---------|-------|-----|
| MySQL | mysql | 8.0.36 |
| DVWA | ghcr.io/digininja/dvwa | latest |
| PHP-Apache | php | 8.2-apache |
| ZAP | ghcr.io/zaproxy/zaproxy | stable |
| BeEF | bcarvajal/beef-xss | latest |

## Host Tools

| Tool | Version Used | Notes |
|------|-------------|-------|
| Docker Desktop | 24.x | Pin in production |
| Python | 3.11 | Tested with 3.10+ |
| Playwright | 1.40 | `pip install playwright` |
| BeautifulSoup4 | 4.12 | `pip install beautifulsoup4` |
| requests | 2.31 | `pip install requests` |
| pytest | 7.4 | `pip install pytest` |
| flake8 | 6.1 | `pip install flake8` |
| shellcheck | 0.9.0 | `apt install shellcheck` |
| htmlhint | 1.1.4 | `npm install -g htmlhint` |
| SQLMap | 1.7.x | Via Docker: `sqlmapproject/sqlmap` |
| Nikto | 2.1.6 | Via Docker: `frapsoft/nikto` |

## Screenshots To Be Added by Operator

The following screenshots are referenced in reports but require human execution:

- `results/screenshots/a2-free-host-deployed.png` — Free hosting deployment
- `results/screenshots/a3-burp-intercept.png` — Burp Suite intercept
- `results/screenshots/a3-nikto-output.png` — Nikto terminal output
- `results/screenshots/a3-zap-alerts.png` — ZAP alerts panel
- `results/screenshots/a4-sqlmap-dump.png` — SQLMap database dump
- `results/screenshots/a5-beef-hook.png` — BeEF hooked browser
- `results/screenshots/a5-portswigger-lab.png` — PortSwigger lab completion
