# Implementation Plan: PSR-20 Relative Date Enforcement

**Branch**: `002-psr20-ban-relative-dates` | **Date**: 2026-07-22 | **Spec**: [spec.md](spec.md)

**Input**: Feature specification from `specs/002-psr20-ban-relative-dates/spec.md`

## Summary

Extend `Psr20Rule` to detect all PHP relative-date string literals passed to `new DateTime` or `new DateTimeImmutable` constructors, not just `'now'` and the empty case. The rule adds a private static method that tests string literals against a compiled catalogue of PCRE patterns covering PHP's documented relative date formats. Absolute date strings and variable arguments continue to pass. The rule identifier `clock.disallowDateTimeNow` and error message format are preserved for backward compatibility. Tests, fixture, and README documentation are updated accordingly.

## Technical Context

**Language/Version**: PHP ^8.2

**Primary Dependencies**: PHPStan ^2.0, php-parser (transitive via PHPStan), PHPUnit ^11 (dev)

**Storage**: N/A

**Testing**: PHPUnit 11 via `PHPStan\Testing\RuleTestCase`

**Target Platform**: PHP CLI (static analysis tool — runs during development/CI)

**Project Type**: PHPStan extension library

**Performance Goals**: Rule evaluation per node is O(number of patterns) string matching — negligible overhead.

**Constraints**: Must remain compatible with PHP 8.2–8.5 and PHPStan ^2.0. No new `require` dependencies.

**Scale/Scope**: Single rule class, single test class, single fixture file, one README section.

## Constitution Check

*GATE: Must pass before Phase 0 research. Re-check after Phase 1 design.*

| Principle | Status | Notes |
|-----------|--------|-------|
| P1 — Broad Compatibility | PASS | Changes use only PHP 8.2-compatible syntax; no new dependencies |
| P2 — Test Coverage per Rule | PASS | Existing `Psr20RuleTest` is updated; fixture extended with positive and negative cases |
| P3 — Documentation per Rule | PASS | README `clock.disallowDateTimeNow` section updated as part of this change |
| P4 — Open-Source Quality Standards | PASS | `final readonly` class retained; `strict_types=1` present; cs-fixer and phpstan must pass |
| P5 — Single-Responsibility Rule Design | PASS | Single `getNodeType()` return (`New_::class`); logic remains in one class |

No violations. Complexity Tracking section not required.

## Project Structure

### Documentation (this feature)

```text
specs/002-psr20-ban-relative-dates/
├── plan.md              # This file
├── research.md          # Phase 0 — detection approach and decisions
├── data-model.md        # Phase 1 — relative date pattern catalogue
├── quickstart.md        # Phase 1 — end-to-end validation guide
├── contracts/
│   └── rule-behavior.md # Phase 1 — triggering/non-triggering contract
├── checklists/
│   └── requirements.md  # Spec quality checklist
└── tasks.md             # Phase 2 output (created by /speckit-tasks)
```

### Source Code (repository root)

```text
src/
└── Rules/
    └── Psr/
        └── Psr20Rule.php          # Add isRelativeDateString(); update processNode()

tests/
└── Rules/
    └── Psr/
        ├── Psr20RuleTest.php      # Update expected errors; add relative-date assertions
        └── fixtures/
            └── psr20.php          # Extend with relative and absolute date examples

README.md                          # Update "Triggers on" / "Does not trigger on" for clock rule
```

**Structure Decision**: Single-project layout (Option 1). Changes are confined to the existing `src/Rules/Psr/` and `tests/Rules/Psr/` directories plus the README.

## Design

### Core Change: `Psr20Rule::isRelativeDateString()`

A new private static method is added to `Psr20Rule`. It applies a catalogue of PCRE patterns (see [data-model.md](data-model.md)) against the trimmed string literal value (case-insensitive). Returns `true` if any pattern matches.

`processNode()` is updated so that, after confirming the first argument is a `Node\Scalar\String_`, it calls `isRelativeDateString()` on the value. If `true`, the error is reported (replacing the existing `'now'`-only check).

The `'now'` case is subsumed by the new keyword pattern `/^now$/i` and no longer requires special-case handling; the existing behavior is preserved.

### Fixture and Test Updates

The existing fixture (`tests/Rules/Psr/fixtures/psr20.php`) is extended with:

**New error-triggering lines** (relative dates):
- `new DateTimeImmutable('yesterday')`
- `new DateTimeImmutable('tomorrow')`
- `new DateTimeImmutable('+1 day')`
- `new DateTimeImmutable('-2 weeks')`
- `new DateTimeImmutable('next Monday')`
- `new DateTimeImmutable('2 days ago')`
- `new DateTimeImmutable('last day of this month')`

**Lines that must not trigger** (absolute dates):
- `new DateTimeImmutable('2023-01-15')` — already present; remains a non-error
- `new DateTimeImmutable('2023-12-31 23:59:59')` — add
- `new DateTimeImmutable('2023-01-15T12:00:00+00:00')` — add

The test's expected-error array is updated to include all new triggering lines with their correct line numbers and error messages.

### README Update

The `clock.disallowDateTimeNow` rule section in `README.md` is updated:

- **Triggers on** block: add representative relative-date examples (`'yesterday'`, `'+1 day'`, `'next Monday'`).
- **Does not trigger on** block: clarify that only absolute date strings and variable arguments pass; remove `'yesterday'` from the passing examples (it currently appears there incorrectly).

### No New Dependencies

The detection logic uses PHP's built-in `preg_match` only. No composer packages are added.

## Complexity Tracking

No constitution violations — table not required.
