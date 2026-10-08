# ADR-0001: Hybrid QR Code & 6-Digit Numeric Pairing

- **Status**: Accepted
- **Date**: 2026-08-14
- **Deciders**: Architecture Team

---

## Context and Problem Statement

To enable peer-to-peer file and data transfer between an untrusted web browser and a mobile Android client over a local network, a session handshake mechanism is required. 
Relying solely on user accounts and passwords creates high friction for a utility intended for quick ad-hoc transfers. Conversely, relying solely on camera QR scanning fails when testing on Android Virtual Devices (emulators), on devices with defective cameras, or in poor lighting environments.

---

## Decision Drivers

- Zero user registration / account requirement.
- Frictionless mobile pairing (< 5 seconds to pair).
- High reliability across diverse hardware (physical smartphones, tablets, and emulators).
- Security against arbitrary brute-forcing on local networks.

---

## Considered Options

1. **User Account / Password Authentication**: Standard login flow.
2. **QR Code Only**: Desktop displays QR code containing session UUID; mobile scans via camera.
3. **Hybrid QR + 6-Digit Numeric Fallback Code**: Desktop displays both a scannable QR code and a high-entropy 6-digit numeric code.

---

## Decision Outcome

Chosen option: **Option 3: Hybrid QR + 6-Digit Numeric Fallback Code**.

### Implementation Details
- The server generates a unique `session_uuid` and a 6-digit numeric pairing PIN upon visiting the landing page, stored in `tbl_pairing_codes`.
- The desktop landing page renders both a visual QR code (via `phpqrcode`) and the numeric digits.
- The mobile app allows camera scanning via ZXing or typing the 6 digits in an alert dialog.
- The code expires after a defined window, and pairing attempts are logged in `tbl_security_audit` to prevent brute-force enumeration.

### Consequences
- **Positive**: Complete compatibility with emulators and camera-impaired hardware.
- **Positive**: Zero account sign-up overhead for transient desktop sessions.
- **Negative**: Requires rate limiting on the numeric PIN endpoint to prevent local network scanning.
