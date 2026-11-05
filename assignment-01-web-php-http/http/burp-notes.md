# Burp Suite Interception & HTTP Analysis Guide
Assignment 01 — Web Technologies Fundamentals

> **Target Scope:** Strictly `127.0.0.1`, `localhost`, and local Docker containers (`php-apache` at `http://localhost:8081`).  
> **Status:** `[HUMAN-EXECUTED — PENDING]` (Follow these reproducible instructions during GUI capture).

---

## 1. Environment Setup

1. **Launch Burp Suite Community / Professional Edition**:
   - Navigate to **Proxy** → **Proxy Settings**.
   - Verify the proxy listener is bound to `127.0.0.1:8080` and status is **Running**.
2. **Browser Configuration**:
   - Use Burp's built-in Chromium browser (**Proxy** → **Open browser**), which is pre-configured with Burp's CA certificate.
   - Or configure your system browser to route HTTP/HTTPS traffic through `127.0.0.1:8080` and install Burp's CA cert from `http://burp/cert`.
3. **Target Scope Definition**:
   - Go to **Target** → **Scope settings**.
   - Check **Use advanced scope control**.
   - Add rule: Protocol `HTTP/HTTPS`, Host or IP range `127\.0\.0\.1|localhost`, Port `8081|8080`.
   - Enable "Show only in-scope items" in Proxy HTTP History to filter out noise.

---

## 2. Lab Exercises (As Specified in Assignment 1)

### Exercise A: Intercept HTTP Requests & Real-Time Parameter Tampering
1. In Burp Suite, turn **Intercept is on** (**Proxy** → **Intercept**).
2. In the browser, navigate to `http://localhost:8081/site/forms.html`.
3. Fill out the contact form:
   - Full Name: `Test Auditor`
   - Email: `auditor@vecesta.co`
   - Subject: `Security Audit Query`
   - Message: `Testing client-side parameter modification.`
4. Click **Send Message**. The browser will pause as Burp intercepts the outgoing HTTP `POST` request.
5. In Burp's Intercept tab, observe the raw request:
   ```http
   POST /php/08_forms.php HTTP/1.1
   Host: localhost:8081
   User-Agent: Mozilla/5.0 ...
   Content-Type: application/x-www-form-urlencoded
   Content-Length: 142
   Cookie: PHPSESSID=...

   csrf_token=9a8b7c...&username=Test+Auditor&email=auditor%40vecesta.co&category=general&message=Testing+client-side+parameter+modification.
   ```
6. **Parameter Tampering**:
   - Modify the `category` value from `general` to an invalid value e.g. `category=unauthorized_admin`.
   - Modify the `username` to test boundary limits or injection characters (`<b style="color:red">Test</b>`).
7. Click **Forward** to send the modified request to the server.
8. Inspect the response rendered in the browser. Verify whether server-side validation correctly caught the tampered parameter or if it reflected unescaped content.

---

### Exercise B: Observe & Analyze Cookies and Session Tokens
1. Navigate to the login portal at `http://localhost:8081/php/login.php`.
2. Inspect the HTTP response headers in Burp's **HTTP history**:
   ```http
   HTTP/1.1 200 OK
   Set-Cookie: PHPSESSID=5f8d9a2b4c1e...; path=/; HttpOnly; SameSite=Lax
   ```
3. **Audit Cookie Security Flags**:
   - `HttpOnly`: **Present** — Prevents client-side JavaScript (`document.cookie`) from accessing the session token, mitigating XSS cookie theft.
   - `SameSite=Lax`: **Present** — Defends against Cross-Site Request Forgery (CSRF) on cross-origin requests.
   - `Secure`: **Note** — In local HTTP lab testing, `Secure` is disabled; in production with TLS/HTTPS, the `Secure` flag must be set to prevent plaintext transmission.
4. Log in using `demo_user` / `Demo@Password123!`.
5. Observe the response: verify that `session_regenerate_id(true)` issued a **new** `PHPSESSID` cookie, preventing **Session Fixation** attacks.

---

### Exercise C: HTTP Headers Deep Dive
Analyze the headers present in local requests and responses using Burp's Inspector:

| Header | Sample Value | Security Role | Analysis |
| :--- | :--- | :--- | :--- |
| `User-Agent` | `Mozilla/5.0 (Windows NT 10.0; Win64; x64)...` | Identifies browser engine & client OS | Can be spoofed trivially; never use for authentication. |
| `Content-Type` | `application/x-www-form-urlencoded` | Declares MIME type of request body | Ensures PHP populates `$_POST` superglobal properly. |
| `Content-Security-Policy` | `default-src 'self'` | Restricts allowable source domains for scripts/styles | Mitigates XSS execution and data exfiltration. |
| `X-Frame-Options` | `SAMEORIGIN` | Governs whether page can be framed | Prevents UI clickjacking attacks. |
| `X-Content-Type-Options` | `nosniff` | Disables browser MIME-sniffing | Enforces declared Content-Type header. |

---

## 3. Evidence Capture Template

When executing this exercise, capture screenshots of:
1. `results/screenshots/a1-burp-intercept.png` — Burp Intercept tab showing a paused `POST` request with highlighted parameter modification.
2. `results/screenshots/a1-burp-cookies.png` — HTTP History showing `Set-Cookie` with `HttpOnly` and `SameSite` flags.
3. `results/screenshots/a1-burp-headers.png` — Response headers showing security controls (`X-Frame-Options`, `Content-Security-Policy`).
