# Contract — Packagist Update API

**Used by:** `.github/workflows/publish.yml`
**External service:** packagist.org

---

## Endpoint

```
POST https://packagist.org/api/update-package
```

## Query Parameters

| Parameter  | Source                        | Description                       |
|------------|-------------------------------|-----------------------------------|
| `username` | `PACKAGIST_USERNAME` secret   | Packagist account username        |
| `apiToken` | `PACKAGIST_API_TOKEN` secret  | Packagist API token               |

## Request Body

```json
{
  "repository": {
    "url": "https://github.com/KnpLabs/phpstan-rules"
  }
}
```

`Content-Type: application/json`

## Response Codes

| Code | Meaning                                  |
|------|------------------------------------------|
| 202  | Accepted — Packagist will re-crawl soon  |
| 400  | Bad request — malformed body             |
| 401  | Unauthorized — invalid credentials       |
| 404  | Package not found on Packagist           |

## Success Condition

HTTP response code in the 2xx range. Any other code is treated as a failure and MUST cause the
GitHub Actions step to exit non-zero.

---

## Contract — GitHub Actions Release Trigger

**Event:** `release`
**Activity type:** `published`

Only fires when a release transitions from draft/pre-release to the `published` state.
Does NOT fire on `created`, `edited`, `prereleased`, or `unpublished` activity types.

**Payload fields used:**

| Field | Value | Description |
|-------|-------|-------------|
| `action` | `"published"` | Activity type filter |
| `release.tag_name` | e.g. `"v1.0.0"` | The version tag (informational; not sent to Packagist) |
| `release.prerelease` | `false` | Guaranteed false when `types: [published]` is used |
| `release.draft` | `false` | Guaranteed false when `types: [published]` is used |
