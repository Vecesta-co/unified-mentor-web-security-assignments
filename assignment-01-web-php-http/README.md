# Assignment 01 — HTML, PHP & HTTP Fundamentals

## Objective
Build a multi-page HTML5 website, a suite of PHP scripts covering core language concepts,
a secure login system, and HTTP analysis tools that demonstrate understanding of web
foundations and their security implications.

## Learning Outcomes
- Construct semantic, accessible HTML5 pages with forms, tables, and lists
- Write PHP scripts covering variables, arrays, functions, loops, file I/O, and DB access
- Implement a secure login system with password hashing, prepared statements, and CSRF protection
- Analyze HTTP requests/responses using Python and Burp Suite
- Extract and audit HTML source for security-relevant artifacts (forms, hidden fields, comments)

## What's Automated vs Human

| Task | Status | Evidence |
|------|--------|---------|
| 5 HTML5 pages (site/) | AUTOMATED-PASS | Linted by htmlhint |
| 10 PHP scripts (php/) | AUTOMATED-PASS | Linted by `php -l` |
| Login system (PHP) | AUTOMATED-PASS | `php -l` passed |
| DB schema (schema.sql) | AUTOMATED-PASS | Syntax reviewed |
| http_probe.py | AUTOMATED-PASS | `python -m py_compile` |
| extractor.py + unit test | AUTOMATED-PASS | `pytest` passed |
| devtools-console.md | NOT APPLICABLE (GUI/LIVE) | Manual browser task |
| Burp Suite capture | HUMAN-EXECUTED — PENDING | See http/burp-notes.md |

## Setup

```bash
# Start the lab environment
make up

# PHP app will be at: http://localhost:8081
```

## How to Run

```bash
# Run the HTTP probe
python assignment-01-web-php-http/http/http_probe.py --target http://localhost:8081

# Route through Burp (requires Burp running on 127.0.0.1:8080)
python assignment-01-web-php-http/http/http_probe.py \
  --target http://localhost:8081 \
  --proxy http://127.0.0.1:8080

# Run the source extractor on a saved HTML file
python assignment-01-web-php-http/source-analysis/extractor.py \
  assignment-01-web-php-http/source-analysis/fixtures/sample.html

# Run unit tests
python -m pytest assignment-01-web-php-http/source-analysis/ -v
```

## Results Summary

| Artifact | File | Status |
|----------|------|--------|
| HTML pages | site/*.html | AUTOMATED-PASS |
| PHP scripts | php/*.php | AUTOMATED-PASS |
| Login system | php/login.php, auth.php, dashboard.php, logout.php | AUTOMATED-PASS |
| DB schema | db/schema.sql | AUTOMATED-PASS |
| HTTP probe | http/http_probe.py | AUTOMATED-PASS |
| Extractor | source-analysis/extractor.py | AUTOMATED-PASS |

## Reflections / Lessons Learned

- Semantic HTML (`<main>`, `<nav>`, `<article>`) aids both accessibility and security analysis.
- PDO prepared statements completely prevent SQLi when used correctly.
- `password_hash()`/`password_verify()` with `PASSWORD_BCRYPT` is the correct PHP pattern.
- Session regeneration on login prevents session fixation attacks.
- CSRF tokens must be tied to the session and validated server-side on every state-changing request.
- Hidden form fields are visible to any user — never use them for authorization decisions.

## References
- [PHP Manual — PDO](https://www.php.net/manual/en/book.pdo.php)
- [OWASP PHP Security Cheat Sheet](https://cheatsheetseries.owasp.org/cheatsheets/PHP_Configuration_Cheat_Sheet.html)
- [OWASP CSRF Prevention](https://cheatsheetseries.owasp.org/cheatsheets/Cross-Site_Request_Forgery_Prevention_Cheat_Sheet.html)
