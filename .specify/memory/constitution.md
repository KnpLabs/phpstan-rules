<!--
SYNC IMPACT REPORT
==================
Version change: (none) → 1.0.0
Modified principles: N/A (initial ratification)
Added sections: Project Identity, Principles 1–5, Governance
Removed sections: N/A
Templates requiring updates:
  - .specify/templates/plan-template.md    ⚠ pending (does not exist yet)
  - .specify/templates/spec-template.md   ⚠ pending (does not exist yet)
  - .specify/templates/tasks-template.md  ⚠ pending (does not exist yet)
Follow-up TODOs:
  - Create the three template files above so speckit-plan/specify/tasks can use them.
  - Expand supported PHP/PHPStan version matrix in composer.json as new releases arrive.
-->

# Project Constitution — knplabs/phpstan-rules

**Constitution Version:** 1.0.0
**Ratification Date:** 2026-07-15
**Last Amended Date:** 2026-07-15
**Maintainers:** KnpLabs organization

---

## Project Identity

**Name:** knplabs/phpstan-rules
**Package type:** PHPStan extension (open-source, MIT)
**Purpose:** Provide PHPStan static-analysis rules shared across KnpLabs organization projects,
made available to the wider PHP community as a Composer package.
**Primary audience:** PHP developers who want to enforce organization-wide coding conventions via
PHPStan.

---

## Core Principles

### Principle 1 — Broad Compatibility

Every release MUST remain installable on the widest reasonable range of PHP and PHPStan versions
to maximize adoption. Dropping a supported version is a MAJOR version bump and MUST be explicitly
justified.

**Non-negotiable rules:**
- `composer.json` `require.php` MUST target `^8.2` or broader (never pin to a single patch).
- `composer.json` `require.phpstan/phpstan` MUST support at least the current stable major series
  and, where API-compatible, the previous major.
- CI MUST gate on every declared PHP minor version (currently 8.2, 8.3, 8.4, 8.5).
- No rule implementation MUST use PHP APIs unavailable in the declared minimum version.
- Dependencies MUST be kept minimal; new `require` entries need explicit justification.

**Rationale:** Restrictive version constraints are the primary reason PHPStan extensions go
un-adopted. A wide compatibility matrix lowers the barrier to entry and encourages community
contributions.

---

### Principle 2 — Test Coverage per Rule

Every PHPStan rule MUST ship with a dedicated `PHPUnit` / `PHPStan\Testing\RuleTestCase` test
covering both triggering and non-triggering cases.

**Non-negotiable rules:**
- Each rule class in `src/Rules/<Namespace>/` MUST have a corresponding test in
  `tests/Rules/<Namespace>/` following the naming convention `<RuleName>Test.php`.
- Each test MUST include at least one fixture file in `tests/Rules/<Namespace>/fixtures/` that
  exercises both the positive (violation) and negative (no violation) paths.
- New rules MUST NOT be registered in `extension.neon` until their test passes on all declared
  PHP versions.
- CI MUST run the full test suite (`composer test`) and it MUST be green before merging.

**Rationale:** Rules without tests are liabilities — they cannot be refactored safely and may
produce false positives or negatives undetected across PHPStan/PHP upgrades.

---

### Principle 3 — Documentation per Rule

Every PHPStan rule MUST be documented in `README.md` before it is released.

**Non-negotiable rules:**
- Each rule entry in `README.md` MUST include:
  - The rule identifier (e.g., `clock.disallowDateTimeNow`).
  - A one-sentence summary of what the rule enforces and why.
  - A "Triggers on" code block with at least one violating example.
  - A "Does not trigger on" code block with at least one passing example.
  - A "Recommended fix" section showing the preferred alternative pattern.
- Documentation MUST be kept in sync with rule behavior; a PR that changes a rule's logic
  MUST also update its documentation.
- Rules that are not yet documented MUST NOT appear in a stable release.

**Rationale:** Without clear documentation, developers cannot evaluate whether a rule is
appropriate for their project, and they cannot understand why a violation was flagged.

---

### Principle 4 — Open-Source Quality Standards

All source files MUST meet the project's code-quality baseline so that community contributors
can work effectively.

**Non-negotiable rules:**
- All PHP source files MUST declare `strict_types=1`.
- Code MUST conform to PSR-12 enforced by `php-cs-fixer` (`composer cs-check` MUST pass).
- The codebase itself MUST pass PHPStan analysis at the configured level (`composer phpstan`
  MUST pass).
- PHPStan baseline (`phpstan-baseline.neon`) MUST be kept as small as possible; new suppressions
  MUST be justified in the PR description.
- Rule classes MUST be `final readonly` where PHP version and design allow.

**Rationale:** Consistent code style and static analysis lower the cognitive load for contributors
and prevent quality regressions from being introduced silently.

---

### Principle 5 — Single-Responsibility Rule Design

Each rule class MUST enforce exactly one logical constraint.

**Non-negotiable rules:**
- A rule MUST be scoped to a single AST node type (one `getNodeType()` return value per class).
- If two related constraints share an AST node, they MUST be implemented as separate classes
  unless their logic is inseparably coupled (requires explicit justification in the PR).
- Rule namespaces (`src/Rules/<Namespace>/`) MUST group rules by domain, not by node type.
- The rule's error message MUST be actionable: it MUST state what to do instead, not only
  what was found wrong.

**Rationale:** Small, focused rules are easier to test, easier to selectively disable in
downstream projects, and easier for contributors to understand and maintain.

---

## Governance

### Amendment Procedure

1. Open a GitHub issue or PR describing the proposed change and its rationale.
2. At least one maintainer MUST review and approve the change.
3. The `CONSTITUTION_VERSION` MUST be bumped according to the semantic rules below.
4. `LAST_AMENDED_DATE` MUST be updated to the merge date.
5. The Sync Impact Report (HTML comment at top of this file) MUST be refreshed.

### Versioning Policy

- **MAJOR** bump: backward-incompatible governance change — removing a principle, redefining
  a non-negotiable rule in a stricter or incompatible way.
- **MINOR** bump: new principle added, new mandatory section introduced, materially expanded
  guidance.
- **PATCH** bump: clarification, wording improvement, typo fix, non-semantic refinement.

### Compliance Review

- Constitution compliance MUST be assessed during each PR review.
- Each speckit feature cycle (specify → plan → tasks → implement) MUST reference the active
  constitution version.
- If a task or implementation choice would violate a principle, the violation MUST be escalated
  before merging — not suppressed silently.
