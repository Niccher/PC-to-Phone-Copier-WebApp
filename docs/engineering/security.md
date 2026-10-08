# Security, Authorization & Session Isolation

This document outlines security practices, multi-tenant session isolation, and IDOR prevention within P2P Copier WebApp.

---

## 1. Multi-Tenant Session Isolation & Query Scoping

In P2P Copier, content (files, texts, notes, and scans) is segregated by `session_uuid`.

### Operator Precedence in Query Scoping
To prevent cross-tenant data leaks when filtering with complex conditions, always wrap queries inside `groupStart()` and `groupEnd()`:

```php
// SECURE: Strict session boundary maintained
$items = $this->modUpload
    ->where('session_id', $sessionUuid)
    ->groupStart()
        ->where('file_type', 'pdf')
        ->orWhere('file_type', 'doc')
    ->groupEnd()
    ->findAll();
```

---

## 2. Insecure Direct Object Reference (IDOR) Prevention

Controllers modifying or deleting resources must verify that the requesting device/session owns the target entity:

- **File Deletion**: Ensure `$file['session_id'] === $activeSessionId` before invoking soft-delete.
- **Text Deletion**: Ensure `$text['session_id'] === $activeSessionId` and `$text['device_uuid'] === $requestingDevUuid`.
- **Download Gate**: Validate session context before streaming file contents from `writable/uploads/`.

---

## 3. Secret Scanning & Environment Hygiene

- **No Secrets in Git**: Never commit `.env` or files containing database passwords, private keys, or API tokens.
- **Gitignore Protection**: `.env`, `.env.local`, and `writable/uploads/*` must remain in `.gitignore`.
- **Key Generation**: Generate cryptographic encryption keys using the CLI:
  ```bash
  php spark key:generate
  ```
