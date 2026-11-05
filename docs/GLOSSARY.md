# Glossary

| Term | Definition |
|------|-----------|
| **DVWA** | Damn Vulnerable Web Application — an intentionally vulnerable PHP/MySQL app for security training |
| **XSS** | Cross-Site Scripting — injection of client-side scripts into web pages viewed by other users |
| **SQLi** | SQL Injection — insertion of malicious SQL into queries via user input |
| **CSRF** | Cross-Site Request Forgery — tricking a victim's browser into making authenticated requests |
| **LFI** | Local File Inclusion — including local server files via path traversal in file parameters |
| **RFI** | Remote File Inclusion — including remote files via URL in file parameters |
| **BeEF** | Browser Exploitation Framework — a penetration testing tool focused on web browser vulnerabilities |
| **ZAP** | OWASP Zed Attack Proxy — open-source web app security scanner |
| **Nikto** | Web server scanner that checks for dangerous files, outdated server software, and misconfigurations |
| **SQLMap** | Automated SQL injection detection and exploitation tool |
| **OWASP** | Open Web Application Security Project — nonprofit producing web security standards |
| **CVSS** | Common Vulnerability Scoring System — standardized framework for rating vulnerability severity |
| **PDO** | PHP Data Objects — PHP extension for database access with prepared statement support |
| **DOM** | Document Object Model — the in-memory tree representation of an HTML/XML document |
| **Allowlist** | A list of permitted values; anything not on the list is denied (preferred term over "whitelist") |
| **Payload** | A string or data crafted to trigger a vulnerability when processed by a target system |
| **Reflected XSS** | XSS where the payload is reflected immediately in the response (not stored) |
| **Stored XSS** | XSS where the payload is persisted in a database and served to future visitors |
| **DOM XSS** | XSS triggered entirely in the client-side DOM without a server round-trip |
| **Blind SQLi** | SQL injection where results are inferred from application behavior rather than displayed |
| **UNION attack** | SQLi technique using UNION SELECT to append data from other tables to query results |
| **Error-based SQLi** | SQLi technique that extracts data via database error messages |
| **Boolean-based blind** | Blind SQLi that sends true/false conditions and observes response differences |
| **Time-based blind** | Blind SQLi that infers data by measuring response delays from sleep/benchmark functions |
