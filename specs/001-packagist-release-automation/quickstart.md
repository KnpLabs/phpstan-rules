# Quickstart Validation Guide — Packagist Release Automation

**Feature:** `specs/001-packagist-release-automation`
**Plan:** `specs/001-packagist-release-automation/plan.md`

---

## Prerequisites

Before validating this feature, a maintainer must complete these one-time steps:

1. **Register the package on Packagist**
   - Visit [packagist.org/packages/submit](https://packagist.org/packages/submit)
   - Submit `https://github.com/KnpLabs/phpstan-rules`
   - Verify the package page appears at `packagist.org/packages/knplabs/phpstan-rules`

2. **Generate a Packagist API token**
   - Log in to packagist.org → Profile → API tokens → "Create token"
   - Copy the token value immediately (it is shown only once)

3. **Store secrets in GitHub**
   - Navigate to: `github.com/KnpLabs/phpstan-rules` → Settings → Secrets and variables → Actions
   - Add `PACKAGIST_USERNAME` = your Packagist username
   - Add `PACKAGIST_API_TOKEN` = the token from step 2

4. **Ensure the workflow file exists** at `.github/workflows/publish.yml` (created by this feature)

---

## Validation Scenarios

### Scenario A — Automated trigger on release publication

**Steps:**
1. On GitHub, create a new release: Releases → "Draft a new release"
2. Create a new tag (e.g., `v1.0.0`), set title, description
3. Click **"Publish release"** (not "Save draft")

**Expected outcome:**
- Within seconds, a new workflow run named `publish` appears in the Actions tab
- The run completes green within ~1 minute
- On `packagist.org/packages/knplabs/phpstan-rules`, the new version appears within 5 minutes

**Failure signal:**
- If the step fails, the Actions log shows the HTTP status code and Packagist error message

---

### Scenario B — Secret masking

**Steps:**
1. Inspect the Actions log of a successful `publish` run

**Expected outcome:**
- The `PACKAGIST_USERNAME` value appears as `***` in the log
- The `PACKAGIST_API_TOKEN` value appears as `***` in the log
- No plaintext credentials visible anywhere in the log

---

### Scenario C — Draft release does NOT trigger

**Steps:**
1. On GitHub, create a release and click **"Save draft"** (do NOT publish)

**Expected outcome:**
- No new `publish` workflow run appears in the Actions tab

---

### Scenario D — Manual re-trigger

**Steps:**
1. Navigate to Actions → `publish` workflow
2. Click **"Run workflow"** → select branch `main` → "Run workflow"

**Expected outcome:**
- A new workflow run starts immediately
- The run calls Packagist and completes green (if secrets are valid)

---

### Scenario E — Existing CI unaffected

**Steps:**
1. Open any passing run of the `CI` workflow

**Expected outcome:**
- All `CI` matrix jobs (PHP 8.2–8.5 × test/phpstan/cs-check) still pass
- `CI` and `publish` workflows do not reference or depend on each other

---

## Interpreting Failures

| Symptom | Likely cause | Next step |
|---------|-------------|-----------|
| HTTP 401 in log | Invalid API token | Regenerate token and update secret |
| HTTP 404 in log | Package not registered on Packagist | Complete prerequisite step 1 |
| HTTP 400 in log | Malformed request body | Check workflow file body JSON |
| Workflow not triggered | Release was draft or pre-release | Publish a full release |
| Workflow not appearing | `publish.yml` not on default branch | Check file was merged to `main` |
