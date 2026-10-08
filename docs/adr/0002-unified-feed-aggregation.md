# ADR-0002: Server-Side Unified Feed Aggregation (`/api/v1/uploaded`)

- **Status**: Accepted
- **Date**: 2026-08-16
- **Deciders**: Architecture Team

---

## Context and Problem Statement

The P2P Copier application handles disparate data entities: uploaded binary files (PDFs, images, videos, audio) and text records (typed text notes, camera OCR text extractions, and QR code scan outputs).
The mobile UI requires displaying a chronological history stream of everything transferred in the active session.
Should the mobile client poll two separate REST endpoints (`/api/v1/files/list` and `/api/v1/texts/list`) and manually merge and sort them, or should the backend provide a unified aggregation endpoint?

---

## Decision Drivers

- Mobile battery and radio efficiency (minimizing redundant HTTP roundtrips).
- Pagination and chronological sorting consistency (`created_at` DESC).
- Simplified client state management in `Adapter_Uploaded_Files`.

---

## Decision Outcome

Chosen option: **Server-Side Aggregation via `POST /api/v1/uploaded`**.

### Implementation Details
- `FilesApi::uploaded` queries `tbl_files` and `tbl_texts` for the authenticated `session_uuid`.
- Items are tagged with a polymorphic discriminator attribute (`type: "file"` or `type: "text"`).
- The combined list is sorted server-side by `created_at` descending and returned in a single atomic JSON response envelope.

### Consequences
- **Positive**: Single HTTP roundtrip populates the entire mobile "Uploaded" screen.
- **Positive**: No race conditions or ordering discrepancies between files and texts.
- **Negative**: Adds slight backend overhead to normalize different table schemas into a unified JSON representation.
