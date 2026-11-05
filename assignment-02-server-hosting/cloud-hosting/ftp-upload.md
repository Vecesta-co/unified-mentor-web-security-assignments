# FTP Client Configuration & File Upload Guide
Assignment 02 — Web Server Hosting Lab (Task 2)

> **Status:** `[HUMAN-EXECUTED — PENDING]`  
> This guide details configuring FileZilla or command-line FTP to upload the benign cloud demonstration application to a remote hosting environment.

---

## 1. FileZilla Client Setup

1. **Download & Launch FileZilla Client** (or WinSCP on Windows).
2. Open **File** → **Site Manager** (`Ctrl + S`).
3. Click **New Site** and name it `InfinityFree-Lab`.
4. Configure connection parameters:
   - **Protocol**: `FTP - File Transfer Protocol`
   - **Host**: Enter your assigned FTP host (e.g., `ftpupload.net`)
   - **Port**: `21`
   - **Encryption**: `Require explicit FTP over TLS if available`
   - **Logon Type**: `Ask for password` (or `Normal`)
   - **User**: Your hosting username (e.g., `epiz_34567890`)
5. Click **Connect** and enter your password.
6. Accept the TLS certificate prompt after verifying the hostname matches your hosting provider.

---

## 2. Directory Navigation & Transfer

1. On the **Remote Site** pane (right side):
   - Double-click to enter the public web root directory:
     - On InfinityFree: `/htdocs/`
     - On standard cPanel: `/public_html/`
2. On the **Local Site** pane (left side):
   - Navigate to: `assignment-02-server-hosting/cloud-hosting/app/`
   - Select both files:
     - `index.php`
     - `db.php`
3. Right-click the selected files and click **Upload**.
4. Confirm in the **Transfer Queue** tab at the bottom that both files transferred successfully with `0 Failed Transfers`.

---

## 3. Command-Line FTP Alternative (lftp / curl)

If performing headless deployment from Kali Linux or WSL:

```bash
# Uploading via curl over FTP with TLS
curl --ftp-ssl -u "epiz_34567890:YourPassword" \
     -T assignment-02-server-hosting/cloud-hosting/app/index.php \
     ftp://ftpupload.net/htdocs/index.php

curl --ftp-ssl -u "epiz_34567890:YourPassword" \
     -T assignment-02-server-hosting/cloud-hosting/app/db.php \
     ftp://ftpupload.net/htdocs/db.php
```

---

## 4. Post-Upload File Permission Verification

Ensure transferred files have standard read permissions:
- PHP files: `644` (`-rw-r--r--`)
- Web root directory: `755` (`drwxr-xr-x`)
Avoid world-writable `777` permissions, as shared hosting environments with suPHP / FastCGI will return `500 Internal Server Error` on files with excessive write permissions.
