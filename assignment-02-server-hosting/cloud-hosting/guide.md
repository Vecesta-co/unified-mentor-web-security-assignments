# Free-Tier Cloud Hosting Deployment Guide
Assignment 02 — Web Server Hosting Lab (Task 2)

> **CRITICAL SECURITY DIRECTIVE:**  
> **NEVER upload DVWA or intentionally vulnerable lab applications to free or public cloud hosting.**  
> Free hosting accounts are publicly accessible across the Internet. Deploy ONLY the lightweight, benign demonstration application provided in `assignment-02-server-hosting/cloud-hosting/app/`.  
> Status: `[HUMAN-EXECUTED — PENDING]` (Follow these instructions when performing live deployment).

---

## 1. Provider Selection & Account Provisioning

Select a supported free-tier web hosting service:
- **InfinityFree** (`infinityfree.com`): Offers free PHP 8.x, MySQL, cPanel / VistaPanel, and FTP access without ads.
- **000webhost** or **ProFreeHost**: Alternative cPanel-compatible PHP platforms.

### Step-by-Step Provisioning:
1. Register with a disposable email address (or educational account).
2. Choose a free subdomain name (e.g. `vecesta-lab.epizy.com` or `student-sec-lab.infinityfreeapp.com`).
3. Complete account verification via email.
4. Note down the allocated server credentials from the hosting client area:
   - **FTP Hostname / Server IP**: e.g., `ftpupload.net`
   - **FTP Username**: e.g., `epiz_34567890`
   - **FTP Port**: `21` (FTP over TLS preferred)
   - **MySQL Hostname**: e.g., `sql205.infinityfree.com`
   - **MySQL Database Name**: e.g., `epiz_34567890_app`
   - **MySQL Username**: e.g., `epiz_34567890`

---

## 2. Database Setup via phpMyAdmin / cPanel

1. Log in to the hosting **Control Panel** (cPanel / VistaPanel).
2. Under **Databases**, select **MySQL Databases**.
3. Create a new database named `app` (resulting name will be prefixed by user id: `epiz_..._app`).
4. Click **phpMyAdmin** next to the created database.
5. In phpMyAdmin, click the **SQL** tab and run the creation script for the benign test table:
   ```sql
   CREATE TABLE IF NOT EXISTS guest_notes (
       id INT AUTO_INCREMENT PRIMARY KEY,
       author VARCHAR(50) NOT NULL,
       note TEXT NOT NULL,
       created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
   ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

   INSERT INTO guest_notes (author, note) 
   VALUES ('System Installer', 'Cloud deployment initial database handshake test.');
   ```

---

## 3. Configuring Application Credentials

Before uploading, open `assignment-02-server-hosting/cloud-hosting/app/db.php`:
1. Populate your cloud database credentials:
   ```php
   $host = 'sql205.infinityfree.com'; // Your allocated MySQL host
   $dbname = 'epiz_34567890_app';
   $user = 'epiz_34567890';
   $pass = 'YourSecretProviderPassword';
   ```
2. Save the file locally.
3. *Reminder*: Never commit your real provider password to Git; `.gitignore` is pre-configured to avoid tracking private credentials.

---

## 4. Live Verification & Evidence Collection

1. Open your assigned subdomain in your browser: `http://vecesta-lab.epizy.com/`
2. Verify the application status displays:
   - `[+] HTTP Server Response: 200 OK`
   - `[+] Database Connectivity: CONNECTED (MySQL via PDO)`
   - Table records rendered safely with `htmlspecialchars()`.
3. Capture a screenshot of the browser showing the URL bar and the successful connection status.
4. Save the screenshot to:
   `results/screenshots/a2-free-host-deployed.png`
