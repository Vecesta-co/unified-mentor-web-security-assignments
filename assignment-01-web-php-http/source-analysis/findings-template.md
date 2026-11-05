# Source Code Finding Documentation Template

---

### Finding ID: `[SEC-SA-001]`
**Title:** [Clear, descriptive title of vulnerability, e.g., Missing CSRF Token in Password Reset Form]  
**Severity:** Critical | High | Medium | Low | Informational  
**OWASP Category:** [e.g., A01:2021-Broken Access Control / A05:2021-Security Misconfiguration]  
**CWE Reference:** [e.g., CWE-352: Cross-Site Request Forgery (CSRF)]  
**Affected Target / File:** `[e.g., /assignment-01-web-php-http/site/register.html]`  

---

#### 1. Vulnerability Description
[Detailed explanation of the issue discovered during source review or automated parsing. Explain the mechanics of the flaw and why it introduces security risk.]

#### 2. Technical Evidence & Extracted Code Snippet
```html
<!-- Paste extracted form markup, comment snippet, or extractor.py JSON output here -->
<form method="POST" action="/api/v1/update-profile">
    <input type="text" name="email" value="user@example.com">
    <!-- Notice absence of anti-forgery token -->
</form>
```

#### 3. Exploit Scenario & Threat Impact
[Describe how an adversary could exploit this weakness in a practical attack scenario, such as submitting unauthorized profile updates on behalf of a victim user who visits an attacker-controlled page.]

#### 4. Remediation Standard & Secure Code Example
[Provide actionable guidance and the corrected code implementation demonstrating defense-in-depth.]

```php
<!-- Corrected implementation with CSRF token generation and validation -->
<form method="POST" action="/api/v1/update-profile">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">
    <input type="text" name="email" value="<?= htmlspecialchars($email, ENT_QUOTES, 'UTF-8') ?>">
    <button type="submit">Update</button>
</form>
```
