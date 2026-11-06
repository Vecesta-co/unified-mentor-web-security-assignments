# 10 Websites Source Code & Technology Stack Analysis
Assignment 01 — Web Technologies Fundamentals (Task 4)

This report fulfills the Task 4 requirements:
1. View page source on 10 diverse web applications.
2. Audit for hidden form fields, sensitive developer comments, and potential info leaks.
3. Identify underlying web frameworks, servers, and technologies using Wappalyzer.
4. Extract form actions, HTTP methods, and client-side input validation rules.

---

## Comparative Technology & Source Audit Matrix

| # | Target Domain / Application | Web Server / Back-End (Wappalyzer) | Front-End & Libraries | Hidden Fields Discovered | Comments & Info Disclosure | Form Actions & Validation Controls |
| :- | :--- | :--- | :--- | :--- | :--- | :--- |
| **1** | **Local DVWA (vulnerables/web-dvwa)** | Apache 2.4, PHP 7.4/8.x, MySQL | Procedural PHP, Vanilla JS | `user_token` (CSRF token in high mode) | Detailed PHP configuration warnings commented out in setup | Action: `vulnerabilities/sqli/`; Method: `GET`; No client validation. |
| **2** | **OWASP Juice Shop (Sandbox)** | Node.js, Express, SQLite | Angular, Material Design | `redirect_url`, OAuth client state | Angular template comments; source maps exposed in dev mode | Action: `/api/Users/`; Method: `POST`; Client regex on email & password. |
| **3** | **Local Custom Hardened App (site-a)** | Apache 2.4, PHP 8.2 | Semantic HTML5, Custom CSS | `csrf_token` (64-character hex hash) | Zero developer comments in production; stripped in build | Action: `/php/08_forms.php`; Method: `POST`; `required`, `pattern`, `minlength`. |
| **4** | **Wikipedia (wikipedia.org)** | Apache / Envoy proxy, PHP (MediaWiki) | OOUI, jQuery, ResourceLoader | `wpEditToken`, `title`, `parentRevId` | Build commit hashes and server cluster names in HTML headers | Action: `/w/index.php`; Method: `POST`; Extensive input constraints. |
| **5** | **GitHub Login (github.com/login)** | Custom reverse proxy (Unicorn / Ruby) | Primer CSS, Rails UJS, Web Components | `authenticity_token`, `required_field_...` | Clean HTML; client assets bundled with cryptographic SRI hashes | Action: `/session`; Method: `POST`; Strict client + WebAuthn challenge. |
| **6** | **Stack Overflow (stackoverflow.com)** | Microsoft-IIS, ASP.NET Core MVC | jQuery, Stimulus, Custom CSS | `fkey` (Anti-CSRF session token) | Minified HTML; minor CDN script comments | Action: `/users/login`; Method: `POST`; Strict email format & length validation. |
| **7** | **Reddit (reddit.com/login)** | Fastly CDN, Node.js GraphQL Gateway | React, Redux, Styled Components | `csrf_token`, `client_id`, `state` | React bundle source references and component hierarchy tags | Action: `/accounts/login`; Method: `POST`; Dynamic asynchronous validation. |
| **8** | **Hacker News (news.ycombinator.com)** | nginx, Arc / Racket | Minimalist pure HTML, no frameworks | `fnid` (Form identification and CSRF) | Bare minimal markup; table-based layout; no extraneous comments | Action: `login`; Method: `POST`; Basic `required` input validation. |
| **9** | **Internet Archive (archive.org)** | Apache, Python/PHP backend | jQuery, Bootstrap 3 | `referer`, `session_token` | Embedded legacy metadata comments and catalog collection IDs | Action: `/account/login`; Method: `POST`; HTML5 standard validation. |
| **10** | **PortSwigger Academy Lab Target** | Apache / Nginx proxy, Java backend | Vanilla JS, Custom Lab CSS | `csrf`, `session` tokens | Lab reset reminders and developer instruction comments | Action: `/login`; Method: `POST`; Basic HTML5 form validation. |

---

## Detailed Case Studies

### Target 1: Damn Vulnerable Web Application (DVWA - Localhost)
- **Wappalyzer Identification**:
  - Web Server: Apache
  - Programming Language: PHP
  - Database: MySQL
- **Hidden Fields**:
  - Investigating `/vulnerabilities/csrf/` revealed: `<input type="hidden" name="user_token" value="..." />` under High security, absent in Low security.
- **Form Extraction**:
  - SQLi endpoint: `<form action="#" method="GET"><input type="text" name="id" /><input type="submit" name="Submit" value="Submit" /></form>`
  - Lack of client validation (`pattern`, `maxlength`) allows arbitrary input payloads.

### Target 3: Custom Hardened Lab App (`assignment-01-web-php-http`)
- **Wappalyzer Identification**:
  - Web Server: Apache 2.4.58
  - Programming Language: PHP 8.2
  - Standards: HTML5, Accessible ARIA
- **Hidden Fields**:
  - Extracted: `<input type="hidden" name="csrf_token" value="6f8a..."/>`
- **Validation**:
  - Name: `pattern="[A-Za-z\s\-']+"`, `required`, `minlength="2"`, `maxlength="80"`.
  - Password: `pattern="(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[@$!%*?&]).{12,}"`.
- **Security Takeaway**: Both client-side and server-side validation are required. Client-side validation improves user experience, but only server-side validation defends the application from manipulated requests.

---

## Key Security Conclusions
1. **Hidden Fields are Public**: Hidden input fields are completely visible to end users and attackers via page source inspection. Storing sensitive data, pricing, or authorization role flags in hidden fields without cryptographic server verification introduces critical vulnerabilities.
2. **Comment Hygiene**: HTML comments can disclose internal paths, API test credentials, and architecture hints to reconnaissance tools like `extractor.py`. Automated CI build pipelines must strip comments prior to public deployment.
3. **Wappalyzer Fingerprint Mitigation**: Hardened web servers suppress disclosure headers (`ServerTokens Prod`, `Header unset X-Powered-By`) to raise the cost of reconnaissance for attackers.

