# ADR-0003: On-Device ML Kit OCR Processing

- **Status**: Accepted
- **Date**: 2026-08-20
- **Deciders**: Architecture Team

---

## Context and Problem Statement

A key capability of the Android client is extracting printed or handwritten text from physical documents (receipts, whiteboards, notes) and transferring the raw text to the desktop browser.
Optical Character Recognition (OCR) can either be performed on the backend server (requiring the phone to upload full-resolution multi-megabyte JPEG bitmaps) or on the client device using an embedded ML engine.

---

## Decision Drivers

- Network bandwidth consumption over local Wi-Fi / cellular hotspots.
- Server CPU / memory scalability (image processing and neural network inference are CPU/GPU-intensive).
- User privacy (avoiding transmitting private document imagery when only text was requested).
- Latency and offline responsiveness.

---

## Decision Outcome

Chosen option: **On-Device OCR using Google ML Kit (`com.google.mlkit:text-recognition:16.0.0`)**.

### Implementation Details
- `Handle_Text_2_Image.java` captures photos using the camera hardware.
- The image is processed entirely on the Android device using ML Kit's local on-device neural model.
- Only the lightweight extracted text string (typically < 2 KB) is transmitted to the server via `POST /api/v1/texts` with `source: "OCR"`.
- The raw image is discarded or kept only in client cache, without ever hitting the WebApp server.

### Consequences
- **Positive**: Server requires zero GPU/CPU OCR libraries (no Tesseract or heavy Python OCR dependencies on the PHP relay).
- **Positive**: Sub-second text extraction and transfer.
- **Positive**: High privacy protection for sensitive personal documents.
- **Negative**: Increases Android APK size slightly due to ML Kit bundled model libraries.
