# Continuous Integration & Automation Pipelines

This document details the automated Continuous Integration (CI) workflows, documentation linters, and testing suites configured for the **P2P Copier WebApp**.

---

## 1. CI Workflow Inventory

The repository uses GitHub Actions located in `.github/workflows/`:

| Workflow File | Trigger Events | Purpose | Execution Environment |
|---|---|---|---|
| [`.github/workflows/docs-verify.yml`](../../.github/workflows/docs-verify.yml) | Push & PR (`main`, `master`) touching `README.md`, `docs/**` | Documentation quality linter (line counts, link integrity, Mermaid syntax, secret detection) | `ubuntu-latest` (Python 3.12) |

---

## 2. Documentation Quality Linter (`docs-verify.yml`)

The documentation verification workflow validates that all engineering and user documents adhere to the `project-docs` v3.1 specification:

```yaml
name: Verify Documentation Quality

on:
  push:
    branches: [ main, master ]
    paths:
      - 'README.md'
      - 'docs/**'
  pull_request:
    paths:
      - 'README.md'
      - 'docs/**'

jobs:
  lint-docs:
    runs-on: ubuntu-latest
    steps:
      - name: Checkout Repository
        uses: actions/checkout@v4

      - name: Set up Python
        uses: actions/setup-python@v5
        with:
          python-version: '3.12'

      - name: Run project-docs Quality Linter
        run: |
          python3 scripts/lint-docs.py .
```

### Checks Executed:
1. **README Length Check**: Enforces root `README.md` is strictly user-focused and $\le 150$ lines.
2. **Code Leak Prevention**: Flags any controller generation, database migration, or code editing instructions inside `README.md`.
3. **Markdown Link Integrity**: Verifies all internal relative links resolve to existing markdown files without broken targets.
4. **Mermaid Block Syntax**: Ensures all Mermaid diagram fences are properly formed and balanced.
5. **Secret Leakage Detection**: Scans for accidental inclusion of private keys, service account JSONs, hardcoded credentials, or JWT tokens.

---

## 3. Running Verification Locally

Before pushing code or opening a Pull Request, engineers can run the linter locally:

```bash
python3 scripts/lint-docs.py .
```

To run PHPUnit test suites:

```bash
vendor/bin/phpunit
```
