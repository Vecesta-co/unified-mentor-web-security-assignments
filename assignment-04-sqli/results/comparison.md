# Manual vs. Automated SQLMap Exploitation Comparison Matrix
Assignment 04 — SQL Injection Mastery

This matrix compares the methodology, detection speed, payload footprint, and extraction accuracy of manual SQL injection testing versus automated SQLMap execution against the local DVWA lab environment.

---

## 1. Technical Comparison Matrix

| Evaluation Dimension | Manual Testing Approach | Automated SQLMap Execution | Match (Y/N) | Analysis & Operational Notes |
| :--- | :--- | :--- | :---: | :--- |
| **Vulnerability Detection** | Single quote (`'`) injection observing database syntax errors | Automated heuristics with multi-payload differential testing | **Y** | Both approaches reliably identified SQLi on `id` parameter. |
| **DBMS Fingerprinting** | Query trial: `version()`, `@@version_comment` | Automated banner analysis and dialect fingerprinting (`--dbms=mysql`) | **Y** | Both accurately identified MySQL / MariaDB 8.x/10.x backend. |
| **Column Count Determination** | Incremental `ORDER BY 1, 2, 3...` | Automated bisect algorithm testing `ORDER BY` and `GROUP BY` | **Y** | Both identified exactly 2 columns in the original query. |
| **Techniques Successfully Applied** | UNION-based, Error-based (UpdateXML), Boolean Blind, Time Blind | UNION query, Boolean blind, Time-based blind, Error-based (`--technique=BEUSTQ`) | **Y** | Identical coverage across all 4 primary SQLi families. |
| **Filter Bypass (Medium Level)** | Manual numeric syntax without quotes (`1 OR 1=1`) | Automated detection that parameter was numeric integer type | **Y** | Both recognized `mysqli_real_escape_string` had no effect on unquoted integers. |
| **Filter Bypass (High Level)** | Appended `#` to neutralize `LIMIT 1` and single quotes | Requires `--csrf-token` / `--second-order` parameters | **Y** | Manual testing required less configuration overhead than configuring SQLMap second-order flows. |
| **Extraction Throughput** | Slow for blind character-by-character extraction (linear/manual) | Extremely rapid; uses multi-threaded bisection binary search | **N** | SQLMap extracted database schema in ~15 seconds; manual blind took > 10 minutes. |
| **Network & Log Footprint** | Extremely low (3–5 focused requests) | High (hundreds to thousands of diagnostic probes without tuning) | **N** | Manual testing is stealthy; SQLMap is noisy and easily flagged by WAF/IDS. |

---

## 2. Definitive Summary
- **Manual Assessment Value**: Critical for nuanced logic flaws, complex multi-step state transitions (e.g. DVWA High session input popups), and strictly controlled low-noise evasion.
- **Automated SQLMap Value**: Unmatched efficiency for full-schema dumps, automated password hash harvesting, and systematic validation once a parameter is confirmed vulnerable.
