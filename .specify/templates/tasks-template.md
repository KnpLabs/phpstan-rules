# Tasks — [FEATURE_NAME]

**Constitution version:** 1.0.0
**Plan reference:** `.specify/specs/[feature-slug]/plan.md`
**Author:** [AUTHOR]
**Date:** [DATE]

---

## Task List

Tasks are ordered by dependency. Mark `[x]` when complete.

### Phase 1 — Rule Implementation

- [ ] **T1** — Create rule class `src/Rules/[Namespace]/[RuleName].php`
  - Implement `Rule<[NodeType]>`
  - Add `getNodeType()` returning `[NodeType]::class`
  - Add `processNode()` with violation detection logic
  - Ensure `final readonly` class modifier
  - Add `declare(strict_types=1)`

- [ ] **T2** — Register rule in `extension.neon`
  - Add service entry with `phpstan.rules.rule` tag

### Phase 2 — Testing (Constitution Principle 2)

- [ ] **T3** — Create fixture file `tests/Rules/[Namespace]/fixtures/[name].php`
  - Include at least one triggering case (with inline comment marking expected violation)
  - Include at least one non-triggering case

- [ ] **T4** — Create test class `tests/Rules/[Namespace]/[RuleName]Test.php`
  - Extend `PHPStan\Testing\RuleTestCase<[RuleName]>`
  - Implement `getRule()` returning a new instance of the rule
  - Add `testRule()` calling `$this->analyse()` with fixture path and expected errors

- [ ] **T5** — Verify test suite passes
  - `docker compose run --rm php composer test` — green on all PHP versions

### Phase 3 — Documentation (Constitution Principle 3)

- [ ] **T6** — Update `README.md`
  - Add rule section with identifier, summary, "Triggers on", "Does not trigger on",
    and "Recommended fix"

### Phase 4 — Quality Gate (Constitution Principle 4)

- [ ] **T7** — Verify static analysis
  - `docker compose run --rm php composer phpstan` — passes, no new baseline entries

- [ ] **T8** — Verify code style
  - `docker compose run --rm php composer cs-check` — passes

---

## Definition of Done

All tasks T1–T8 checked AND a PR reviewer has confirmed compliance with the constitution
(all five principles satisfied).
