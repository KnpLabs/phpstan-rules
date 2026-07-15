# Implementation Plan — Packagist Release Automation

**Constitution version:** 1.0.0
**Spec reference:** `specs/001-packagist-release-automation/spec.md`
**Research reference:** `specs/001-packagist-release-automation/research.md`
**Author:** KnpLabs
**Date:** 2026-07-15

---

## Constitution Check

| Principle | Check | Notes |
|-----------|-------|-------|
| 1 — Broad Compatibility | ✅ N/A | CI automation; no PHP/PHPStan constraint changes |
| 2 — Test Coverage per Rule | ✅ N/A | No new PHPStan rule added |
| 3 — Documentation per Rule | ✅ Required | `README.md` MUST document the setup prerequisites |
| 4 — Open-Source Quality Standards | ✅ Required | Workflow file must follow GitHub Actions best practices |
| 5 — Single-Responsibility Rule Design | ✅ Satisfied | Standalone `publish.yml`, separate from `ci.yml` |

---

## Approach

Create a dedicated GitHub Actions workflow that fires on `release: published` (and optionally
`workflow_dispatch` for manual re-runs). The workflow calls the Packagist REST API via `curl`
to notify Packagist that a new version is available. Credentials are sourced from encrypted
repository secrets.

No source code changes to the PHP extension itself are required.

### Alternatives considered

| Alternative | Rejected because |
|-------------|-----------------|
| GitHub webhook approach | No failure signal in Actions; harder to audit |
| Third-party publish Action | Supply-chain risk; curl is sufficient |

---

## File Inventory

| File | Action | Notes |
|------|--------|-------|
| `.github/workflows/publish.yml` | **Create** | Release trigger + Packagist API call |
| `README.md` | **Edit** | Add "Release & Publishing" section with setup steps |

---

## Workflow Design — `.github/workflows/publish.yml`

**Triggers:**
- `release: types: [published]` — automatic on GitHub release publication
- `workflow_dispatch:` — manual re-run from the Actions UI

**Jobs:**

```
notify-packagist
  runs-on: ubuntu-latest
  steps:
    1. Call Packagist update API via curl
       - URL: https://packagist.org/api/update-package
       - Method: POST
       - Query params: username, apiToken (from secrets)
       - Body: {"repository":{"url":"https://github.com/KnpLabs/phpstan-rules"}}
       - Assert HTTP response is 2xx; non-2xx fails the step
```

**Secrets required (set by maintainer once):**
- `PACKAGIST_USERNAME` — Packagist account username
- `PACKAGIST_API_TOKEN` — Packagist API token (generated on packagist.org profile page)

**Secret exposure mitigation:**
- Secrets injected as environment variables, never echoed or logged
- Passed as query parameters (HTTPS), not in the request body or log output

---

## README Changes

Add a new section **"Release & Publishing"** covering:
1. Prerequisite: register the package on Packagist once (link to Packagist docs).
2. Set `PACKAGIST_USERNAME` and `PACKAGIST_API_TOKEN` in GitHub repository secrets.
3. When a GitHub release is published, the workflow runs automatically.
4. How to manually re-trigger from the Actions UI.

---

## Edge Cases & Risks

| Case | Handling |
|------|----------|
| Packagist API outage | `curl` returns non-2xx; workflow step fails; maintainer sees error in Actions |
| Invalid credentials | Packagist returns 4xx; workflow step fails with HTTP status in logs |
| Draft or pre-release published | `types: [published]` excludes these; no trigger |
| Accidental secret exposure | Secrets masked by GitHub Actions runner; HTTPS transport |
| Workflow re-run needed | `workflow_dispatch` trigger allows manual re-run |

---

## Validation Steps

1. Create a test release on GitHub and verify:
   - The `publish` workflow appears in the Actions tab.
   - The workflow completes with a green status.
   - The new version appears on `packagist.org/packages/knplabs/phpstan-rules`.
2. Verify secret values are masked (`***`) in the workflow log output.
3. Verify that a draft release does NOT trigger the workflow.
4. Manually trigger the workflow via the Actions UI ("Run workflow" button) and confirm it runs.
5. Verify the existing `ci.yml` matrix jobs are unaffected.
