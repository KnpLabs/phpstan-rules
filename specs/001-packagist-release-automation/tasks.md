# Tasks — Packagist Release Automation

**Constitution version:** 1.0.0
**Plan reference:** `specs/001-packagist-release-automation/plan.md`
**Spec reference:** `specs/001-packagist-release-automation/spec.md`
**Author:** KnpLabs
**Date:** 2026-07-15

---

## User Stories

| ID  | Story | Priority |
|-----|-------|----------|
| US1 | As a maintainer, when I publish a GitHub release, Packagist is automatically notified so the new version becomes installable via Composer without any manual step | P1 |

---

## Implementation Strategy

MVP = US1. All tasks are part of this single story. Deliver in one PR:
1. Phase 1 (Setup) unblocks Phase 2.
2. Phase 2 (Workflow) and Phase 3 (Docs) can be implemented in parallel once Phase 1 is done.
3. Phase 4 (Polish) validates the output of Phases 2–3.

---

## Phase 1 — Setup

> Establish the implementation baseline. No code shipped here.

- [x] T001 Read `.github/workflows/ci.yml` to note the actions version pinning (`actions/checkout@v7`, `actions/cache@v6`), `runs-on` value, and job naming conventions before writing the new workflow

**Phase 1 done when:** Implementation conventions are confirmed and T002 can start.

---

## Phase 2 — Workflow Implementation [US1: Automated Packagist Notification]

> Delivers the core automation: a GitHub Actions workflow that fires on release publication
> and notifies Packagist via its REST API.

**Independent test criterion (US1):**
Create a published GitHub release → the `publish` workflow appears in the Actions tab, completes
green, and the package version appears on `packagist.org/packages/knplabs/phpstan-rules`
within 5 minutes.

- [x] T002 [US1] Create `.github/workflows/publish.yml` with:
  - `on.release.types: [published]` trigger (FR-1: fires only on published releases, not drafts or pre-releases)
  - `on.workflow_dispatch:` trigger (FR-6: enables manual re-run from the Actions UI without a new release)
  - Single job `notify-packagist` running on `ubuntu-latest`
  - One step that calls `POST https://packagist.org/api/update-package` via `curl`:
    - Query params `username` and `apiToken` sourced from `${{ secrets.PACKAGIST_USERNAME }}` and `${{ secrets.PACKAGIST_API_TOKEN }}`
    - Request body `{"repository":{"url":"https://github.com/KnpLabs/phpstan-rules"}}`
    - Header `Content-Type: application/json`
    - HTTP response code captured with `--write-out "%{http_code}"` and `--output /dev/null`; step fails on any non-2xx response (FR-4)
    - Secrets passed only through environment variables; they MUST NOT be echoed or interpolated into shell strings (FR-3)

**Phase 2 done when:** T002 is complete. Workflow file is syntactically valid YAML and follows the
same `actions/checkout` version and `runs-on` value as `ci.yml`.

---

## Phase 3 — Documentation [US1]

> Satisfies constitution Principle 3: every publicly visible feature must be documented.
> Can run in parallel with Phase 2 (different file).

- [x] T003 [P] [US1] Add a "Release & Publishing" section to `README.md` covering:
  - Prerequisite: register the package on Packagist once via `packagist.org/packages/submit` (FR-2 prerequisite)
  - Step-by-step instructions to create the two required repository secrets (`PACKAGIST_USERNAME`, `PACKAGIST_API_TOKEN`) in GitHub Settings → Secrets and variables → Actions (FR-3)
  - Confirmation that publishing is automatic on release publication (FR-1)
  - Instructions for manually re-triggering via Actions → `publish` → "Run workflow" (FR-6)

**Phase 3 done when:** T003 is complete. README section references both secret names correctly
and is consistent with the workflow file created in T002.

---

## Phase 4 — Polish & Validation

> Cross-cutting audit before the PR is opened.

- [x] T004 Audit `.github/workflows/publish.yml` against the following checklist:
  - [x] No `echo`, `run: echo`, or `::set-output` statements that could print secret values
  - [x] HTTP status code check is present and causes a non-zero exit on non-2xx response
  - [x] `workflow_dispatch` trigger is present alongside the release trigger
  - [x] Job and step names are human-readable (appear clearly in the Actions UI)
  - [x] `actions/checkout` is NOT included (not needed — the workflow only calls an API, not the code)
  - [x] The workflow does not reference, import, or call `ci.yml` (FR-5: no interference)

- [x] T005 Verify `README.md` "Release & Publishing" section:
  - [x] Lists both `PACKAGIST_USERNAME` and `PACKAGIST_API_TOKEN` with correct casing
  - [x] Explains what happens on draft releases (nothing — automation does not trigger)
  - [x] Section is placed logically after the existing "Contributing" section

**Phase 4 done when:** T004 and T005 pass. PR is ready for review.

---

## Dependencies

```
T001
  └── T002 (Phase 2)
  └── T003 (Phase 3, parallel to T002)
        └── T004 (Phase 4, depends on T002)
        └── T005 (Phase 4, depends on T003)
```

---

## Parallel Execution Opportunities

| Parallel group | Tasks | Condition |
|----------------|-------|-----------|
| Group A | T002 + T003 | After T001 is complete |
| Group B | T004 + T005 | After T002 and T003 are both complete |

---

## Definition of Done

All tasks T001–T005 checked AND:
- `.github/workflows/publish.yml` exists, is valid YAML, and follows project workflow conventions
- `README.md` "Release & Publishing" section is complete and accurate
- No secrets appear in any log output
- Existing CI workflow (`ci.yml`) is untouched and unaffected
