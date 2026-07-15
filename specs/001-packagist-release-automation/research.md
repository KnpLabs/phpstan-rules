# Research — Packagist Release Automation

**Feature:** `specs/001-packagist-release-automation`
**Date:** 2026-07-15

---

## Decision 1 — Packagist update mechanism

**Decision:** Use the Packagist REST API (`POST /api/update-package`) via `curl` in the workflow.

**Rationale:**
Packagist exposes two update mechanisms:
- **GitHub webhook**: Packagist registers itself as a GitHub webhook; works automatically for
  public repos but requires GitHub–Packagist integration setup and doesn't give a failure signal
  in GitHub Actions.
- **Packagist API call** (`POST https://packagist.org/api/update-package?username=…&apiToken=…`
  with body `{"repository":{"url":"…"}}`): Called explicitly from a GitHub Actions step.
  Returns `HTTP 202` on success. A non-2xx response causes the workflow step to fail with a
  visible error, satisfying FR-4.

The API-call approach wins because it provides a verifiable success/failure signal inside GitHub
Actions and requires no external webhook configuration. Using `curl` keeps the workflow
dependency-free (no third-party Actions needed).

**Alternatives considered:**

| Alternative | Rejected because |
|-------------|-----------------|
| GitHub → Packagist webhook | Silent on failure; no signal in Actions run |
| `hughcube/packagist-update` Action | Third-party dependency; adds supply-chain risk |
| VCS polling by Packagist | No guarantee of immediate update; no failure signal |

---

## Decision 2 — GitHub Actions release trigger

**Decision:** `on: release: types: [published]`

**Rationale:**
The `release` event in GitHub Actions fires for multiple activity types:
- `published` — the release is publicly visible (satisfies FR-1)
- `created` — fires for draft creation (must be excluded per scope)
- `prereleased` — fires for pre-releases (must be excluded per scope)

Using `types: [published]` ensures the workflow fires only when a release transitions to the
published state, not on draft creation or pre-release tagging.

**Alternatives considered:**

| Alternative | Rejected because |
|-------------|-----------------|
| `on: push: tags:` | Fires on tag push, not release publication; no draft exclusion |
| `on: create:` | Fires on branch and tag creation, too broad |

---

## Decision 3 — Manual re-trigger (FR-6)

**Decision:** Add `workflow_dispatch:` trigger alongside `release: types: [published]`.

**Rationale:**
`workflow_dispatch` enables a maintainer to manually trigger the workflow from the GitHub Actions
UI without creating a new release. This is the standard GitHub-native approach for ad-hoc
re-runs and requires no additional tooling.

---

## Decision 4 — Secret names

**Decision:** Use `PACKAGIST_USERNAME` and `PACKAGIST_API_TOKEN` as repository secret names.

**Rationale:**
These names are the de-facto community standard (used in the Packagist documentation and
referenced in most open-source release workflows). Using the standard names makes the setup
instructions immediately recognizable to maintainers familiar with Packagist.

---

## Decision 5 — Workflow file placement

**Decision:** Create `.github/workflows/publish.yml` as a standalone file, separate from
`ci.yml`.

**Rationale:**
Keeping release automation in its own file satisfies FR-5 (no interference with CI) and follows
the GitHub Actions convention of one-workflow-one-concern. It also makes it easy to audit or
disable publishing independently of CI.

---

## Decision 6 — README documentation

**Decision:** Add a "Release & Publishing" section to `README.md` documenting the one-time setup
steps a maintainer must perform.

**Rationale:**
The automation depends on two prerequisites (Packagist registration and GitHub secret setup)
that are outside the scope of automated work. Documenting them in `README.md` ensures future
maintainers know what to do when they onboard or rotate credentials. This aligns with
constitution Principle 3 (Documentation).

---

## Resolved unknowns

All technical unknowns from the spec are now resolved. No NEEDS CLARIFICATION items remain.
