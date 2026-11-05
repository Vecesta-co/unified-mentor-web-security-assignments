# Web Security Lab Portfolio

A comprehensive, public, portfolio-grade cybersecurity laboratory and research repository implementing five structured web-security lab assignments (A1–A5). Engineered with reproducibility, defense-in-depth principles, and strict ethical constraints at its core, this project features Dockerized testbeds, defensive full-stack PHP applications, custom Python exploit engines with hardcoded loopback allowlists, automated DAST correlation tooling, headless browser XSS test harnesses, and technical research reports.

> 🚀 **Internship & Founder Spotlight:**  
> This repository represents the foundational cybersecurity and web technology assignment project completed during my **first internship at the age of 17 at [Unified Mentor](https://unifiedmentor.com/)**.  
> Maintained & engineered under my company **[vecesta.co](https://vecesta.co)**.  
> *Reference Repository:* [adityasasikumar422-ui/ASSIGNMENT-1-WEB-TECHNOLOGIES](https://github.com/adityasasikumar422-ui/ASSIGNMENT-1-WEB-TECHNOLOGIES)

```mermaid
flowchart TD
    subgraph Host["Host System (Workstation / CI)"]
        Operator["Security Engineer / Assessor"]
        Make["GNU Make / Automated Test Harness"]
        Pytest["pytest Test Suite (Mock Extractors & Parsers)"]
    end

    subgraph DockerEnv["Docker Compose Lab Network (172.28.0.0/16)"]
        subgraph TargetGroup["Localhost Target Services (127.0.0.1 Only)"]
            DVWA["DVWA Service\n(:8080)\nDamn Vulnerable Web App"]
            PHPApp["Hardened PHP-Apache App\n(:8081 / :8082)\nCustom VHosts"]
            MySQL["MySQL 8.0 Daemon\n(:3306)\nRelational Backend"]
        end

        subgraph ToolGroup["Containerized Security Tooling"]
            ZAP["OWASP ZAP Daemon\n(:8090)\nActive DAST Proxy"]
            BeEF["BeEF Framework\n(:3000)\nBrowser Exploitation"]
        end
    end

    Operator -->|make up / make test| Make
    Make --> Pytest
    Pytest -->|Audits & Probes| PHPApp
    Pytest -->|Blind SQLi Algorithms| DVWA
    PHPApp -->|PDO Prepared Statements| MySQL
    DVWA -->|Vulnerable Queries| MySQL
    ZAP -.->|Targeted Scans| DVWA
    BeEF -.->|Simulated Telemetry| PHPApp
```

---

## ⚠️ Hard Scope, Legal & Ethics Notice

> **IMPORTANT:** All offensive techniques, payloads, exploit scripts, and scanning tools in this repository are strictly designed for educational and authorized defensive training.
> - **Targets**: All automated scripts strictly enforce a hardcoded target allowlist containing only `127.0.0.1`, `localhost`, `dvwa`, and `php-apache`. Any attempt to target external domains causes immediate execution termination.
> - **Exposure**: DVWA and lab containers are intentionally vulnerable and **must never be internet-exposed**. Port bindings are restricted to `127.0.0.1`.
> - **Integrity**: In accordance with the project evidence policy, this repository never fabricates command outputs or screenshots. Automated runs are marked `AUTOMATED-PASS`, while manual GUI/remote tasks are accurately designated `[HUMAN-EXECUTED — PENDING]` or `NOT APPLICABLE (GUI/LIVE)`.
> - **Secrets**: No real passwords, credentials, or private API keys exist in this codebase. All references use `.env.example` placeholders.

---

## Quickstart

### Prerequisites
- [Docker Desktop](https://docs.docker.com/get-docker/) (v24.x+) & Docker Compose
- [Python 3.10+](https://www.python.org/downloads/)
- [PHP 8.1+ CLI](https://www.php.net/) (for local linting; or run via container)
- [Git](https://git-scm.com/)

### 1. Clone & Configure Environment
```bash
git clone https://github.com/YOUR_USERNAME/web-security-lab-portfolio.git
cd web-security-lab-portfolio
cp .env.example .env
```

### 2. Launch Local Lab Containers
```bash
make up
```
*(On Windows systems using Docker Compose v2 standalone, invoke `docker-compose up -d --build`)*

### 3. Service Access Endpoints
| Service | URL | Default / Seed Credentials |
| :--- | :--- | :--- |
| **DVWA** | [http://localhost:8080](http://localhost:8080) | `admin` / `password` |
| **Hardened Lab App (Site A)** | [http://localhost:8081](http://localhost:8081) | `demo_user` / `Demo@Password123!` |
| **Vulnerable XSS Harness (Site B)** | [http://localhost:8082](http://localhost:8082) | N/A (Vulnerable testbed) |
| **OWASP ZAP API** | [http://localhost:8090](http://localhost:8090) | API Key in `.env` |
| **BeEF Management Console** | [http://localhost:3000/ui/panel](http://localhost:3000/ui/panel) | `beef` / Configured in `.env` |

### 4. Execute Test Suite
```bash
make test
```
Or run pytest directly:
```bash
pytest -v
```

### 5. Tear Down Environment
```bash
make down       # Stop containers
make reset      # Reset database volumes and restart fresh
```

---

## Repository Map

```
web-security-lab-portfolio/
├── README.md                           # Master repository documentation & architecture
├── LICENSE                             # MIT Open Source License
├── SECURITY.md                         # Ethics policy, disclosure policy & scope rules
├── CONTRIBUTING.md                     # Contribution guidelines and coding conventions
├── .gitignore                          # Strict gitignore preventing secret/artifact leakage
├── .env.example                        # Template environment variables (no real secrets)
├── Makefile                            # Automation recipes (up, down, reset, test, lint, report)
├── docker-compose.yml                  # Pinned services (dvwa, mysql, php-apache, zap, beef)
├── .github/workflows/ci.yml            # GitHub Actions CI workflow
├── docs/                               # Core foundational methodology documentation
│   ├── SETUP.md                        # Prerequisites & clean-machine setup guide
│   ├── METHODOLOGY.md                  # Build-test-review-fix development protocol
│   ├── EVIDENCE-POLICY.md              # Evidence labeling & artifact verification standard
│   ├── TOOL-VERSIONS.md                # Pinned tool versions & operator action items
│   └── GLOSSARY.md                     # Comprehensive cybersecurity terminology reference
├── assignment-01-web-php-http/         # Assignment 1: Web Technologies Fundamentals
│   ├── README.md                       # Assignment overview, objectives & results summary
│   ├── site/                           # 5 Semantic HTML5 pages (forms, tables, lists, register)
│   ├── php/                            # 10 PHP scripts + secure authentication system
│   ├── db/schema.sql                   # Relational database schema with seeded demo user
│   ├── http/                           # HTTP probe script & Burp Suite interception guides
│   ├── source-analysis/                # Automated DOM extractor, 10-websites audit & checklist
│   ├── devtools-console.md             # Browser DevTools console inspection snippets
│   └── results/                        # Execution evidence and probe outputs
├── assignment-02-server-hosting/       # Assignment 2: Web Server Hosting & Hardening
│   ├── README.md                       # Infrastructure architecture & hardening overview
│   ├── infra/apache/                   # Multi-site vhosts, .htaccess, and 403/404/500 error pages
│   ├── cloud-hosting/                  # Free-tier cPanel/FTP deployment guide & app package
│   ├── misconfig/                      # Automated misconfiguration & upload fuzzer scripts
│   └── results/                        # Misconfiguration scan logs and evaluation outputs
├── assignment-03-pentest/              # Assignment 3: Web Application Penetration Testing
│   ├── README.md                       # Methodology, testing outcomes & scanner comparison
│   ├── scanners/                       # Nikto & ZAP Docker wrappers + correlation engine
│   ├── manual-testing/                 # OWASP Top 10 checklist & DVWA level diff matrix
│   ├── tryhackme/                      # External lab documentation and progress trackers
│   ├── findings/                       # CVSS 3.1 scoring guide & standardized report template
│   └── results/                        # DAST scan comparisons and normalized tables
├── assignment-04-sqli/                 # Assignment 4: SQL Injection Mastery
│   ├── README.md                       # SQLi exploitation theory, detection & prevention
│   ├── payloads/                       # Curated payloads (auth bypass, union, error, blind)
│   ├── sqlmap/                         # Automated SQLMap cookbook & execution script
│   ├── scripts/                        # Custom Python Boolean & Time-based blind extractors
│   ├── walkthroughs/                   # Technical walkthroughs for Low, Medium, High tiers
│   └── results/                        # Manual vs automated extraction comparisons
├── assignment-05-xss/                  # Assignment 5: Cross-Site Scripting (XSS)
│   ├── README.md                       # XSS taxonomy, filter evasion & impact analysis
│   ├── payloads/                       # Curated payloads (reflected, stored, DOM, events, polyglot)
│   ├── harness/                        # 7 Contextual vulnerable pages & Playwright test matrix
│   ├── beef/                           # BeEF Docker configuration, hook guides & listener
│   ├── impact/                         # Contained cookie stealer, keylogger & redirect demos
│   ├── portswigger/                    # Web Security Academy interactive lab notes
│   └── results/                        # Headless Playwright execution matrix
└── reports/                            # Comprehensive Technical Reports & Evidentiary Audits
    ├── assignment-01-report.md         # A1 Detailed Technical & Verification Report
    ├── assignment-02-report.md         # A2 Detailed Technical & Verification Report
    ├── assignment-03-report.md         # A3 Detailed Technical & Verification Report
    ├── assignment-04-report.md         # A4 Detailed Technical & Verification Report
    ├── assignment-05-report.md         # A5 Detailed Technical & Verification Report
    ├── consolidated-report.md          # Master Consolidated Report & Definition-of-Done
    └── evidence-index.md               # Evidence Index mapping all claims to file paths
```

---

## Assignment Summary & Status Table

| Assignment | Focus Areas | Automated Artifacts | Status | Evidence Reference |
| :--- | :--- | :--- | :---: | :--- |
| **A1: Web Tech** | HTML5, PHP 8.x, HTTP Protocol | Site markup, 10 PHP scripts, Auth system, HTTP probe, Extractor, 10-sites audit | `AUTOMATED-PASS` | [`reports/assignment-01-report.md`](reports/assignment-01-report.md) |
| **A2: Hosting** | VHosts, Hardening, Misconfigurations | Apache vhosts, Error pages, Misconfig scanner, Upload fuzzer | `AUTOMATED-PASS` | [`reports/assignment-02-report.md`](reports/assignment-02-report.md) |
| **A3: Pentest** | OWASP Top 10, DAST Scanners, DVWA | Scanner scripts, Result correlation engine, Level diff matrix | `AUTOMATED-PASS` | [`reports/assignment-03-report.md`](reports/assignment-03-report.md) |
| **A4: SQLi** | Union, Error, Blind SQL Injection | Payload libraries, Custom Boolean & Time extractors, Unit tests | `AUTOMATED-PASS` | [`reports/assignment-04-report.md`](reports/assignment-04-report.md) |
| **A5: XSS** | Reflected, Stored, DOM XSS, Impact | 25+ Payloads, 7 test pages, Playwright matrix, 3 impact servers | `AUTOMATED-PASS` | [`reports/assignment-05-report.md`](reports/assignment-05-report.md) |

---

## Tool Versions

| Component | Pinned Version | Execution Scope |
| :--- | :--- | :--- |
| **MySQL Daemon** | `mysql:8.0.36` | Docker container backend |
| **DVWA** | `ghcr.io/digininja/dvwa:latest` | Docker target application |
| **PHP-Apache** | `php:8.2-apache` | Docker custom multi-vhost host |
| **OWASP ZAP** | `ghcr.io/zaproxy/zaproxy:stable` | Dockerized DAST scanning proxy |
| **BeEF** | `bcarvajal/beef-xss:latest` | Docker browser exploitation engine |
| **Python** | `3.11.x` | Local automation, tests, extractors |
| **pytest** | `9.2.x` | Unit test execution |
| **Playwright** | `1.40.x` | Headless browser XSS validation |

---

## About the Author & Project

This project was developed by the founder of **[vecesta.co](https://vecesta.co)** as an official assignment portfolio during an internship at **Unified Mentor** at the age of 17. The portfolio demonstrates technical excellence in secure software engineering, penetration testing, containerization, and defensive systems architecture.

Reference implementation: [ASSIGNMENT-1-WEB-TECHNOLOGIES](https://github.com/adityasasikumar422-ui/ASSIGNMENT-1-WEB-TECHNOLOGIES)

---

## License

This repository is licensed under the [MIT License](LICENSE).
All security educational materials and source code are provided without warranty for authorized training only.
