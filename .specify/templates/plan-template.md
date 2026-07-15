# Implementation Plan — [FEATURE_NAME]

**Constitution version:** 1.0.0
**Spec reference:** `.specify/specs/[feature-slug]/spec.md`
**Author:** [AUTHOR]
**Date:** [DATE]

---

## Constitution Check

Before finalizing this plan, confirm compliance with the active constitution:

| Principle | Check |
|-----------|-------|
| 1 — Broad Compatibility | [ ] No new PHP/PHPStan version constraint tightening |
| 2 — Test Coverage per Rule | [ ] Test class and fixture planned |
| 3 — Documentation per Rule | [ ] README section planned |
| 4 — Open-Source Quality Standards | [ ] strict_types, PSR-12, PHPStan passing |
| 5 — Single-Responsibility Rule Design | [ ] One node type per class confirmed |

---

## Approach

[Describe the chosen implementation approach and why it was selected over alternatives.]

### Alternatives considered

| Alternative | Rejected because |
|-------------|-----------------|
| [option] | [reason] |

---

## File Inventory

| File | Action | Notes |
|------|--------|-------|
| `src/Rules/[Namespace]/[RuleName].php` | Create | Core rule class |
| `tests/Rules/[Namespace]/[RuleName]Test.php` | Create | PHPUnit test |
| `tests/Rules/[Namespace]/fixtures/[name].php` | Create | Test fixture |
| `extension.neon` | Edit | Register rule service |
| `README.md` | Edit | Add rule documentation |

---

## Edge Cases & Risks

- [List known edge cases the implementation must handle]
- [List risks, e.g., PHPStan API instability, PHP version quirks]

---

## Validation Steps

1. `docker compose run --rm php vendor/bin/phpunit tests/Rules/[Namespace]/[RuleName]Test.php`
2. `docker compose run --rm php composer phpstan`
3. `docker compose run --rm php composer cs-check`
4. Manual review of fixture file to confirm positive/negative cases.
