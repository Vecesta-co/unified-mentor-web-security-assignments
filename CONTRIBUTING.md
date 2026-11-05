# Contributing to Web Security Lab Portfolio

Thank you for your interest in contributing! This is a closed academic portfolio.
External contributions are welcome for documentation and bug fixes only.

## Ground Rules

1. **Ethics first** — Never add payloads, scripts, or instructions targeting real external systems.
2. **No secrets** — Never commit real credentials, API keys, or personal data.
3. **Label honestly** — If something wasn't actually run, label it `[HUMAN-EXECUTED — PENDING]`.
4. **Test everything** — Run `make test` before submitting a PR.

## How to Contribute

1. Fork the repository
2. Create a feature branch: `git checkout -b feat/your-feature`
3. Make your changes with clear commit messages (e.g., `docs(a1): clarify burp proxy steps`)
4. Run `make lint` and `make test`
5. Open a Pull Request with a description of what changed and why

## Commit Message Format

```
<type>(<scope>): <short description>

Types: feat, fix, docs, test, chore, refactor
Scopes: a1, a2, a3, a4, a5, ci, docker, root
```

## Code Style

- Python: PEP 8, type hints where practical, docstrings on all public functions
- PHP: PSR-12 style, comments on every file header
- Shell: `shellcheck`-clean
- HTML: valid HTML5, `htmlhint`-clean
- Markdown: consistent heading hierarchy, no bare URLs
