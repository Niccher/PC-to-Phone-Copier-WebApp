# Contributing Guidelines

This document outlines the contribution workflow, branch conventions, and quality standards for P2P Copier WebApp.

---

## 1. Branching Strategy

- **`main`**: Production-ready branch. Only merges from tested PRs.
- **`feature/<name>`**: New features, controllers, or UI views.
- **`bugfix/<name>`**: Bug fixes and patches.
- **`chore/<name>`**: Tooling, dependencies, and documentation updates.

---

## 2. Contribution Workflow

1. Fork or clone the repository:
   ```bash
   git checkout -b feature/my-feature-name
   ```
2. Implement your changes following CodeIgniter 4 best practices.
3. If changing API endpoints or database schemas, update:
   - `docs/api/contract.md`
   - `docs/architecture/data-and-storage.md`
4. Run documentation linting to verify no broken links or leaked secrets:
   ```bash
   python scripts/lint-docs.py .
   ```
5. Commit with clear, imperative messages:
   ```bash
   git commit -m "feat(api): add batch file deletion endpoint"
   ```
6. Push to your branch and open a Pull Request against `main`.
