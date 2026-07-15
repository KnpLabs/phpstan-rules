# Feature Specification — Packagist Release Automation

**Constitution version:** 1.0.0
**Status:** draft
**Author:** KnpLabs
**Date:** 2026-07-15

---

## Overview

When a maintainer publishes a GitHub release for `knplabs/phpstan-rules`, the package should
automatically become available on Packagist so that PHP developers can install it via Composer
without any manual steps from the maintainer. This eliminates the risk of forgetting to update
Packagist and ensures every release is immediately discoverable by the community.

---

## Scope

### In scope

- Triggering a Packagist update automatically whenever a GitHub release is published.
- Ensuring the release is available on Packagist within a short window after the GitHub release.
- Providing clear feedback to maintainers when the update succeeds or fails.
- Storing required credentials (Packagist API token) securely in the repository secrets.

### Out of scope

- The one-time manual registration of the package on Packagist (prerequisite, done once by a
  maintainer).
- Version number validation or changelog generation (separate concerns).
- Automated pre-release or draft release publishing.
- Publishing to other package registries (e.g., npm, PyPI).

---

## User Scenarios & Testing

### Scenario 1 — Successful release publication

1. A maintainer creates a new GitHub release (e.g., `v1.0.0`) on the repository.
2. Packagist is automatically notified within 5 minutes of the release being published.
3. The new version appears on the Packagist package page and is installable via
   `composer require knplabs/phpstan-rules`.
4. The maintainer receives confirmation (via the GitHub Actions log) that the update succeeded.

### Scenario 2 — Failed Packagist update

1. A maintainer creates a GitHub release.
2. The Packagist notification fails (e.g., invalid credentials, Packagist API outage).
3. The failure is visible in the GitHub Actions tab with a clear error message.
4. The maintainer can re-trigger the workflow manually to retry.

### Scenario 3 — Draft or pre-release (excluded)

1. A maintainer publishes a draft or pre-release on GitHub.
2. The automation does NOT trigger; Packagist is not updated.

---

## Functional Requirements

### FR-1 — Automatic trigger on release publication

The automation MUST activate when a GitHub release is published (not on draft or pre-release
events).

### FR-2 — Packagist notification

Upon activation, the automation MUST notify Packagist that a new version is available, using
the official Packagist update mechanism.

### FR-3 — Credential management

The Packagist API credentials (username and token) MUST be stored as encrypted repository
secrets. They MUST NOT appear in any log, artifact, or source file.

### FR-4 — Success and failure visibility

The outcome of the Packagist update MUST be surfaced in the GitHub Actions run: success returns
a zero exit code; failure returns a non-zero exit code and surfaces the error message.

### FR-5 — No interference with existing CI

The release automation MUST run independently from the existing CI workflow (`ci.yml`). It MUST
NOT block or be blocked by test, PHPStan, or code-style jobs.

### FR-6 — Manual re-trigger

Maintainers MUST be able to manually re-run the failed workflow from the GitHub Actions UI
without creating a new release.

---

## Success Criteria

- A new GitHub release results in the corresponding version appearing on Packagist within
  5 minutes, without any manual action from the maintainer.
- Failed notifications are surfaced immediately in the GitHub Actions tab, with a descriptive
  error message that allows the maintainer to diagnose and retry.
- Zero secrets are exposed in workflow logs or repository artifacts.
- Existing CI workflows continue to pass unaffected after the automation is added.

---

## Key Entities

| Entity | Description |
|--------|-------------|
| GitHub Release | A tagged, published release event on the repository |
| Packagist | The PHP package registry where the package is publicly listed |
| Packagist API Token | Credential authorizing the automation to call Packagist on behalf of the package owner |
| GitHub Actions Workflow | The automation job triggered by the release event |
| Repository Secret | Encrypted key-value pair stored in GitHub, injected at runtime |

---

## Dependencies & Assumptions

- **Assumption:** The package `knplabs/phpstan-rules` is already registered on Packagist before
  this automation runs. Registration is a one-time manual step outside this feature's scope.
- **Assumption:** The repository maintainer has a valid Packagist API token and can store it as
  a GitHub repository secret.
- **Assumption:** GitHub Actions is enabled for this repository (already confirmed by existing
  `ci.yml`).
- **Dependency:** Packagist public API availability (external service).

---

## Open Questions

_(None — all significant decisions resolved via assumptions above.)_
