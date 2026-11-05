# Testing Methodology

## Build → Test → Review → Fix Loop

For every milestone in this portfolio we follow a strict four-pass process:

### Pass 1 — Build
Write all code, configs, and documentation for the milestone in full.
No `...` placeholders. No `TODO` stubs in runnable code.

### Pass 2 — Self-Test
Execute everything that can be executed:
- `php -l` on all PHP files
- `python -m py_compile` + `pytest` on all Python
- `shellcheck` on all shell scripts
- `htmlhint` on all HTML pages
- `docker compose config` on the compose file
- `make test` for the full harness

Capture real output into `results/<milestone>.log`. Never fabricate terminal output.

### Pass 3 — Adversarial Review
Switch to the perspective of a hostile senior reviewer and answer in writing:
1. What would break on a clean machine?
2. Which paths are hardcoded / assumed?
3. Which README claims are not backed by a file in `results/`?
4. Which scripts could be pointed at a non-localhost host?
5. What is untested and is it honestly labeled?

Document findings in `reports/review-<milestone>.md`.

### Pass 4 — Fix & Re-Run
Apply fixes, re-run Pass 2, and append the re-run output to the same log file.
Update `reports/evidence-index.md` so every documentation claim maps to a file path.

---

## DVWA Security Levels

| Level | What Changes | Lab Purpose |
|-------|-------------|------------|
| **Low** | No sanitization, no CSRF protection | Demonstrate raw vulnerability |
| **Medium** | Basic blacklisting / magic quotes | Show blacklist bypass techniques |
| **High** | Token-based CSRF, stricter validation | Show high-effort bypass or accepted limitations |
| **Impossible** | Parameterized queries, full escaping | Understand what secure code looks like |

---

## Status Labels Used Throughout

| Label | Meaning |
|-------|---------|
| `AUTOMATED-PASS` | Script or test ran and produced verifiable output |
| `HUMAN-EXECUTED — PENDING` | Requires a human at a GUI or live service; not yet completed |
| `NOT APPLICABLE (GUI/LIVE)` | Cannot be automated in this environment |
