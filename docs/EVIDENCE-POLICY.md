# Evidence Policy

## What Counts as Evidence

| Type | Acceptable | Location |
|------|-----------|----------|
| Automated test log | Yes — `results/*.log` with timestamp | `results/` per assignment |
| Script stdout captured to file | Yes — via `tee` or redirection | `results/` per assignment |
| Burp Suite screenshot | Yes — `.png` saved to `results/screenshots/` | Per assignment |
| Manual HTTP response | Yes — saved raw text | `results/` per assignment |
| "I ran it and it worked" | No — must have artifact | — |

## Evidence Labeling Rules

1. Every claim in a report must map to an artifact via `reports/evidence-index.md`.
2. Automated evidence is labeled `AUTOMATED-PASS` with a timestamp and tool version.
3. Human-executed evidence (screenshots, Burp captures) is labeled `[HUMAN-EXECUTED — PENDING]` until the screenshot/log is committed.
4. Never invent tool output. Never copy-paste "example" output as if it were real.

## Screenshot Naming Convention

```
results/screenshots/<assignment>-<tool>-<finding>-<YYYYMMDD>.png
```

Example: `results/screenshots/a3-zap-xss-reflected-20241201.png`
