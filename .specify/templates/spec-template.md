# Feature Specification — [FEATURE_NAME]

**Constitution version:** 1.0.0
**Status:** [draft | review | approved]
**Author:** [AUTHOR]
**Date:** [DATE]

---

## Overview

[One paragraph describing the feature, the problem it solves, and why it belongs in this package.]

---

## Scope

### In scope
- [What this feature covers]

### Out of scope
- [What this feature explicitly does not cover]

---

## Rule Definition (if applicable)

**Rule identifier:** `[namespace.ruleName]`
**Node type:** `[PhpParser\Node\...]`
**Namespace / directory:** `src/Rules/[Namespace]/`

### Triggers on
```php
// [example code that should produce a violation]
```

### Does not trigger on
```php
// [example code that should NOT produce a violation]
```

### Error message
> [Exact error message string, must be actionable]

### Recommended fix
[Describe the pattern the developer should use instead.]

---

## Compatibility Requirements

<!-- Constitution Principle 1 check -->
- Minimum PHP version affected: [e.g., 8.2+]
- PHPStan version requirements: [e.g., ^2.0]
- Any new `composer.json` dependencies: [none | list with justification]

---

## Acceptance Criteria

<!-- Constitution Principle 2 & 3 check -->
- [ ] Rule class exists at `src/Rules/[Namespace]/[RuleName].php` and is `final readonly`.
- [ ] Rule is registered in `extension.neon`.
- [ ] Test class exists at `tests/Rules/[Namespace]/[RuleName]Test.php`.
- [ ] Fixture file covers at least one triggering and one non-triggering case.
- [ ] `composer test` passes on PHP 8.2, 8.3, 8.4, 8.5.
- [ ] `composer phpstan` passes with no new baseline suppressions (or suppressions justified).
- [ ] `composer cs-check` passes.
- [ ] `README.md` updated with rule documentation (identifier, summary, examples, fix).

---

## Open Questions

- [List any unresolved design or scope questions]
